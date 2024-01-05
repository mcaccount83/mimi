@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Coordinator List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator List</li>
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
              <h3 class="box-title">List of Coordinators</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="coordinatorlist" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th></th>
                    <th>Region</th>
                    <th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Secondary Position</th>
					<th>Hire Date</th>
                    <th>Email</th>
                    <th>Reports To</th>
                    <th>Home Chapter</th>
                </tr>
                </thead>
                <tbody>
                  @foreach($coordinatorList as $list)
                    <tr>
                      <td><a href="<?php echo url("/coordinator/edit/{$list->cor_id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a></td>
                      <td>{{ $list->reg }}</td>
                      <td>{{ $list->cor_fname }}</td>
                      <td>{{ $list->cor_lname }}</td>
                      <td>{{ $list->position }}</td>
                      <td>{{ $list->sec_pos }}</td>
                	  <td>{{ $list->coordinator_start_date }}</td>
                      <td><a href="mailto:{{ $list->cor_email }}">{{ $list->cor_email }}</a></td>
                      <td>{{$list->report_fname}} {{$list->report_lname}}</td>
                      <td>{{ $list->cor_chapter }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
					</label>
				   <span>Show only my direct reports</span>
                </div>
              </div>
            </div>
			<div class="clearfix"></div>
            <div class="box-body text-center">
              <a class="btn btn-themeBlue margin" href="{{ route('coordinator.create') }}">New Coordinator</a>
              <?php
			 if($checkBoxStatus){ ?>
				<a href="{{ route('export.coordinator',$corId) }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Coordinator List</button></a>
			<?php
			 }
			 else{ ?>
				<a href="{{ route('export.coordinator','0') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Coordinator List</button></a>
			 <?php } ?>


              <a class="btn btn-themeBlue margin" href="mailto:{{ $emailListCord }}">E-mail Listed Coordinators</a>
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
