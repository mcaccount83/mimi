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
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinatr ToDo List Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
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
              <table id="chapterlist" class="table table-bordered table-hover">
				<thead>
			    <tr>
					<th>Region</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
                    <th>Checklist Last Saved</th>
					<th>Re-Reg Reminders</th>
                    <th>Re-Reg Late Notices</th>
                    <th>Re-Reg Payments</th>
					<th>M2M Payments</th>
					<th>Database Backups</th>
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
                    <td  @if($list->todo_send_rereg == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_send_rereg == '1')
                            YES
                        @else
                            NO
                        @endif
                    </td>
                    <td  @if($list->todo_send_late == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_send_late == '1')
                            YES
                        @else
                            NO
                        @endif
                    </td>
                    <td  @if($list->todo_record_rereg == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_record_rereg == '1')
                            YES
                        @else
                            NO
                        @endif
                    </td>
                    <td  @if($list->todo_record_m2m == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_record_m2m == '1')
                            YES
                        @else
                            NO
                        @endif
                    </td>
                    <td  @if($list->todo_export_reports == '1')style="background-color: transparent;"
                        @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->todo_export_reports == '1')
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
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
