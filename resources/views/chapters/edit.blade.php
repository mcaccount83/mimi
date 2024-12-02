@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Board Information')

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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.update", $chapterList[0]->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$chapterList[0]->state}}">
            <input type="hidden" name="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
            <input type="hidden" name="ch_hid_preknown" value="{{$chapterList[0]->former_name}}">
            <input type="hidden" name="ch_hid_sistered" value="{{$chapterList[0]->sistered_by}}">
            <input type="hidden" name="ch_hid_primarycor" value="{{$chapterList[0]->primary_coordinator_id}}">
            <input type="hidden" name="ch_hid_status" value="{{ $chapterList[0]->status }}">
            <input type="hidden" name="ch_hid_boundariesterry" value="{{ $chapterList[0]->territory}}" >

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterList[0]->statename}}</h3>
                  <p class="text-center">{{ $chapterList[0]->confname }} Conference, {{ $chapterList[0]->regname }} Region
                  <br>
                  EIN: {{$chapterList[0]->ein}}
                  </p>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                    <label class="col-form-label">IRS Notes:</label><input type="text" name="ch_einnotes" id="ch_einnotes" class="form-control float-right col-sm-8 mb-1 text-right" value="{{ $chapterList[0]->ein_notes }}" placeholder="IRS/EIN Notes">
                    </li>
                      <li class="list-group-item">
                          <b>Re-Registration Dues:</b><span class="float-right">
                              @if ($chapterList[0]->members_paid_for)
                                  <b>{{ $chapterList[0]->members_paid_for }} Members</b> on <b><span class="date-mask">{{ $chapterList[0]->dues_last_paid }}</span></b>
                              @else
                                  No Payment Recorded
                              @endif
                          </span><br>
                          <b>M2M Donation:</b><span class="float-right">
                              @if ($chapterList[0]->m2m_payment)
                                  <b>${{ $chapterList[0]->m2m_payment }}</b> on <b><span class="date-mask">{{ $chapterList[0]->m2m_date }}</span></b>
                              @else
                                  No Donation Recorded
                              @endif
                          </span><br>
                          <b>Sustaining Chapter Donation: </b><span class="float-right">
                              @if ($chapterList[0]->sustaining_donation)
                                  <b>${{ $chapterList[0]->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chapterList[0]->sustaining_date }}</span></b>
                              @else
                                  No Donation Recorded
                              @endif
                          </span>
                          <br>
                      </li>
                      <li class="list-group-item">
                        <label class="col-form-label mb-1">Founded:</label><span class="form-control-plaintext float-right col-sm-6 mb-1 text-right custom-span">{{ $chapterList[0]->startmonth }} {{ $chapterList[0]->start_year }}</span>
                           <br>
                          <label class="col-form-label mb-1">Formerly Known As:</label><input type="text" name="ch_preknown" id="ch_preknown" class="form-control float-right col-sm-6 mb-1 text-right" value="{{ $chapterList[0]->former_name }}" placeholder="Former Chapter Name">
                          <br>
                          <label class="col-form-label">Sistered By:</label><input type="text" name="ch_sistered" id="ch_sistered" class="form-control float-right col-sm-6 text-right" value="{{ $chapterList[0]->sistered_by }}" placeholder="Chapter Name">
                      </li>
                        @if($regionalCoordinatorCondition)
                        <li class="list-group-item">
                            <label class="ch_primarycor">Update Primary Coordinator:</label>
                            <select name="ch_primarycor" id="ch_primarycor" class="form-control float-right col-sm-6 text-right" style="width: 100%;" onchange="loadCoordinatorList(this.value)" required>
                                <option value="">Select Primary Coordinator</option>
                                @foreach($primaryCoordinatorList as $pcl)
                                    <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                                @endforeach
                            </select>
                            <span id="display_corlist" style="display: block; margin-top: 10px;"></span>
                        </li>
                        @else
                        <li class="list-group-item" id="display_corlist" ></li>
                        @endif
                  </ul>
                  <div class="text-center">
                      @if ($chapterList[0]->is_active == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @else
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chapterList[0]->zap_date }}</span><br>
                          {{ $chapterList[0]->disband_reason }}
                      @endif
                  </div>
                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">General Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Chapter Name:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_name" id="ch_name" class="form-control" value="{{ $chapterList[0]->name }}"  required onchange="PreviousNameReminder()">
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Boundaries:</label>
                                <div class="col-sm-10">
                                <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" value="{{ $chapterList[0]->territory }}"  required >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_status" id="ch_status"class="form-control" style="width: 100%;" required>
                                        <option value="">Select Status</option>
                                        @foreach($chapterStatusArr as $statusKey => $statusText)
                                            <option value="{{ $statusKey }}" {{ $chapterList[0]->status == $statusKey ? 'selected' : '' }}>
                                                {{ $statusText }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="ch_notes" id="ch_notes" class="form-control" value="{{ $chapterList[0]->notes }}"  placeholder="Status Notes" >
                                </div>
                            </div>
                             <!-- /.form group -->
                             <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email/Mailing:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_email" id="ch_email" class="form-control" value="{{ $chapterList[0]->email }}"  placeholder="Chapter Email Address" >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="cch_pobox" id="ch_pobox" class="form-control" value="{{ $chapterList[0]->po_box }}"  placeholder="Chapter PO Box/Mailing Address" >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Inquiries:</label>
                                <div class="col-sm-3">
                                <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" value="{{ $chapterList[0]->inquiries_contact }}"  required >
                                </div>
                                <div class="col-sm-7">
                                <input type="text" name="ch_inqnote" id="ch_inqnote" class="form-control" value="{{ $chapterList[0]->inquiries_note }}"  placeholder="Inquiries Notes" >
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Additional Information:</label>
                                <div class="col-sm-10">
                                    <textarea name="ch_addinfo" class="form-control" rows="4" >{{ $chapterList[0]->additional_info }}</textarea>
                                </div>
                            </div>
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                <div class="col-sm-7">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control"
                                           data-inputmask='"mask": "http://*{1,250}.*{2,6}"' data-mask
                                           value="{{ strpos($chapterList[0]->website_url, 'http://') === 0 ? substr($chapterList[0]->website_url, 7) : $chapterList[0]->website_url }}"
                                           onchange="updateWebsiteStatus()" placeholder="Chapter Website">
                                </div>
                                {{-- <div class="col-sm-7">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control" data-inputmask='"mask": "http://*{1,250}.*{2,6}"' data-mask  value="{{ strpos($chapterList[0]->website_url, 'http://') === 0 ? substr($chapterList[0]->website_url, 7) : $chapterList[0]->website_url }}"
                                        onchange="updateWebsiteStatus()" placeholder="Chapter Website">                                </div> --}}
                                <div class="col-sm-3">
                                    <select name="ch_webstatus" id="ch_webstatus"class="form-control" style="width: 100%;" required>
                                        <option value="">Select Status</option>
                                        @foreach($webStatusArr as $webstatusKey => $webstatusText)
                                            <option value="{{ $webstatusKey }}" {{ $chapterList[0]->website_status == $webstatusKey ? 'selected' : '' }} >
                                                {{ $webstatusText }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Social Media:</label>
                                <div class="col-sm-3.3">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chapterList[0]->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-3.3">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chapterList[0]->social1 }}" placeholder="Facebook"  >
                                </div>
                                <div class="col-sm-3.3">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chapterList[0]->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-3.3">
                                    <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chapterList[0]->social3 }}"  placeholder="Instagram" >
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
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate();"><i class="fas fa-save mr-2"></i>Save Chapter Information</button>
                @endif
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chapterList[0]->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
// Disable fields, links and buttons
var $chIsActive = @json($chIsActive);
$(document).ready(function () {
    // Disable fields for chapters that are not active
    if (($chIsActive != 1)) {
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });
    }
});

//If Chapter Name Change Warning
function PreviousNameReminder(){
    customWarningAlert("If you are changing the chapter name, please be sure to note the old name in the 'Previously Known As' field.");
}

//If Website URL Changes for Website Status Change
function updateWebsiteStatus() {
    customWarningAlert("If you are changing the chapter website url, please be sure to update the 'Link Status' accordingly.");
}

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(corId) {
        if(corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
                type: "GET",
                success: function(result) {
                $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    // Get the selected coordinator ID on page load
    var selectedCorId = $("#ch_primarycor").val();
        loadCoordinatorList(selectedCorId);

        // Update the coordinator list when the dropdown changes
        $("#ch_primarycor").change(function() {
            var selectedValue = $(this).val();
            loadCoordinatorList(selectedValue);
    });
});

</script>
@endsection
