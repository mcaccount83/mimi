@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Website/Social Media')
@section('breadcrumb', 'Internatioal Website List')

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
                        International Website List
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
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterwebsiteedit/{$list->id}") }}"><i class="fas fa-eye "></i></a></td>
                    <td class="text-center align-middle">
                        <a onclick="showChapterEmailModal('{{ $list->name }}', {{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}', 'Website Review')"><i class="far fa-envelope text-primary"></i></a>
                  </td>
                   <td>
                        @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                    </td>
                    <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                    <td>{{ $list->name }}</td>
                    <td @if ( $list->website_status == 3 ) style="background-color: #dc3545; color: #ffffff;"
                        @elseif ( $list->website_status == 2 ) style="background-color: #ffc107;"
                        @endif>
                    {{ $list->webLink->link_status?? null }}</td>
                    <td>
                        @if($list->website_url == 'http://' || empty($list->website_url))
                            &nbsp;
                        @else
                            <a href="{{ url($list->website_url) }}" target="_blank">{{ $list->website_url }}</a>
                        @endif
                    </td>                    <td>{{ $list->egroup }}</td>
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
