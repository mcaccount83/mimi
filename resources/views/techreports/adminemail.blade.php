@extends('layouts.coordinator_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'System Email Settings')

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
                            System Email Settings
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_tech')
                    </div>
                </div>

<div class="card-body">
    <table id="chapterlist" class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Edit</th>
                <th>Admin Position</th>
                <th>Notificaiton Email</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($emailList as $list)
            <tr data-email-id="{{ $list->id }}">
                <td class="text-center align-middle">
                    <a href="#" class="edit-folder-btn"
                        data-id="{{ $list->id }}"
                        data-name="{{ $list->name }}"
                        data-description="{{ $list->description }}"
                        data-folder-id="{{ $list->email }}">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
                <td>
                    {{ $list->description }}

                </td>
                <td>
                    {{ $list->email }}
                </td>
                 <td class="text-center align-middle"><i class="fa fa-ban"
                        onclick="showDeleteEmailModal({{ $list->id }}, '{{ $list->description }}')"
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
                <i class="fas fa-plus"></i> Add New System Email
            </button>
        </div>
    </div>

            </div>
        </div>
    </div>
</div>
</section>
<!-- /.content -->

<!-- Add/Edit System Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Add New System Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    @csrf
                    <input type="hidden" id="email_id" name="email_id">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="description" name="description" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm bg-gradient-success" id="saveEmailBtn">Save</button>
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
        $('#emailModalLabel').text('Add New System Email');
        $('#emailForm')[0].reset();
        $('#email_id').val('');
        $('#emailModal').modal('show');
    });

    // Edit button click - FIXED: moved outside and changed class name
    $('.edit-folder-btn').on('click', function(e) {
        e.preventDefault();

        isEditMode = true;
        $('#emailModalLabel').text('Edit System Email');

        // Populate form with existing data
        $('#email_id').val($(this).data('id'));
        $('#name').val($(this).data('name'));
        $('#description').val($(this).data('description'));
        $('#email').val($(this).data('folder-id')); // This matches your data attribute

        $('#emailModal').modal('show');
    });

    // Save button click
    $('#saveEmailBtn').on('click', function() {
        var formData = {
            _token: '{{ csrf_token() }}',
            name: $('#name').val().trim(),
            description: $('#description').val().trim(),
            email: $('#email').val().trim()
        };

        // Validate required fields
        if (!formData.name || !formData.description || !formData.email) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                confirmButtonText: 'OK'
            });
            return;
        }

        var url = '{{ route('techreports.addadminemail') }}';
        var successMessage = 'System email added successfully!';

        if (isEditMode) {
            var emailId = $('#email_id').val();
            url = "{{ route('techreports.updateadminemail', ['id' => 'PLACEHOLDER']) }}";
            url = url.replace('PLACEHOLDER', emailId);
            successMessage = 'System email updated successfully!';
        }

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                // Close modal
                $('#emailModal').modal('hide');

                // Reset form
                $('#emailForm')[0].reset();

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
                var errorMessage = 'Error saving email. Please try again.';
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

function showDeleteEmailModal(emailId, emailDescription) {
    Swal.fire({
        title: 'Email Deletion',
        html: `
            <p>This will remove the System Email "<strong>${emailDescription}</strong>" from the database.</p>
            <p>PLEASE USE CAUTION, this cannot be undone!!</p>
            <input type="hidden" id="emailId" name="emailId" value="${emailId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const emailId = Swal.getPopup().querySelector('#emailId').value;

            return {
                emailId: emailId  // FIXED: changed driveId to emailId
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
                        url: '{{ route('techreports.deleteadminemail') }}',
                        type: 'POST',
                        data: {
                            emailId: data.emailId,  // FIXED: changed driveId to emailId
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'System email successfully deleted.',
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
                        error: function(xhr) {
                            var errorMessage = 'Something went wrong, Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
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
