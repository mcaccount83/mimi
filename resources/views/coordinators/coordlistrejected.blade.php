@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Not Approved Coordinator List')

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
                            Not Approved Coordinator List
                        </h3>
                        <span class="ml-2">New Coordinator Applications Not Approved</span>
                        @include('layouts.dropdown_menus.menu_chapters_new')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="coordinatorlist"  class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Conf</th>
                    <th>Coordinator Name</th>
					<th>Display Position</th>
					<th>Application Date</th>
                    <th>Reason Not Approved</th>
                    <th>Rejected By</th>
                    @if ($ITCondition && ($checkBox5Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                    <td class="text-center align-middle"><a href="{{ url("/application/coordapplication/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                            <td>{{ $list->displayPosition->long_title }}</td>
                	  <td><span class="date-mask">{{ $list->coordinator_start_date }}</span></td>
                      <td>{{ $list->reason_retired }}</td>
                      <td>{{ $list->reportsTo?->first_name }} {{ $list->reportsTo?->last_name }}</td>
                      @if ($ITCondition && ($checkBox5Status ?? '') == 'checked')
                        <td class="text-center align-middle"><i class="fa fa-ban"
                            onclick="showDeleteCoordModal({{ $list->id }}, '{{ $list->first_name }}', '{{ $list->last_name }}', '{{ $list->activeStatus->active_status }}')"
                            style="cursor: pointer; color: #dc3545;"></i>
                        </td>
                    @endif
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
              <!-- /.card-body -->
              @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Coordinators</label>
                        </div>
                    </div>
                @endif

              <div class="col-sm-12">


            </div>

                <div class="card-body text-center">

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

function showAll() {
    var base_url = '{{ url("/application/coordrejected") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::INTERNATIONAL }}=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
