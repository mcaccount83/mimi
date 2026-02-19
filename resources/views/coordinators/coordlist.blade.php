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
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                     @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coordinator/details/{$list->id}") }}"><i class="bi bi-eye"></i></a></td>
                    <td>
                        <a onclick="showCoordEmailModal('{{ $list->first_name }}', '{{ $list->last_name }}', {{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="bi bi-envelope text-primary"></i></a>
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
                      @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
                        <td class="text-center align-middle"><i class="bi bi-ban"
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

              <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showDirect" id="showDirect" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showDirect()" />
                        <label class="form-check-label" for="showDirect">Only show my Direct Reports</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                                @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Coordinators in Conference (Export Available)</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Coordinators in Region (Export Available)</label>
                                @endif
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{$checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Coordinators (Export Available)</label>
                        </div>
                    </div>
                @endif
            </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
                @if($conferenceCoordinatorCondition)
                    @if ($checkBox51Status)
                        <a class="btn btn-primary bg-gradient mb-2" href="{{ route('coordinators.coordpending', ['check5' => 'yes']) }}"><i class="bi bi-person-fill-add me-2"></i>New International Coordinators Pending</a>
                    @else
                        <a class="btn btn-primary bg-gradient mb-2" href="{{ route('coordinators.coordpending') }}"><i class="bi bi-person-fill-add me-2"></i>New Coordinators Pending</a>
                    @endif
                @endif
                @if ($regionalCoordinatorCondition)
                    @if ($checkBox3Status)
                        <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('coordinator', 'Coordinator List')"><i class="bi bi-download me-2"></i>Export Coordinator List</button>
                    @elseif ($checkBox51Status)
                        <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('intcoordinator', 'International Coordinator List')"><i class="bi bi-download me-2"></i>Export International Coordinator List</button>
                    @endif
                @endif
                @if (!$checkBox51Status && !$checkBox3Status)
                    <a class="btn btn-primary bg-gradient mb-2" onclick="showCoordUplineEmailModal('{{ $userCoordId }}', '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="bi bi-envelope-fill me-2"></i>E-mail Coordinators</a>
                @endif
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
