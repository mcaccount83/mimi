@extends('layouts.coordinator_theme')

@section('page_title', 'Inquiry Details')
@section('breadcrumb', 'Inquiry Information')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.custom-span {
    border: none !important;
    background-color: transparent !important;
    padding: 0.375rem 0 !important; /* Match the vertical padding of form-control */
    box-shadow: none !important;
}


</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("inquiries.updateinquiryapplication", $inqDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="chId" value="{{$inqDetails->chapter_id}}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center"><b>{{ $stateLongtName}}</b></h3>
                  <h3 class="profile-username text-center">{{ $regionLongName }} Region</h3>
                  <h3 class="profile-username text-center">{{ $conferenceDescription }} Conference</h3>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <div class="d-flex align-items-center justify-content-between w-100">
                                <label class="col-form-label">Chapter Available:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="available" id="available" class="custom-control-input"
                                        @if($regionalCoordinatorCondition)
                                                {{$inqDetails->available == 1 ? 'checked' : ''}}>
                                                @else
                                                {{$inqDetails->available == 1 ? 'checked' : ''}} disabled>
                                                @endif
                                    <label class="custom-control-label" for="available"></label>
                                </div>
                            </div>
                            <div class="form-group row mt-3" id="chapter-container" >
                                <label class="col-form-label col-sm-6">Chapter:</label>
                                <div class="col-sm-6">
                                    <select id="chapter" name="chapter" class="form-control float-right" required>
                                        <option value="">Select Chapter</option>
                                        @foreach($stateChapters as $chapter)
                                            <option value="{{$chapter->id}}"
                                                @if($inqDetails->chapter_id == $chapter->id) selected @endif>
                                                {{$chapter->name}}, {{$chapter->state->state_short_name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <label class="col-form-label">Response Sent:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="response" id="response" class="custom-control-input"
                                        {{$inqDetails->response == 1 ? 'checked' : ''}} disabled>
                                    <label class="custom-control-label" for="response"></label>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                Save chpater information before sending emails
                                <button type="submit" class="btn bg-gradient-primary m-1"><i class="fas fa-save mr-2"></i>Save Updates</button>
                            </div>
                    </form>
                        </li>

                        <li class="list-group-item">
                            <div class="card-body text-center">
                                Send email responses to Inquiring Member & Chapter
                                @if (($inqDetails->response != 1) && ($inqDetails->chapter_id != null))
                                    <button type="button" class="btn bg-gradient-success btn-sm m-1"
                                        onclick="showYesChapterInquiryEmailModal({{ $inqDetails->id }}, '{{ $inqDetails->inquiry_first_name }}', '{{ $inqDetails->inquiry_last_name }}', '{{ $chDetails->name }}', {{ $chapterId }})">
                                        <i class="fas fa-envelope mr-2"></i>YES CHAPTER RESPONSE</button>
                                @elseif ($inqDetails->response != 1)
                                    <button type="button" class="btn bg-gradient-success btn-sm m-1" disabled>
                                        <i class="fas fa-envelope mr-2"></i>YES CHAPTER RESPONSE</button>
                                @endif

                                @if ($inqDetails->response != 1)
                                    <button type="button" class="btn bg-gradient-danger btn-sm m-1"
                                        onclick="showNoChapterInquiryEmailModal({{ $inqDetails->id }}, '{{ $inqDetails->inquiry_first_name }}', '{{ $inqDetails->inquiry_last_name }}')">
                                        <i class="fas fa-envelope mr-2"></i>NO CHAPTER RESPONSE</button>
                                {{-- @else
                                    <button type="button" class="btn bg-gradient-danger btn-sm m-1" disabled>
                                        <i class="fas fa-envelope mr-2"></i>NO CHAPTER RESPONSE</button> --}}
                                @endif
                                <br>
                                <button type="button" class="btn bg-gradient-primary btn-sm m-1"
                                    onclick="showMemberInquiryEmailModal('{{ $inqDetails->id }}', '{{ $inqDetails->inquiry_first_name }}', '{{ $inqDetails->inquiry_last_name }}', '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                                    <i class="fas fa-envelope mr-2"></i>SEND CUSTOM EMAIL TO MEMBER</button>
                                <br>
                                @if ($inqDetails->chapter_id != null)
                                    <button type="button" class="btn bg-gradient-primary btn-sm m-1"
                                        onclick="showChapterInquiryEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $inqDetails->id }}', '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                                        <i class="fas fa-envelope mr-2"></i>SEND CUSTOM EMAIL TO CHAPTER</button>
                                @else
                                    <button type="button" class="btn bg-gradient-primary btn-sm m-1" disabled>
                                        <i class="fas fa-envelope mr-2"></i>SEND CUSTOM EMAIL TO CHAPTER</button>
                                @endif

                                 @if ($inqDetails->response != 1)
                                    <br>
                                    <br>
                                    <span style="color: red;">NOTE: Sending a Yes or No response email will automatically mark as sent.
                                    If you send a custom email and need to manually mark as sent, you can do that here.
                                    </span>
                                    <br>
                                    <form id="mark-response-form" action="{{ route('inquiries.updateinquiryresponse', ['id' => $inqDetails->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    <button type="button" class="btn bg-gradient-success btn-sm m-1"
                                            onclick="document.getElementById('mark-response-form').submit()">
                                        <i class="fas fa-check mr-2"></i>MARK RESPONSE AS SENT
                                    </button>
                                @elseif ($inqDetails->response == 1)
                                     <br>
                                    <br>
                                    <span style="color: red;">NOTE: In order to resend the Yes or No response emails to the potenial member and chapter, you will need to clear the response.
                                    </span>
                                    <br>
                                    <form id="mark-response-form" action="{{ route('inquiries.clearinquiryresponse', ['id' => $inqDetails->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                    <button type="button" class="btn bg-gradient-danger btn-sm m-1"
                                            onclick="document.getElementById('mark-response-form').submit()">
                                        <i class="fas fa-check mr-2"></i>CLEAR SENT RESPONSE
                                    </button>
                                @endif
                            </div>
                        </li>

                    </ul>
                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">

                <h3 class="profile-username">Inquiry Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Date:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->created_at->format('m-d-Y') }}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">Name:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_first_name }} {{ $inqDetails->inquiry_last_name }}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">Email:</label>
                                 <div class="col-sm-10">
                                    <p class="form-control-plaintext"><a href="mailto:{{ $inqDetails->inquiry_email }}">{{ $inqDetails->inquiry_email }}</a></p>
                                </div>
                                <label class="col-sm-2 col-form-label">Phone:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_phone}}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">Address:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_address}}<br>
                                        {{ $inqDetails->inquiry_city}}, {{ $inquiryStateShortName}} {{ $inqDetails->inquiry_zip}}<br>
                                        {{ $inquiryCountryShortName}}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">County:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_county}}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">Township:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_township}}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">Area:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_area}}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">School District:</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_school}}</p>
                                </div>
                                <label class="col-sm-2 col-form-label">Comments (not sent to chapter):</label>
                                <div class="col-sm-10">
                                    <p class="form-control-plaintext">{{ $inqDetails->inquiry_comments}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                <button type="button" id="back-pending" class="btn bg-gradient-primary m-1 keep-enabled" onclick="window.location.href='{{ route('inquiries.inquiryapplication') }}'"><i class="fas fa-reply mr-2"></i>Back to Inquiries Application List</button>
            </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->

    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chapter available and name
    const availableCheckbox = document.getElementById('available');
    const chapterContainer = document.getElementById('chapter-container');
    const chapterSelect = document.getElementById('chapter');

    // Check if elements exist before adding listeners
    if (availableCheckbox && chapterContainer && chapterSelect) {
        // Initially set chapter field visibility based on checkbox state
        toggleChapterField();

        // Add event listener to the checkbox
        availableCheckbox.addEventListener('change', toggleChapterField);

        function toggleChapterField() {
            if (availableCheckbox.checked) {
                // Show chapter field
                chapterContainer.style.display = 'flex';
                chapterSelect.setAttribute('required', 'required');
            } else {
                // Hide chapter field
                chapterContainer.style.display = 'none';
                chapterSelect.removeAttribute('required');
                chapterSelect.value = "";
            }
        }
    }
});


</script>
@endsection
