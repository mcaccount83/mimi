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
                                <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Active Board Members with Inactive User
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
                    <th>User ID</th>
                    <th>Chapter ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($noActiveList as $list)
                  <tr>
                    <td>
                        @if($list->type_id == \App\Enums\UserTypeEnum::COORD)
                            <a href="{{ url("/userreports/editusercoord/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                        @else
                            <a href="{{ url("/userreports/edituserboard/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                        @endif
                    </td>
                        <td>{{ $list->id }}</td>
                        <td>{{ $list->board->chapter_id }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{$list->userStatus->user_status}}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
        </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
@endsection
