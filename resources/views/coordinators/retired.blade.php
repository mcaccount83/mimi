@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Retired Coordinator List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Retired Coordinator List</li>
      </ol>
    </section>
    	@if ($message = Session::get('success'))
      <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif
	 @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Retired Coordinators</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="coordinatorlist_retired" class="table table-bordered table-hover">
              <thead>
			    <tr>
				  <th></th>
				   <th>First Name</th>
                  <th>Last Name</th>
                  <th>Position</th>
                  <th>Retire Date</th>
                  <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($retiredCoordinatorList as $list)
                  <tr>
                        <td><a href="<?php echo url("/coordinator/retired/view/{$list->cor_id}") ?>"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
                        <td>{{ $list->cor_fname }}</td>
                        <td>{{ $list->cor_lname }}</td>
                        <td>{{ $list->position }}</td>
                         <td>{{ $list->cor_zapdate }}</td>
						<td>{{ $list->cor_reason }}</td>

                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>


          <div class="box-body text-center"><a href="{{ route('export.retiredcoordinator')}}"><button class="btn btn-themeBlue margin">Export Retired Coordinator List</button></a>
        </div>
            </div>

           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
