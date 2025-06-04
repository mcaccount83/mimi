@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'International Pending Chapter List')

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
                            International Pending Chapter List
                        </h3>
                        <span class="ml-2">New Chapter Applications Waiting for Review</span>
                        @include('layouts.dropdown_menus.menu_chapters_new')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Conf</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>President</th>
                    <th>Email</th>
                    <th>Phone</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/pendingchapterdetailsedit/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                            <td>
                                @if ($list->region->short_name != "None")
                                    {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                                @else
                                    {{ $list->conference->short_name }}
                                @endif
                            </td>
                            <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->pendingPresident->first_name }} {{ $list->pendingPresident->last_name }}</td>
                            <td class="email-column">
                                <a href="mailto:{{ $list->pendingPresident->email }}">{{ $list->pendingPresident->email }}</a>
                            </td>
                            <td><span class="phone-mask">{{ $list->pendingPresident->phone }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">

                </div>
                <div class="card-body text-center">


                    </div>
                </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
  @endsection
<!-- /.content-wrapper -->

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
