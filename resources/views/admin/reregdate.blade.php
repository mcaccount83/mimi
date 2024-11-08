@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Re-Registration Renewal Dates')

@section('content')

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
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Re-Registration Renewal Dates
                            </h3>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ route('admin.reregdate') }}">Re-Registration Renewal Dates</a>
                                  <a class="dropdown-item" href="{{ route('admin.eoy') }}">End of Year Procedures</a>
                                  <a class="dropdown-item" href="{{ route('admin.duplicateuser') }}">Duplicate Users</a>
                                  <a class="dropdown-item" href="{{ route('admin.duplicateboardid') }}">Duplicate Board Details</a>
                                  <a class="dropdown-item" href="{{ route('admin.nopresident') }}">Chapters with No President</a>
                                  <a class="dropdown-item" href="{{ route('admin.outgoingboard') }}">Outgoing Board Members</a>
                                  <a class="dropdown-item" href="{{ route('admin.googledrive') }}">Google Drive Settings</a>
                                  <a class="dropdown-item" href="{{ route('queue-monitor::index') }}">Outgoing Mail Queue</a>
                                  <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}" target="_blank">Sent Mail</a>
                                  <a class="dropdown-item" href="{{ route('logs') }}" target="_blank">System Error Logs</a>
                              </div>
                        </div>
                    </div>
                 <!-- /.card-header -->
    <div class="card-body">
        <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_reRegDate" class="table table-bordered table-hover"> --}}
              <thead>
      			    <tr>
          			<th>Details</th>
                    <th>Conf/Reg</th>
          			<th>State</th>
                    <th>Name</th>
                    <th class="nosort" id="due_sort">Renewal Date</th>
                    <th>Last Paid</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($reChapterList as $list)
                  <tr>
                        <td class="text-center align-middle"><a href="{{ url("/admin/reregdate/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td>
                            @if ($list->reg != "None")
                                {{ $list->conf }} / {{ $list->reg }}
                            @else
                                {{ $list->conf }}
                            @endif
                        </td>
                        <td>{{ $list->state_short_name }}</td>
                        <td>{{ $list->name }}</td>
						<td style="
                                    @php
                                        $due = $list->month_short_name . ' ' . $list->next_renewal_year;
                                        $overdue = (date('Y') * 12 + date('m')) - ($list->next_renewal_year * 12 + $list->start_month_id);
                                        if ($overdue > 1) {
                                            echo 'background-color: #dc3545; color: #ffffff;';
                                        } elseif ($overdue == 1) {
                                            echo 'background-color: #ffc107;';
                                        }
                                    @endphp
                                " data-sort="{{ $list->next_renewal_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                                {{ $due }}
                        </td>
						<td><span class="date-mask">{{ $list->dues_last_paid }}</span></td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            </div>
        </div>
      </div>
    </div>
    </section>
@endsection

@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});
</script>
@endsection
