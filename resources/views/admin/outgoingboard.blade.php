@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Outgoing Board Members')

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
                                    Outgoing Board Members
                                </h3>
                                @include('layouts.dropdown_menus.menu_admin')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                  <th>Chapter</th>
                  <th>Name</th>
                  <th>Email</th>
                <th>User Type</th>
                </tr>
                </thead>
                <tbody>
                @foreach($OutgoingBoard as $list)
                  <tr>
                    <td>{{ $list->chapter_name }}, {{ $list->chapter_state }}</td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ $list->user_type }}</td>
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
