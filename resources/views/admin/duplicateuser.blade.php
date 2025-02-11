@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
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
                                @include('layouts.dropdown_menus.menu_admin')
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
				  <th>Active</th>
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

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});
</script>
@endsection
