@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'IRS Information')

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
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updateirs", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="ch_state" value="{{$stateShortName}}">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName}} Region
                  <br>
                  EIN: {{$chDetails->ein}}
                  </p>
                    {{-- <div class="card-body text-center">
                        @if($chDocuments->ein_letter_path != null)
                  <button class="btn bg-gradient-primary btn-sm mb-3" onclick="window.open('{{ $chDocuments->ein_letter_path }}', '_blank')">View/Download EIN Letter</button>
              @else
                  <button class="btn bg-gradient-primary btn-sm mb-3 disabled">No EIN Letter on File</button>
              @endif
              <br>
                        <button type="button" class="btn bg-gradient-primary btn-sm" onclick="updateEIN()">Update EIN Number</button>
                        <button class="btn bg-gradient-primary btn-sm showFileUploadModal" data-ein-letter="{{ $chDocuments->ein_letter_path }}">Update EIN Letter</button>
                    </div> --}}

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">

                            <b>Founded:</b> <span class="float-right">{{ $startMonthName }} {{ $chDetails->start_year }}</span>

                            </li>                            <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                            <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                  </ul>
                  <div class="text-center">
                      @if ($chDetails->is_active == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @else
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
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
                <h3 class="profile-username">IRS Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
<!-- /.form group -->
<div class="form-group row align-items-center mb-3">
    <label class="col-sm-2 col-form-label">EIN Letter Received:</label>
    <div class="col-sm-10 custom-control custom-switch">
        <input type="checkbox" name="ch_ein_letter_display" id="ch_ein_letter" class="custom-control-input" {{$chDocuments->ein_letter == 1 ? 'checked' : ''}} disabled>
        <label class="custom-control-label" for="ch_ein_letter"></label>
        <!-- Hidden input to submit the value -->
        <input type="hidden" name="ch_ein_letter" value="{{ $chDocuments->ein_letter }}">
    </div>
</div>

                        <!-- /.form group -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">EIN Letter:</label>
                            <div class="col-sm-10">
                                @if($chDocuments->ein_letter_path != null)
                                <button class="btn bg-gradient-primary btn-sm" onclick="window.open('{{ $chDocuments->ein_letter_path }}', '_blank')">View/Download EIN Letter</button>
                            @else
                                <button class="btn bg-gradient-primary btn-sm disabled">No EIN Letter on File</button>
                            @endif
                        </div>
                        </div>

 <!-- /.form group -->
 <div class="form-group row align-items-center  mb-3">
    <label class="col-sm-2 col-form-label">990N Verifed with IRS:</label>
    <div class="col-sm-10 custom-control custom-switch">
        <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input" {{$chDocuments->irs_verified == 1 ? 'checked' : ''}}>
        <label class="custom-control-label" for="irs_verified"></label>
    </div>
</div>




                                                <!-- /.form group -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label mb-1">IRS Notes:</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" name="irs_notes" id="irs_notes" class="form-control" value="{{ $chDocuments->irs_notes }}" placeholder="990N Notes">
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
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="updateEIN()"><i class="fas fa-university mr-2"></i>Update EIN Number</button>
                <button class="btn bg-gradient-primary mb-3 showFileUploadModal" data-ein-letter="{{ $chDocuments->ein_letter_path }}"><i class="fas fa-upload mr-2"></i>Update EIN Letter</button>
<br>
                    <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return PreSaveValidate();"><i class="fas fa-save mr-2"></i>Save IRS Information</button>
                @endif
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapreports.chaprpteinstatus') }}'"><i class="fas fa-reply mr-2"></i>Back to IRS Status Report</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter Details</button>
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

function updateEIN() {
    const chapterId = '{{ $chDetails->id }}'; // Get the chapter ID from the Blade variable

    // Check if the chapter already has an EIN
    $.ajax({
        url: '{{ route('chapters.checkein') }}',
        type: 'GET',
        data: {
            chapter_id: chapterId
        },
        success: function(response) {
            if (response.ein) {
                // Show a warning if an EIN already exists
                Swal.fire({
                    title: 'Warning!',
                    text: 'This chapter already has an EIN. Do you want to replace it?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, replace it',
                    cancelButtonText: 'No',
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                        cancelButton: 'btn-sm btn-danger'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed to input the new EIN
                        promptForNewEIN(chapterId);
                    }
                });
            } else {
                // No existing EIN, proceed directly
                promptForNewEIN(chapterId);
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error!',
                text: 'Unable to check the existing EIN. Please try again later.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                }
            });
        }
    });
}

// Function to prompt the user for a new EIN
function promptForNewEIN(chapterId) {
    Swal.fire({
        title: 'Enter EIN',
        html: `
            <p>Please enter the EIN for the chapter.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="ein" name="ein" class="swal2-input" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask placeholder="Enter EIN" required style="width: 100%;">
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            <br>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const ein = Swal.getPopup().querySelector('#ein').value;

            return {
                chapter_id: chapterId,
                ein: ein,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request to update the EIN
            $.ajax({
                url: '{{ route('chapters.updateein') }}',
                type: 'POST',
                data: {
                    chapter_id: data.chapter_id,
                    ein: data.ein,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.showFileUploadModal').addEventListener('click', function(e) {
        e.preventDefault();

        const einLetter = this.getAttribute('data-ein-letter');

        Swal.fire({
            title: 'Upload EIN Letter',
            html: `
                <form id="uploadEINForm" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" required>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            cancelButtonText: 'Close',
            preConfirm: () => {
                const formData = new FormData(document.getElementById('uploadEINForm'));

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we upload your file.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        $.ajax({
                            url: '{{ url('/files/storeEIN/'. $id) }}',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'File uploaded successfully!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(jqXHR, exception) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong, please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });

                return false;
            },
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        });
    });
});


</script>
@endsection
