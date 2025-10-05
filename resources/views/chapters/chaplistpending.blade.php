@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Pending Chapter List')

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
                            Pending Chapter List
                        </h3>
                        <span class="ml-2">New Chapter Applications Waiting for Review</span>
                        @include('layouts.dropdown_menus.menu_chapters_new')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Email</th>
                    <th>Conf</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>President</th>
                    <th>Email</th>
                    <th>Phone</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/pendingchapterdetailsedit/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                            <td class="text-center align-middle">
                                <a onclick="showChapterSetupEmailModal({{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="far fa-envelope text-primary"></i></a>
                           </td>
                            <td>
                                @if ($list->region->short_name != "None")
                                    {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                                @else
                                    {{ $list->conference->short_name }}
                                @endif
                            </td>
                            <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->pendingPresident->first_name }} {{ $list->pendingPresident->last_name }}</td>
                            <td class="email-column">
                                <a href="mailto:{{ $list->pendingPresident->email }}">{{ $list->pendingPresident->email }}</a>
                            </td>
                            <td><span class="phone-mask">{{ $list->pendingPresident->phone }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">

                </div>
                <div class="card-body text-center">
                @if ($regionalCoordinatorCondition)
                If your new chapter is not listed above, you can manually add them.<br>
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.addnew') }}"><i class="fas fa-plus mr-2" ></i>Manually Add New Chapter</a>
                    @endif

                    </div>
                </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  @endsection
<!-- /.content-wrapper -->

@section('customscript')
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
});

// function showChapterSetupModal(chapterId) {
//     Swal.fire({
//         title: 'Chapter Startup Details',
//         html: `
//             <p>This will send the initial chapter startup email to the potential founder to facilitate the discussion on boundaries and name. Please enter additional boundary and name details to include in the email and press OK to send.</p>
//             <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
//                 <textarea id="boundary_details" name="boundary_details" class="swal2-textarea" placeholder="Boundary Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
//             </div>
//             <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
//                 <textarea id="name_details" name="name_details" class="swal2-textarea" placeholder="Name Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
//             </div>
//             <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
//         `,
//         showCancelButton: true,
//         confirmButtonText: 'OK',
//         cancelButtonText: 'Close',
//         customClass: {
//             confirmButton: 'btn-sm btn-success',
//             cancelButton: 'btn-sm btn-danger'
//         },
//         preConfirm: () => {
//             const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
//             const boundaryDetails = Swal.getPopup().querySelector('#boundary_details').value;
//             const nameDetails = Swal.getPopup().querySelector('#name_details').value;

//             if (!boundaryDetails) {
//                 Swal.showValidationMessage('Please enter the boundary details.');
//                 return false;
//             }
//             if (!nameDetails) {
//                 Swal.showValidationMessage('Please enter the chapter name details.');
//                 return false;
//             }

//             return {
//                 chapter_id: chapterId,
//                 boundary_details: boundaryDetails,
//                 name_details: nameDetails,
//             };
//         }
//     }).then((result) => {
//         if (result.isConfirmed) {
//             const data = result.value;

//             Swal.fire({
//                 title: 'Processing...',
//                 text: 'Please wait while we process your request.',
//                 allowOutsideClick: false,
//                 customClass: {
//                     confirmButton: 'btn-sm btn-success',
//                     cancelButton: 'btn-sm btn-danger'
//                 },
//                 didOpen: () => {
//                     Swal.showLoading();

//                     // Perform the AJAX request
//                     $.ajax({
//                         url: '{{ route('chapters.sendstartup') }}',
//                         type: 'POST',
//                         data: {
//                             chapterId: data.chapter_id,
//                             boundaryDetails: data.boundary_details,
//                             nameDetails: data.name_details,
//                             _token: '{{ csrf_token() }}'
//                         },
//                         success: function(response) {
//                             Swal.fire({
//                                 title: 'Success!',
//                                 text: response.message,
//                                 icon: 'success',
//                                 showConfirmButton: false,  // Automatically close without "OK" button
//                                 timer: 1500,
//                                 customClass: {
//                                     confirmButton: 'btn-sm btn-success'
//                                 }
//                             }).then(() => {
//                                 location.reload(); // Reload the page to reflect changes
//                             });
//                         },
//                         error: function(jqXHR, exception) {
//                             Swal.fire({
//                                 title: 'Error!',
//                                 text: 'Something went wrong, Please try again.',
//                                 icon: 'error',
//                                 confirmButtonText: 'OK',
//                                 customClass: {
//                                     confirmButton: 'btn-sm btn-success'
//                                 }
//                             });
//                         }
//                     });
//                 }
//             });
//         }
//     });
// }

function showChapterSetupEmailModal(chapterId, userName, userPosition, userConfName, userConfDesc, predefinedBoundaries = '', predefinedName = '') {
    Swal.fire({
        title: 'Chapter Startup Message',
        html: `
            <p>This will send the initial chapter startup email to potential founder to facilitate the discussion on boundaries and name. Please enter additional boundary and name details to include in the email and press OK to send.</p>
            <div style="width: 100%; margin-bottom: 10px;">
                <textarea id="boundary_details" name="boundary_details" class="rich-editor" ${predefinedBoundaries ? '' : 'placeholder="Boundary Details/Options"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedBoundaries}</textarea>
            </div>
            <div style="width: 100%; margin-bottom: 10px;">
                <textarea id="name_details" name="name_details" class="rich-editor" ${predefinedName ? '' : 'placeholder="Name Details/Options"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedName}</textarea>
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            <div style="width: 100%; margin-bottom: 10px; text-align: left;">
            <p><b>MCL,</b><br>
                ${userName}<br>
                ${userPosition}<br>
                ${userConfName}, ${userConfDesc}<br>
                International MOMS Club</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger',
            popup: 'swal-wide-popup'
        },
        didOpen: () => {
            // Initialize Summernote on both textareas
            $('#boundary_details').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        $(this).val(contents);
                    }
                }
            });

            $('#name_details').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        $(this).val(contents);
                    }
                }
            });

            // Add some styling for the wider popup
            if (!document.getElementById('swal-wide-popup-style')) {
                const style = document.createElement('style');
                style.id = 'swal-wide-popup-style';
                style.innerHTML = `
                    .swal-wide-popup {
                        width: 80% !important;
                        max-width: 800px !important;
                    }
                    .note-editor {
                        margin-bottom: 10px !important;
                        width: 100% !important;
                    }
                    .note-editable {
                        text-align: left !important;
                    }
                    .note-editing-area {
                        width: 100% !important;
                    }
                `;
                document.head.appendChild(style);
            }
        },
        preConfirm: () => {
            const boundaryDetails = $('#boundary_details').summernote('code');
            const nameDetails = $('#name_details').summernote('code');
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            if (!boundaryDetails) {
                Swal.showValidationMessage('Please enter boundary details.');
                return false;
            }

            if (!nameDetails) {
                Swal.showValidationMessage('Please enter name details.');
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
@endsection
