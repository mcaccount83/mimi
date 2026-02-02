@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Inquiries Notifications')

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
                                    Inquiries Notifications
                                </h3>
                                @include('layouts.dropdown_menus.menu_inquiries')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
    <table id="chapterlist" class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Conf</th>
                <th>Region</th>
                <th>States</th>
                <th>Email</th>
                <th>Coordinator</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regList as $list)
            <tr data-region-id="{{ $list->id }}">
                <td>
                    {{ $list->conference->short_name }}
                </td>
                <td>
                    {{ $list->long_name }}
                </td>
                <td>
                    @if ($list->id == 0)
                        N/A
                    @else
                        {{ $list->states->pluck('state_short_name')->implode(', ') }}
                    @endif
                </td>
                <td class="email-column">
                    @if ($list->id == 0)
                        N/A
                    @else
                        <span class="email-display">
                            <a href="mailto:{{ $list->inquiries?->inquiries_email }}">{{ $list->inquiries?->inquiries_email }}</a>
                        </span>
                        <span class="email-edit" style="display: none;">
                            <input type="email" class="form-control form-control-sm email-input" value="{{ $list->inquiries?->inquiries_email }}">
                        </span>
                    @endif
                </td>
                <td class="name-column">
                    @if ($list->id == 0)
                        N/A
                    @else
                        <span class="name-display">{{ $list->inquiries?->inquiries_name }}</span>
                        <span class="name-edit" style="display: none;">
                            <input type="text" class="form-control form-control-sm name-input" value="{{ $list->inquiries?->inquiries_name }}">
                        </span>
                    @endif
                </td>
                <td>
                    @if ($list->id != 0)
                        <button class="btn bg-gradient-primary btn-sm edit-btn">Edit Email/Name</button>
                        <button class="btn bg-gradient-success btn-sm save-btn" style="display: none;">Save</button>
                        <button class="btn bg-gradient-danger btn-sm cancel-btn" style="display: none;">Cancel</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
                @if ($inquiriesInternationalCondition || $ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input"
                                {{ $checkBox5Status ? 'checked' : '' }} onchange="showInqAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Regions</label>
                        </div>
                    </div>
                @endif
             <div class="card-body text-center">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Edit button click
    $(document).on('click', '.edit-btn', function() {
        var $row = $(this).closest('tr');

        // Show inputs, hide displays
        $row.find('.email-display').hide();
        $row.find('.email-edit').show();
        $row.find('.name-display').hide();
        $row.find('.name-edit').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-btn, .cancel-btn').show();
    });

    // Cancel button click
    $(document).on('click', '.cancel-btn', function() {
        var $row = $(this).closest('tr');

        // Hide inputs, show displays
        $row.find('.email-edit').hide();
        $row.find('.email-display').show();
        $row.find('.name-edit').hide();
        $row.find('.name-display').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-btn').hide();
        $row.find('.edit-btn').show();

        // Reset inputs to original values
        var originalEmail = $row.find('.email-display a').text();
        var originalName = $row.find('.name-display').text();
        $row.find('.email-input').val(originalEmail);
        $row.find('.name-input').val(originalName);
    });

    // Save button click
    $(document).on('click', '.save-btn', function() {
        var $row = $(this).closest('tr');
        var regionId = $row.data('region-id');
        var newEmail = $row.find('.email-input').val();
        var newName = $row.find('.name-input').val();

        // Validate email
        if (!newEmail || !isValidEmail(newEmail)) {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'Please enter a valid email address.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        // Validate name
        if (!newName || newName.trim() === '') {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'Please enter a coordinator name.',
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }

        // Send AJAX request
        $.ajax({
            url: '{{ route('adminreports.updateinquiries', ['id' => '__ID__']) }}'.replace('__ID__', regionId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inquiries_email: newEmail,
                inquiries_name: newName
            },
            success: function(response) {
                // Update displays
                $row.find('.email-display a').text(response.email).attr('href', 'mailto:' + response.email);
                $row.find('.name-display').text(response.name);

                // Hide inputs, show displays
                $row.find('.email-edit').hide();
                $row.find('.email-display').show();
                $row.find('.name-edit').hide();
                $row.find('.name-display').show();

                // Toggle buttons
                $row.find('.save-btn, .cancel-btn').hide();
                $row.find('.edit-btn').show();

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
                var errorMessage = 'Error updating information. Please try again.';
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
    });

    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
</script>
@endpush
