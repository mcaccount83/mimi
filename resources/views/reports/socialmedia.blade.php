@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Social Media Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Social Media Report</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Social Media</h3>
              
            </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead> 
			    <tr>
                  <th>State</th>
                  <th>Name</th>
                    <th>Facebook</th>
                    <th>Twitter</th>
                    <th>Instagram</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>{{ $list->social1 }}</td>
						<td>{{ $list->social2 }}</td>
						<td>{{ $list->social3 }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>

                </div>
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
@section('customscript')

@endsection