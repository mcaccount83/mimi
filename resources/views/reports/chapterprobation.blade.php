@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Probation Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Probation Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapter Probation</h3>

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
				 <th>Status/Re-Reg Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
						<td><center><a href="<?php echo url("/chapter/edit/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						<td>
                            @if($list->status == '5')
                                Probation
                            @elseif ($list->status == '4')
                                On Hold Do Not Refer
                            @elseif ($list->status == '6')
                                Probation Do Not Refer
                            @else
                                Probation No Link
                            @endif
                        </td>
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
                  <span> Only show chapters I am Primary For</span>
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
    var base_url = '{{ url("/reports/chapterprobation") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
