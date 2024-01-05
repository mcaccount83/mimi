@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Inquiries Zapped List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Inquiries Zapped List</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Zapped Chapters</h3>
              </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>

                    <th>State</th>
                    <th>Chapter Name</th>
                    <th>Boundaries</th>
                   <th>Disband Date</th>

                </tr>
                </thead>
                <tbody>
                @foreach($inquiriesList as $list)
                  <tr>

                        <td>{{ $list->state }}</td>
                        <td>{{ $list->chapter_name }}</td>
                        <td>{{ $list->terry }}</td>
                        <td>{{ $list->zap_date }}</td>



                    </tr>

                  @endforeach
                  </tbody>
                </table>
            </div>
           </div>
           </div>
          <!-- /.box -->
        </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
