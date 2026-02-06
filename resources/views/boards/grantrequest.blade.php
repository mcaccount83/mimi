
@extends('layouts.board_theme')

<style>
.custom-switch .custom-control-label {
    color: #000 !important;
}
/* Or use the theme's default text color */
.custom-switch .custom-control-label {
    color: inherit !important;
}
</style>

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-12">
         <div class="card card-widget widget-user">
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                </div>
                <div class="card-body">

                    <div class="col-md-12"><br><br></div>
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center">Mother-to-Mother Fund Grant Request Details</h4>
                         {{-- @if ()
                             <h4 class="text-center">Mother-to-Mother Fund Grant Requests</h4>
                        @endif --}}
                    <div class="col-md-12"><br></div>

                        </div>
                    </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        <div class="container-fluid">
                   <form id="grant_request" name="grant_request" role="form"
    enctype="multipart/form-data" method="POST"
    action='{{ route("board.updatenewgrantrequest", ["id" => $chDetails->id]) }}'>
    @csrf

            <div class="row">
    <div class="col-12" >
         <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                         <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>BEFORE YOU BEGIN</h5>
                        <p><strong>Please read this section before filling out the questions!</strong></p>
                        <p>If your chapter is requesting assistance from the Mother-to-Mother Fund for one of your members, please read the Mother-to-Mother Fund Fact Sheet. It contains important information on what kinds of grants can be given and what kinds cannot.</p>
                        <p>Before you ask for a grant, be sure the situation fits what we can help. There are many situations we cannot help with – divorce, unemployment, and birth defects are a few. We understand those are very difficult challenges for any mother, but they cannot be helped by the Fund.</p>
                        <p>If the situation might qualify for a grant, first ask the mother-in-need if she wants you to apply for her. Some people are very private. They do not want assistance nor for people to know they have a problem. If that is the case, we cannot give a grant. While we do not publish the names of grant recipients, we do publish information about the grants that are given, and it would be easy for people who know the mother to figure out if a grant was given and how much.</p>
                        <p>Only a chapter may apply for a grant for a member. The grant request should be filled out by a member of the Executive Board. That officer will be the liaison between the Mother-to-Mother Fund Committee and the mother-in-need. A mother-in-need may not apply for a grant on her own. The request has to come from the chapter, but the chapter may work with the mother to answer the questions here. If an officer is not available, due to a natural disaster or other problem, then another member may submit the request, but the Board will be contacted to confirm the information.</p>
                        <p>Be as specific as possible in answering the questions. Be sure to fill out all questions before submitting the form!</p>
                        <br>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="understood" id="understood" class="custom-control-input" value="1" required>
                                <label class="custom-control-label" for="understood">
                                    I have read this section and understand the limits of the fund<span class="field-required">*</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="member_agree" id="member_agree" class="custom-control-input" value="1" required>
                                <label class="custom-control-label" for="member_agree">
                                    Some people do not want a grant request to be submitted for them. The mother has been asked if she wants you to submit this grant on her behalf<span class="field-required">*</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="member_accept" id="member_accept" class="custom-control-input" value="1" required>
                                <label class="custom-control-label" for="member_accept">
                                    The mother has agreed to accept a grant request if one is given<span class="field-required">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                         <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>MEMBER IN NEED</h5>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Member First Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="member_fname" required>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Members Last Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="member_lname" required>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
                         <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <h5>CHAPTER/BOARD SUBMITTING REQUEST</h5>
                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Chapter Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="chapter_name" value="{{ $chDetails->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Chapter State<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="chapter_name" value="{{$stateName}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="board_name" value="{{ $borDetails->first_name }} {{ $borDetails->last_name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Position<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="board_position" value="{{ $borDetails->position->position }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Phone<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="board_name" value="{{ $borDetails->phone }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Email<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="board_position" value="{{ $borDetails->email }}" disabled>
                                </div>
                            </div>
                        </div>

                          <div class="card-body text-center">
                            <button type="submit" id="btn-submit" class="btn btn-primary"><i class="fas fa-share-square"></i>&nbsp; Continue to Grant Request</button>
                        </div>

                </div>
            </div>
        </div>
        </div>

</form>
            <div class="card-body text-center">

                {{-- @if ($userTypeId != \App\Enums\UserTypeEnum::OUTGOING && $userTypeId != \App\Enums\UserTypeEnum::DISBANDED)
                    @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                        <button type="button" id="btn-back" class="btn btn-primary" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply mr-2" ></i>Back to Profile</a>
                    @endif
                @endif

                @if($chEOYDocuments->financial_report_received !='1')
                    <button type="button" id="btn-save" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>
                @endif

                @if($chEOYDocuments->financial_report_received =='1')
                    <button type="button" id="btn-download-pdf" class="btn btn-primary" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chEOYDocuments->$yearColumnName  }}'"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</button>
                @endif --}}
            </div>

        <!-- End Modal Popups -->
    </div>
</div>
@endsection
@section('customscript')
<script>
  /* Disable fields and buttons  */
    $(document).ready(function () {
            var userTypeId = @json($userTypeId);
            var userAdmin = @json($userAdmin);

       if (userTypeId == 1 && userAdmin != 1) {
            $('button, input, select, textarea').not('#btn-back').prop('disabled', true);
        }

        });

 /* Save & Submit Verification */
$(document).ready(function() {
    $("#btn-submit").click(function(e) {
        e.preventDefault();

        // Validation checks
        if (!ValidateContinue()) return false;

        // Use SweetAlert2 for confirmation
        Swal.fire({
            title: 'Confirmation',
            text: "This will begin your grant request, click OK to continue.",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing spinner
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                });

                // Submit the form normally
                $('#grant_request').submit();
            }
        });
    });

    function ValidateContinue() {
        var understood = document.querySelector('input[name="understood"]:checked');
        var memberAgree = document.querySelector('input[name="member_agree"]:checked');
        var memberAccept = document.querySelector('input[name="member_accept"]:checked');
        var missingFields = [];

        if (!understood) {
            missingFields.push("I have read this section and understand the limits of the fund");
        }
        if (!memberAgree) {
            missingFields.push("The mother has been asked if she wants you to submit this grant");
        }
        if (!memberAccept) {
            missingFields.push("The mother has agreed to accept a grant if one is given");
        }
        if (!document.querySelector('input[name="member_fname"]').value.trim()) {
            missingFields.push("Member First Name");
        }
        if (!document.querySelector('input[name="member_lname"]').value.trim()) {
            missingFields.push("Member Last Name");
        }

        if (missingFields.length > 0) {
            var missingFieldsText = missingFields.map(field => `<li>${field}</li>`).join('');
            var message = `<p>The following information is required:</p>
                          <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                              ${missingFieldsText}
                          </ul>`;
            customWarningAlert(message);
            return false;
        }
        return true;
    }

    function customWarningAlert(message) {
        Swal.fire({
            icon: 'warning',
            title: 'Required Information Missing',
            html: message,
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-primary'
            }
        });
    }
});

</script>
@endsection


