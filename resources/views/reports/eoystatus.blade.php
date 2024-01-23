@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      EOY Status Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">End of Year Status Report</li>
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
    @if ($message = Session::get('info'))
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
    </div>
@endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of End of Year</h3>
              &nbsp;&nbsp;(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
				<th></th>
				<th>State</th>
                <th>Name</th>
                <th>Board Report Received</th>
                <th>Board Report Activated</th>
				<th>Financial Report Received</th>
				<th>Financial Review Completed</th>
				</tr>
                </thead>
                <tbody>

                @foreach($chapterList as $list)
                  <tr>
                      <td>
                         <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
						<a href="<?php echo url("/chapter/statusview/{$list->id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
                        <?php }?>
                          </td>
				        <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td style="background-color: @if($list->new_board_submitted == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->new_board_submitted == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
				  	    <td style="background-color: @if($list->new_board_active == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->new_board_active == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
						<td style="background-color: @if($list->financial_report_received == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->financial_report_received == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
						<td style="background-color: @if($list->financial_report_complete == '1') transparent; @else #FFC7CE; @endif;">
                            @if($list->financial_report_complete == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
                 </tr>
                  @endforeach
                  </tbody>
                </table>
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                   <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                  </label>
                  <span> Only show chapters I am primary for</span>
                </div>
              </div>
              </div>
              <div class="box-body text-center">
              <a title="EOY Late Notices will be sent to all chapters who have not submitted reports." href="{{ route('report.eoylatereminder') }}"><button class="btn btn-themeBlue margin">Send EOY Late Notices</button></a>
          <a href="{{ route('export.eoystatus')}}"><button class="btn btn-themeBlue margin">Export EOY Status List</button></a>
            </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->
    <!-- /.content -->
@endsection
@section('customscript')
<script>
        function showPrimary() {
    var base_url = '{{ url("/yearreports/eoystatus") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}


</script>
@endsection
