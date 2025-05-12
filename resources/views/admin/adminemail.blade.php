@extends('layouts.coordinator_theme')

@section('page_title', 'Admin')
@section('breadcrumb', 'Admin Email Settings')

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
                           Admin EMail Settings
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
                        <label>List Admin Email:</label>&nbsp;{{ $adminEmail[0]->list_admin }}
                    </div>
                    <div class="form-group">
                        <label>Payments Admin Email:</label>&nbsp;{{ $adminEmail[0]->payments_admin }}
                    </div>
                    <div class="form-group">
                        <label>EIN Admin Email:</label>&nbsp;{{ $adminEmail[0]->ein_admin }}
                    </div>
                    <div class="form-group">
                        <label>GSuite Admin Email:</label>&nbsp;{{ $adminEmail[0]->gsuite_admin }}
                    </div>
                    <div class="form-group">
                        <label>MIMI Admin Email:</label>&nbsp;{{ $adminEmail[0]->mimi_admin }}
                    </div>
                    </div>

                    <div class="card-body text-center">
                        <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#editEmail"><i class="fas fa-redo"  ></i>&nbsp; Update Admin Email</button>
                    </div>

                </div>
            </div>

             <!-- Modal for editing task -->
             <div class="modal fade" id="editEmail" tabindex="-1" role="dialog" >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="editDrive">Admin Email Addresses</h3>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('admin.updateadminemail', $adminEmail[0]->id) }}">
                                @csrf
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="listAdminEmail">ListAdmin Email</label>
                                        <input type="text" class="form-control" id="listAdminEmail" value="{{ $adminEmail[0]->list_admin }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="paymentAdminEmail">Payments Admin Email</label>
                                        <input type="text" class="form-control" id="paymentAdminEmail" value="{{ $adminEmail[0]->payments_admin }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="einAdminEmail">EIN Admin Email</label>
                                        <input type="text" class="form-control" id="einAdminEmail" value="{{ $adminEmail[0]->ein_admin }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="gsuiteAdminEmail">GSuite Admin Email</label>
                                        <input type="text" class="form-control" id="gsuiteAdminEmail" value="{{ $adminEmail[0]->gsuite_admin }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="mimiAdminEmail">MIMI Admin Email</label>
                                        <input type="text" class="form-control" id="mimiAdminEmail" value="{{ $adminEmail[0]->mimi_admin }}">
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
    document.getElementById('saveChanges').addEventListener('click', updateEmail);
});

function updateEmail() {
    console.log('Save button clicked');  // For debugging

    var listAdminEmail = document.getElementById('listAdminEmail').value;
    var paymentAdminEmail = document.getElementById('paymentAdminEmail').value;
    var einAdminEmail = document.getElementById('einAdminEmail').value;
    var gsuiteAdminEmail = document.getElementById('gsuiteAdminEmail').value;
    var mimiAdminEmail = document.getElementById('mimiAdminEmail').value;

    var formData = new FormData();
    formData.append('listAdminEmail', listAdminEmail);
    formData.append('paymentAdminEmail', paymentAdminEmail);
    formData.append('einAdminEmail', einAdminEmail);
    formData.append('gsuiteAdminEmail', gsuiteAdminEmail);
        formData.append('mimiAdminEmail', mimiAdminEmail);

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we update email information.',
        allowOutsideClick: false,
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        didOpen: () => {
            Swal.showLoading();

            $.ajax({
                url: '{{ route('admin.updateadminemail') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Admin Eamil Updated Successfully',
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });

                    // Close the modal
                    $('#editEmail').modal('hide');
                },
                error: function(xhr, status, error) {
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

</script>
</html>
