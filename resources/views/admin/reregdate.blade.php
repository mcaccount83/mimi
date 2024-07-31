@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Re-Registration Dates Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Re-Registration Dates Report</li>
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
                <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Report of Chapter Re-Registration Dates</h3>
                </div>
                 <!-- /.card-header -->
    <div class="card-body">
        <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_reRegDate" class="table table-bordered table-hover"> --}}
              <thead>
      			    <tr>
          			<th></th>
          			<th>Chapter State</th>
                    <th>Chapter Name</th>
                    <th class="nosort" id="due_sort">Renew Date</th>
                    <th>Last Paid</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($reChapterList as $list)
                  <tr>
                        <td class="text-center align-middle">
                            <a href="<?php echo url("/admin/reregdate/{$list->id}") ?>"><i class="fas fa-edit"></i></a>
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

</script>
@endsection
