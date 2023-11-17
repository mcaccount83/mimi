@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Board Election Reports
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Board Election Reports</li>
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
              <h3 class="box-title">Report of Board Elections</h3>
              &nbsp;&nbsp;(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)

            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead>
			    <tr>
				<th></th>
                <!--<th>Email Board</th>-->
				<th>State</th>
                <th>Name</th>
                <th>Primary Coordinator</th>
                <th>Received</th>
				<th>Activated</th>
				</tr>
                </thead>
                <tbody>

                @foreach($chapterList as $list)
                <?php
                $chapterEmailList = DB::table('board_details as bd')
                                          ->select('bd.email as bor_email')
                                          ->where('bd.chapter_id', '=', $list->id)
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

	                $mail_message = "<br>Don't forget to complete the Board Election Report for your chapter!  This report is available now and should be filled out as soon as your chapter has held its election but is due no later than June 30th at 11:59pm.";
	                $mail_message = "Please submit your report as soon as possible to ensure that your incoming board members have access to all the tools they need to be successful. The information from the report is used for:";
	                $mail_message .= "<ul><li>Chapter Contacts for your Coordinator Team</li>
                    <li>Access to MIMI</li>
                    <li>Inclusion in the Board Discussion Group</li>
                    <li>Receipt of Conference Newsletter</li>
                    <li>Payments received after the last day of your renewal month should include a late fee of $10</li></ul>";
                   $mail_message .= "The Board Election Report (as well as the Financial Report) can be accessed by logging into your MIMI account (<a href='https://momsclub.org/mimi'>https://momsclub.org/mimi</a>) and selecting the buttons at the top of your screen."

                      ?>
                  <tr>
                      <td>
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                         @if($list->new_board_active=='1')
								<a href="#" <?php echo "disabled";?>></a>
							@else
								<a href="<?php echo url("/chapter/boardinfo/{$list->id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
							@endif
                        <?php }?>
                          </td>
                        <!--<td><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Board Election Report Due - MOMS Club of {{ $list->name }}, {{ $list->state }}&body={{ $mail_message }}"><i class="fa fa-envelope" aria-hidden="true"></i></a></td>
			     	    -->
                            <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
						<td>{{ $list->cor_fname }} {{ $list->cor_lname }}</td>
                        <td style="background-color: @if($list->new_board_submitted == '1') transparent; @else #FF000050; @endif;">
                            @if($list->new_board_submitted == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td style="background-color: @if($list->new_board_active == '1') transparent; @else #FF000050; @endif;">
                            @if($list->new_board_active == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
                 </tr>
                  @endforeach

                  </tbody>
                </table>
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                   <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                  </label>
                  <span> Only show chapters I am primary for</span>
                </div>
              </div>
              </div>

			  <div class="box-body text-center">
				<?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                    <a title="Board Election Report reminders will be sent to all chapters who have not submitted a report." href="{{ route('report.boardinforeminder') }}"><button class="btn btn-themeBlue margin">Send Board Election Reminders</button></a>
				    <button type="button" id="board-active" class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Make Received Boards Active</button>
				    <a href="{{ route('export.boardelection')}}"><button class="btn btn-themeBlue margin">Export UN-Activated Board List</button></a>
				<?php }?>
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
$(document).ready(function(){
    $("#board-active").click(function() {
        var base_url = '{{ url("/yearreports/boardinfo") }}';

        if ($("#board-active").prop("checked") == true) {
            window.location.href = base_url + '?board=active';
        } else {
            window.location.href = base_url;
        }
    });
});


function showPrimary() {
    var base_url = '{{ url("/yearreports/boardinfo") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
