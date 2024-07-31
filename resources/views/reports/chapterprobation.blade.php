@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Probation Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Probation Report</li>
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
                  <h3 class="card-title">Report of Chapters on Probation<h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
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
						<td class="text-center align-middle">
                            <a href="<?php echo url("/chapter/edit/{$list->id}") ?>"><i class="fas fa-edit "></i></a></td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
						@switch($list->status)
                                @case(4)
                                    <td style="background-color: #dc3545; color: #ffffff;">On Hold Do Not Refer</td>
                                    @break
                                @case(5)
                                    <td style="background-color: #ffc107;">Probation</td>
                                    @break
                                @case(6)
                                    <td style="background-color: #dc3545; color: #ffffff;">Probation Do Not Refer</td>
                                    @break
                            @endswitch
						<td>{{ $list->notes }}</td>
			        </tr>
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
