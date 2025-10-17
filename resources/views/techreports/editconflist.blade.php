@extends('layouts.coordinator_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'Conference List')

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
                                Conference List
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_tech')
                        </div>
                        <div class="card-tools">
                            <button class="btn btn-success btn-sm" onclick="addNewRow()">
                                <i class="fas fa-plus"></i> Add Conference
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        <div id="message-container"></div>
                        <table id="chapterlist" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Conf Number</th>
                                    <th>Conf Name</th>
                                    <th>Short Name</th>
                                    <th>Description</th>
                                    <th>Abbreviation</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($confList as $conference)
                                    <tr id="conference-{{ $conference->id }}" data-id="{{ $conference->id }}">
                                        <td class="conf-id">{{ $conference->id }}</td>
                                        <td class="conference-name">{{ $conference->conference_name }}</td>
                                        <td class="short-name">{{ $conference->short_name }}</td>
                                        <td class="conference-description">{{ $conference->conference_description }}</td>
                                        <td class="short-description">{{ $conference->short_description }}</td>
                                        <td class="table-actions">
                                            <button class="btn btn-primary btn-sm edit-btn" onclick="editRow(this)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-btn" onclick="deleteRow(this)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<style>
    .editing {
        background-color: #fff3cd;
    }
    .add-row {
        background-color: #e3f2fd;
    }
    .table-actions {
        white-space: nowrap;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;
        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });

    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

let editingRow = null;

function editRow(button) {
    const row = button.closest('tr');
    const id = row.dataset.id;

    // If already editing another row, cancel it
    if (editingRow && editingRow != row) {
        cancelEdit(editingRow);
    }

    editingRow = row;
    row.classList.add('editing');

    // Get current values
    const confName = row.querySelector('.conference-name').textContent;
    const shortName = row.querySelector('.short-name').textContent;
    const confDescription = row.querySelector('.conference-description').textContent;
    const shortDescription = row.querySelector('.short-description').textContent;

    // Replace with input fields
    row.querySelector('.conference-name').innerHTML = `<input type="text" class="form-control form-control-sm" value="${confName}">`;
    row.querySelector('.short-name').innerHTML = `<input type="text" class="form-control form-control-sm" value="${shortName}">`;
    row.querySelector('.conference-description').innerHTML = `<input type="text" class="form-control form-control-sm" value="${confDescription}">`;
    row.querySelector('.short-description').innerHTML = `<input type="text" class="form-control form-control-sm" value="${shortDescription}">`;

    // Change buttons
    row.querySelector('.table-actions').innerHTML = `
        <button class="btn btn-success btn-sm" onclick="saveRow(this)">
            <i class="fas fa-save"></i> Save
        </button>
        <button class="btn btn-secondary btn-sm" onclick="cancelEdit(this.closest('tr'))">
            <i class="fas fa-times"></i> Cancel
        </button>
    `;
}

function saveRow(button) {
    const row = button.closest('tr');
    const id = row.dataset.id;

    // Get values from inputs
    const confName = row.querySelector('.conference-name input').value;
    const shortName = row.querySelector('.short-name input').value;
    const confDescription = row.querySelector('.conference-description input').value;
    const shortDescription = row.querySelector('.short-description input').value;

    // Validate
    if (!confName.trim() || !shortName.trim() || !confDescription.trim() || !shortDescription.trim()) {
        showMessage('Please fill in all required fields', 'danger');
        return;
    }

    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    // Make AJAX call to save
    $.ajax({
        url: '{{ route("techreports.updateconflist") }}',
        method: 'POST',
        data: {
            id: id,
            conference_name: confName,
            short_name: shortName,
            conference_description: confDescription,
            short_description: shortDescription
        },
        success: function(response) {
            if (response.success) {
                // Update display
                row.querySelector('.conference-name').textContent = confName;
                row.querySelector('.short-name').textContent = shortName;
                row.querySelector('.conference-description').textContent = confDescription;
                row.querySelector('.short-description').textContent = shortDescription;

                // Reset buttons
                row.querySelector('.table-actions').innerHTML = `
                    <button class="btn btn-primary btn-sm edit-btn" onclick="editRow(this)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete-btn" onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                `;

                row.classList.remove('editing');
                editingRow = null;

                showMessage('Conference updated successfully!', 'success');
            } else {
                showMessage('Error updating conference', 'danger');
            }
        },
        error: function(xhr) {
            showMessage('Error updating conference', 'danger');
            console.error(xhr);
        }
    });
}

function cancelEdit(row) {
    if (row.dataset.id == 'new') {
        row.remove();
    } else {
        location.reload(); // Simple approach to restore original values
    }
    editingRow = null;
}

function deleteRow(button) {
    const row = button.closest('tr');
    const id = row.dataset.id;
    const confName = row.querySelector('.conference-name').textContent;

    if (confirm(`Are you sure you want to delete "${confName}"?`)) {
        // Show loading
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        $.ajax({
            url: `/adminreports/deleteconf/${id}`,
            method: 'DELETE',
            success: function(response) {
                if (response.success) {
                    row.remove();
                    showMessage('Conference deleted successfully!', 'success');
                } else {
                    showMessage('Error deleting conference', 'danger');
                }
            },
            error: function(xhr) {
                showMessage('Error deleting conference', 'danger');
                console.error(xhr);
            }
        });
    }
}

function addNewRow() {
    const tbody = document.querySelector('#chapterlist tbody');
    const newRow = document.createElement('tr');
    newRow.dataset.id = 'new';
    newRow.classList.add('add-row', 'editing');

    newRow.innerHTML = `
        <td class="conf-id">New</td>
        <td class="conference-name">
            <input type="text" class="form-control form-control-sm" placeholder="Conference Name">
        </td>
        <td class="short-name">
            <input type="text" class="form-control form-control-sm" placeholder="Short Name">
        </td>
        <td class="conference-description">
            <input type="text" class="form-control form-control-sm" placeholder="Description">
        </td>
        <td class="short-description">
            <input type="text" class="form-control form-control-sm" placeholder="Abbreviation">
        </td>
        <td class="table-actions">
            <button class="btn btn-success btn-sm" onclick="saveNewRow(this)">
                <i class="fas fa-save"></i> Save
            </button>
            <button class="btn btn-secondary btn-sm" onclick="cancelEdit(this.closest('tr'))">
                <i class="fas fa-times"></i> Cancel
            </button>
        </td>
    `;

    tbody.appendChild(newRow);
    newRow.querySelector('input').focus();
    editingRow = newRow;
}

function saveNewRow(button) {
    const row = button.closest('tr');

    // Get values from inputs
    const confName = row.querySelector('.conference-name input').value;
    const shortName = row.querySelector('.short-name input').value;
    const confDescription = row.querySelector('.conference-description input').value;
    const shortDescription = row.querySelector('.short-description input').value;

    // Validate
    if (!confName.trim() || !shortName.trim() || !confDescription.trim() || !shortDescription.trim()) {
        showMessage('Please fill in all required fields', 'danger');
        return;
    }

    // Show loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    // Make AJAX call to create
    $.ajax({
        url: '{{ route("techreports.storeconf") }}',
        method: 'POST',
        data: {
            conference_name: confName,
            short_name: shortName,
            conference_description: confDescription,
            short_description: shortDescription
        },
        success: function(response) {
            if (response.success) {
                // Update the row with new ID
                row.dataset.id = response.id;
                row.querySelector('.conf-id').textContent = response.id;

                // Update display
                row.querySelector('.conference-name').textContent = confName;
                row.querySelector('.short-name').textContent = shortName;
                row.querySelector('.conference-description').textContent = confDescription;
                row.querySelector('.short-description').textContent = shortDescription;

                // Reset buttons
                row.querySelector('.table-actions').innerHTML = `
                    <button class="btn btn-primary btn-sm edit-btn" onclick="editRow(this)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm delete-btn" onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                `;

                row.classList.remove('add-row', 'editing');
                editingRow = null;

                showMessage('Conference created successfully!', 'success');
            } else {
                showMessage('Error creating conference', 'danger');
            }
        },
        error: function(xhr) {
            showMessage('Error creating conference', 'danger');
            console.error(xhr);
        }
    });
}

function showMessage(message, type = 'success') {
    const alertClass = type == 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    const container = document.getElementById('message-container');
    container.innerHTML = alertHtml;

    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

@endsection
