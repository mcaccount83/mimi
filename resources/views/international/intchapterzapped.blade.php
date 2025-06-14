@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'International Zapped Chapter List')

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
                            International Zapped Chapter List
                        </h3>
                        @include('layouts.dropdown_menus.menu_chapters')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover">
              <thead>
			    <tr>
                    <th>Details</th>
                    @if ($userAdmin == 'Admin')
                        <th>Delete</th>
                    @endif
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>Disband Date</th>
                    <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                     @if ($userAdmin == 'Admin')
                        <td class="text-center align-middle"><i class="fa fa-ban"
                            onclick="showDeleteChapterModal({{ $list->id }}, '{{ $list->name }}', '{{ $list->activeStatus->active_status }}')"
                            style="cursor: pointer; color: #dc3545;"></i>
                        </td>
                    @endif
                    <td>
                        @if ($list->region?->short_name && $list->region->short_name != "None")
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
                    <td>{{ $list->ein }}</td>
                    <td><span class="date-mask">{{ $list->zap_date }}</span></td>
                    <td>{{ $list->disband_reason }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="card-body text-center">
                <button class="btn bg-gradient-primary" onclick="startExport('intzapchapter', 'International Zapped Chapter List')"><i class="fas fa-download mr-2" ></i>Export Chapter List</button>
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

function showDeleteChapterModal(chapterId, chapterName, activeStatus) {
    Swal.fire({
        title: 'Chapter Deletion',
        html: `
            <p>This will remove the chapter "<strong>${chapterName}</strong>" and all board members from the database.</p>
            <p>PLEASE USE CAUTION, this cannot be undone!!</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            <input type="hidden" id="active_status" name="active_status" value="${activeStatus}">
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
            const activeStatus = Swal.getPopup().querySelector('#active_status').value;

            return {
                chapter_id: chapterId,
                active_status: activeStatus
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
                url: '{{ route('admin.updatechapterdelete') }}',
                type: 'POST',
                data: {
                    chapterid: data.chapter_id,
                    activeStatus: data.active_status,  // Add this line
                    _token: '{{ csrf_token() }}'
                },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Chapter successfully deleted.',
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
