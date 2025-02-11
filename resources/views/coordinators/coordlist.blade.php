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
                    <th>Conf/Reg</th>
                    <th>Coordinator Name</th>
					<th>Primary (Display) Position</th>
                    <th>Secondary Position</th>
                    {{-- <th>Primary (MIMI) Position</th> --}}
					<th>Hire Date</th>
                    <th>Email</th>
                    <th>Reports To</th>
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coorddetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
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
                            <td>{{ $list->secondaryPosition?->long_title }} </td>
                        @endif
                         {{-- <td>{{ $list->mimiPosition->short_title }}</td> --}}
                	  <td><span class="date-mask">{{ $list->coordinator_start_date }}</span></td>
                      <td><a href="mailto:{{ $list->email }}">{{ $list->email }}</a></td>
                      <td>{{ $list->reportsTo->first_name }} {{ $list->reportsTo->last_name }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
              <!-- /.card-body -->
              <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showDirect" id="showDirect" class="custom-control-input" {{$checkBoxStatus}} onchange="showDirect()" />
                    <label class="custom-control-label" for="showDirect">Show only my direct reports</label>
                </div>
            </div>

                <div class="card-body text-center">
                @if($conferenceCoordinatorCondition)
                    <a class="btn bg-gradient-primary" href="{{ route('coordinators.editnew') }}"><i class="fas fa-plus mr-2" ></i>Add New Coordinator</a>
                    @if($checkBoxStatus == null)
                        <a class="btn bg-gradient-primary" href="{{ route('export.coordinator','0') }}"><i class="fas fa-download mr-2" ></i>Export Coordinator List</a>
                    @else
                        <button class="btn bg-gradient-primary" disabled><i class="fas fa-download mr-2" ></i>Export Coordinator List</button></a>
                    @endif
                    <a class="btn bg-gradient-primary" href="mailto:{{ $emailListCord }}"><i class="fas fa-envelope mr-2" ></i>E-mail Listed Coordinators</a>
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

@section('customscript')
<script>
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

function showDirect() {
    var base_url = '{{ url("/coordinator/coordlist") }}';

    if ($("#showDirect").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
