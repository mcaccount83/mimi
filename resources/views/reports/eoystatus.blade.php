@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>EOY Status Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">EOY Status Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
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
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
                <h3 class="card-title">Report of End of Year Status&nbsp;<small>(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)</small></h3>
              </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
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
                      <td class="text-center align-middle">
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
						    <a href="<?php echo url("/chapter/statusview/{$list->id}") ?>"><i class="fas fa-edit"></i></a>
                        <?php }?>
                          </td>
                        <td class="text-center align-middle">
                          <?php if ($boardElectionReportReceived == null || $financialReportReceived == null || $boardElectionReportReceived == 0 || $financialReportReceived == 0) { ?>
                            <a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=End of Year Reports - Late Notice | {{$name}}, {{$state}}&body={{ urlencode($mail_message) }}"><i class="far fa-envelope"></i></a>
                          <?php }?>
                        </td>
                        <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td @if($list->report_extension == '1')style="background-color: #ffc107;"
                            @else style="background-color: transparent;" @endif>
                            @if($list->report_extension == '1')
                                YES
                            @else

                            @endif
                        </td>
                        <td @if($list->new_board_submitted == '1')style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->new_board_submitted == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td @if($list->new_board_active == '1')style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->new_board_active == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td  @if($list->financial_report_received == '1')style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->financial_report_received == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td @if($list->financial_report_complete == '1')style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
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
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="card-body text-center">
                <p>**Known issue - may not send more than 10 messages before returning 500 error.**</p>
                <a href="{{ route('report.eoylatereminder') }}" onclick="return confirmSendReminder();"> <button class="btn bg-gradient-primary"><i class="fas fa-envelope"></i>&nbsp;&nbsp;&nbsp;Send EOY Late Notices </button> </a>
                <a href="{{ route('export.eoystatus')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export EOY Status List</button></a>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
</div>
    </section>
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
