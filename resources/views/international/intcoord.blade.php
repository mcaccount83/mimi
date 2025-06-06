@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinators')
@section('breadcrumb', 'International Active Coordinator List')

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
                        International Active Coordinator List
                    </h3>
                    @include('layouts.dropdown_menus.menu_coordinators')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="coordinatorlist" class="table table-sm table-hover">
              <thead>
			    <tr>
			      <th>Details</th>
			      <th>Conf/Reg</th>
                  <th>Coordinator Name</th>
                  <th>Display Position</th>
                  {{-- <th>MIMI Position</th> --}}
                  <th>Secondary Positions</th>
                  <th>Email</th>
                  <th>Reports To</th>
                </tr>
                </thead>
                <tbody>
                @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coorddetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                            @if ($list->region->short_name != "None")
                            {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                        @else
                            {{ $list->conference->short_name }}
                        @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        @if ( $list->on_leave == 1 )
                            <td @if ( $list->on_leave == 1 ) style="background-color: #ffc107;" @endif>ON LEAVE</td><td></td>
                        @else
                            <td>{{ $list->displayPosition->long_title }}</td>
                            {{-- <td>{{ $list->mimiPosition->short_title }}</td> --}}
                            <td>
                                @forelse($list->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                @endforelse
                            </td>
                        @endif
                        <td><a href="mailto:{{ $list->email }}">{{ $list->email }}</a></td>
                        <td>{{ $list->reportsTo?->first_name }} {{ $list->reportsTo?->last_name }}</td>
                        @endforeach
                  </tbody>
                </table>
             </div>
             <!-- /.card-body -->
             <div class="card-body text-center">
                <button class="btn bg-gradient-primary" onclick="startExport('intcoordinator', 'International Coordinator List')"><i class="fas fa-download mr-2" ></i>Export Coordinator List</button>
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


