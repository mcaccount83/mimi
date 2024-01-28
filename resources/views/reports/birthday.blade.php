@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Volunteer Birthday Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Volunteer Birthday Report</li>
      </ol>
    </section>
@if ($message = Session::get('success'))
      <div class="alert alert-success">
         <p>{{ $message }}</p>
      </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Volunteer Birthdays</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="coordinatorlist_birthday" class="table table-bordered table-hover">
				<thead>
			    <tr>
			        <th>Edit</th>
			        <th>Conference</th>
					<th>Reigon</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Birthday</th>
					<th>Card Sent</th>
                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                      <td><center><a href="<?php echo url("/coordinator/birthday/{$list->cor_id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                      <td>{{ $list->cor_conf }}</td>
                      <td>{{ $list->reg }}</td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
                    <td>{{ $list->month }}  {{ $list->b_day }}</td>
                    <td>{{ $list->card_sent }}</td>
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
