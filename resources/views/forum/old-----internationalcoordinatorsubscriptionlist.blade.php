@extends('layouts.coordinator_theme')

@section('page_title', 'List Subscriptions')
@section('breadcrumb', 'International Coordinator Subscription List')

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
                                International Coordinator Subscription List
                            </h3>
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
                    <th>Coordinator Name</th>
                    <th>Primary Position</th>
                    <th>Announcements</th>
                    <th>CoordinatorList</th>
                    <th>BoardList</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coordinator/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>

                        <td>
                            @if ($list->region?->short_name != "None" )
                                {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        @if ( $list->on_leave == 1 )
                            <td @if ( $list->on_leave == 1 ) style="background-color: #ffc107;" @endif>ON LEAVE</td>
                        @else
                            <td>{{ $list->displayPosition->long_title }}</td>
                        @endif
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(2, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(3, $Subscriptions) ? 'YES' : 'NO' }}
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
                        <form action="{{ route('forum.coordinatorlist.bulk-subscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Subscribe All to CoordinatorList
                            </button>
                        </form>
                        <form action="{{ route('forum.coordinatorpublidannouncement.bulk-subscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Subscribe All to Public Announcments
                            </button>
                        </form>
                        <form action="{{ route('forum.coordinatorboardlist.bulk-subscribe') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Subscribe All to BoardList
                            </button>
                        </form>
                    </div>
                    <div class="card-body text-center">
                        <form action="{{ route('forum.coordinatorboardlist.bulk-unsubscribe') }}" method="POST" class="d-inline">
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

@
