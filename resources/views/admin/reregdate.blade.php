@extends('layouts.coordinator_theme')

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
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Re-Reg Dates
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Re-Reg Dates</li>
      </ol>
    </section>
    <!-- Main content -->

    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Chapters Re-Reg Dates</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                </div>
              </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">

              <table id="chapterlist_reRegDate" class="table table-bordered table-hover">
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
                        <td><center><a href="<?php echo url("/admin/reregdate/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                        <td>{{ $list->state_short_name }}</td>
                        <td>{{ $list->name }}</td>
						<td style="
                            @php
                                $due = $list->month_short_name . " " . $list->next_renewal_year;
                                $overdue = (date("Y") * 12 + date("m")) - ($list->next_renewal_year * 12 + $list->start_month_id);
                                if($overdue > 1)
                                    echo "background-color: #FFC7CE;";
                                elseif($overdue == 1)
                                    echo "background-color: #FFEB9C;";
                            @endphp
                        ">{{ $due }}</td>

						<td>{{ $list->dues_last_paid }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
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
