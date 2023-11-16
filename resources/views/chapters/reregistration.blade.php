@extends('layouts.coordinator_theme')

@section('content')
<style type="text/css">
  /*#due_sort{
    opacity: 0.2;
    content: "\e150";
  }*/
</style>
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
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Re-Registrations
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Re-Registrations</li>
      </ol>
    </section>
    <!-- Main content -->

    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Chapters Re-Registrations</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                </div>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">

              <table id="chapterlist_reReg" class="table table-bordered table-hover">
              <thead>
      			    <tr>
          			<th>Payment</th>
          			<th>Notes</th>
          			<th>Email Board</th>
          			<th>Chapter State</th>
                    <th>Chapter Name</th>
                    <th>Re-Registration Notes</th>
                    <th class="nosort" id="due_sort">Due</th>
                    <th>Last Paid</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($reChapterList as $list)
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

                    $reregistration_url = "https://momsclub.org/resources/re-registration-payment/";

	                $mail_message = "<br><hr>
                    <b><p>Calculate your payment:</p></b>
                    <ul><li>Determine how many people paid dues to your chapter since your last re-registration payment through today.</li>
                    <li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
                    <li>If this total amount of members is less than 10, make your check for the amount of $50</li>
                    <li>If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due</li>
                    <li>Payments received after the last day of your renewal month should include a late fee of $10</li></ul>";
                    $mail_message .= "<b><p>Make your payment:</p></b>
	                <ul><li><a href='$reregistration_url'>Pay Online</a> (Password:  daytime support)</li>
	                <li>Pay via Mail to: Chapter Re-Registration, 208 Hewitt Dr. Ste 103 #328, Waco, TX 76712</li></ul>";
                      ?>
                  <tr>
                        <td>
                        <?php if (Session::get('positionid') ==6 ){ ?>
                                <a href="<?php echo url("/chapter/re-registration/payment/{$list->id}") ?>"><i class="fa fa-credit-card" aria-hidden="true"></i> </a>
                        <?php }?>
                        </td>
                        <td>
                        <?php if (Session::get('positionid') ==6 ){ ?>
                                <a href="<?php echo url("/chapter/re-registration/notes/{$list->id}") ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <?php }?>
                        </td>
                        <td><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Re-Registration Reminder - MOMS Club of {{ $list->name }}, {{ $list->state_short_name }}&body={{ $mail_message }}"><i class="fa fa-envelope" aria-hidden="true"></i></a></td>
                        <td>{{ $list->state_short_name }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->reg_notes }}</td>
						<td style="
                            @php
                                $due = $list->month_short_name . " " . $list->next_renewal_year;
                                $overdue = (date("Y") * 12 + date("m")) - ($list->next_renewal_year * 12 + $list->start_month_id);
                                if($overdue > 1)
                                    echo "background-color: #FF000050;";
                                elseif($overdue == 1)
                                    echo "background-color: #FFFF0050;";
                            @endphp
                        ">{{ $due }}</td>

						<td>{{ $list->dues_last_paid }}</td>

                    </tr>
                  @endforeach
                  </tbody>
                </table>
				<div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onclick="showPrimary()" /><div><div></div></div>
                    </label>
                  <span> Show all chapters</span>
                </div>
              </div>
              </div>
						 <div class="box-body text-center">


            <?php if(Session::get('positionid') ==6 || Session::get('positionid') == 10 || Session::get('secpositionid') ==10){ ?>
              <a title="Re-registration reminders will be sent to all unpaid chapters in your conference with renewal dates this month." href="{{ route('chapter.reminder') }}"><button class="btn btn-themeBlue margin"   <?php if($checkBoxStatus) echo "disabled";?>>Send Current Month Reminders</button></a>
			<?php }?>
	        <?php if(Session::get('positionid') ==6 || Session::get('positionid') == 10 || Session::get('secpositionid') ==10){ ?>
			 	<a href="{{route('chapter.latereminder')}}" class="btn btn-themeBlue margin" <?php if($checkBoxStatus) echo "disabled";?>>Send One Month Late Notices</a>
			<?php }?>
					<a href="{{ route('export.rereg')}}"><button class="btn btn-themeBlue margin">Export Overdue Chapter List</button></a>

            </div>
            </div>


            </div>
        </div>
      </div>
    </section>
@endsection

@section('customscript')
<script>

  $(document).ready( function () {

  });

  function showPrimary(){
	  	if($("#showPrimary").prop("checked") == true){
			window.location.href = "/mimi/chapter/re-registration?check=yes";
		}
		else{
		  window.location.href = "/mimi/chapter/re-registration";
		}
	}
	function ConfirmSend(){
		var result=confirm("Re-registration reminders will be sent to all unpaid chapters in your conference with renewal dates this month.  Do you wish to continue?");

        if(result){
			return true;
		}

		else{
			return false;
		}

	}

</script>
@endsection
