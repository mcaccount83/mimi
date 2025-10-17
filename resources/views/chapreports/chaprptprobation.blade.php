@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Probation Report')

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
                            Chapter Probation Report
                        </h3>
                        <span class="ml-2">Includes chapters that have a Probationary status</span>
                        @include('layouts.dropdown_menus.menu_reports_chap')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Details</th>
                  <th>QTR Report</th>
                  <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Status</th>
                 <th>Reason</th>
				 <th>Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td class="text-center align-middle">
                        @if ($list->probation_id == '3')
                            <a href="{{ url("/board/probation/{$list->id}") }}"><i class="fas fa-file"></i></a>
                        @else
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
                        <td @if ( $list->status_id == 4 || $list->status_id == 6) style="background-color: #dc3545; color: #ffffff;"
                            @elseif ( $list->status_id == 5) style="background-color: #ffc107;"
                            @endif>
                        {{ $list->status->chapter_status }}</td>
                        <td>{{ $list->probation?->probation_reason }}</td>
						<td>{{ $list->notes }}</td>
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
                <div class="card-body text-center">
                    @if ($userAdmin)
                        <button type="button" id="reset-probation" class="btn bg-gradient-primary"><i class="fas fa-undo mr-2"></i>Reset Quarterly Report Data</button>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- /.box -->
    </div>
</section>
<!-- Main content -->

<!-- /.content -->

@endsection
@section('customscript')
<script>

function showPrimary() {
    var base_url = '{{ url("/chapterreports/probation") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

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

$(document).ready(function() {
    var resetBaseUrl = '{{ url("/techreports/resetProbationSubmission") }}';

    function handleAjaxRequest(baseUrl) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.success,
                    timer: 2000, // Auto-close after 2 seconds
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Reload AFTER SweetAlert message
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: xhr.responseJSON?.fail || "An unexpected error occurred.",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    // Attach the function to all buttons
    $("#reset-probation").click(function() {
        handleAjaxRequest(resetBaseUrl);
    });
});



</script>
@endsection
