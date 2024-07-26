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
              <table id="chapterlist_review" class="table table-bordered table-hover">
              <thead>
			    <tr>
					<th>Review</th>
                    <th>PDF</th>
					<th>Email</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Primary Coordinator</th>
                 <th>Assigned Reviewer</th>
                 <th>Extension Given</th>
                 <th>Report Received</th>
                 <th>Date Received</th>
                 <th>Review Completed</th>
                 <th>Date Completed</th>
                 {{-- <th>Ending Balance</th> --}}
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                    @php
                    $emailDetails = app('App\Http\Controllers\ChapterController')->getEmailDetails($list->chap_id);
                    $emailListCord = $emailDetails['emailListCord'];
                    $cc_string = $emailDetails['cc_string'];
                    $financialReportReceived = $emailDetails['report_received'];
                    $name = $emailDetails['name'];
                    $state = $emailDetails['state'];
                    $mimi_url = "https://momsclub.org/mimi";

                    $mail_message = "Don't forget to complete the Financial Report for your chapter!  This report is available now and is due no later than July 10th at 11:59pm.<br>
                            After receiving your completed reports, your Coordinator Team will review the report and reach out if they have any questions.<br>
                            The Financial Report (as well as the Board Election Report) can be accessed by logging into your MIMI account $mimi_url and selecting the buttons at the top of your screen.<br>"
                    @endphp
                  <tr>
						<td><center><a href="<?php echo url("/chapter/financial/{$list->chap_id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                        <td><center>
                                @if($list->financial_report_received == '1')
                                {{-- <a id="viewPdfLink" href="https://drive.google.com/file/d/{{$list->financial_pdf_path}}/view" target="_blank"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></a>&nbsp; --}}
                                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{$list->financial_pdf_path}}"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true" ></i></a>
                                    {{-- <a href="{{ url("/chapter/financial/pdf/{$list->chap_id}") }}" target="_blank"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></a> --}}
                                @endif
                            </center></td>
                        <td>
                            <?php if ($financialReportReceived == null  || $financialReportReceived == 0) { ?>
                            <center><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Financial Report Reminder | {{$name}}, {{$state}}&body={{ urlencode($mail_message) }}"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></center></td>
                            <?php }?>
                        </td>
                        <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td>{{ $list->fname }} {{ $list->lname }}</td>
                        <td>{{ $list->pcfname }} {{$list->pclname}}</td>
                        <td style="background-color: @if($list->report_extension == '1') #FFEB9C; @else transparent; @endif;">
                            @if($list->report_extension == '1')
                                YES
                            @else

                            @endif
                        </td>
                        <td style="background-color: @if($list->financial_report_received == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->financial_report_received == '1')
                                YES
                            @else
                                NO
                            @endif
						</td>
                        <td style="background-color: @if($list->financial_report_received == '1') transparent; @else #FFC7CE; @endif;">
                            {{-- @if($list->financial_report_received != null) --}}
                                {{ $list->report_received }}
                            {{-- @else

                            @endif --}}
						</td>
                        <td style="background-color: @if($list->report_complete == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->report_complete == '1')
                                YES
                            @else
                                NO
                            @endif
						</td>
                        <td style="background-color: @if($list->report_complete == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->review_complete != null)
                                {{ $list->review_complete }}
                            @else

                            @endif
						</td>
                        {{-- <td>
                            @if($list->post_balance != null)
                            ${{ $list->post_balance }}
                            @else

                            @endif
                        </td> --}}
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
              <div class="radio-chk labelcheck">
                        <label style="display: block;"><input type="checkbox" name="show2Primary" id="show2Primary" class="ios-switch green bigswitch" {{$checkBox2Status}} onchange="show2Primary()" /><div><div></div></div>
                        </label>
                    <span> Only show chapters I am Primary Coordinator for</span>
                </div>
            </div>
            </div>
            </div>
        </div>
              <div class="box-body text-center">
                <p>**Known issue - may not send more than 10 messages before returning 500 error.**</p>
              <a href="{{ route('report.financialreminder') }}" onclick="return confirmSendReminder();"><button class="btn btn-themeBlue margin"><i class="fa fa-envelope-o fa-fw" aria-hidden="true" ></i>&nbsp; Send Financial Report Reminders</button></a>
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

function confirmSendReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Financial Report, excluding those with an extension or wtih an assigned reviewer. \n\nAre you sure you want to send the Financial Report Reminders?');
    }

</script>
@endsection
