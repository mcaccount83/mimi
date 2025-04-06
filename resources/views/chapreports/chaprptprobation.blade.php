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
                    <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                    <td>{{ $list->state->state_short_name }}</td>
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
                <div class="card-body text-center">&nbsp;</div>
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

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});


</script>
@endsection
