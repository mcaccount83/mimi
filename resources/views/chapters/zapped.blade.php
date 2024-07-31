@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Zapped Chapter List</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Zapped Chapter List</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
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
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">List of Zapped Chapters</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover">
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
                    <td><center><a href="<?php echo url("/chapter/zapped/view/{$list->id}") ?>"><i class="fas fa-eye"></i></a></center></td>
                    <td>{{ $list->state }}</td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->ein }}</td>
                    <td><span class="date-mask">{{ $list->zap_date }}</span></td>
                    <td>{{ $list->disband_reason }}</td>
                </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="card-body text-center">
              <a href="{{ route('export.zapchapter') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download"></i>&nbsp;&nbsp;&nbsp;Export Zapped Chapter List</button></a>
             </div>

          </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection

@section('customscript')
<script>


</script>
@endsection

