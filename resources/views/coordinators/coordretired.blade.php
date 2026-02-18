@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinators')
@section('breadcrumb', 'Retired Coordinator List')

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
                        Retired Coordinator List
                    </h3>
                    @include('layouts.dropdown_menus.menu_coordinators')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="coordinatorlist" class="table table-sm table-hover">
              <thead>
			    <tr>
				  <th>Details</th>
                  <th>Conf/Reg</th>
				   <th>Coordinator Name</th>
                  <th>Position</th>
                  <th>Secondary Positions</th>
                  <th>Retire Date</th>
                  <th>Reason</th>
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
                            @if ($list->region?->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->displayPosition->long_title }}</td>
                        <td>
                            @forelse($list->secondaryPosition as $position)
                                {{ $position->long_title }}@if(!$loop->last)<br>@endif
                            @empty
                            @endforelse
                        </td>
                        <td><span class="date-mask">{{ $list->zapped_date }}</span></td>
                        <td>{{ $list->reason_retired }}</td>
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
                @if ($assistConferenceCoordinatorCondition)
                    @if ($checkBox51Status)
                        <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('intretiredcoordinator', 'International Retired Coordinator List')"><i class="bi bi-download me-2"></i>Export International Retired Coordinator List</button>
                    {{-- @else
                        <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('retiredcoordinator', 'Retired Coordinator List')"><i class="bi bi-download me-2"></i>Export Retired Coordinator List</button> --}}
                    @endif
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
