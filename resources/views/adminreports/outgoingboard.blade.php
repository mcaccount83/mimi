@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Outgoing Board Members')

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
                                    Outgoing Board Members
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_admin')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                    <tr>
                      <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Chapter</th>
                      <th>Name</th>
                      <th>Email</th>
                    <th>User Type</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($outgoingList as $list)
                        <td>
                            @if ($list->boardOutgoing?->chapters->region?->short_name != "None")
                                {{ $list->boardOutgoing?->chapters->conference?->short_name }} / {{ $list->boardOutgoing?->chapters->region?->short_name }}
                            @else
                                {{ $list->boardOutgoing?->chapters->conference?->short_name }}
                            @endif
                        </td>
                        <td>
                           @if($list->boardOutgoing?->state_id < 52)
                                {{$list->boardOutgoing?->state?->state_short_name}}
                            @else
                                {{$list->boardOutgoing?->country?->short_name}}
                            @endif
                        </td>
                        <td>{{ $list->boardOutgoing?->chapters->name }}</td>
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
        title: 'Make All Outgoing Users Inactive',
        html: `
            <p>This will make all outgoing users inactive.  They will no longer have access to edit the Chapter's Fiancial Report</p>
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
                        url: '{{ route('admin.resetoutgoingusers') }}',
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
