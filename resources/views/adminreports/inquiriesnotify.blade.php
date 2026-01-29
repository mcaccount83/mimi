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
                                @include('layouts.dropdown_menus.menu_reports_admin')
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
                <th>Inquiries Email</th>
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
                            <a href="mailto:{{ $list->inquiries_email }}">{{ $list->inquiries_email }}</a>
                        </span>
                        <span class="email-edit" style="display: none;">
                            <input type="email" class="form-control form-control-sm email-input" value="{{ $list->inquiries_email }}">
                        </span>
                    @endif
                </td>
                <td>
                    @if ($list->id != 0)
                        <button class="btn bg-gradient-primary btn-sm edit-email-btn">Edit Email</button>
                        <button class="btn bg-gradient-success btn-sm save-email-btn" style="display: none;">Save</button>
                        <button class="btn bg-gradient-danger btn-sm cancel-email-btn" style="display: none;">Cancel</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
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
    // Edit button click - ALREADY HAS EVENT DELEGATION ✓
    $(document).on('click', '.edit-email-btn', function() {
        var $row = $(this).closest('tr');
        var $emailColumn = $row.find('.email-column');

        // Show input, hide display
        $emailColumn.find('.email-display').hide();
        $emailColumn.find('.email-edit').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-email-btn, .cancel-email-btn').show();
    });

    // Cancel button click - EVENT DELEGATION
    $(document).on('click', '.cancel-email-btn', function() {
        var $row = $(this).closest('tr');
        var $emailColumn = $row.find('.email-column');

        // Hide input, show display
        $emailColumn.find('.email-edit').hide();
        $emailColumn.find('.email-display').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-email-btn').hide();
        $row.find('.edit-email-btn').show();

        // Reset input to original value
        var originalEmail = $emailColumn.find('.email-display a').text();
        $emailColumn.find('.email-input').val(originalEmail);
    });

    // Save button click - EVENT DELEGATION
    $(document).on('click', '.save-email-btn', function() {
        var $row = $(this).closest('tr');
        var $emailColumn = $row.find('.email-column');
        var regionId = $row.data('region-id');
        var newEmail = $emailColumn.find('.email-input').val();

        console.log('Region ID:', regionId);
        console.log('New Email:', newEmail);

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

        // Send AJAX request - USE NAMED ROUTE LIKE YOUR WORKING EXAMPLE
        $.ajax({
            url: '{{ route('adminreports.updateinquiries', ['id' => '__ID__']) }}'.replace('__ID__', regionId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inquiries_email: newEmail
            },
            success: function(response) {
                // Update display
                $emailColumn.find('.email-display a').text(response.email).attr('href', 'mailto:' + response.email);

                // Hide input, show display
                $emailColumn.find('.email-edit').hide();
                $emailColumn.find('.email-display').show();

                // Toggle buttons
                $row.find('.save-email-btn, .cancel-email-btn').hide();
                $row.find('.edit-email-btn').show();

                // Show success message with SweetAlert
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function(xhr) {
                var errorMessage = 'Error updating email. Please try again.';
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
