@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Zapped Inquiries Chapter List')

<style>
    .hidden-column {
        display: none !important;
    }
    </style>

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
                            Inquiries Zapped Chapter List
                        </h3>
                        @include('layouts.dropdown_menus.menu_chapters')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              <table id="chapterlist" class="table table-sm table-hover">
              <thead>
			    <tr>
					<th>Details</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Chapter Name</th>
                    <th>Boundaries</th>
                    <th>Inquiries Notes</th>
                    <th>Inquiries Email</th>
                </tr>
                </thead>
                <tbody>
				<?php $row = 0;?>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center "><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                        @if ($list->region?->short_name && $list->region->short_name != "None")
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
                    <td>{{ $list->territory }}</td>
                    <td>{{ $list->inquiries_note }}</td>
                        <td>{{ $list->inquiries_contact }}</td>
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
