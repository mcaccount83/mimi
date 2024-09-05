@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Large Chapter Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Large Chapter Report</li>
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
                  <h3 class="card-title">Report of Large Chapters</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_large" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
					<th>Details</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Chapter Size</th>
				 <th>Last Reported</th>

                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
						<td class="text-center align-middle">
                            <a href="<?php echo url("/chapter/edit/{$list->id}") ?>"><i class="fas fa-edit"></i></a></td>
						<td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->members_paid_for }}</td>
						<td><span class="date-mask">{{ $list->dues_last_paid }}</span></td>
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
    <!-- /.content -->
@endsection
@section('customscript')
<script>
function showPrimary() {
    var base_url = '{{ url("/reports/chapterlarge") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
