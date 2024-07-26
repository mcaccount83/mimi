@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      EOY Status Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">End of Year Status Report</li>
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
              <h3 class="box-title">Report of End of Year</h3>
              &nbsp;&nbsp;(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
				<th>Edit</th>
                <th>Email</th>
				<th>State</th>
                <th>Name</th>
                <th>Extension Given<br>Do Not Send Late Notice</th>
                <th>Board Report Received</th>
                <th>Board Report Activated</th>
				<th>Financial Report Received</th>
				<th>Financial Review Completed</th>
				</tr>
                </thead>
                <tbody>

                @foreach($chapterList as $list)
                    @php
                    $emailDetails = app('App\Http\Controllers\ChapterController')->getEmailDetails($list->id);
                    $emailListCord = $emailDetails['emailListCord'];
                    $cc_string = $emailDetails['cc_string'];
                    $boardElectionReportReceived = $emailDetails['board_submitted'];
                    $financialReportReceived = $emailDetails['report_received'];
                    $einLetterCopyReceived = $emailDetails['ein_letter'];
                    $name = $emailDetails['name'];
                    $state = $emailDetails['state'];
                    $mimi_url = "https://momsclub.org/mimi";

                    $mail_message = "At this time, we have not received one or more of your chapter's End of Year Reports. They are now considered PAST DUE.<br>
                    The following items are missing:<ul>";

                    if ($boardElectionReportReceived == null || $boardElectionReportReceived == 0) {
                        $mail_message .= "<li>Board Election Report</li>";
                    }
                    if ($financialReportReceived == null || $financialReportReceived == 0) {
                        $mail_message .= "<li>Financial Report</li>";
                    }
                    if ($einLetterCopyReceived == null || $einLetterCopyReceived == 0) {
                        $mail_message .= "<li>Copy of EIN Letter</li>";
                    }

                    $mail_message .= "</ul>";
                    $mail_message .= "Please submit through your MIMI account ($mimi_url) as soon as possible. If you are having trouble submitting, have any questions, or need more time, please let us know!<br>";

                    @endphp
                    <tr>
                      <td>
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
						    <center><a href="<?php echo url("/chapter/statusview/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center>
                        <?php }?>
                          </td>
                        <td>
                          <?php if ($boardElectionReportReceived == null || $financialReportReceived == null || $boardElectionReportReceived == 0 || $financialReportReceived == 0) { ?>
                            <center><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=End of Year Reports - Late Notice | {{$name}}, {{$state}}&body={{ urlencode($mail_message) }}"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></center>
                          <?php }?>
                        </td>
                        <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td style="background-color: @if($list->report_extension == '1') #FFEB9C; @else transparent; @endif;">
                            @if($list->report_extension == '1')
                                YES
                            @else

                            @endif
                        </td>
                        <td style="background-color: @if($list->new_board_submitted == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->new_board_submitted == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
				  	    <td style="background-color: @if($list->new_board_active == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->new_board_active == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
						<td style="background-color: @if($list->financial_report_received == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->financial_report_received == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
						<td style="background-color: @if($list->financial_report_complete == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->financial_report_complete == '1')
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
            </div>

              <div class="box-body text-center">
                <p>**Known issue - may not send more than 10 messages before returning 500 error.**</p>
                <a href="{{ route('report.eoylatereminder') }}" onclick="return confirmSendReminder();"> <button class="btn btn-themeBlue margin"><i class="fa fa-envelope-o fa-fw" aria-hidden="true"></i>&nbsp; Send EOY Late Notices </button> </a>
                <a href="{{ route('export.eoystatus')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export EOY Status List</button></a>
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
    var base_url = '{{ url("/yearreports/eoystatus") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
        return confirm('This action will send a Late Notice to all chapters who have not submitted their Board Election Report OR their Financial Report, excluding those with an extension or an assigned reviewer. \n\nAre you sure you want to send the EOY Late Notices?');
    }

</script>
@endsection
