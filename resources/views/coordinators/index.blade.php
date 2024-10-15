@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator List</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator List</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

   <!-- Main content -->
   <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">List of Coordinators</h3>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="coordinatorlist"  class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Region</th>
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
                      <td><center><a href="<?php echo url("/coordinator/edit/{$list->cor_id}") ?>"><i class="fas fa-edit"></i></a></center></td>
                      <td>{{ $list->reg }}</td>
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
              <a class="btn bg-gradient-primary" href="{{ route('coordinator.create') }}"><i class="fas fa-plus" ></i>&nbsp;&nbsp;&nbsp;Add New Coordinator</a>
              <?php
			 if($checkBoxStatus){ ?>
				<a href="{{ route('export.coordinator',$corId) }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Coordinator List</button></a>
			<?php
			 }
			 else{ ?>
				<a href="{{ route('export.coordinator','0') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Coordinator List</button></a>
			 <?php } ?>


              <a class="btn bg-gradient-primary" href="mailto:{{ $emailListCord }}"><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;E-mail Listed Coordinators</a>
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
    var base_url = '{{ url("/coordinatorlist") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
