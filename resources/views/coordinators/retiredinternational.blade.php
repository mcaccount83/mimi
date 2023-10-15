@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      International Coordinator List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">International Coordinator List</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of International Coordinators</h3>
            </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <table id="coordinatorlist_retired" class="table table-bordered table-hover">
              <thead> 
			    <tr>
			      <th></th>
			      <th>Conference</th>
                  <th>Region</th>
				  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Retired Date</th>
                  <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($intCoordinatorList as $list)
                  <tr>
                        <td><a href="<?php echo url("/coordinator/retired/view/{$list->cor_id}") ?>"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                        <td>{{ $list->cor_cid }}</td>
                        <td>{{ $list->reg_name }}</td>
                        <td>{{ $list->cor_fname }}</td>
                        <td>{{ $list->cor_lname }}</td>
                        <td>{{ $list->zapdate }}</td>
                        <td>{{ $list->reason }}</td>

                  </tr>
                  @endforeach
                  </tbody>
                </table>
             </div>
       
            <!-- /.box -->
        </div>
      </div>
    </section>    
    <!-- Main content -->
    
    <!-- /.content -->
 
@endsection

