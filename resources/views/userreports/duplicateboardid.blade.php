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
                                @if($list->user->type_id == \App\Enums\UserTypeEnum::COORD)
                                    {{ match($list->user->coordinator->active_status) {
                                        \App\Enums\ActiveStatusEnum::ACTIVE => 'Coordinator Active',
                                        \App\Enums\ActiveStatusEnum::ZAPPED => 'Coordinator Retired',
                                        \App\Enums\ActiveStatusEnum::PENDING => 'Coordinator Pending',
                                        \App\Enums\ActiveStatusEnum::NOTAPPROVED => 'Coordinator Not Approved',
                                        default => ''
                                    } }}
                                @else
                                    {{ match($list->user->type_id) {
                                        \App\Enums\UserTypeEnum::BOARD => 'Board Active',
                                        \App\Enums\UserTypeEnum::DISBANDED => 'Board Disbanded',
                                        \App\Enums\UserTypeEnum::OUTGOING => 'Board Outgoing',
                                        \App\Enums\UserTypeEnum::INCOMING => 'Board Incoming',
                                        \App\Enums\UserTypeEnum::PENDING => 'Board Pending',
                                        default => ''
                                    } }}
                                @endif
                            </td>
                            <td>
                                {{ match($list->user->is_active) {
                                    \App\Enums\UserStatusEnum::ACTIVE => 'YES',
                                    \App\Enums\UserStatusEnum::INACTIVE => 'NO',
                                    default => ''
                                } }}
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
