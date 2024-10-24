@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Mail Queue</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Mail Queue</li>
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
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Report of Mail in Outgoing Queue</h3>
                    </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>id</th>
                  <th>queue</th>
                <th>attempts</th>
                <th>reserved</th>
                <th>available</th>
                <th>created</th>

                </tr>
                </thead>
                <tbody>
                @foreach($Queue as $list)
                  <tr>
                        <td>{{ $list->id }}</td>
                        <td>{{ $list->queue }}</td>
                        <td>{{ $list->attempts }}</td>
                        <td>{{ $list->reserved_at }}</td>
                        <td>{{ $list->available_at }}</td>
                        <td>{{ $list->created_at }}</td>

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
    <!-- /.content -->

@endsection
@section('customscript')
<script>

</script>
@endsection
