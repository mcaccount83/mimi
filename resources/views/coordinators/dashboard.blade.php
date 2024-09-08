@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Welcome, {{ $coordinatorDetails[0]->first_name }} {{ $coordinatorDetails[0]->last_name }}</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action="{{ route('coordinator.updatedashboard', $coordinatorDetails[0]->coordinator_id) }}" id="update-dashboard">
        @csrf

    <section class="content">
        <div class="container-fluid">
            <div class="row">

		<div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Monthly ToDo List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
			<div class="todolist">
				@php
                    use Illuminate\Support\Carbon;
                    $currentMonthName = Carbon::now()->format('F'); // Full month name (e.g., "August")
                    $currentMonth = Carbon::now()->month; // Numeric month (e.g., 8)
                @endphp

                      <div class="col-12">
                          <label >Current Month:</label>&nbsp;&nbsp;{{ $currentMonthName }}
                      </div>
                      <div class="col-12">
                        <label >Checklist Last Updated:</label>&nbsp;&nbsp;<span class="date-mask">{{ $coordinatorDetails[0]->dashboard_updated }}</span>
                    </div>
                      <div class="col-12">
                        <div class="col-12">
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
                </div>
            </div>

                    <a href="#" onclick="uncheckAll(event)"><i class="fas fa-times"></i> Uncheck All</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="javascript:void(0);" onclick="document.getElementById('update-dashboard').submit();"><i class="fas fa-save"></i> Save</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php if ($positionid == 6 || $secpositionid == 25) {?>
                        <a href="{{ url('/reports/coordinatortodo') }}"><i class="fas fa-check-square"></i> View To Do List Report</a>
                    <?php } ?>
                    <?php if ($positionid == 7) {?>
                        <a href="{{ url('/reports/intcoordinatortodo') }}"><i class="fas fa-check-square"></i> View To Do List Report</a>
                    <?php } ?>
                </div>
				</div>
				</div>

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Contact Your Coordinator</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
					<p style="font-size:16px">{{ $primaryCoordinatorList[0]->cor_f_name }} {{$primaryCoordinatorList[0]->cor_l_name}}<br/>
					<a href="mailto:{{ $primaryCoordinatorList[0]->cor_email }}">{{ $primaryCoordinatorList[0]->cor_email }}</a><br/>
					{{ $primaryCoordinatorList[0]->cor_phone }}
					</p>
		</div>
		</div>
		</div>

		<div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Important EOY Dates</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
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
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Resource Links</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
				    <p><ul><li><a href="{{ url('/admin/resources') }}">Chapter Resources</a></li>
                        <li><a href="{{ url('/admin/toolkit') }}" >Cordinator Toolkit</a></li>
                        <li><a href="https://momsclub.org/elearning/" target="_blank">eLearning Library</a><small>&nbsp;(Toolkit2021)</small></li>
                        <li><a href="https://groups.google.com/u/1/a/momsclub.org/g/2024-25boardlist" target="_blank">Board List Group</a></li>
                        <li><a href="https://www.facebook.com/InternationalMOMSClub" target="_blank">Facebook Page</a></li>
                    </ul></p>
		    </div>
		    </div>
		</div>

    </div>
    </section>
</form>

@endsection
@section('customscript')
<script>

function uncheckAll(event) {
    event.preventDefault(); // Prevent form submission if necessary

    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });
}
document.getElementById("save-btn").addEventListener("click", function() {
  // Get the value of the todo_month input
//   var todoMonth = document.getElementById("todo_month").value;

//   // Check if the todo_month input is empty
//   if (todoMonth.trim() === "") {
//     // Show an error message to the user
//     alert("The Month field is required.");
//     return false;
//   }

  // Submit the form if the input is valid
  document.getElementById("update-dashboard").submit();
});

</script>
@endsection
