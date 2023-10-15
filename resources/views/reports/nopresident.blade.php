@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      No President Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">No President Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapters with no President</h3>
              
            </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead> 
			    <tr>
                  <th>Chapter ID</th>
                  <th>Chapter Name</th>
                <th>Conference</th>

                </tr>
                </thead>
                <tbody>
                @foreach($ChapterPres as $list)
                  <tr>
                        <td>{{ $list->id }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->conference }}</td>
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
@section('customscript')
<script>

</script>
@endsection