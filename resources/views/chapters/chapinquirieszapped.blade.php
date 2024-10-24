@extends('layouts.coordinator_theme')
<style>
    .hidden-column {
        display: none !important;
    }
    </style>

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Inquiries Zapped Chapter List</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Inquiries Zapped Chapter List</li>
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
                  <h3 class="card-title">List of Zapped Inquiries Chapters</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
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
    </div>

    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
