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
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="{{ route('admin.reregdate') }}">Re-Registration Renewal Dates</a>
                                      <a class="dropdown-item" href="{{ route('admin.eoy') }}">End of Year Procedures</a>
                                      <a class="dropdown-item" href="{{ route('admin.duplicateuser') }}">Duplicate Users</a>
                                      <a class="dropdown-item" href="{{ route('admin.duplicateboardid') }}">Duplicate Board Details</a>
                                      <a class="dropdown-item" href="{{ route('admin.nopresident') }}">Chapters with No President</a>
                                      <a class="dropdown-item" href="{{ route('admin.outgoingboard') }}">Outgoing Board Members</a>
                                      <a class="dropdown-item" href="{{ route('admin.googledrive') }}">Google Drive Settings</a>
                                      <a class="dropdown-item" href="{{ route('queue-monitor::index') }}">Outgoing Mail Queue</a>
                                      <a class="dropdown-item" href="{{ url(config('sentemails.routepath')) }}" target="_blank">Sent Mail</a>
                                      <a class="dropdown-item" href="{{ route('logs') }}" target="_blank">System Error Logs</a>
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
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

</script>
@endsection
