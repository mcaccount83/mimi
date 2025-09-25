@extends('layouts.coordinator_theme')

@section('page_title', 'List Subscriptions')
@section('breadcrumb', 'International Chapter Subscription List')

@section('content')

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

    @if ($message = Session::get('info'))
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
    </div>
@endif

 <!-- Main content -->
 <section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                International Chapter Subscription List
                            </h3>
                            <span class="ml-2">PA=Public Announcements | BL=BoardList</span>
                            @include('layouts.dropdown_menus.menu_forum')
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
                    <th>Chapter</th>
                    <th>President<br>
                        PA|BL</th>
                    <th>AVP<br>
                        PA|BL</th>
                    <th>MVP<br>
                        PA|BL</th>
                    <th>Secretary<br>
                        PA|BL</th>
                    <th>Treasurer<br>
                        PA|BL</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>

                        <td>
                            @if ($list->region?->short_name != "None" )
                                {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
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
                        <td>
                            @php
                                $presSubscriptions = $list->president?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(1, $presSubscriptions) ? 'YES' : 'NO' }} |
                            {{ in_array(3, $presSubscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @if($list->avp)
                                @php
                                    $avpSubscriptions = $list->avp?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $avpSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $avpSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->mvp)
                                @php
                                    $mvpSubscriptions = $list->mvp?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $mvpSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $mvpSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->secretary)
                                @php
                                    $secSubscriptions = $list->secretary?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $secSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $secSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->treasurer)
                                @php
                                    $tresSubscriptions = $list->treasurer?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $tresSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $tresSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>
            </div>

            <div class="col-md-12">
                    @if ($ITCondition || $listAdminCondition)
                    <div class="card-body text-center">
                        <form action="{{ route('forum.boardpublcannouncements.bulk-subscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Subscribe All to Public Announcments
                            </button>
                        </form>
                        <form action="{{ route('forum.boardboardlist.bulk-subscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Subscribe All to BoardList
                            </button>
                        </form>
                    </div>
                    <div class="card-body text-center">
                        <form action="{{ route('forum.boardpublcannouncements.bulk-unsubscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Unsubscribe All from Public Announcments
                            </button>
                        </form>
                        <form action="{{ route('forum.boardboardlist.bulk-unsubscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Unsubscribe All from BoardList
                            </button>
                        </form>
                    </div>
                    @endif
            </div>

        </div>
      </div>
    </div>
    </section>
@endsection

@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});
</script>
@endsection
