@extends('layouts.coordinator_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'Duplicate Board Details')

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
                                    Duplicate Board Details
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_user')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>Email Address</th>
                  <th>ID</th>
                  <th>Chapter ID</th>
                  <th>Position ID</th>
			        <th>First Name</th>
				  <th>Last Name</th>
                  <th>User Type</th>
				  <th>Active User</th>
                </tr>
                </thead>
                <tbody>
                @foreach($boardList as $list)
                        <tr>
                            <td>{{ $list->email }}</td>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->chapter_id }}</td>
                            <td>{{ $list->board_position_id }}</td>
                            <td>{{ $list->first_name }}</td>
                            <td>{{ $list->last_name }}</td>
                            <td>
                                  @IF($list->user->type_id == \App\Enums\UserTypeEnum::COORD)
                                    {{$list->user->userType->user_type}} {{$list->user->coordinator->ActiveStatus->active_status}}
                                @else
                                    {{$list->user->userType->user_type}}
                                @endif
                            </td>
                            <td>{{$list->user->userStatus->user_status}}</tr>
                    @endforeach
                  </tbody>
                </table>
		        </div>
			</div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
