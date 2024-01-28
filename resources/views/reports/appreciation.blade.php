@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Volunteer Appreciation Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Volunteer Appreciation Report</li>
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
              <h3 class="box-title">Report of Volunteer Appreciation</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="coordinatorlist" class="table table-bordered table-hover">
				<thead>
			    <tr>
			        <th>Edit</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Start Date</th>
					<th>< 1 Year</th>
					<th>1 Year</th>
					<th>2 Years</th>
                    <th>3 Years</th>
                    <th>4 Years</th>
                    <th>5 Years</th>
                    <th>6 Years</th>
                    <th>7 Years</th>
                    <th>8 Years</th>
                    <th>9 Years</th>
                    <th>Necklace</th>
                    <th>Top Tier/Other</th>
                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                    <td><center><a href="<?php echo url("/coordinator/appreciation/{$list->cor_id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
                    <td>{{ $list->start_date }}</td>
                    <td>{{ $list->yr_0 }}</td>
					<td>{{ $list->yr_1 }}</td>
					<td>{{ $list->yr_2 }}</td>
					<td>{{ $list->yr_3 }}</td>
					<td>{{ $list->yr_4 }}</td>
					<td>{{ $list->yr_5 }}</td>
					<td>{{ $list->yr_6 }}</td>
					<td>{{ $list->yr_7 }}</td>
					<td>{{ $list->yr_8 }}</td>
					<td>{{ $list->yr_9 }}</td>
					<td>@if($list->necklace=='1')
							YES
							@endif
						</td>
					<td>{{ $list->toptier }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
           </div>
                     <div class="box-body text-center"><a href="{{ route('export.appreciation')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Coordinator Appreciation List</button></a>
        </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
