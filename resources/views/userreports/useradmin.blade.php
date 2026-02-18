@extends('layouts.coordinator_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'User Admins')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <div class="dropdown">
                                <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    User Admins
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
                    <th>Admin</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Email Address</th>
                    <th>User Type</th>
                    <th>Active User</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                @foreach($adminList as $list)
                  <tr>
                    <td>
                        @if($list->type_id == \App\Enums\UserTypeEnum::COORD)
                            <a href="{{ url("/userreports/editusercoord/{$list->id}") }}"><i class="bi bi-eye"></i></a>
                        @else
                            <a href="{{ url("/userreports/edituserboard/{$list->id}") }}"><i class="bi bi-eye"></i></a>
                        @endif
                    </td>
                    <td>{{$list->adminRole->admin_role}}</td>
                    <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                    <td>@if ($list->type_id == \App\Enums\UserTypeEnum::COORD){{{ $list->coordinator->displayPosition->long_title }}}
                        @else {{{ $list->board->position->position }}}
                        @endif</td>
                        <td>{{ $list->email }}</td>
                        <td>
                            @IF($list->type_id == \App\Enums\UserTypeEnum::COORD)
                                {{$list->userType->user_type}} {{$list->coordinator->ActiveStatus->active_status}}
                            @else
                                {{$list->userType->user_type}}
                            @endif
                        </td>
						<td>{{$list->userStatus->user_status}}</td>
                        <td class="text-center align-middle"><i class="bi bi-ban"
                            onclick="showDeleteUserModal({{ $list->id }}, '{{ $list->first_name }}', '{{ $list->last_name }}')"
                            style="cursor: pointer; color: #dc3545;"></i>
                        </td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
          </div>
              <!-- /.card-body -->

              <div class="card-body">
            </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
            </div>
            <!-- /.card-body for buttons -->

         </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection
