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
    <form class="form-horizontal" method="POST" action='{{ route("payment.updategrantdetails", $grantDetails->id) }}'>
    @csrf

    <input type="hidden" name="submitted" id="submitted" value="{{ $grantDetails['submitted'] }}" />
    <input type="hidden" name="submit_type" id="submit_type" value="" />

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                        @if($chDetails != null)
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    @else
                        <h3 class="mb-0"> {{$stateShortName}}</h3>
                    @endif
                    <p class="mb-0">{{ $chDetails->confname }} Conference, {{ $chDetails->regname }} Region
                  </p>
                    </div>

            <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item mt-2">
                <div class="d-flex align-items-center">
                    <label class="me-2 mb-0">Grant Approved:</label>
                    <div class="form-check form-switch me-4 ms-auto">
                        <input type="radio" class="form-check-input" id="approvedYes" name="grant_approved" value="1"
                            {{ $grantDetails->grant_approved === 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="approvedYes">Yes</label>
                    </div>

                    <div class="form-check form-switch me-2">
                        <input type="radio" class="form-check-input" id="approvedNo" name="grant_approved" value="0"
                            {{ $grantDetails->grant_approved === 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="approvedNo">No</label>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-8 col-form-label">Amount Awarded:</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                                <span class="input-group-text">$</span>
                            <input type="text" name="amount_awarded" id="amount_awarded" class="form-control" value="{{ $grantDetails['amount_awarded'] }}">
                        </div>
                    </div>
                </div>

                <div class="row mb-3" >
                    <label for="Review_Note">Add New Note:</label>
                <textarea class="form-control" style="width:100%" rows="3" name="Review_Note" id="Review_Note" {{ $grantDetails['review_complete'] != "" ? 'readonly' : '' }}></textarea>
                <div class="row mb-3" style="margin-left: 5px; margin-top: 5px">
                    <button type="button" id="AddNote" class="btn btn-success bg-gradient btn-sm mb-2 disabled" disabled>
                    <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Add Note to Log
                </button>
                    </div>
                </div>

                <div class="row mb-3" >
                    <label for="Review_Log">Review Notes Logged:</label>
                    <small>Not visible to chapters</small>
                    <textarea class="form-control" style="width:100%" rows="8" name="Review_Log" id="Review_Log" readonly>{{ $grantDetails['review_notes'] }}</textarea>
                </div>

                <div class="row mb-3" id="reviewNotes">
                    <label for="AssignedReviewer">Review Description:</label>
                    <small>To be published with public list information</small>
                    <textarea class="form-control" style="width:100%" rows="6" name="review_description" id="review_description">{{ $grantDetails['review_description'] }}</textarea>
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

                <li class="list-group-item mt-2">
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

                <li class="list-group-item mt-2">
                <div class="text-center">
                    @if($chDetails != null)
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
                    @endif
                </div>
                </li>

                <li class="list-group-item mt-2">
                <div class="card-body text-center mt-3">
                <button type="submit" id="save" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-floppy-fill me-2"></i>Save Grant Review</button>
                <br>
                @if ($grantDetails->review_complete != null && $grantDetails->submitted != null)
                    <button type="button" class="btn btn-success bg-gradient mb-2" id="review-clear"><i class="bi bi-dash-circle me-2"></i>Clear Review Complete</button>
                @else
                    <button type="button" class="btn btn-success bg-gradient mb-2" id="review-complete"><i class="bi bi-check-lg me-2"></i>Mark as Review Complete</button>
                @endif
                    <button type="button" class="btn btn-danger bg-gradient mb-2" id="unsubmit"><i class="bi bi-arrow-counterclockwise me-2"></i>UnSubmit Request</button>
                <br><br>
                @if ($grantDetails->grant_pdf_path != null)
                    <button class="btn btn-primary bg-gradient mb-2" type="button" id="financial-pdf" onclick="openPdfViewer('{{ $grantDetails->grant_pdf_path }}')"><i class="bi bi-file-earmark-pdf-fill me-2"></i>View/Download Grant Request PDF</button>
                @else
                    <button class="btn btn-primary bg-gradient mb-2 disabled" type="button" id="financial-pdf" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>No PDF Report Available</button><br>
                @endif
                @if($chDetails != null)
                    <button type="button" id="btn-back" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('payment.paymenthistory', $grantDetails->chapter_id) }}'"><i class="bi bi-currency-dollar me-2" ></i>Chapter Donation History</button>
                @endif
                    <button type="button" id="btn-back" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('payment.grantlist') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-currency-dollar me-2"></i>Back to Grant List</button>
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
                                @if ( $grantDetails->first_name )
                                {{ $grantDetails->first_name}} {{ $grantDetails->last_name}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <strong>Address:</strong><br>
                                @if ( $grantDetails->address )
                                {{ $grantDetails->address}}<br>
                                @if( $grantDetails->city != null){{ $grantDetails->city}}, @endif{{ $grantDetails->state?->state_short_name}} {{ $grantDetails->zip}}<br>
                                {{ $grantDetails->country?->short_name}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother-in-need been a member of your chapter? You may answer with a join date or the number of years/months she has been in your chapter. Is she a member now or has she "retired" or moved from your chapter?</label>
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
                                <label class="mb-0">Who is living in the home? Is there a spouse? How many family members and what are the ages of the children?</label>
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
                                <label class="mb-0">If the member's home is uninhabitable, where is she living now? Please provide mailing address if different from above.</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->alt_address != null)
                                {{ $grantDetails->alt_address}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>
                          <div class="mt-2">
                            <strong>Has the chapter ever asked for a grant for this mother or family in the past?</strong><br>
                            {{ is_null($grantDetails['previous_grant']) ? 'Not Answered' : ($grantDetails['previous_grant'] == 0 ? 'NO' : ($grantDetails['previous_grant'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>

                        </div>
                    </div>

                    <hr>

                     <h3 class="profile-username">EXPLAINATION OF SITUATION</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0">Please provide a summary of the situation. What happened, how did it happen and what is the result of it?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->situation_summary != null)
                                {{ $grantDetails->situation_summary}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">What has the family done to improve or handle the situation?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->family_actions != null)
                                {{ $grantDetails->family_actions}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">What is the financial situation of the family? Do they have insurance that will help with this? How much will it cover? Do they have savings? If so, how much? Are they getting help from their family or any other grants or loans?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->financial_situation != null)
                                {{ $grantDetails->financial_situation}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">What are the family’s most pressing needs right now? What are they having to do without because of this situation?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->pressing_needs != null)
                                {{ $grantDetails->pressing_needs}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Is there anything else that the family needs and is having to do without because of the situation?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->other_needs != null)
                                {{ $grantDetails->other_needs}}
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
                                <label class="mb-0">What amount is being requested? What will it be used for?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->amount_requested != null)
                                {{ $grantDetails->amount_requested}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">A chapter should always be the first ones to help a member-in-need. How has the chapter supported the member up to this point? Has the chapter done any fundraisers or made any donations to the family? What are the chapter’s future plans to help this family??</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->chapter_support != null)
                                {{ $grantDetails->chapter_support}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Is there anything else we should know about this family or their situation?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->additional_info != null)
                                {{ $grantDetails->additional_info}}
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
                        <div>
                            <strong>Does the chapter stand behind this request for a grant? Has the Executive Board discussed the situation and decided to submit this request? And does the Executive Board assure the Mother-to-Mother Fund Committee that the information in this request is true?</strong><br>
                            {{ is_null($grantDetails['chapter_backing']) ? 'Not Answered' : ($grantDetails['chapter_backing'] == 0 ? 'NO' : ($grantDetails['chapter_backing'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>
                        <div class="mt-2">
                            <strong>Has the chapter donated to the Mother-to-Mother Fund in the past?</strong><br>
                            {{ is_null($grantDetails['m2m_donation']) ? 'Not Answered' : ($grantDetails['m2m_donation'] == 0 ? 'NO' : ($grantDetails['m2m_donation'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>

                        <div class="mt-2">
                            <strong>I affirm that the information in this submission is true and the mother-in-need agrees with the submission and the information herein.</strong><br>
                            {{ is_null($grantDetails['affirmation']) ? 'Not Answered' : ($grantDetails['affirmation'] == 0 ? 'NO' : ($grantDetails['affirmation'] == 1 ? 'YES' : 'Not Answered')) }}
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
    function EnableNoteLogButton(){
        var noteValue = document.getElementById("Review_Note").value.trim();
        var button = document.getElementById("AddNote");

        if(noteValue !== ""){
            button.disabled = false;
            button.classList.remove('disabled');
        } else {
            button.disabled = true;
            button.classList.add('disabled');
        }
    }

    function AddNote(){
        // Validate note is not empty
        var noteValue = document.getElementById("Review_Note").value.trim();
        if(noteValue === ""){
            return false;
        }

        var Log = "";

        // Format: Date, User, Note
        Log = "\n{{ date('m/d/Y') }}, {{ $loggedInName }}, " + noteValue;

        // Append to log
        document.getElementById("Review_Log").value += Log;

        // Clear the note input
        document.getElementById("Review_Note").value = "";

        // Disable the button again
        document.getElementById("AddNote").disabled = true;
        document.getElementById("AddNote").classList.add('disabled');
    }

    $(document).ready(function() {
        // Add event listener for textarea input
        $("#Review_Note").on('input', function() {
            EnableNoteLogButton();
        });

        // Add event listener for AddNote button
        $("#AddNote").click(function() {
            AddNote();
        });

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
                        window.location.href = "{{ url('/payment/cleargrantreview/' . $grantDetails->id) }}";
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
                        window.location.href = "{{ url('/payment/unsubmitgrant/' . $grantDetails->id) }}";
                    }
                });
            });
        }
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
