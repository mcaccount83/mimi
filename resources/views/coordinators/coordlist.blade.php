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
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($coordinatorCondition)
                            <a class="dropdown-item" href="{{ route('coordinators.coordlist') }}">Active Coordinator List</a>
                            <a class="dropdown-item" href="{{ route('coordinators.coordretired') }}">Retired Coordinator List</a>
                        @endif
                        @if ($adminReportCondition)
                            <a class="dropdown-item" href="{{ route('international.intcoord') }}">International Active Coordinator List</a>
                            <a class="dropdown-item" href="{{ route('international.intcoordretired') }}">International Retired Coordinator List</a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="coordinatorlist"  class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Conf/Reg</th>
                    <th>First Name</th>
					<th>Last Name</th>
					<th>Primary Position</th>
                    <th>Primary (MIMI) Position</th>
					<th>Secondary Position</th>
					<th>Hire Date</th>
                    <th>Email</th>
                    <th>Reports To</th>
                    {{-- <th>Home Chapter</th> --}}
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                        <td class="text-center align-middle"><a href="{{ url("/coorddetails/{$list->cor_id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                        @if ($list->reg != "None")
                            {{ $list->conf }} / {{ $list->reg }}
                        @else
                            {{ $list->conf }}
                        @endif
                    </td>
                      <td>{{ $list->cor_fname }}</td>
                      <td>{{ $list->cor_lname }}</td>
                      <td>{{ $list->display_pos }}</td>
                      <td>{{ $list->position }}</td>
                      <td>{{ $list->sec_pos }}</td>
                	  <td><span class="date-mask">{{ $list->coordinator_start_date }}</span></td>
                      <td><a href="mailto:{{ $list->cor_email }}">{{ $list->cor_email }}</a></td>
                      <td>{{$list->report_fname}} {{$list->report_lname}}</td>
                      {{-- <td>{{ $list->cor_chapter }}</td> --}}
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
              <!-- /.card-body -->
              <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Show only my direct reports</label>
                </div>
            </div>

                <div class="card-body text-center">
              <a class="btn bg-gradient-primary" href="{{ route('coordinators.editnew') }}"><i class="fas fa-plus mr-2" ></i>Add New Coordinator</a>
              <?php
            if($checkBoxStatus){ ?>
				 <button class="btn bg-gradient-primary" disabled><i class="fas fa-download mr-2" ></i>Export Coordinator List</button></a>
			<?php
			 }
			 else{ ?>
				<a href="{{ route('export.coordinator','0') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download mr-2" ></i>Export Coordinator List</button></a>
			 <?php } ?>
              <a class="btn bg-gradient-primary" href="mailto:{{ $emailListCord }}"><i class="fas fa-envelope mr-2" ></i>E-mail Listed Coordinators</a>
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

function showPrimary() {
    var base_url = '{{ url("/coordinator/coordlist") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
