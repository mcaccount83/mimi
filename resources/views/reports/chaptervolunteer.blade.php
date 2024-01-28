@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Volunteer Utilization Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Volunteer Utilization Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Volunteer Utilization</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="coordinatorlist" class="table table-bordered table-hover">
				<thead>
			    <tr>
			        <th>Details</th>
			        <th>Conference</th>
					<th>Region</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Secondary Position</th>
					<th>Direct Report</th>
                    <th>InDirect Report</th>
                    <th>Total Report</th>
                </tr>
                </thead>
                <tbody>
                @foreach($coordinatorList as $list)
                  <tr>
                    <td><center><a href="<?php echo url("/coordinator/edit/{$list->cor_id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                    <td>{{ $list->cor_conf }}</td>
                    <td>{{ $list->reg }}</td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
					<td>{{ $list->position }}</td>
        			<td>{{ $list->sec_pos }}</td>
                    <td>{{ $list->direct_report }}</td>
                    <td>{{ $list->indirect_report }}</td>
                    <td>{{ $list->total_report }}</td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
