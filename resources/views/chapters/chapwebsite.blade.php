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
                    @include('layouts.dropdown_menus.menu_website')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
					<th>Details</th>
                    <th>Email</th>
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
                    {{-- @php
                        $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($list->id);
                        $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                        $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string
                    @endphp --}}

                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterwebsiteedit/{$list->id}") }}"><i class="fas fa-eye "></i></a></td>
                    <td class="text-center align-middle">
                        <a onclick="showChapterEmailModal('{{ $list->name }}', {{ $list->id }})"><i class="far fa-envelope text-primary"></i></a>

                        {{-- <a href="mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('Website Review | MOMS Club of ' . $list->name . ', ' . $list->state->state_short_name) }}"><i class="far fa-envelope"></i></a></td> --}}
                   </td>
                   <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                    <td>{{ $list->state->state_short_name }}</td>
                    <td>{{ $list->name }}</td>
                    <td @if ( $list->website_status == 3 ) style="background-color: #dc3545; color: #ffffff;"
                        @elseif ( $list->website_status == 2 ) style="background-color: #ffc107;"
                        @endif>
                    {{ $list->webLink->link_status?? null }}</td>
                    <td><a href="{{ url("{$list->website_url}") }}" target="_blank">{{ $list->website_url }}</a></td>
                    <td>{{ $list->egroup }}</td>
                    <td>{{ $list->website_notes }}</td>
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
