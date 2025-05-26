@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'International Not Approved Coordinator List')

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
                            International Not Approved Coordinator List
                        </h3>
                        <span class="ml-2">New Coordinator Applications Not Approved</span>
                        @include('layouts.dropdown_menus.menu_chapters_new')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="coordinatorlist"  class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Conf</th>
                    <th>Coordinator Name</th>
					<th>Display Position</th>
					<th>Application Date</th>
                    <th>Reason Not Approved</th>
                    <th>Rejected By</th>
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coordapplication/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                            <td>{{ $list->displayPosition->long_title }}</td>
                	  <td><span class="date-mask">{{ $list->coordinator_start_date }}</span></td>
                      <td>{{ $list->reason_retired }}</td>
                      <td>{{ $list->reportsTo?->first_name }} {{ $list->reportsTo?->last_name }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
              <!-- /.card-body -->
              <div class="col-sm-12">


            </div>

                <div class="card-body text-center">

            </div>
         </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

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



function showChapterSetupModal(chapterId) {
    Swal.fire({
        title: 'Chapter Startup Details',
        html: `
            <p>This will send the initial chapter startup email to the potential founderto facilitate the discussion on boundaries and name. Please enter additional boundary and name details to include in the email and press OK to send.</p>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="boundary_details" name="boundary_details" class="swal2-textarea" placeholder="Boundary Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="name_details" name="name_details" class="swal2-textarea" placeholder="Name Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const boundaryDetails = Swal.getPopup().querySelector('#boundary_details').value;
            const nameDetails = Swal.getPopup().querySelector('#name_details').value;

            if (!boundaryDetails) {
                Swal.showValidationMessage('Please enter the boundary details.');
                return false;
            }
            if (!nameDetails) {
                Swal.showValidationMessage('Please enter the chapter name details.');
                return false;
            }

            return {
                chapter_id: chapterId,
                boundary_details: boundaryDetails,
                name_details: nameDetails,
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
                        url: '{{ route('chapters.sendstartup') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapter_id,
                            boundaryDetails: data.boundary_details,
                            nameDetails: data.name_details,
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

function showChapterSetupModalOLD() {
    Swal.fire({
        title: 'Chapter Startup Details',
        html: `
            <p>This will send the initial chapter startup email to the potential founder to facilitate the discussion on boundaries and name. This will NOT add the new chapter to MIMI. Please enter the founder's information as well as the additional boundary and name details to include in the email and press OK to send.</p>
            <div class="name-fields-container" style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <input type="text" id="founder_first_name" name="founder_first_name" class="swal2-input" placeholder="Founder's First Name" required style="width: calc(50% - 3px); margin: 0 5px 0 0 !important; box-sizing: border-box;">
                <input type="text" id="founder_last_name" name="founder_last_name" class="swal2-input" placeholder="Founder's Last Name" required style="width: calc(50% - 3px); margin: 0 !important; box-sizing: border-box;">
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <input type="text" id="founder_email" name="founder_email" class="swal2-input" placeholder="Founder Email" required style="width: 100%; margin: 0 !important; box-sizing: border-box;">
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="boundary_details" name="boundary_details" class="swal2-textarea" placeholder="Boundary Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="name_details" name="name_details" class="swal2-textarea" placeholder="Name Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const founderFirstName = Swal.getPopup().querySelector('#founder_first_name').value;
            const founderLastName = Swal.getPopup().querySelector('#founder_last_name').value;
            const founderEmail = Swal.getPopup().querySelector('#founder_email').value;
            const boundaryDetails = Swal.getPopup().querySelector('#boundary_details').value;
            const nameDetails = Swal.getPopup().querySelector('#name_details').value;

            if (!founderEmail) {
                Swal.showValidationMessage('Please enter the founders email address.');
                return false;
            }
            if (!founderFirstName) {
                Swal.showValidationMessage('Please enter the founders first name.');
                return false;
            }
            if (!founderLastName) {
                Swal.showValidationMessage('Please enter the founders last name.');
                return false;
            }
            if (!boundaryDetails) {
                Swal.showValidationMessage('Please enter the boundary details.');
                return false;
            }
            if (!nameDetails) {
                Swal.showValidationMessage('Please enter the chapter name details.');
                return false;
            }

            return {
                founder_email: founderEmail,
                founder_first_name: founderFirstName,
                founder_last_name: founderLastName,
                boundary_details: boundaryDetails,
                name_details: nameDetails,
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
                        url: '{{ route('chapters.sendstartup') }}',
                        type: 'POST',
                        data: {
                            founderEmail: data.founder_email,
                            founderFirstName: data.founder_first_name,
                            founderLastName: data.founder_last_name,
                            boundaryDetails: data.boundary_details,
                            nameDetails: data.name_details,
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
