@extends('layouts.coordinator_theme')

@section('page_title', 'IT Reports')
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
                        @include('layouts.dropdown_menus.menu_reports_tech')
                    </div>
                </div>

<div class="card-body">
    <table id="chapterlist" class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Edit</th>
                <th>Drive Folder</th>
                <th>Folder ID</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($driveList as $list)
            <tr data-drive-id="{{ $list->id }}">
                <td class="text-center align-middle">
                    <a href="#" class="edit-folder-btn"
                        data-id="{{ $list->id }}"
                        data-name="{{ $list->name }}"
                        data-description="{{ $list->description }}"
                        data-version="{{ $list->version }}"
                        data-folder-id="{{ $list->folder_id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
                <td>
                    {{ $list->description }}
                    @if ($list->version)
                        ({{ $list->version }})
                    @endif
                </td>
                <td>
                    {{ $list->folder_id }}
                </td>
                 <td class="text-center align-middle"><i class="fa fa-ban"
                        onclick="showDeleteDriveModal({{ $list->id }}, '{{ $list->description }}')"
                        style="cursor: pointer; color: #dc3545;"></i>
                    </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

   <div class="card-body text-center">
        <div class="card-tools">
            <button type="button" class="btn bg-gradient-primary mb-3" id="addNewBtn">
                <i class="fas fa-plus"></i> Add New Drive Folder
            </button>
        </div>
    </div>

            </div>
        </div>
    </div>
</div>
</section>
<!-- /.content -->

<!-- Add/Edit Drive Folder Modal -->
<div class="modal fade" id="driveModal" tabindex="-1" role="dialog" aria-labelledby="driveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driveModalLabel">Add New Google Drive Folder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="driveForm">
                    @csrf
                    <input type="hidden" id="drive_id" name="drive_id">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="version">Version</label>
                        <input type="text" class="form-control" id="version" name="version" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label for="folder_id">Folder ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="folder_id" name="folder_id" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm bg-gradient-success" id="saveDriveBtn">Save</button>
                <button type="button" class="btn btn-sm bg-gradient-danger" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var isEditMode = false;

    // Add new button click
    $('#addNewBtn').on('click', function() {
        isEditMode = false;
        $('#driveModalLabel').text('Add New Google Drive Folder');
        $('#driveForm')[0].reset();
        $('#drive_id').val('');
        $('#driveModal').modal('show');
    });

    // Edit button click
$('.edit-folder-btn').on('click', function(e) {
    e.preventDefault(); // Prevent default link behavior

    isEditMode = true;
    $('#driveModalLabel').text('Edit Google Drive Folder');

    // Populate form with existing data
    $('#drive_id').val($(this).data('id'));
    $('#name').val($(this).data('name'));
    $('#description').val($(this).data('description'));
    $('#version').val($(this).data('version'));
    $('#folder_id').val($(this).data('folder-id'));

    $('#driveModal').modal('show');
});

    // Save button click
    $('#saveDriveBtn').on('click', function() {
        var formData = {
            _token: '{{ csrf_token() }}',
            name: $('#name').val().trim(),
            description: $('#description').val().trim(),
            version: $('#version').val().trim(),
            folder_id: $('#folder_id').val().trim()
        };

        // Validate required fields
        if (!formData.name || !formData.description || !formData.folder_id) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                confirmButtonText: 'OK'
            });
            return;
        }

        var url = '{{ route('techreports.addgoogledrive') }}';
        var successMessage = 'Google Drive folder added successfully!';

        if (isEditMode) {
            var driveId = $('#drive_id').val();
            url = "{{ route('techreports.updategoogledrive', ['id' => 'PLACEHOLDER']) }}";
            url = url.replace('PLACEHOLDER', driveId);
            successMessage = 'Google Drive folder updated successfully!';
        }

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                // Close modal
                $('#driveModal').modal('hide');

                // Reset form
                $('#driveForm')[0].reset();

                // Show success message
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: successMessage,
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    // Reload page to show changes
                    location.reload();
                });
            },
            error: function(xhr) {
                var errorMessage = 'Error saving folder. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

function showDeleteDriveModal(driveId, driveDescription) {
    Swal.fire({
        title: 'Drive Deletion',
        html: `
            <p>This will remove the Google Drive "<strong>${driveDescription}</strong>" from the database.</p>
            <p>PLEASE USE CAUTION, this cannot be undone!!</p>
            <input type="hidden" id="driveId" name="driveId" value="${driveId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const driveId = Swal.getPopup().querySelector('#driveId').value;

            return {
                driveId: driveId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('techreports.deletegoogledrive') }}',
                        type: 'POST',
                        data: {
                            driveId: data.driveId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Drive successfully deleted.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload();
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
    });
}
</script>
@endpush
