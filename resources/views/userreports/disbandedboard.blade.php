@extends('layouts.coordinator_theme')

@section('page_title', 'User Reports')
@section('breadcrumb', 'Disbanded Board Members')

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
                                    Disbanded Board Members
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_user')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Chapter</th>
                    <th>Board Member</th>
                  <th>Email</th>
                <th>User Type</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($disbandedList as $list)
                    <tr>
                        <td>
                            @if ($list->boardDisbanded?->chapters->region->short_name != "None")
                                {{ $list->boardDisbanded?->chapters->conference->short_name }} / {{ $list->boardDisbanded?->chapters->region->short_name }}
                            @else
                                {{ $list->boardDisbanded?->chapters->conference->short_name }}
                            @endif
                        </td>
                        <td>
                           @if($list->boardDisbanded?->state_id < 52)
                                {{$list->boardDisbanded?->state->state_short_name}}
                            @else
                                {{$list->boardDisbanded?->country->short_name}}
                            @endif
                        </td>
                        <td>{{ $list->boardDisbanded?->chapters->name }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td class="email-column">
                            <a href="mailto:{{ $list->email }}">{{ $list->email }}</a>
                        </td>
                        <td>
                            @if($list->type_id == \App\Enums\UserTypeEnum::COORD)
                                {{ match($list->coordinator->active_status) {
                                    \App\Enums\ActiveStatusEnum::ACTIVE => 'Coordinator Active',
                                    \App\Enums\ActiveStatusEnum::ZAPPED => 'Coordinator Retired',
                                    \App\Enums\ActiveStatusEnum::PENDING => 'Coordinator Pending',
                                    \App\Enums\ActiveStatusEnum::NOTAPPROVED => 'Coordinator Not Approved',
                                    default => ''
                                } }}
                            @else
                            {{ match($list->type_id) {
                                \App\Enums\UserTypeEnum::BOARD => 'Board Active',
                                \App\Enums\UserTypeEnum::DISBANDED => 'Board Disbanded',
                                \App\Enums\UserTypeEnum::OUTGOING => 'Board Outgoing',
                                \App\Enums\UserTypeEnum::INCOMING => 'Board Incoming',
                                \App\Enums\UserTypeEnum::PENDING => 'Board Pending',
                                default => ''
                            } }}
                            @endif
                            </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <div class="card-body text-center">
				@if ($regionalCoordinatorCondition)
                    @if ($countList > '0')
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="showUserInactiveModel()"><i class="fas fa-users-slash mr-2"></i>Make all Users Inactive</button>
                    @else
                        <button type="button" class="btn bg-gradient-primary mb-3 disabled" disabled><i class="fas fa-users-slash mr-2"></i>Make all Users Inactive</button>
                    @endif
				@endif
             </div>
        </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
