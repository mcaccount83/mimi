@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Region List')

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
                                    Region List
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regList as $list)
            <tr data-region-id="{{ $list->id }}" data-conference-id="{{ $list->conference_id }}">
                <td class="conf-column">
                    <span class="conf-display">
                        {{ $list->conference->short_name }}
                    </span>
                    <span class="conf-edit" style="display: none;">
                        <select class="form-control form-control-sm conf-select">
                            <option value="">Select Conference</option>
                            @foreach($conferenceList as $conf)
                                <option value="{{ $conf->id }}"
                                    {{ $list->conference_id == $conf->id ? 'selected' : '' }}>
                                    {{ $conf->short_name }}
                                </option>
                            @endforeach
                        </select>
                    </span>
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
                <td>
                    @if ($list->id != 0)
                        <button class="btn btn-sm bg-gradient-primary edit-region-btn">Edit Conference</button>
                        <button class="btn btn-sm bg-gradient-success save-region-btn" style="display: none;">Save</button>
                        <button class="btn btn-sm bg-gradient-danger cancel-region-btn" style="display: none;">Cancel</button>
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

    // Edit button click
    $('.edit-region-btn').on('click', function() {
        var $row = $(this).closest('tr');

        // Show select, hide display
        $row.find('.conf-display').hide();
        $row.find('.conf-edit').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-region-btn, .cancel-region-btn').show();
    });

    // Cancel button click
    $('.cancel-region-btn').on('click', function() {
        var $row = $(this).closest('tr');

        // Hide select, show display
        $row.find('.conf-edit').hide();
        $row.find('.conf-display').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-region-btn').hide();
        $row.find('.edit-region-btn').show();

        // Reset dropdown to original value
        var originalConfId = $row.data('conference-id');
        $row.find('.conf-select').val(originalConfId);
    });

    // Save button click
    $('.save-region-btn').on('click', function() {
        var $row = $(this).closest('tr');
        var regionId = $row.data('region-id');
        var conferenceId = $row.find('.conf-select').val();

        // Validate selection
        if (!conferenceId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select a Conference.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Send AJAX request
        $.ajax({
            url: '/admin/regions/' + regionId + '/update-conference',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                conference_id: conferenceId
            },
            success: function(response) {
                // Update display
                $row.find('.conf-display').text(response.conference_name);

                // Update data attribute
                $row.data('conference-id', conferenceId);

                // Hide select, show display
                $row.find('.conf-edit').hide();
                $row.find('.conf-display').show();

                // Toggle buttons
                $row.find('.save-region-btn, .cancel-region-btn').hide();
                $row.find('.edit-region-btn').show();

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
                var errorMessage = 'Error updating region conference. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
</script>
@endpush
