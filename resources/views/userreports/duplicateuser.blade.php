@extends('layouts.coordinator_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'Duplicate Users')

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
                                    Duplicate Users
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_user')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>Edit User</th>
                  <th>Email Address</th>
			        <th>First Name</th>
				  <th>Last Name</th>
                  <th>User Type</th>
				  <th>Active User</th>
                <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($userList as $list)
                  <tr>
                    <td>
                        @if($list->type_id == \App\Enums\UserTypeEnum::COORD)
                            <a href="{{ url("/userreports/editusercoord/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                        @else
                            <a href="{{ url("/userreports/edituserboard/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                        @endif
                    </td>
                    <td>{{ $list->email }}</td>
					<td>{{ $list->first_name }}</td>
						<td>{{ $list->last_name }}</td>
                        <td>
                            @IF($list->type_id == \App\Enums\UserTypeEnum::COORD)
                                {{$list->userType->user_type}} {{$list->coordinator->ActiveStatus->active_status}}
                            @else
                                {{$list->userType->user_type}}
                            @endif
                        </td>
						<td>{{$list->userStatus->user_status}}</td>
                         <td class="text-center align-middle"><i class="fa fa-ban"
                            onclick="showDeleteUserModal({{ $list->id }}, '{{ $list->first_name }}', '{{ $list->last_name }}')"
                            style="cursor: pointer; color: #dc3545;"></i>
                        </td>
			        </tr>
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
