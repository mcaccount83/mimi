@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      BoardList
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">BoardList</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">BoardList</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead>
			    <tr>
			      <th>Conf</th>
				  <th>State</th>
                  <th>Name</th>
                  <th>Chapter Email</th>
                  <th>Prez Email</th>
                  <th>AVP Email</th>
              <th>MVP Email</th>
              <th>Sec Email</th>
              <th>Treas Email</th>
                </tr>
                </thead>
                <tbody>
                @foreach($activeChapterList as $list)
                  <tr>
					<td>{{ $list->conference }}</td>
					<td>{{ $list->state }}</td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->chapter_email }}</td>
                    <td>{{ $list->pre_email }}</td>
                    <td>{{ $list->avp_email }}</td>
                    <td>{{ $list->mvp_email }}</td>
                    <td>{{ $list->sec_email }}</td>
                    <td>{{ $list->trs_email }}</td>
			      </tr>
                  @endforeach
                  </tbody>
                </table>
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

</script>
@endsection
