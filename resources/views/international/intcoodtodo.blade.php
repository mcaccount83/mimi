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
        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
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
					<th>Conf/Reg</th>
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
                    <td>
                        @if ($list->reg != "None")
                            {{ $list->conf }} / {{ $list->reg }}
                        @else
                            {{ $list->conf }}
                        @endif
                    </td>
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
