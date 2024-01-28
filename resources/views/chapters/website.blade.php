@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Website List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Website List</li>
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
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Chapter Website</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
					<th>Review</th>
					<th>Chapter State</th>
					<th>Chapter Name</th>
                    <th>Status</th>
                    <th>Website</th>
                    <th>Meetup/Google Groups/Etc</th>
                    <th>Web Reviewer Notes</th>

                </tr>
                </thead>
                <tbody>
                @foreach($websiteList as $list)
                  <tr>
						<td><center><a href="<?php echo url("/chapter/website/edit/{$list->id}") ?>"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a></center></td>
                        <td>{{ $list->state }}</td>
                        <td>{{ $list->chapter_name }}</td>
                        <td>
                            @if($list->status == '1')
                                Linked
                            @elseif ($list->status == '2')
                                Add Link Requested
                            @elseif ($list->status == '3')
                                Do No Link
                            @else

                            @endif
                        </td>
                        <td>{{ $list->web }}</td>
                        <td>{{ $list->egroup }}</td>
                        <td>{{ $list->web_notes }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <div class="box-body text-center">

			<button type="button" class="btn btn-themeBlue margin" onclick="window.open('https://momsclub.org/chapters/chapter-links/')"><i class="fa fa-eye fa-fw" aria-hidden="true" ></i>&nbsp; View Chapter Links Page</button>
		</div>

          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
