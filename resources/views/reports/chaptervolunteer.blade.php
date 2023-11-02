@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Volunteer Utilization Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Volunteer Utilization Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Volunteer Utilization</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
				<thead>
			    <tr>
			        <th></th>
			        <th>Conference</th>
					<th>Region</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Secondary Position</th>
					<th>Direct Report</th>
          <th>InDirect Report</th>
          <th>Total Report</th>

                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                      <td><a href="/coordinator/edit/{$list->cor_id}"><i class="fa fa-pencil-square" aria-hidden="true"></i></a></td>
                      <td>{{ $list->cor_conf }}</td>
                    <td>{{ $list->reg }}</td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
					          <td>{{ $list->position }}</td>

        					  <?php
                    // Calculate direct chapter report
                    $coordinatorSecPos = DB::table('coordinator_position as cp')
                        ->select('cp.long_title as sec_pos')
                        ->join('coordinator_details as cd', 'cd.sec_position_id', '=', 'cp.id')
                        ->where('cd.coordinator_id', $list->cor_id)
                        ->get();

                        $coordinator_options = DB::table('chapters')
                            ->select('name')
                            ->where('primary_coordinator_id', $list->cor_id)
                            ->where('is_active', '1')
                            ->get();

                            $direct_report=count($coordinator_options);

                    // calculate indirect chpater report
                    $corlayerId = $list->layer_id;
                        $sqlLayerId = 'crt.layer'.$corlayerId;
                    $reportIdList = DB::table('coordinator_reporting_tree as crt')
                                        ->select('crt.id')
                                        ->where($sqlLayerId, '=', $list->cor_id)
                                        ->get();
                    $inQryStr ='';
                    foreach($reportIdList as $key => $val)
                    {
                       $inQryStr .= $val->id.',';
                    }
                    $inQryStr = rtrim($inQryStr,',');
                    $inQryArr = explode(',',$inQryStr);
//print_r($list->cor_id);
                    $indirectChapterReport = DB::table('chapters')
                                        ->select('chapters.id as id','chapters.name as chapter_name','chapters.inquiries_contact as inq_con','chapters.territory as terry','chapters.status as status','chapters.inquiries_note as inq_note')
                                        ->leftJoin('coordinator_details as cd', 'cd.coordinator_id', '=', 'chapters.primary_coordinator_id')
                                        ->leftJoin('board_details as bd', 'bd.chapter_id', '=', 'chapters.id')
                                        ->where('chapters.is_active', '=', '1')
                                        ->where('bd.board_position_id', '=', '1')
                                        ->whereIn('chapters.primary_coordinator_id', $inQryArr)
                                        ->get();
                   // print_r($inQryArr);
                    $indirect_report = count($indirectChapterReport) - $direct_report;
                    $total_report = $direct_report + $indirect_report;
        					   ?>
        			  <td>{{ $list->sec_pos }}</td>
                      <td>{{ $direct_report }}</td>
                      <td>{{ $indirect_report }}</td>
                      <td>{{ $total_report }}</td>
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
