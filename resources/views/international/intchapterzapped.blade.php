@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'International Zapped Chapter List')

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
                            International Zapped Chapter List
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
                    <th>Name</th>
                    <th>EIN</th>
                    <th>Disband Date</th>
                    <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
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
                    <td>{{ $list->ein }}</td>
                    <td><span class="date-mask">{{ $list->zap_date }}</span></td>
                    <td>{{ $list->disband_reason }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="card-body text-center">
                <button class="btn bg-gradient-primary" onclick="startExport('intzapchapter', 'International Zapped Chapter List')"><i class="fas fa-download mr-2" ></i>Export Chapter List</button>
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
