@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Boundary Issues Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Boundary Issues Report</li>
      </ol>
    </section>
        	 @if ($message = Session::get('success'))
      <div class="alert alert-success">
         <p>{{ $message }}</p>
      </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Boundary Issues</h3>
            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
					<th>Review</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Reported Issue</th>
				 <th>Boundary on File</th>
				 <th>Resolved</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                @if($list->boundary_issue_notes != '')
                  <tr>
						<td><center><a href="<?php echo url("/chapter/boundaryview/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
						<td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td>{{ $list->boundary_issue_notes}}</td>
                        <td>{{ $list->territory}}</td>
                        <td style="background-color: @if($list->boundary_issue_resolved == '1') transparent; @else #FF000050; @endif;">
                            @if($list->boundary_issue_resolved == '1')
                                YES
                            @else
                                NO
                            @endif
                        </td>
			        </tr>
                  @endif
                  @endforeach
                  </tbody>
                </table>
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>

                  </label>
                  <span> Only show chapters I am Primary Reviewer for</span>
                </div>
              </div>
              </div>
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
    var base_url = '{{ url("/yearreports/boundaryissue") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}


</script>
@endsection
