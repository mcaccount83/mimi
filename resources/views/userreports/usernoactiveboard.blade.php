@extends('layouts.coordinator_theme')

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
                    <th>Edit User</th>
                    <th>User ID</th>
                    {{-- <th>Chapter ID</th> --}}
                    <th>Name</th>
                    <th>Email</th>
                    <th>Table</th>
                    <th>User Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($bdNoChapterList as $list)
                  <tr>
                    <td>
                            <a href="{{ url("/userreports/edituser/{$list->id}") }}"><i class="bi bi-eye"></i></a>
                    </td>
                        <td>{{ $list->id }}</td>
                        {{-- <td>{{ $list->board->chapter_id }}</td> --}}
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ ucfirst($list->incorrect_table) }}</td>
                        <td>{{$list->userStatus->user_status}}</td>
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
