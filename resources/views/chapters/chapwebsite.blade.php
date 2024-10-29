@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Website List</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Website List</li>
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
              <h3 class="card-title">List of Chapters</h3>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
					<th>Review</th>
                    <th>Conf/Reg</th>
					<th>State</th>
					<th>Name</th>
                    <th>Status</th>
                    <th>Website</th>
                    <th>Online Group/App</th>
                    <th>Web Reviewer Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($websiteList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/websiteview/{$list->id}") }}"><i class="fas fa-edit "></i></a></td>
                        <td>
                            @if ($list->reg != "None")
                                {{ $list->conf }} / {{ $list->reg }}
                            @else
                                {{ $list->conf }}
                            @endif
                        </td>
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
            <div class="card-body text-center">

			<button type="button" class="btn bg-gradient-primary" onclick="window.open('https://momsclub.org/chapters/chapter-links/')"><i class="fas fa-eye" ></i>&nbsp;&nbsp;&nbsp;View Chapter Links Page</button>
		</div>

          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
