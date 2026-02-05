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
    <form class="form-horizontal" method="POST" action='{{ route("adminreports.updategrantdetails", $grantDetails->id) }}'>
    @csrf

    <input type="hidden" name="submitted" id="submitted" value="{{ $grantDetails['submitted'] }}" />
    <input type="hidden" name="submit_type" id="submit_type" value="" />

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                 <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                <br>
                </p>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                <div class="d-flex align-items-center">
                    <label class="mr-3 mb-0">Grant Approved:</label>
                    <div class="custom-control custom-switch mr-4 ml-auto">
                        <input type="radio" class="custom-control-input" id="approvedYes" name="grant_approved" value="1"
                            {{ $grantDetails->grant_approved === 1 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="approvedYes">Yes</label>
                    </div>

                    <div class="custom-control custom-switch mr-2">
                        <input type="radio" class="custom-control-input" id="approvedNo" name="grant_approved" value="0"
                            {{ $grantDetails->grant_approved === 0 ? 'checked' : '' }}>
                        <label class="custom-control-label" for="approvedNo">No</label>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label class="col-md-8 col-form-label">Amount Rewarded:</label>
                    <div class="col-sm-4">
                        <div class="input-group row">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="text" name="amount_awarded" id="amount_awarded" class="form-control" value="{{ $grantDetails['amount_awarded'] }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2" id="reviewNotes">
                    <label for="AssignedReviewer">Review Notes:</label>
                    <textarea class="form-control" style="width:100%" rows="6" name="review_notes" id="review_notes">{{ $grantDetails['review_notes'] }}</textarea>
                </div>

                 <div class="d-flex align-items-center justify-content-between w-100">
                    <span style="display: inline; color: red;">Reviewer will be used as the From/Signature for email correspondence.
                        Please udpate as needed (prior to triggering emails).<br></span>
                </div>
                <div class="d-flex align-items-center justify-content-between w-100">
                    <label for="reviewer_id">Assigned Reviewer:</label>
                    <select class="form-control" name="reviewer_id" id="reviewer_id" style="width: 250px;"  required>
                        <option value="" style="display:none" disabled selected>Select a reviewer</option>
                        @foreach($grList as $coordinator)
                            <option value="{{ $coordinator['cid'] }}"
                                {{ isset($grantDetails->reviewer_id) && $grantDetails->reviewer_id == $coordinator['cid'] ? 'selected' : '' }}>
                                {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" id="grant_reviewer" value="{{ $grantDetails->reviewer_id }}">
                </li>

                <li class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <label>Submitted By:</label>
                        {{ $grantDetails->board_name }} ({{ $grantDetails->board_position }})
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <label>Email:</label>
                        <a href="mailto:{{ $grantDetails->board_email }}">{{ $grantDetails->board_email }}</a>
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <label>Phone:</label>
                        {{ $grantDetails->board_phone }}
                    </div>
                </li>

                <li class="list-group-item">
                <div class="text-center">
                    @if ($chDetails->active_status == 1 )
                        <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                    @elseif ($chDetails->active_status == 2)
                    <b><span style="color: #ff851b;">Chapter is PENDING</span></b>
                    @elseif ($chDetails->active_status == 3)
                    <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b><br>
                        Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                        {{ $chDetails->disband_reason }}
                    @elseif ($chDetails->active_status == 0)
                        <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                        Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                        {{ $chDetails->disband_reason }}
                    @endif
                </div>
                </li>

                <li class="list-group-item">
                <div class="card-body text-center">
                <button type="submit" id="save" class="btn bg-gradient-primary mb-2"><i class="fas fa-save mr-2"></i>Save Grant Review</button>
                <br>
                @if ($grantDetails->review_complete != null && $grantDetails->submitted != null)
                    <button type="button" class="btn bg-gradient-success" id="review-clear"><i class="fas fa-minus-circle mr-2"></i>Clear Review Complete</button>
                @else
                    <button type="button" class="btn bg-gradient-success" id="review-complete"><i class="fas fa-check mr-2"></i>Mark as Review Complete</button>
                @endif
                    <button type="button" class="btn bg-gradient-danger" id="unsubmit"><i class="fas fa-undo mr-2"></i>UnSubmit Request</button>
                <br><br>
                @if ($grantDetails->grant_pdf_path != null)
                    <button class="btn bg-gradient-primary mb-2" type="button" id="financial-pdf" onclick="openPdfViewer('{{ $grantDetails->grant_pdf_path }}')"><i class="fas fa-file-pdf mr-2"></i>View/Download Grant Request PDF</button>
                @else
                    <button class="btn bg-gradient-primary mb-2 disabled" type="button" id="financial-pdf" disabled><i class="fas fa-file-pdf mr-2"></i>No PDF Report Available</button><br>
                @endif
                    <button type="button" id="btn-back" class="btn btn-primary mb-2" onclick="window.location.href='{{ route('adminreports.grantlist') }}'"><i class="fas fa-reply mr-2" ></i>Back to Grant List</button>
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
                     @if($grantDetails->submitted != '1')
                        <h3><span style="color:red">GRANT DRAFT NOT YET SUBMITTED FOR REVIEW</span></h3>
                    @endif

                <h3 class="profile-username">GRANT INFORMATION</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                        <div>
                            <strong>I have read this section and understand the limits of the fund:</strong><br>
                            {{ is_null($grantDetails['understood']) ? 'Not Answered' : ($grantDetails['understood'] == 0 ? 'NO' : ($grantDetails['understood'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>
                        <div class="mt-2">
                            <strong>The mother has been asked if she wants you to submit this grant on her behalf:</strong><br>
                            {{ is_null($grantDetails['member_agree']) ? 'Not Answered' : ($grantDetails['member_agree'] == 0 ? 'NO' : ($grantDetails['member_agree'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>
                        <div class="mt-2">
                            <strong>The mother has agreed to accept a grant request if one is given:</strong><br>
                            {{ is_null($grantDetails['member_accept']) ? 'Not Answered' : ($grantDetails['member_accept'] == 0 ? 'NO' : ($grantDetails['member_accept'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>

                        </div>
                    </div>

                    <hr>

                    <h3 class="profile-username">MEMBER IN NEED</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <strong>Name:</strong><br>
                                {{ $grantDetails->first_name}} {{ $grantDetails->last_name}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <strong>Address:</strong><br>
                                {{ $grantDetails->address}}<br>
                                @if( $grantDetails->city != null){{ $grantDetails->city}}, @endif{{ $grantDetails->state?->state_short_name}} {{ $grantDetails->zip}}<br>
                                {{ $grantDetails->country?->short_name}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother been a member?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->member_length != null)
                                {{ $grantDetails->member_length}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        </div>
                    </div>

                    <hr>

                    <h3 class="profile-username">GRANT REQUEST DETAILS</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother been a member?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->member_length != null)
                                {{ $grantDetails->member_length}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>


                        </div>
                    </div>

                    <hr>

                    <h3 class="profile-username">CHAPTER BACKING & AFFIRMATION</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                          <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother been a member?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->member_length != null)
                                {{ $grantDetails->member_length}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="mt-2">
                            <strong>I affirm that the information in this submission is true:</strong><br>
                            {{ is_null($grantDetails['affirmation']) ? 'Not Answered' : ($grantDetails['affirmation'] == 0 ? 'NO' : ($grantDetails['affirmation'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>

                        </div>
                    </div>

                    <hr>



                </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->

          <div class="col-md-12">
            <div class="card-body text-center">


            </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields')
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const reviewClearButton = document.getElementById('review-clear');
    const unsubmitButton = document.getElementById('unsubmit');

    if (reviewClearButton) {
        reviewClearButton.addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will clear the 'review complete' flag and coordinators will be able to edit the report again. Do you wish to continue?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Clear Review',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('/adminreports/cleargrantreview/' . $grantDetails->id) }}";
                }
            });
        });
    }

    if (unsubmitButton) {
        unsubmitButton.addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Unsubmitting this request will make it editable by the chapter again and will disable coordinator editing until the chapter has resubmitted - any unsaved changes will be lost.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Unsubmit',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url('/adminreports/unsubmitgrant/' . $grantDetails->id) }}";
                }
            });
        });
    }
});

$(document).ready(function() {
    $("#review-complete").click(function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will finalize this review and flag it as 'review complete'. Do you wish to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Mark Complete',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $("#submit_type").val('review_complete');
                $("form").submit();
            }
        });
    });
});

</script>
<script>

    /* Disable fields and buttons  */
  $(document).ready(function () {
    var completed = @json($grantDetails->review_complete);
    var submitted =  @json($grantDetails->submitted);

    if (submitted != '1') {
        $('button').not('#btn-back').prop('disabled', true);
        $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').prop('disabled', true);
    } else if (completed == '1') {
        $('button').not('#btn-back, #review-clear, #financial-pdf, #generate-pdf').prop('disabled', true);
        $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').prop('disabled', true);
    } else {
        // Don't disable #review-complete, #save, and #unsubmit when submitted but not completed
        $('button').not('#btn-back, #review-complete, #save, #unsubmit, #financial-pdf, #generate-pdf').prop('disabled', false);
        $('button, input, select, textarea').prop('disabled', false);
    }

    var allDisabled = true;
    $('input, select, textarea').not('#logout-form input, #logout-form select, #logout-form textarea').each(function() {
        if (!$(this).prop('disabled')) {
            allDisabled = false;
            return false;
        }
    });

    if (allDisabled) {
        $('.description').show();
    } else {
        $('.description').hide();
    }
});

</script>
@endsection
