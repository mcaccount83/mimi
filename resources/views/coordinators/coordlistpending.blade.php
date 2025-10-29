@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Pending Coordinator List')

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
                            Pending Coordinator List
                        </h3>
                        <span class="ml-2">New Coordinator Applications Waiting for Review</span>
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
                    <th>Contact Email</th>
                    <th>Phone</th>
                    <th>Reports To</th>
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
                      <td><a href="mailto:{{ $list->sec_email }}">{{ $list->sec_email }}</a></td>
                    <td>{{ $list->phone }}</td>
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
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showCoordAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Coordinators</label>
                        </div>
                    </div>
                @endif

              <div class="col-sm-12">


            </div>

                <div class="card-body text-center">
                @if($conferenceCoordinatorCondition)
                                If your new coordinator is not listed above, you can manually add them.<br>
                    <a class="btn bg-gradient-primary" href="{{ route('coordinators.editnew') }}"><i class="fas fa-plus mr-2" ></i>Manually Add New Coordinator</a>
                @endif
            </div>
         </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->
@endsection
