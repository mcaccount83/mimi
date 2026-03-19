@extends('layouts.mimi_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Inquiries Mpas')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <div class="dropdown">
                                <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Inquiries Map Links
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
                <th>Inquiries Map Link</th>
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
                <td class="link-column">
                    @if ($list->id == 0)
                        N/A
                    @else
                        <span class="link-display">
                            @if ($list->inquiries?->inquiries_map_link)
                                <a href="{{ $list->inquiries->inquiries_map_link }}" target="_blank">
                                    {{ $list->inquiries->inquiries_map_link }}
                                </a>
                            @else
                                <em class="text-muted">No link set</em>
                            @endif
                        </span>
                        <span class="link-edit" style="display: none;">
                            <input type="url" class="form-control form-control-sm link-input"
                                value="{{ $list->inquiries?->inquiries_map_link }}"
                                data-original="{{ $list->inquiries?->inquiries_map_link }}">
                        </span>
                    @endif
                </td>
                <td>
                    @if ($list->id != 0)
                        <button type="button" class="btn btn-primary bg-gradient btn-sm edit-btn">Edit Link</button>
                        <button type="button" class="btn btn-success bg-gradient btn-sm save-btn" style="display: none;">Save</button>
                        <button type="button" class="btn btn-danger bg-gradient btn-sm cancel-btn" style="display: none;">Cancel</button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
   </div>
            <!-- /.card-body -->

            <div class="card-body">
                @if ($inquiriesInternationalCondition || $ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input"
                                {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showAll">Show All International Regions</label>
                        </div>
                    </div>
                @endif
                  </div>
            <!-- /.card-body for checkboxes -->

             <div class="card-body text-center mt-3">
             </div>
            <!-- /.card-body for buttons -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Edit button click
    $(document).on('click', '.edit-btn', function() {
        var $row = $(this).closest('tr');

        // Show inputs, hide displays
        $row.find('.link-display').hide();
        $row.find('.link-edit').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-btn, .cancel-btn').show();
    });

    // Cancel button click
    $(document).on('click', '.cancel-btn', function() {
        var $row = $(this).closest('tr');

        // Hide inputs, show displays
        $row.find('.link-edit').hide();
        $row.find('.link-display').show();

        // Toggle buttons
        $(this).hide();
        $row.find('.save-btn').hide();
        $row.find('.edit-btn').show();

        // Reset input to original value
        $row.find('.link-input').val($row.find('.link-input').data('original'));
    });

    // Save button click
    $(document).on('click', '.save-btn', function() {
        var $row = $(this).closest('tr');
        var regionId = $row.data('region-id');
        var newLink = $row.find('.link-input').val();

        // Validate URL
        if (newLink && !isValidUrl(newLink)) {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'Please enter a valid URL (e.g. https://...).',
                showConfirmButton: false,
                timer: 2000
            });
            return;
        }

        // Send AJAX request
        $.ajax({
            url: '{{ route('adminreports.updateinquiriesmap', ['id' => '__ID__']) }}'.replace('__ID__', regionId),
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                inquiries_link: newLink,
            },
            success: function(response) {
                // Update displays
                var $display = $row.find('.link-display');
                if (newLink) {
                    $display.html('<a href="' + newLink + '" target="_blank">' + newLink + '</a>');
                } else {
                    $display.html('<em class="text-muted">No link set</em>');
                }
                $row.find('.link-input').data('original', newLink);

                // Hide inputs, show displays
                $row.find('.link-edit').hide();
                $row.find('.link-display').show();

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
                        confirmButton: 'btn btn-sm btn-success'
                    }
                });
            }
        });
    });

    function isValidUrl(url) {
        try {
            var u = new URL(url);
            return u.protocol === 'http:' || u.protocol === 'https:';
        } catch (e) {
            return false;
        }
    }
});
</script>
@endpush
