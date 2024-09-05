@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Coordinator ToDo List Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator ToDo List Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Coordinator ToDo List</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
				<thead>
			    <tr>
					<th>Region</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Month</th>
					<th>Re-Reg Reminders</th>
                    <th>Re-Reg Late Notices</th>
                    <th>Re-Reg Payments</th>
					<th>M2M Payments</th>
					<th>Database Backups</th>
                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                    <td>{{ $list->reg }}</td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
					<td>{{ $list->position }}</td>
                    <td>{{ $list->todo_month }}</td>
                    <td bgcolor="<?php
							if($list->todo_send_rereg !='1')
									echo "#FFC7CE";
							?>">
							@if($list->todo_send_rereg=='1')
							YES
							@else
								NO
							@endif
            		</td>
                    <td bgcolor="<?php
							if($list->todo_send_late !='1')
									echo "#FFC7CE";
							?>">
							@if($list->todo_send_late=='1')
							YES
							@else
								NO
							@endif
            		</td>
            		<td bgcolor="<?php
							if($list->todo_record_rereg !='1')
									echo "#FFC7CE";
							?>">
							@if($list->todo_record_rereg=='1')
							YES
							@else
								NO
							@endif
            		</td>
            		<td bgcolor="<?php
							if($list->todo_record_m2m !='1')
									echo "#FFC7CE";
							?>">
							@if($list->todo_record_m2m=='1')
							YES
							@else
								NO
							@endif
            		</td>
            		<td bgcolor="<?php
							if($list->todo_export_reports !='1')
									echo "#FFC7CE";
							?>">
							@if($list->todo_export_reports=='1')
							YES
							@else
								NO
							@endif
            		</td>
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
