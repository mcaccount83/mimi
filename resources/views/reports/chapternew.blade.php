@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      New Chapter Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">New Chapter Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of New Chapters 1 Year or Younger</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
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
						<td><center><a href="<?php echo url("/chapter/edit/{$list->ch_id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
						<td>{{ $list->ch_state }}</td>
                        <td>{{ $list->ch_name }}</td>
						<td>{{ $list->month_name }} {{ $list->year }}</td>
                        <td style="background-color: @if($list->ein_letter_path != null) transparent; @else #FFC7CE; @endif;">
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
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
