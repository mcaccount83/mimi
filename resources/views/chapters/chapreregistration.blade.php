@extends('layouts.coordinator_theme')

@section('page_title', 'Payments/Donations')
@section('breadcrumb', 'Re-Registration Payments')

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
                        Re-Registration Payments
                    </h3>
                    @include('layouts.dropdown_menus.menu_payment')
                </div>
            </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Payment</th>
                            <th>Send</th>
                            <th>Conf/Reg</th>
                            <th>State</th>
                            <th>Name</th>
                            <th>Re-Registration Notes</th>
                            <th>Due</th>
                            <th>Last Paid</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reChapterList as $list)
                        @php
                            $due = $list->startMonth->month_short_name . ' ' . $list->next_renewal_year;
                            $overdue = (date('Y') * 12 + date('m')) - ($list->next_renewal_year * 12 + $list->start_month_id);
                        @endphp
                        <tr>
                            <td class="text-center align-middle">
                                @if ($conferenceCoordinatorCondition)
                                    <a href="{{ url("/chapterpaymentedit/{$list->id}") }}"><i class="far fa-credit-card"></i></a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($due && !$overdue)
                                    <a onclick="showChapterReRegModal('{{ $list->name }}', {{ $list->id }})"><i class="far fa-envelope text-primary"></i></a>
                                @endif
                                @if ($overdue == 1)
                                    <a onclick="showChapterReRegLateModal('{{ $list->name }}', {{ $list->id }})"><i class="far fa-envelope text-primary"></i></a>
                                @endif
                                @if ($overdue > 1)

                                @endif
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
                            <td>{{ $list->payments->rereg_notes }}</td>
                            <td @if ($overdue > 1) style="background-color: #dc3545; color: #ffffff;"
                                @elseif ($overdue == 1) style="background-color: #ffc107;"
                                @endif
                                data-sort="{{ $list->next_renewal_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                                {{ $due }}
                            </td>
                            <td><span class="date-mask">{{ $list->payments->rereg_date }}</span></td>
                            <td>{{ $list->payments->rereg_members }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
          <!-- /.card-body -->
          <div class="col-sm-12">
            <div class="custom-control custom-switch">
                <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
            </div>
        </div>
          <div class="col-sm-12">
            <div class="custom-control custom-switch">
                        <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox3Status}} onchange="showAll()" />
                        <label class="custom-control-label" for="showAll">Show All Chapters</label>
                    </div>
                </div>
                <div class="card-body text-center">
                    @if($conferenceCoordinatorCondition)
                    @if($checkBoxStatus == null && $checkBox3Status == null)
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chapreregreminder') }}"><i class="fas fa-envelope mr-2" ></i>Send Current Month Reminders</a>
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chaprereglatereminder') }}"><i class="fas fa-envelope mr-2" ></i>Send One Month Late Notices</a>
                    @else
                        <button class="btn bg-gradient-primary" disabled><i class="fas fa-envelope mr-2" ></i>Send Current Month Reminders</button>
                        <button class="btn bg-gradient-primary" disabled><i class="fas fa-envelope mr-2" ></i>Send One Month Late Notices</button>
                    @endif
                        <a href="{{ route('export.rereg')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download mr-2" ></i>Export Overdue Chapter List</button></a>
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

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

function showPrimary() {
var base_url = '{{ url("/chapter/reregistration") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAll() {
    var base_url = '{{ url("/chapter/reregistration") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?check3=yes';
    } else {
        window.location.href = base_url;
    }
}

function showChapterReRegModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Re-Registration Reminder',
        html: `
            <p>This will send the regular re-registration reminder for <b>${chapterName}</b> to the full board and all coordinators.</p>
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

            return {
                chapter_id: chapterId,
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
                        url: '{{ route('chapters.sendchapterrereg') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapter_id,
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

function showChapterReRegLateModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Re-Registration Late Notice',
        html: `
            <p>This will send the regular re-registration late notice for <b>${chapterName}</b> to the full board and all coordinators.</p>
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

            return {
                chapter_id: chapterId,
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
                        url: '{{ route('chapters.sendchapterrereglate') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapter_id,
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
