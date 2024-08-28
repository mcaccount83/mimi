@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Board Election Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Board Election Report</li>
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
                <h3 class="card-title">Report of Board Elections&nbsp;<small>(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)</small></h3>
              </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
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
                      <td class="text-center align-middle">
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                         @if($list->new_board_active=='1')
								<a href="#" <?php echo "disabled";?>></a>
							@else
								<a href="<?php echo url("/chapter/boardinfo/{$list->id}") ?>"><i class="fas fa-edit"></i></a>
							@endif
                        <?php }?>
                          </td>
                          <td class="text-center align-middle">
                            <?php if ($boardElectionReportReceived == null || $boardElectionReportReceived == 0){ ?>
                              <a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Board Election Reminder | {{$name}}, {{$state}}&body={{ urlencode($mail_message) }}"><i class="far fa-envelope"></i></a>
                            <?php }?>
                          </td>
                            <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
						<td>{{ $list->cor_fname }} {{ $list->cor_lname }}</td>
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
				<?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
                    {{-- <p>**Known issue - may not send more than 10 messages before returning 500 error.**</p> --}}
                    <a href="{{ route('report.boardinforeminder') }}" onclick="return confirmSendReminder();">><button class="btn bg-gradient-primary"><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Send Board Election Reminders</button></a>
				    <button type="button" id="board-active" class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-play" ></i>&nbsp;&nbsp;&nbsp;Make Received Boards Active</button>
				    <a href="{{ route('export.boardelection')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export UN-Activated Board List</button></a>
				<?php }?>
             </div>
           </div>
          <!-- /.box -->
        </div>
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

function confirmSendReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Board Election Report. \n\nAre you sure you want to send the Board Elecion Report Reminders?');
    }


</script>
@endsection
