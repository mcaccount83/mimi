@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Boundary Issues Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Boundary Issues Report</li>
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
                  <h3 class="card-title">Report of Boundary Issues</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
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
						<td class="text-center align-middle">
                            <a href="<?php echo url("/chapter/boundaryview/{$list->id}") ?>"><i class="fas fa-edit"></i></a></td>
						<td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        <td>{{ $list->boundary_issue_notes}}</td>
                        <td>{{ $list->territory}}</td>
                        <td @if($list->boundary_issue_resolved == '1')style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
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
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="card-body text-center">&nbsp;</div>
              </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
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
