@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Website/Social Media')
@section('breadcrumb', 'Website List')

@section('content')
   <!-- Main content -->
   <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Website List
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('chapters.chapwebsite') }}">Website List</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptsocialmedia') }}">Social Media List</a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
					<th>Details</th>
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
                    <td class="text-center align-middle"><a href="{{ url("/chapterwebsiteedit/{$list->id}") }}"><i class="fas fa-eye "></i></a></td>
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
                        <td><a href="{{ url("{$list->web}") }}" target="_blank">{{ $list->web }}</a></td>
                        <td>{{ $list->egroup }}</td>
                        <td>{{ $list->web_notes }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <div class="card-body text-center">

			<button type="button" class="btn bg-gradient-primary" onclick="window.open('https://momsclub.org/chapters/chapter-links/')"><i class="fas fa-eye mr-2" ></i>View Chapter Links Page</button>
		</div>

          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection
