@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>New Chapter Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">New Chapter Report</li>
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
                  <h3 class="card-title">Report of New Chapters <small>(1 Year or Younger)</small></h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
					<th>Details</th>
					<th>State</th>
					<th>Name</th>
					<th>Founded</th>
					<th>EIN Letter on File</th>
					<th>Primary Coordinator</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle">
                            <a href="<?php echo url("/chapter/edit/{$list->ch_id}") ?>"><i class="fas fa-edit "></i></a></td>
						<td>{{ $list->ch_state }}</td>
                        <td>{{ $list->ch_name }}</td>
						<td>{{ $list->month_name }} {{ $list->year }}</td>
                        <td  @if($list->ein_letter_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>

						<td>{{ $list->cor_fname }} {{ $list->cor_lname }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <div class="card-body text-center"></div>
            </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
