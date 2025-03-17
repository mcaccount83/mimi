@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Disbanded Board Members')

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
                                    Disbanded Board Members
                                </h3>
                                @include('layouts.dropdown_menus.menu_admin')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>Chapter</th>
                  <th>Name</th>
                  <th>Email</th>
                <th>User Type</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($disbandedList as $list)
                    <tr>
                        <td>{{ $list->board->chapters->name }}, {{ $list->board->chapters->state->state_short_name }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td class="email-column">
                            <a href="mailto:{{ $list->email }}">{{ $list->email }}</a>
                        </td>
                        <td>{{ $list->user_type }}</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="card-body text-center">
				@if ($regionalCoordinatorCondition)
                    @if ($countList > '0')
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="showUserInactiveModel()"><i class="fas fa-users-slash mr-2"></i>Make all Users Inactive</button>
                    @else
                        <button type="button" class="btn bg-gradient-primary mb-3" disabled><i class="fas fa-users-slash mr-2"></i>Make all Users Inactive</button>
                    @endif
				@endif
             </div>
        </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
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

function showUserInactiveModel() {
    Swal.fire({
        title: 'Make All Disbanded Users Inactive',
        html: `
            <p>This will make all disbanded users inactive.  They will no longer have access to edit the Disbanding Checklist and/or Final Fiancial Report</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },

    }).then((result) => {
        if (result.isConfirmed) {

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
                        url: '{{ route('admin.resetdisbandedusers') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
@endsection
