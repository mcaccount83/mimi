@extends('layouts.mimi_theme')

@section('page_title', 'Coordinator Reports')
@section('breadcrumb', 'Coordinator eLearning Report')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Coordinator eLearning Report
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_coor')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
			        <th>Course<br>Details</th>
			        <th>Conf/Reg</th>
					<th>Coordinator Name</th>
					<th>Completed</th>
					<th>In Progress</th>
                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                      <td class="text-center align-middle">
                        <a href="{{ url("/coordinator/details/viewelearning/{$list->id}") }}"><i class="bi bi-mortarboard-fill"></i></a>
                    </td>
                        <td>
                            @if ($list->region->short_name != "None")
                                {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                    <td >{{ $bulkProgress[$list->email]['completed'] ?? 0 }} Courses</td>
                    <td >{{ $bulkProgress[$list->email]['in_progress'] ?? 0 }} Courses</td>
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
                            <label class="form-check-label" for="showIntl">Show All International Coordinators</label>
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
