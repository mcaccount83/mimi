@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Coordinator ToDo List Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator ToDo List Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Coordinator ToDo List</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="coordinatorlist" class="table table-bordered table-hover">
				<thead>
				@php
                  use Illuminate\Support\Carbon;

                  $currentMonth = Carbon::now()->month;
                @endphp
			    <tr>
					<th>Region</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Month</th>
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
                    <td>{{ $list->todo_month }}</td>
                    <td style="background-color: @if($list->todo_check_chapters == '1') transparent; @else #FFC7CE; @endif;">
                        @if($list->todo_check_chapters == '1')
                            YES
                        @else
                            NO
                        @endif
                    </td>
                    @if (($currentMonth == 2) || ($currentMonth == 3))
                    <td style="background-color: @if($list->todo_election_faq == '1') transparent; @else #FFC7CE; @endif;">
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
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
