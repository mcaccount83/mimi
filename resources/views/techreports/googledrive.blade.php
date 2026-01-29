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
                <th>Drive Folder</th>
                <th>Folder ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($driveList as $list)
            <tr data-drive-id="{{ $list->id }}">
                <td>
                    {{ $list->description }}
                    @if ($list->version)
                        ({{ $list->version }})
                    @endif
                </td>
                <td class="folder-column">
                    <span class="folder-display">
                        {{ $list->folder_id }}
                    </span>
                    <span class="folder-edit" style="display: none;">
                        <input type="text" class="form-control form-control-sm folder-input" value="{{ $list->folder_id }}">
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm bg-gradient-primary edit-folder-btn">Edit Folder ID</button>
                    <button class="btn btn-sm bg-gradient-success save-folder-btn" style="display: none;">Save</button>
                    <button class="btn btn-sm bg-gradient-danger cancel-folder-btn" style="display: none;">Cancel</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

            </div>
        </div>
    </div>
</div>
</section>
<!-- /.content -->

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Edit button click
    $('.edit-folder-btn').on('click', function() {
        var $row = $(this).closest('tr');
        var $folderColumn = $row.find('.folder-column');

        // Show input, hide display
        $folderColumn.find('.folder-display').hide();
        $folderColumn.find('.folder-edit').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-folder-btn, .cancel-folder-btn').show();
    });

    // Cancel button click
    $('.cancel-folder-btn').on('click', function() {
        var $row = $(this).closest('tr');
        var $folderColumn = $row.find('.folder-column');

        // Hide input, show display
        $folderColumn.find('.folder-edit').hide();
        $folderColumn.find('.folder-display').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-folder-btn').hide();
        $row.find('.edit-folder-btn').show();

        // Reset input to original value
        var originalFolderId = $folderColumn.find('.folder-display').text().trim();
        $folderColumn.find('.folder-input').val(originalFolderId);
    });

    // Save button click
    $('.save-folder-btn').on('click', function() {
        var $row = $(this).closest('tr');
        var $folderColumn = $row.find('.folder-column');
        var driveId = $row.data('drive-id');
        var newFolderId = $folderColumn.find('.folder-input').val().trim();

        // Validate folder ID
        if (!newFolderId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Folder ID cannot be empty.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Send AJAX request
        $.ajax({
            url: '/admin/google-drive/' + driveId + '/update-folder',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                folder_id: newFolderId
            },
            success: function(response) {
                // Update display
                $folderColumn.find('.folder-display').text(response.folder_id);

                // Hide input, show display
                $folderColumn.find('.folder-edit').hide();
                $folderColumn.find('.folder-display').show();

                // Toggle buttons
                $row.find('.save-folder-btn, .cancel-folder-btn').hide();
                $row.find('.edit-folder-btn').show();

                // Show success message
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr) {
                var errorMessage = 'Error updating folder ID. Please try again.';
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
</script>
@endpush
