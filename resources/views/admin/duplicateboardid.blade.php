@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Duplicate Board Details Report<small>&nbsp;(Active)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Duplicate Board Details Report</li>
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
                        <h3 class="card-title">Report of Users wtih Duplicate Board Details</h3>
                    </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>Email Address</th>
                  <th>ID</th>
                  <th>Chapter ID</th>
                  <th>Position ID</th>
			        <th>First Name</th>
				  <th>Last Name</th>
				  <th>Active</th>
                </tr>
                </thead>
                <tbody>
                @foreach($userList as $list)
                  <tr>
                        <td>{{ $list->email }}</td>
                          <td>{{ $list->id }}</td>
                        <td>{{ $list->chapter_id }}</td>
                        <td>{{ $list->board_position_id }}</td>
					<td>{{ $list->first_name }}</td>
						<td>{{ $list->last_name }}</td>
						<td>
							@if($list->is_active=='1')
							YES
							@else
								NO
							@endif
						</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
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
