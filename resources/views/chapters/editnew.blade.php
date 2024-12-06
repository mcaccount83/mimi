@extends('layouts.coordinator_theme')

@section('page_title', 'New Chapter Details')
@section('breadcrumb', 'New Chapter')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatenew") }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username ">Chapter Information</h3>
                  <div class="form-group row">
                    <label class="col-sm-4 col-form-label">MOMS Club of:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_name" id="ch_name" class="form-control" placeholder="Chapter Name" required>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">State:</label>
                    <div class="col-sm-8">
                        <select id="ch_state" name="ch_state" class="form-control" required>
                            <option value="">Select State</option>
                            @foreach($stateArr as $state)
                                <option value="{{$state->id}}">{{$state->state_long_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">Region:</label>
                    <div class="col-sm-8">
                        <select id="ch_region" name="ch_region" class="form-control" required>
                            <option value="">Select Region</option>
                            @foreach($regionList as $rl)
                                <option value="{{$rl->id}}">{{$rl->long_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">EIN:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_ein" id="ch_ein" class="form-control" placeholder="EIN Number">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Boundaries:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_boundariesterry" id="ch_boundariesterry" class="form-control" placeholder="Boundaries" required>
                    </div>
                </div>

                <div class="form-group row mt-1">
                    <label class="col-sm-4 col-form-label">Status:</label>
                    <div class="col-sm-8">
                        <select id="ch_status" name="ch_status" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($chapterStatusArr as $statusKey => $statusText)
                                <option value="{{ $statusKey }}">{{ $statusText }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Inquiries Email:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ch_inqemailcontact" id="ch_inqemailcontact" class="form-control" placeholder="Inquiries Email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Primary Coordinator</label>
                    <div class="col-sm-8">
                        <select name="ch_primarycor" id="ch_primarycor" class="form-control select2-bs4" style="width: 100%;" required>
                            <option value="">Select Primary Coordinator</option>
                            @foreach($primaryCoordinatorList as $pcl)
                                <option value="{{$pcl->cid}}">{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                            @endforeach
                        </select>
                    </div>
                  </div>
                  <div id="display_corlist"> </div>

                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">Board Member Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                            <!-- /.form group -->
                            <div class="form-group row">
                                <label class="col-sm-2 mb-1 col-form-label">President:</label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_fname" id="ch_pre_fname" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_lname" id="ch_pre_lname" class="form-control" required placeholder="Last Name" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_email" id="ch_pre_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required placeholder="Email Address" >
                                </div>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask  required placeholder="Phone Number" >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"></label>
                                <div class="col-sm-10 mb-1">
                                <input type="text" name="ch_pre_street" id="ch_pre_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="col-sm-5 mb-1">
                                <input type="text" name="ch_pre_city" id="ch_pre_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_pre_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}"></option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_pre_zip" id="ch_pre_zip" class="form-control" placeholder="Zip" required >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <div class="d-flex align-items-center mb-1">
                                    <label class="col-form-label mr-2">AVP:</label>
                                    <div class="custom-control custom-switch mr-5">
                                        <input type="checkbox" name="AVPVacant" id="AVPVacant" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="AVPVacant">Vacant</label>
                                    </div>
                                </div>
                                <div class="avp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_avp_fname" id="ch_avp_fname" class="form-control" required placeholder="First Name">
                                </div>
                                <div class="avp-field col-sm-5 mb-1">
                                    <input type="text" name="ch_avp_lname" id="ch_avp_lname" class="form-control" required placeholder="Last Name">
                                </div>

                                <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="avp-field col-sm-5 mb-1">
                                <input type="text" name="ch_avp_email" id="ch_avp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" required placeholder="Email Address" >
                                </div>
                                <div class="avp-field col-sm-5 mb-1">
                                <input type="text" name="ch_avp_phone" id="ch_avp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                                </div>
                                <label class="avp-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="avp-field col-sm-10 mb-1">
                                <input type="text" name="ch_avp_street" id="ch_avp_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="avp-field col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="avp-field col-sm-5 mb-1">
                                <input type="text" name="ch_avp_city" id="ch_avp_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="avp-field col-sm-3 mb-1">
                                    <select name="ch_avp_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}"></option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="avp-field col-sm-2 mb-1">
                                    <input type="text" name="ch_avp_zip" id="ch_avp_zip" class="form-control" placeholder="Zip" required >
                                </div>
                            </div>

                             <!-- /.form group -->
                             <div class="form-group row">
                                <div class="d-flex align-items-center mb-1">
                                    <label class="col-form-label mr-2">MVP:</label>
                                    <div class="custom-control custom-switch mr-5">
                                        <input type="checkbox" name="MVPVacant" id="MVPVacant" class="custom-control-input"checked>
                                        <label class="custom-control-label" for="MVPVacant">Vacant</label>
                                    </div>
                                </div>
                                <div class="mvp-field col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_fname" id="ch_mvp_fname" class="form-control" required placeholder="First Name" >
                                </div>
                                <div class="mvp-field col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_lname" id="ch_mvp_lname" class="form-control" required placeholder="Last Name" >
                                </div>
                                <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="mvp-field col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_email" id="ch_mvp_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" required placeholder="Email Address" >
                                </div>
                                <div class="mvp-field col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_phone" id="ch_mvp_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                                </div>
                                <label class="mvp-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="mvp-field col-sm-10 mb-1">
                                <input type="text" name="ch_mvp_street" id="ch_mvp_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="mvp-field col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="mvp-field col-sm-5 mb-1">
                                <input type="text" name="ch_mvp_city" id="ch_mvp_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="mvp-field col-sm-3 mb-1">
                                    <select name="ch_mvp_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}" ></option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="mvp-field col-sm-2 mb-1">
                                    <input type="text" name="ch_mvp_zip" id="ch_mvp_zip" class="form-control" placeholder="Zip" required >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <div class="d-flex align-items-center mb-1">
                                    <label class="col-form-label mr-2">Treasurer:</label>
                                    <div class="custom-control custom-switch mr-2">
                                        <input type="checkbox" name="TreasVacant" id="TreasVacant" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="TreasVacant">Vacant</label>
                                    </div>
                                </div>
                                <div class="treas-field col-sm-5 mb-1">
                                <input type="text" name="ch_trs_fname" id="ch_trs_fname" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="treas-field col-sm-5 mb-1">
                                <input type="text" name="ch_trs_lname" id="ch_trs_lname" class="form-control"  required placeholder="Last Name" >
                                </div>
                                <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="treas-field col-sm-5 mb-1">
                                <input type="text" name="ch_trs_email" id="ch_trs_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required placeholder="Email Address" >
                                </div>
                                <div class="treas-field col-sm-5 mb-1">
                                <input type="text" name="ch_trs_phone" id="ch_trs_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask  required placeholder="Phone Number" >
                                </div>
                                <label class="treas-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="treas-field col-sm-10 mb-1">
                                <input type="text" name="ch_trs_street" id="ch_trs_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="treas-field col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="treas-field col-sm-5 mb-1">
                                <input type="text" name="ch_trs_city" id="ch_trs_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="treas-field col-sm-3 mb-1">
                                    <select name="ch_trs_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}"></option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="treas-field col-sm-2 mb-1">
                                    <input type="text" name="ch_trs_zip" id="ch_trs_zip" class="form-control" placeholder="Zip" required >
                                </div>
                            </div>

                            <!-- /.form group -->
                            <div class="form-group row">
                                <div class="d-flex align-items-center mb-1">
                                    <label class="col-form-label mr-2">Secretary:</label>
                                    <div class="custom-control custom-switch mr-2">
                                        <input type="checkbox" name="SecVacant" id="SecVacant" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="SecVacant">Vacant</label>
                                    </div>
                                </div>
                                <div class="sec-field col-sm-5 mb-1">
                                <input type="text" name="ch_sec_fname" id="ch_sec_fname" class="form-control"  required placeholder="First Name" >
                                </div>
                                <div class="sec-field col-sm-5 mb-1">
                                <input type="text" name="ch_sec_lname" id="ch_sec_lname" class="form-control"  required placeholder="Last Name" >
                                </div>
                                <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="sec-field col-sm-5 mb-1">
                                <input type="text" name="ch_sec_email" id="ch_sec_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required placeholder="Email Address" >
                                </div>
                                <div class="sec-field col-sm-5 mb-1">
                                <input type="text" name="ch_sec_phone" id="ch_sec_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required placeholder="Phone Number" >
                                </div>
                                <label class="sec-field col-sm-2 mb-1 col-form-label"></label>
                                <div class="sec-field col-sm-10 mb-1">
                                <input type="text" name="ch_sec_street" id="ch_sec_street" class="form-control" placeholder="Address" required >
                                </div>
                                <label class="sec-field col-sm-2 mb-1 col-form-label"><br></label>
                                <div class="sec-field col-sm-5 mb-1">
                                <input type="text" name="ch_sec_city" id="ch_sec_city" class="form-control" placeholder="City" required >
                                </div>
                                <div class="sec-field col-sm-3 mb-1">
                                    <select name="ch_sec_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                                <option value="{{$state->state_short_name}}"></option>
                                            @endforeach
                                    </select>
                                </div>
                                <div class="sec-field col-sm-2 mb-1">
                                    <input type="text" name="ch_sec_zip" id="ch_sec_zip" class="form-control" placeholder="Zip" required >
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
                <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save New Chapter</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter List</button>
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
// Function to handle show/hide logic for vacant checkboxes
function handleVacantCheckbox(checkboxId, fieldClass) {
    var fields = $("." + fieldClass);

    $("#" + checkboxId).change(function () {
        if ($(this).prop("checked")) {
            fields.hide().find('input, select, textarea').prop('required', false).val(null);
        } else {
            fields.show().find('input, select, textarea').prop('required', true);
        }
    });

    // Initial show/hide logic on page load
    if ($("#" + checkboxId).prop("checked")) {
        fields.hide().find('input, select, textarea').prop('required', false).val(null);
    } else {
        fields.show().find('input, select, textarea').prop('required', true);
    }
}

// Apply the logic for each checkbox with a specific class
handleVacantCheckbox("MVPVacant", "mvp-field");
handleVacantCheckbox("AVPVacant", "avp-field");
handleVacantCheckbox("SecVacant", "sec-field");
handleVacantCheckbox("TreasVacant", "treas-field");

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(corId) {
        if (corId != "") {
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

function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    alert('This Email already used in the system. Please try with new one.');
                    $("#" + id).val('');
                    $("#" + id).focus();
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
    }

function validateEmailsBeforeSubmit() {
    // Get the values from the input fields
    const emails = [
        $('#ch_pre_email').val().trim(),
        $('#ch_avp_email').val().trim(),
        $('#ch_mvp_email').val().trim(),
        $('#ch_trs_email').val().trim(),
        $('#ch_sec_email').val().trim()
    ];

    // Filter out empty emails and check for duplicates
    const emailSet = new Set();
    const duplicateEmails = [];

    emails.forEach(email => {
        if (email !== '') {
            if (emailSet.has(email)) {
                // Check if the duplicate email is already in the array to avoid listing it multiple times
                if (!duplicateEmails.includes(email)) {
                    duplicateEmails.push(email);
                }
            } else {
                emailSet.add(email);
            }
        }
    });

    // If duplicates are found, show an alert
    if (duplicateEmails.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Emails Found',
            html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>') + '<br>Please correct them before submitting.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success'
            }
        });
        return false;
    }
    return true;
}

</script>
@endsection
