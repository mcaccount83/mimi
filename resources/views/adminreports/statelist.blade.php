@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'State List')

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
                                    State List
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_admin')
                            </div>
                        </div>
                     <!-- /.card-header -->
       <div class="card-body">
    <table id="chapterlist" class="table table-sm table-hover">
        <thead>
            <tr>
                <th>State</th>
                <th>Conf</th>
                <th>Region</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stateList as $list)
            <tr data-state-id="{{ $list->id }}"
                data-conference-id="{{ $list->conference_id }}"
                data-region-id="{{ $list->region_id }}">
                <td>
                    {{ $list->state_short_name }}
                </td>
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
                <td class="region-column">
                    <span class="region-display">
                        {{ $list->region->long_name }}
                    </span>
                    <span class="region-edit" style="display: none;">
                        <select class="form-control form-control-sm region-select">
                            <option value="">Select Region</option>
                            @foreach($regionList as $reg)
                                <option value="{{ $reg->id }}"
                                    data-conference-id="{{ $reg->conference_id }}"
                                    {{ $list->region_id == $reg->id ? 'selected' : '' }}>
                                    {{ $reg->long_name }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm bg-gradient-primary edit-state-btn">Edit Conf/Reg</button>
                    <button class="btn btn-sm bg-gradient-success save-state-btn" style="display: none;">Save</button>
                    <button class="btn btn-sm bg-gradient-danger cancel-state-btn" style="display: none;">Cancel</button>
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

    // Edit button click - EVENT DELEGATION
    $(document).on('click', '.edit-state-btn', function() {
        var $row = $(this).closest('tr');

        // Show selects, hide displays
        $row.find('.conf-display, .region-display').hide();
        $row.find('.conf-edit, .region-edit').show();

        // Filter regions based on current conference
        filterRegions($row);

        // Toggle buttons
        $(this).hide();
        $row.find('.save-state-btn, .cancel-state-btn').show();
    });

    // Conference dropdown change - filter regions - EVENT DELEGATION
    $(document).on('change', '.conf-select', function() {
        var $row = $(this).closest('tr');
        filterRegions($row);
    });

    // Filter regions based on selected conference
    function filterRegions($row) {
        var selectedConfId = $row.find('.conf-select').val();
        var $regionSelect = $row.find('.region-select');

        // Show all options first
        $regionSelect.find('option').show();

        // Hide options that don't match the conference
        $regionSelect.find('option[data-conference-id]').each(function() {
            var optionConfId = $(this).data('conference-id');
            if (optionConfId != selectedConfId) {
                $(this).hide();
            }
        });

        // Reset region selection if current selection is not in filtered list
        var currentRegionId = $regionSelect.val();
        var currentRegionConfId = $regionSelect.find('option[value="' + currentRegionId + '"]').data('conference-id');

        if (currentRegionConfId != selectedConfId) {
            $regionSelect.val('');
        }
    }

    // Cancel button click - EVENT DELEGATION
    $(document).on('click', '.cancel-state-btn', function() {
        var $row = $(this).closest('tr');

        // Hide selects, show displays
        $row.find('.conf-edit, .region-edit').hide();
        $row.find('.conf-display, .region-display').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-state-btn').hide();
        $row.find('.edit-state-btn').show();

        // Reset dropdowns to original values
        var originalConfId = $row.data('conference-id');
        var originalRegionId = $row.data('region-id');
        $row.find('.conf-select').val(originalConfId);
        $row.find('.region-select').val(originalRegionId);
    });

    // Save button click - EVENT DELEGATION
    $(document).on('click', '.save-state-btn', function() {
        var $row = $(this).closest('tr');
        var stateId = $row.data('state-id');
        var conferenceId = $row.find('.conf-select').val();
        var regionId = $row.find('.region-select').val();

        // Validate selections
        if (!conferenceId || !regionId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: 'Please select both Conference and Region.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Build URL using named route
        var updateUrl = "{{ route('adminreports.updatestate', ['id' => 'PLACEHOLDER']) }}";
        updateUrl = updateUrl.replace('PLACEHOLDER', stateId);

        // Send AJAX request
        $.ajax({
            url: updateUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                conference_id: conferenceId,
                region_id: regionId
            },
            success: function(response) {
                // Update displays
                $row.find('.conf-display').text(response.conference_name);
                $row.find('.region-display').text(response.region_name);

                // Update data attributes
                $row.data('conference-id', conferenceId);
                $row.data('region-id', regionId);

                // Hide selects, show displays
                $row.find('.conf-edit, .region-edit').hide();
                $row.find('.conf-display, .region-display').show();

                // Toggle buttons
                $row.find('.save-state-btn, .cancel-state-btn').hide();
                $row.find('.edit-state-btn').show();

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
                var errorMessage = 'Error updating state assignment. Please try again.';
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
