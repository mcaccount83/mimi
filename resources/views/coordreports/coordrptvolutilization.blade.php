@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Reports')
@section('breadcrumb', 'Coordinator Utilization Report')

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
                            Coordinator Utilization Report
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_coor')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
			        <th>Details</th>
			        <th>Conf/Reg</th>
					<th>Coordinator Name</th>
					<th>Primary (Display) Position</th>
                    <th>Secondary Position</th>
					<th>Direct Report</th>
                    <th>InDirect Report</th>
                    <th>Total Report</th>
                </tr>
                </thead>
                <tbody>
                @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coordinator/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
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
                    <td>{{ $list->direct_report }}</td>
                    <td>{{ $list->indirect_report }}</td>
                    <td>{{ $list->total_report }}</td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
             <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showDirect" id="showDirect" class="custom-control-input" {{$checkBoxStatus}} onchange="showCoordDirect()" />
                        <label class="custom-control-label" for="showDirect">Only show my Direct Reports</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showCoordAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Coordinators</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showCoordAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Coordinators</label>
                        </div>
                    </div>
                @endif

            <!-- /.card-body -->
            </div>
            <div class="card-body text-center">&nbsp;</div>
        </div>
    </div>
      </div>
    </section>

    <!-- /.content -->

@endsection
