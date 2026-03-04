@extends('layouts.mimi_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'Active Board Members with Inactive User')

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
                                    Active User with No Active Board Member
                                </h3>
                                <span class="ms-2">Update User Active Status and/or Type</span>
                                @include('layouts.dropdown_menus.menu_reports_user')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>User<br>Details</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Status</th>
                    <th>Missing/Found</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($userList as $list)
                  <tr>
                    <td>

                            <a href="{{ url("/userreports/edituser/{$list->id}") }}"><i class="bi bi-person-fill-gear"></i></a>
                    </td>
                        <td>{{ $list->id }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ match($list->type_id) {
                                \App\Enums\UserTypeEnum::COORD     => 'Coordinator',
                                \App\Enums\UserTypeEnum::BOARD     => 'Board',
                                \App\Enums\UserTypeEnum::DISBANDED => 'Board Disbanded',
                                \App\Enums\UserTypeEnum::OUTGOING  => 'Board Outgoing',
                                \App\Enums\UserTypeEnum::PENDING   => 'Board Pending',
                                default => 'unknown'
                            } }}</td>
                        <td>{{ $list->userStatus->user_status }}</td>
                        <td>
                            @if($list->missing_from)
                               {{ $list->missing_from }}/
                            @else
                                None/
                            @endif
                            @if(!empty($list->wrong_tables))
                                @foreach($list->wrong_tables as $table)
                                    {{ $table }}
                                @endforeach
                            @else
                                None
                            @endif
                        </td>
                        <td>
                            @if($list->missing_from !== null && empty($list->wrong_tables))
                                <span class="badge bg-danger fs-7">Make user inactive</span>

                            @elseif(!empty($list->wrong_tables))
                                @foreach($list->wrong_tables as $table)
                                    <span class="badge bg-warning text-dark fs-7">Change user type to match {{ $table }}</span>
                                @endforeach

                            @else
                                <span class="badge bg-success fs-7">No action needed</span>
                            @endif
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
