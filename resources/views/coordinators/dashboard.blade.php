@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      Welcome, {{ $coordinatorDetails[0]->first_name }} {{ $coordinatorDetails[0]->last_name }}
       <small></small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator Dashboard</li>
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
    <!-- Main content -->
    <form method="POST" action='{{ route("coordinator.updatedashboard",$coordinatorDetails[0]->coordinator_id) }}' id="update-dashboard">
    @csrf
    <section class="content">
		<div class="row">
		<div class="col-md-4">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Monthly To Do List</h3>
				</div>
				<div class="box-body"><div class="todolist">
				@php
                  use Illuminate\Support\Carbon;
                  $currentMonth = Carbon::now()->month;
                @endphp
				<div class="row">
                      <div class="col-sm-6 col-xs-12">
                        <div class="form-inline">
                          <label for="todo_month" style="vertical-align: bottom;">Month:</label><span class="field-required" style="vertical-align: bottom;">*</span>
                          <input type="text" id="todo_month" name="todo_month" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->todo_month }}" maxlength="50" required>
                        </div>
                      </div>
                      <div class="col-sm-6 col-xs-12">
                        <div class="form-group"><br><br>
                        </div>
                      </div>
                    </div>
                    <label><input type="checkbox" name="todo_check_chapters" id="todo_check_chapters" {{ $coordinatorDetails[0]->todo_check_chapters ? 'checked' : '' }}>&nbsp;&nbsp;Check in with your Chapters</label><br/>
                    @if (($currentMonth == 2) || ($currentMonth == 3))
                      <label><input type="checkbox" name="todo_election_faq" id="todo_election_faq" {{ $coordinatorDetails[0]->todo_election_faq ? 'checked' : '' }}>&nbsp;&nbsp;Send Election FAQ to Chapters</label><br/>
                    @endif
                    @if (($currentMonth == 7) || ($currentMonth == 8))
                      <label><input type="checkbox" name="todo_welcome" id="todo_welcome" {{ $coordinatorDetails[0]->todo_welcome ? 'checked' : '' }}>&nbsp;&nbsp;Send Welcome Letter to New Board</label><br/>
                    @endif
                    @if ($currentMonth == 6)
                      <label><input type="checkbox" name="todo_election_due" id="todo_election_due" {{ $coordinatorDetails[0]->todo_election_due ? 'checked' : '' }}>&nbsp;&nbsp;Remind Chapters Board Reports are Due</label><br/>
                    @endif
                    @if ($currentMonth == 7)
                      <label><input type="checkbox" name="todo_financial_due" id="todo_financial_due" {{ $coordinatorDetails[0]->todo_financial_due ? 'checked' : '' }}>&nbsp;&nbsp;Remind Chapters Financial Reports are Due</label><br/>
                    @endif
                    @if ($currentMonth == 7)
                      <label><input type="checkbox" name="todo_990_due" id="todo_990_due" {{ $coordinatorDetails[0]->todo_990_due ? 'checked' : '' }}>&nbsp;&nbsp;Remind Chapters 990Ns are Due</label><br/>
                    @endif
                <?php if (($positionid >= 6 && $positionid <= 7) || $secpositionid == 25){ ?>
                    <label><input type="checkbox" name="todo_send_rereg" id="todo_send_rereg" {{ $coordinatorDetails[0]->todo_send_rereg ? 'checked' : '' }}>&nbsp;&nbsp;Send Re-Registration Reminders</label><br/>
                    <label><input type="checkbox" name="todo_send_late" id="todo_send_late" {{ $coordinatorDetails[0]->todo_send_late ? 'checked' : '' }}>&nbsp;&nbsp;Send Re-Registration Late Notices</label><br/>
                    <label><input type="checkbox" name="todo_record_rereg" id="todo_record_rereg" {{ $coordinatorDetails[0]->todo_record_rereg ? 'checked' : '' }}>&nbsp;&nbsp;Record Re-Registration Payments</label><br/>
                    <label><input type="checkbox" name="todo_record_m2m" id="todo_record_m2m" {{ $coordinatorDetails[0]->todo_record_m2m ? 'checked' : '' }}>&nbsp;&nbsp;Record M2M Donations</label><br/>
                    <label><input type="checkbox" name="todo_export_reports" id="todo_export_reports" {{ $coordinatorDetails[0]->todo_export_reports ? 'checked' : '' }}>&nbsp;&nbsp;Download Conference Reports</label><br/>
                <?php } ?>
                <?php if ($positionid == 7 || $secpositionid == 13) {?>
                    <label><input type="checkbox" name="todo_export_int_reports" id="todo_export_int_reports" {{ $coordinatorDetails[0]->todo_export_int_reports ? 'checked' : '' }}>&nbsp;&nbsp;Download International Reports</label><br/>
                <?php } ?>
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-inline"> <br>
                        </div>
                      </div>
                    <a href="#" onclick="uncheckAll();"><i class="fa fa-times"></i> Uncheck All</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#" id="save-btn"><i class="fa fa-save"></i> Save</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php if ($positionid == 6 || $secpositionid == 25) {?>
                        <a href="{{ url('/reports/coordinatortodo') }}"><i class="fa fa-check-square-o"></i> View To Do List Report</a>
                    <?php } ?>
                    <?php if ($positionid == 7) {?>
                        <a href="{{ url('/reports/intcoordinatortodo') }}"><i class="fa fa-check-square-o"></i> View To Do List Report</a>
                    <?php } ?>
                </div>
				</div>
				</div>

				<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Contact Your Coordinator</h3>
				</div>
				<div class="box-body">
					<p style="font-size:16px">{{ $primaryCoordinatorList[0]->cor_f_name }} {{$primaryCoordinatorList[0]->cor_l_name}}<br/>
					<a href="mailto:{{ $primaryCoordinatorList[0]->cor_email }}">{{ $primaryCoordinatorList[0]->cor_email }}</a><br/>
					{{ $primaryCoordinatorList[0]->cor_phone }}
					</p>
		</div>
		</div>
		</div>

		<div class="col-md-4">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Important EOY Dates</h3>
				</div>
				<div class="box-body">
					<p><ul><li><strong>March</strong> - Nomination Committees are selected and Election Process Begins for chapters</li>
					<li><strong>June</strong> - In Person Elections are held at Genreral Business Meetings</li>
					<li><strong>June 1</strong> - Election Reports are OPEN in MIMI and can be SUBMITTED as soon as elections are complete</li>
					<li><strong>June 15</strong> - Financial Reports are OPEN in MIMI for chapters to view</li>
					<li><strong>June 30</strong> - Elections should be finished and Election Report SUBMITTED in MIMI</li>
					<li><strong>July 1</strong> - Financial Reports are able to be SUBMITTED in MIMI</li>
					<li><strong>July 1</strong> - 990N can be filed with IRS</li>
					<li><strong>July 15</strong> - Financial Reports are DUE</li>
					<li><strong>Dec 30</strong> - Financial Reports can no longer be downloaded in MIMI</li>
					</ul></p>
		</div>
		</div>
		</div>

		<div class="col-md-4">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Resource Links</h3>
				</div>
				<div class="box-body">
				    <p style="font-size:16px"><a href="https://momsclub.org/resources/" target="_blank">Chapter Resources</a>:&nbsp;&nbsp;daytime support</p>
					<p style="font-size:16px"><a href="https://momsclub.org/coordinator-toolkit/" target="_blank">Cordinator Toolkit</a>:&nbsp;&nbsp;Toolkit2021</p>
					<p style="font-size:16px"><a href="https://momsclub.org/elearning/" target="_blank">eLearning Library</a>:&nbsp;&nbsp;Toolkit2021</p>
					<p style="font-size:16px"><a href="https://groups.google.com/u/1/a/momsclub.org/g/2022-23boardlist" target="_blank">Board List Group</a></p>
					<p style="font-size:16px"><a href="https://www.facebook.com/InternationalMOMSClub" target="_blank">Facebook Page</a></p>
		</div>
		</div>
		</div>


		<!--
		<div class="box-body text-center">
			<button type="submit" class="btn btn-themeBlue margin">Save</button>
		</div> -->
        </div>
    </section>
</form>

@endsection
@section('customscript')
<script>

function uncheckAll() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false;
        }
    }


document.getElementById("save-btn").addEventListener("click", function() {
  // Get the value of the todo_month input
  var todoMonth = document.getElementById("todo_month").value;

  // Check if the todo_month input is empty
  if (todoMonth.trim() === "") {
    // Show an error message to the user
    alert("The Month field is required.");
    return false;
  }

  // Submit the form if the input is valid
  document.getElementById("update-dashboard").submit();
});




</script>
@endsection
