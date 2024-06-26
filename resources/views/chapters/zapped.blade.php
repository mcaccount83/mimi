@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Zapped Chapter List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Zapped Chapter List</li>
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
              <h3 class="box-title">List of Zapped Chapters</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
                    <th>Details</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>Disband Date</th>
                    <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td><center><a href="<?php echo url("/chapter/zapped/view/{$list->id}") ?>"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></a></center></td>
                    <td>{{ $list->state }}</td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->ein }}</td>
                    <td>{{ $list->zap_date }}</td>
                    <td>{{ $list->disband_reason }}</td>
                </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="box-body text-center">
              <a href="{{ route('export.zapchapter') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Zapped Chapter List</button></a>
             </div>

          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
