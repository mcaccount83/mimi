@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Status Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Status Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapter Status</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
				<th>Details</th>
				<th>State</th>
                <th>Name</th>
                <th>Status</th>
                <th>Status Notes</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr>
                            <td><center><a href="{{ url("/chapter/edit/{$list->id}") }}"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                            <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            @switch($list->status)
                                @case(1)
                                    <td>OK</td>
                                    @break
                                @case(4)
                                    <td>On Hold Do Not Refer</td>
                                    @break
                                @case(5)
                                    <td>Probation</td>
                                    @break
                                @case(6)
                                    <td>Probation Do Not Refer</td>
                                    @break
                            @endswitch
                            <td>{{ $list->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                  </label>
                  <span> Only show chapters 'Not Ok'</span>
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
    var base_url = '{{ url("/reports/chapterstatus") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
