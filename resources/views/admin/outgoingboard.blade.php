@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Admin Tasks/Reports</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Outgoing Board Members</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

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
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="/admin/reregdate">Re-Registration Renewal Dates</a>
                                      <a class="dropdown-item" href="/admin/eoy">End of Year Procedures</a>
                                      <a class="dropdown-item" href="/adminreports/duplicateuser">Duplicate Users</a>
                                      <a class="dropdown-item" href="/adminreports/duplicateboardid">Duplicate Board Details</a>
                                      <a class="dropdown-item" href="/adminreports/nopresident">Chapters with No President</a>
                                      <a class="dropdown-item" href="/adminreports/outgoingboard">Outgoing Board Members</a>
                                      <a class="dropdown-item" href="/admin/googledrive">Google Drive Settings</a>
                                      <a class="dropdown-item" href="/admin/jobs">Outgoing Mail Queue</a>
                                      <a class="dropdown-item" href="/admin/sentemails" target="_blank">Sent Mail</a>
                                      <a class="dropdown-item" href="/admin/logs" target="_blank">System Error Logs</a>
                                  </div>
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
        // Check if the item's href matches the current path
        if (item.getAttribute("href") === currentPath) {
            item.classList.add("active");
        }
    });
});

</script>
@endsection
