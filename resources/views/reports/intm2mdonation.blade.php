@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>International M2M Donations Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">International M2M Donations Report</li>
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
              <h3 class="card-title">International M2M Donations</h3>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
					<!--<th>Donation</th> -->
					<th>Conference</th>
				  <th>State</th>
                  <th>Name</th>
                    <th>Donation Amount</th>
                    <th>Donation Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
	                    <!--<td>
	                    <a href="<?php echo url("/chapter/m2mdonation/{$list->id}") ?>"><i class="fa fa-credit-card" aria-hidden="true"></i></a>
	                    </td> -->
	                    <td>{{ $list->conference }}</td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>${{ $list->m2m_payment }}</td>
						<td>{{ $list->m2m_date }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
                </div>
                <div class="card-body text-center">&nbsp;</div>
              </div>
              </div>
            </div>

           </div>
          <!-- /.box -->

    </section>

    <!-- /.content -->

@endsection
@section('customscript')

@endsection
