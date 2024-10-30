@extends('layouts.coordinator_theme')
<style>
    .hidden-column {
        display: none !important;
    }
    </style>

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapters</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Zapped Inquiries Chapter List</li>
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
                            Inquiries Zapped Chapter List
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if ($coordinatorCondition)
                                <a class="dropdown-item" href="{{ route('chapters.chaplist') }}">Active Chapter List</a>
                                <a class="dropdown-item" href="{{ route('chapters.chapzapped') }}">Zapped Chapter List</a>
                            @endif
                            @if (($inquiriesCondition) || ($regionalCoordinatorCondition) || ($adminReportCondition))
                                <a class="dropdown-item" href="{{ route('chapters.chapinquiries') }}">Inquiries Active Chapter List</a>
                                <a class="dropdown-item" href="{{ route('chapters.chapinquirieszapped') }}">Inquiries Zapped Chapter List</a>
                            @endif
                            @if (($einCondition) || ($adminReportCondition))
                                <a class="dropdown-item" href="{{ route('international.intchapter') }}">International Active Chapter List</a>
                                <a class="dropdown-item" href="{{ route('international.intchapterzapped') }}">International Zapped Chapter List</a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover">
              <thead>
			    <tr>
					<th>Details</th>
                    <th>State</th>
                    <th>Chapter Name</th>
                    <th>Boundaries</th>
                    <th>Inquiries Notes</th>
                    <th>Inquiries Email</th>
                </tr>
                </thead>
                <tbody>
				<?php $row = 0;?>
                @foreach($inquiriesList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td>{{ $list->state }}</td>
                        <td>{{ $list->chapter_name }}</td>
                        <td>{{ $list->terry }}</td>
                        <td>{{ $list->inq_note }}</td>
                        <td>{{ $list->inq_con }}</td>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="card-body text-center">

            </div>
        </div>

          </div>

          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

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
