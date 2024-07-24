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
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
				<th>Review</th>
                <th>Email</th>
				<th>State</th>
                <th>Name</th>
                <th>Primary Coordinator</th>
                <th>Received</th>
				<th>Activated</th>
				</tr>
                </thead>
                <tbody>

                @foreach($chapterList as $list)
                    @php
                    $emailDetails = app('App\Http\Controllers\ChapterController')->getEmailDetails($list->id);
                    $emailListCord = $emailDetails['emailListCord'];
                    $cc_string = $emailDetails['cc_string'];
                    $boardElectionReportReceived = $emailDetails['board_submitted'];
                    $name = $emailDetails['name'];
                    $state = $emailDetails['state'];
                    $mimi_url = "https://momsclub.org/mimi";

                    $mail_message = "Don't forget to complete the Board Election Report for your chapter!  This report is available now and should be filled out as soon as your chapter has held its election but is due no later than June 30th at 11:59pm.<br>
                            Please submit your report as soon as possible to ensure that your incoming board members have access to all the tools they need to be successful. The information from the report is used for<br><ul>";
                    $mail_message .="<li>Chapter Contacts for your Coordinator Team</li>";
                    $mail_message .="<li>Access to MIMI</li>";
                    $mail_message .="<li>Inclusion in the Board Discussion Group</li>";
                    $mail_message .="<li>Receipt of Conference Newsletter</li>";
                    $mail_message .="<li>Automated Messages from MIMI, including Re-Registration payment reminders</li>";

                    $mail_message .="</ul>";
                    $mail_message .="The Board Election Report can be accessed by logging into your MIMI account $mimi_url and selecting the buttons at the top of your screen.<br>";
                    @endphp
                  <tr>
                      <td>
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                         @if($list->new_board_active=='1')
								<a href="#" <?php echo "disabled";?>></a>
							@else
								<center><a href="<?php echo url("/chapter/boardinfo/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center>
							@endif
                        <?php }?>
                          </td>
                          <td>
                            <?php if ($boardElectionReportReceived == null || $boardElectionReportReceived == 0){ ?>
                              <center><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Board Election Reminder | {{$name}}, {{$state}}&body={{ urlencode($mail_message) }}"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></center>
                            <?php }?>
                          </td>
                            <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
						<td>{{ $list->cor_fname }} {{ $list->cor_lname }}</td>
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
				<?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                    <p>**Known issue - will not send more than 10 messages.**</p>
                    <a title="Board Election Report reminders will be sent to all chapters who have not submitted a report." href="{{ route('report.boardinforeminder') }}"><button class="btn btn-themeBlue margin"><i class="fa fa-envelope-o fa-fw" aria-hidden="true" ></i>&nbsp; Send Board Election Reminders</button></a>
				    <button type="button" id="board-active" class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>><i class="fa fa-play fa-fw" aria-hidden="true" ></i>&nbsp; Make Received Boards Active</button>
				    <a href="{{ route('export.boardelection')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export UN-Activated Board List</button></a>
				<?php }?>
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
    var base_url = '{{ url("/yearreports/boardinfo") }}';

    $("#board-active").click(function() {
        window.location.href = base_url + "?board=active";
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
