@extends('layouts.coordinator_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'Board Members with No Active User')

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
                                    Board Members with No Active User
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_user')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>User ID</th>
                    <th>Chapter ID</th>
                  <th>Name</th>
                <th>Email</th>

                </tr>
                </thead>
                <tbody>
                @foreach($noActiveList as $list)
                  <tr>
                        <td>{{ $list->id }}</td>
                        <td>{{ $list->board->chapter_id }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
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
