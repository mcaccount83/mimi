@extends('layouts.mimi_theme')

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
                <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                        <h3><b>{{ $inqDetails->state->state_long_name}}</b></h3>
                        <h3>{{ $inqDetails->state->region->long_name }} Region</h3>
                        <h3>{{ $inqDetails->state->conference->conference_description }} Conference</h3>
                    </div>

                  <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <div class="row mb-1">
                                <label class="col-auto">Chapter Available:</label>
                                <div class="col text-end form-switch">
                                    <input type="checkbox" name="available" id="available" class="form-check-input"
                                        {{$inqDetails->available == 1 ? 'checked' : ''}}>
                                    <label class="form-check-label" for="available"></label>
                                </div>
                            </div>
                            <div class="row mb-1" id="chapter-container" >
                                <label class="col-form-label col-sm-6">Chapter:</label>
                                <div class="col-sm-6">
                                    <select id="chapter" name="chapter" class="form-control float-end" required>
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
                            <div class="row mb-1">
                                    <label class="col-auto">Response Sent:</label>
                                <div class="fcol text-end form-switch">
                                    <input type="checkbox" name="response" id="response" class="form-check-input"
                                        {{$inqDetails->response == 1 ? 'checked' : ''}} disabled>
                                    <label class="form-check-label" for="response"></label>
                                </div>
                            </div>
                            <div class="card-body text-center mt-3">
                                Save chapter information before sending emails
                                <button type="submit" class="btn btn-primary bg-gradient m-1"><i class="bi bi-floppy-fill me-2"></i>Save Updates</button>
                            </div>
                    </form>
                        </li>

                        <li class="list-group-item">
                            <div class="card-body text-center mt-3">
                                Send email responses to Inquiring Member & Chapter
                                @if (($inqDetails->response != 1) && ($inqDetails->chapter_id != null))
                                    <button type="button" class="btn btn-success bg-gradient btn-sm m-1"
                                        onclick="showYesChapterInquiryEmailModal({{ $inqDetails->id }}, '{{ $inqDetails->inquiry_first_name }}', '{{ $inqDetails->inquiry_last_name }}', '{{ $chDetails->name }}', {{ $chapterId }})">
                                        <i class="bi bi-envelope-fill me-2"></i>YES CHAPTER RESPONSE</button>
                                @elseif ($inqDetails->response != 1)
                                    <button type="button" class="btn btn-success bg-gradient btn-sm m-1" disabled>
                                        <i class="bi bi-envelope-fill me-2"></i>YES CHAPTER RESPONSE</button>
                                @endif

                                @if ($inqDetails->response != 1)
                                    <button type="button" class="btn btn-danger bg-gradient btn-sm m-1"
                                        onclick="showNoChapterInquiryEmailModal({{ $inqDetails->id }}, '{{ $inqDetails->inquiry_first_name }}', '{{ $inqDetails->inquiry_last_name }}')">
                                        <i class="bi bi-envelope-fill me-2"></i>NO CHAPTER RESPONSE</button>
                                @endif
                                <br>
                                <button type="button" class="btn btn-primary bg-gradient btn-sm m-1"
                                    onclick="showMemberInquiryEmailModal('{{ $inqDetails->id }}', '{{ $inqDetails->inquiry_first_name }}', '{{ $inqDetails->inquiry_last_name }}', '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                                    <i class="bi bi-envelope-fill me-2"></i>SEND CUSTOM EMAIL TO MEMBER</button>
                                <br>
                                @if ($inqDetails->chapter_id != null)
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm m-1"
                                        onclick="showChapterInquiryEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $inqDetails->id }}', '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                                        <i class="bi bi-envelope-fill me-2"></i>SEND CUSTOM EMAIL TO CHAPTER</button>
                                @else
                                    <button type="button" class="btn btn-primary bg-gradient btn-sm m-1" disabled>
                                        <i class="bi bi-envelope-fill me-2"></i>SEND CUSTOM EMAIL TO CHAPTER</button>
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
                                    <button type="button" class="btn btn-success bg-gradient btn-sm m-1"
                                            onclick="document.getElementById('mark-response-form').submit()">
                                        <i class="bi bi-check-lg me-2"></i>MARK RESPONSE AS SENT
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
                                    <button type="button" class="btn btn-danger bg-gradient btn-sm m-1"
                                            onclick="document.getElementById('mark-response-form').submit()">
                                        <i class="bi bi-ban me-2"></i>CLEAR SENT RESPONSE
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
                <div class="card-body">
                    <div class="card-header bg-transparent border-0">
                            <h3>Inquiry Information</h3>
                     </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Date:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->created_at->format('m-d-Y') }}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Name:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_first_name }} {{ $inqDetails->inquiry_last_name }}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Email:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                <a href="mailto:{{ $inqDetails->inquiry_email }}">{{ $inqDetails->inquiry_email }}</a>
                            </div>
                        </div>
                         <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Phone:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_phone}}
                            </div>
                        </div>
                         <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Address:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_address}}<br>
                                        {{ $inqDetails->inquiry_city}}, {{ $inqDetails->inquirystate->state_short_name}} {{ $inqDetails->inquiry_zip}}<br>
                                        {{ $inqDetails->inquirycountry->short_name}}
                            </div>
                        </div>
                         <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>County:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_county}}
                            </div>
                        </div>
                         <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Township:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_township}}
                            </div>
                        </div>
                         <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Area:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_area}}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>School District:</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_school}}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-sm-2 mb-2">
                                <label>Comments (not sent to chapter):</label>
                            </div>
                            <div class="col-sm-8 mb-2">
                                {{ $inqDetails->inquiry_comments}}
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
            <div class="card-body text-center mt-3">
                @if ($confId == $inqConfId)
                    <button type="button" id="back-inquiries" class="btn btn-primary bg-gradient m-1 keep-enabled" onclick="window.location.href='{{ route('inquiries.inquiryapplication') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to Inquiries Application List</button>
                @elseif ($confId != $inqConfId && ($inquiriesInternationalCondition || $ITCondition))
                    <button type="button" id="back-inquiries" class="btn btn-primary bg-gradient m-1 keep-enabled" onclick="window.location.href='{{ route('inquiries.inquiryapplication', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to International Inquiries Application List</button>
                @endif
               </div>
            </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')

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
