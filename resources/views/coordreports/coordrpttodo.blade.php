@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">

        @php
use Illuminate\Support\Carbon;
  $currentMonthName = Carbon::now()->format('F'); // Full month name (e.g., "August")
  $currentMonth = Carbon::now()->month; // Numeric month (e.g., 8)
@endphp

      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinatr ToDo List Report<small>&nbsp;({{ $currentMonthName }})</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinatr ToDo List Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

   <!-- Main content -->
   <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Report of Coordinator ToDo List</h3>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
					<th>Region</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
                    <th>Checklist Last Saved</th>
					<th>Check with Chapters</th>
                    @if (($currentMonth == 2) || ($currentMonth == 3))
                      <th>Election FAQ Sent</th>
                    @elseif ($currentMonth == 6)
                      <th>Election Report Reminder</th>
                    @elseif (($currentMonth == 7) || ($currentMonth == 8))
                      <th>Welcome Letter</th>
                    @else
                      <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    @endif
                    @if ($currentMonth == 7))
                      <th>990 Report Reminder</th>
                    @else
                      <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    @endif
					@if ($currentMonth == 7))
                      <th>Financial Report Reminder</th>
                    @else
                      <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($coordinatorList as $list)
                  <tr>
                    <td>{{ $list->reg }}</td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
					<td>{{ $list->position }}</td>
                	<td><span class="date-mask">{{ $list->dashboard_updated }}</span></td>
                    <td  @if($list->todo_check_chapters == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_check_chapters == '1')
                            YES
                        @else
                            NO
                        @endif
                    </td>
                    @if (($currentMonth == 2) || ($currentMonth == 3))
                    <td  @if($list->todo_election_faq == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_election_faq == '1')
                            YES
                        @else
                            NO
                        @endif
                    @else
                      <td></td>
                    @endif
                    <td></td>
                    <td></td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            </div>
            <div class="card-body text-center">&nbsp;</div>
        </div>
    </div>
      </div>
    </section>

    <!-- /.content -->

@endsection
