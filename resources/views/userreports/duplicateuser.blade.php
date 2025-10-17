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
                  <th>Email Address</th>
                  <th>User Type</th></th>
			        <th>First Name</th>
				  <th>Last Name</th>
                  <th>
                        Status
                  </th>
				  <th>Active User</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($userList as $list)
                  <tr>
                        <td>{{ $list->email }}</td>
                        <td>{{ $list->user_type }}</td>
					<td>{{ $list->first_name }}</td>
						<td>{{ $list->last_name }}</td>
                        <td>
                            @if($list->user_type == 'coordinator')
                                @if($list->coordinator->active_status == '1')
                                    ACTIVE
                                @elseif($list->coordinator->active_status == '2')
                                    PENDING
                                @elseif($list->coordinator->active_status == '3')
                                    NOT APPROVED
                                @elseif($list->coordinator->active_status == '0')
                                    NOT ACTIVE
                                @endif
                            @endif
                            @if($list->user_type == 'board')
                                ACTIVE
                            @endif
                            @if($list->user_type == 'disbanded')
                                DISBANDED
                            @endif
                            @if($list->user_type == 'outgoing')
                                OUTGOING
                            @endif
                            @if($list->user_type == 'incoming')
                                INCOMING
                            @endif
                            @if($list->user_type == 'pending')
                                PENDING
                            @endif
                        </td>
						<td>
							@if($list->is_active=='1')
							    YES
							@else
								NO
							@endif
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
@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});
</script>
@endsection
