@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Google Drive Settings')

@section('content')
<!-- Main content -->
  <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Google Drive Settings
                        </h3>
                        @include('layouts.dropdown_menus.menu_admin')
                    </div>
                </div>
                 <!-- /.card-header -->
                 <div class="card-body">
                    <div class="row">
                    <!-- /.form group -->
                        <div class="col-12 ">
                    <div class="form-group">
                        <label>EIN Letters:</label>&nbsp;{{ $googleDrive[0]->ein_letter_uploads }}
                    </div>
                    <div class="form-group">
                        <label>Chapter Resources & Coordinator Toolkit:</label>&nbsp;{{ $googleDrive[0]->resources_uploads }}
                    </div>
                    <div class="form-group">
                        <label>EOY Report Attachments for {{ $googleDrive[0]->eoy_uploads_year }}:</label>&nbsp;{{ $googleDrive[0]->eoy_uploads }}
                    </div>
                    <div class="form-group">
                        <label>Disband Letters:</label>&nbsp;{{ $googleDrive[0]->disband_letter }}
                    </div>
                    <div class="form-group">
                        <label>Good Standing Letters:</label>&nbsp;{{ $googleDrive[0]->good_standing_letter }}
                    </div>
                    <div class="form-group">
                        <label>Probation Letters:</label>&nbsp;{{ $googleDrive[0]->probation_letter }}
                    </div>
                    </div>

                    <div class="card-body text-center">
                        <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#editDrive"><i class="fas fa-redo"  ></i>&nbsp; Update Shared Drive ID</button>
                    </div>

                </div>
            </div>

             <!-- Modal for editing task -->
             <div class="modal fade" id="editDrive" tabindex="-1" role="dialog" >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="editDrive">Google Drive for Uploads</h3>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.updategoogledrive', $googleDrive[0]->id) }}">
                                @csrf
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="einLetterDrive">Shared Drive ID for EIN Letters</label>
                                        <input type="text" class="form-control" id="einLetterDrive" value="{{ $googleDrive[0]->ein_letter_uploads }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="resourcesDrive">Shared Drive ID for Chapter Resources & Coordinator Toolkit</label>
                                        <input type="text" class="form-control" id="resourcesDrive" value="{{ $googleDrive[0]->resources_uploads }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="eoyDrive">Shared Drive ID for EOY Report Attachmnets</label>
                                        <input type="text" class="form-control" id="eoyDrive" value="{{ $googleDrive[0]->eoy_uploads }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="eoyDriveYear">Subfolder for Attachments based on Report Year</label>
                                        <input type="text" class="form-control" id="eoyDriveYear" value="{{ $googleDrive[0]->eoy_uploads_year }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="disbandDrive">Shared Drive ID for Disband Letters</label>
                                        <input type="text" class="form-control" id="disbandDrive" value="{{ $googleDrive[0]->disband_letter }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="goodStandingDrive">Shared Drive ID for Good Standing Letters</label>
                                        <input type="text" class="form-control" id="goodStandingDrive" value="{{ $googleDrive[0]->good_standing_letter }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="probationDrive">Shared Drive ID for Probation Letters</label>
                                        <input type="text" class="form-control" id="probationDrive" value="{{ $googleDrive[0]->probation_letter }}">
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-12"><br></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                            <button type="button" class="btn btn-success" id="saveChanges"><i class="fas fa-save"></i>&nbsp; Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </div>
</div>
</section>
<!-- /.content -->

@endsection
<script>

document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('saveChanges').addEventListener('click', updateDrive);
});

function updateDrive() {
    console.log('Save button clicked');  // For debugging

    var einLetterDrive = document.getElementById('einLetterDrive').value;
    var eoyDrive = document.getElementById('eoyDrive').value;
    var eoyDriveYear = document.getElementById('eoyDriveYear').value;
    var resourcesDrive = document.getElementById('resourcesDrive').value;
    var disbandDrive = document.getElementById('disbandDrive').value;
    var goodStandingDrive = document.getElementById('goodStandingDrive').value;
    var probationDrive = document.getElementById('probationDrive').value;

    var formData = new FormData();
    formData.append('einLetterDrive', einLetterDrive);
    formData.append('eoyDrive', eoyDrive);
    formData.append('eoyDriveYear', eoyDriveYear);
    formData.append('resourcesDrive', resourcesDrive);
    formData.append('disbandDrive', disbandDrive);
    formData.append('goodStandingDrive', goodStandingDrive);
    formData.append('probationDrive', probationDrive);

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    $.ajax({
        url: '{{ route('admin.updategoogledrive') }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            alert('Shared DriveId Updated Successfully');
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle error if needed
        }
    });

    // Close the modal
    $('#editDrive').modal('hide');
}

</script>
</html>
