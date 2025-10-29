@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinators')
@section('breadcrumb', 'Active Coordinator List')

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
                        Active Coordinator List
                    </h3>
                    @include('layouts.dropdown_menus.menu_coordinators')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="coordinatorlist"  class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Email</th>
                    <th>Conf/Reg</th>
                    <th>Coordinator Name</th>
					<th>Display Position</th>
                    <th>Secondary Positions</th>
					<th>Hire Date</th>
                    <th>Email</th>
                    <th>Reports To</th>
                     @if ($ITCondition && ($checkBox5Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coordinator/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                        <a onclick="showCoordEmailModal('{{ $list->first_name }}', '{{ $list->last_name }}', {{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="far fa-envelope text-primary"></i></a>
                    </td>
                    <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        @if ( $list->on_leave == 1 )
                            <td @if ( $list->on_leave == 1 ) style="background-color: #ffc107;" @endif>ON LEAVE</td><td></td>
                        @else
                            <td>{{ $list->displayPosition->long_title }}</td>
                            <td>
                                @forelse($list->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                @endforelse
                            </td>
                        @endif
                	  <td><span class="date-mask">{{ $list->coordinator_start_date }}</span></td>
                      <td><a href="mailto:{{ $list->email }}">{{ $list->email }}</a></td>
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
             <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showDirect" id="showDirect" class="custom-control-input" {{$checkBoxStatus}} onchange="showDirect()" />
                        <label class="custom-control-label" for="showDirect">Only show my Direct Reports</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Coordinators</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Coordinators</label>
                        </div>
                    </div>
                @endif

                <div class="card-body text-center">
                @if($conferenceCoordinatorCondition)
                    <a class="btn bg-gradient-primary mb-3" href="{{ route('coordinators.coordpending') }}"><i class="fas fa-share mr-2" ></i>New Coordinators Pending</a>
                @endif
                @if ($regionalCoordinatorCondition)
                    @if ($checkBox3Status)
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('coordinator', 'Coordinator List')"><i class="fas fa-download mr-2" ></i>Export Coordinator List</button>
                    @elseif ($checkBox5Status)
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('intcoordinator', 'International Coordinator List')"><i class="fas fa-download"></i>&nbsp; Export International Coordinator List</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('coordinator', 'Coordinator List')" disabled><i class="fas fa-download mr-2" ></i>Export Coordinator List</button>
                    @endif
                @endif
                <a class="btn bg-gradient-primary mb-3" onclick="showCoordUplineEmailModal('{{ $userCoordId }}', '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="far fa-envelope mr-2"></i>E-mail Coordinators</a>
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

function showDirect() {
    var base_url = '{{ url("/coordinator/coordlist") }}';
    if ($("#showDirect").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::DIRECT_REPORT }}=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAllConf() {
    var base_url = '{{ url("/coordinator/coordlist") }}';
    if ($("#showAllConf").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::CONFERENCE_REGION }}=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAll() {
    var base_url = '{{ url("/coordinator/coordlist") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::INTERNATIONAL }}=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
