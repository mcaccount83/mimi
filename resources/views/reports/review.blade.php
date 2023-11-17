@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Financial Reports
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Financial Reports</li>
      </ol>
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
    @if ($message = Session::get('info'))
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
    </div>
@endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapter Financials</h3>
              &nbsp;&nbsp;(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead>
			    <tr>
					<th></th>
					<th>Email Board</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Assigned Reviewer</th>
                 <th>Primary Coordinator</th>
                 <th>Report Received</th>
                 <th>Review Completed</th>
                 <th>Date Completed</th>
                 <th>Ending Balance</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                <?php
               // var_dump($list);die;
                $chapterEmailList = DB::table('board_details as bd')
                                          ->select('bd.email as bor_email')
                                          ->where('bd.chapter_id', '=', $list->chap_id)
                                          ->get();
                      $emailListCord="";
                      foreach($chapterEmailList as $val){
                        $email = $val->bor_email;
                        $escaped_email=str_replace("'", "\\'", $email);
                        if ($emailListCord==""){
                            $emailListCord = $escaped_email;
                        }
                        else{
                            $emailListCord .= ";" . $escaped_email;
                        }
                      }
                      $cc_string="";
                     // var_dump($emailListCord); die;
                      $reportingList = DB::table('coordinator_reporting_tree')
                                            ->select('*')
                                            ->where('id', '=', $list->primary_coordinator_id)
                                            ->get();
                            foreach($reportingList as $key => $value)
                            {
                                $reportingList[$key] = (array) $value;
                            }
                            $filterReportingList = array_filter($reportingList[0]);
                            unset($filterReportingList['id']);
                            unset($filterReportingList['layer0']);
                            $filterReportingList = array_reverse($filterReportingList);
                            $str = "";
                            $array_rows=count($filterReportingList);
                            $down_line_email="";
                            foreach($filterReportingList as $key =>$val){
                                //if($corId != $val && $val >1){
								if($val >1){
                                    $corList = DB::table('coordinator_details as cd')
                                                    ->select('cd.email as cord_email')
                                                    ->where('cd.coordinator_id', '=', $val)
                                                    ->where('cd.is_active', '=', 1)
                                                    ->get();
                                  if ($down_line_email==""){
                                    if(isset($corList[0]))
                                      $down_line_email = $corList[0]->cord_email;
                                  }
                                  else{
                                    if(isset($corList[0]))
                                      $down_line_email .= ";" . $corList[0]->cord_email;
                                  }

                                }
                            }
                            $cc_string = "?cc=" . $down_line_email;
                           // var_dump($list);die;
                ?>
                  <tr>
						<td>
						<?php if (Session::get('positionid') <=7 || $positionid = 25){ ?>
						<a href="<?php //echo url("/chapter/edit/{$list->id}")
							echo url("/chapter/financial/{$list->chap_id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
							</td>
						<td><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Financial Report Review - MOMS Club of {{ $list->name }}, {{ $list->state }}"><i class="fa fa-envelope" aria-hidden="true"></i></a></i></td>
						<td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td>{{ $list->fname }} {{ $list->lname }}</td>
                                			<?php
                                                    $primaryCoor = DB::table('coordinator_details as cd')
                                                        ->select('cd.first_name as pfname', 'cd.last_name as plname')
                                                        ->where('cd.coordinator_id', $list->primary_coordinator_id)
                                                        ->get();
                                            ?>
                        <td>{{ $primaryCoor[0]->pfname }} {{$primaryCoor[0]->plname}}</td>
                        <td style="background-color: @if($list->financial_report_received == '1') transparent; @else #FF000050; @endif;">
                            @if($list->financial_report_received == '1')
                                YES
                            @else
                                NO
                            @endif


							<?php }?>
						</td>
                        <td style="background-color: @if($list->report_complete == '1') transparent; @else #FF000050; @endif;">
                            @if($list->report_complete == '1')
                                YES
                            @else
                                NO
                            @endif
						</td>
                        <td style="background-color: @if($list->report_complete == '1') transparent; @else #FF000050; @endif;">
                            @if($list->review_complete != null)
                                {{ $list->review_complete }}
                            @else
                                NO
                            @endif
						</td>
                        <td>${{ $list->post_balance }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                  </label>
                  <span> Only show chapters I am Assigned Reviewer for</span>
                </div>
              <div class="radio-chk labelcheck">
                <div class="form-group">
                        <label style="display: block;"><input type="checkbox" name="show2Primary" id="show2Primary" class="ios-switch green bigswitch" {{$checkBox2Status}} onchange="show2Primary()" /><div><div></div></div>
                        </label>
                    <span> Only show chapters I am Primary Coordinator for</span>
                </div>
            </div>
            </div>
              <div class="box-body text-center">
              <a title="Financial Report reminders will be sent to all chapters who have not submitted a report." href="{{ route('report.financialreminder') }}"><button class="btn btn-themeBlue margin">Send Financial Report Reminders</button></a>
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
<script>
  function showPrimary() {
    var base_url = '{{ url("/yearreports/review") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function show2Primary() {
    var base_url = '{{ url("/yearreports/review") }}';

    if ($("#show2Primary").prop("checked") == true) {
        window.location.href = base_url + '?check2=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
