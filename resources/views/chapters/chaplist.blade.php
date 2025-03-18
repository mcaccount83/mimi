@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Active Chapter List')

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
                            Active Chapter List
                        </h3>
                        @include('layouts.dropdown_menus.menu_chapters')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Details</th>
                    <th>Email</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>President</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Primary Coordinator</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    @php
                        $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($list->id);
                        $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                        $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string
                    @endphp

                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                            <td class="text-center align-middle">
                                <a href="mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $list->name . ', ' . $list->state->state_short_name) }}"><i class="far fa-envelope"></i></a></td>
                           </td>
                            <td>
                                @if ($list->region->short_name != "None")
                                    {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                                @else
                                    {{ $list->conference->short_name }}
                                @endif
                            </td>
                            <td>{{ $list->state->state_short_name }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->ein }}</td>
                            <td>{{ $list->president->first_name }} {{ $list->president->last_name }}</td>
                            <td class="email-column">
                                <a href="mailto:{{ $list->president->email }}">{{ $list->president->email }}</a>
                            </td>
                            <td><span class="phone-mask">{{ $list->president->phone }}</span></td>
                            <td>{{ $list->primaryCoordinator?->first_name }} {{ $list->primaryCoordinator?->last_name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="card-body text-center">
                    @if ($regionalCoordinatorCondition)
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.addnew') }}"><i class="fas fa-plus mr-2" ></i>Add New Chapter</a>
                        @if ($checkBoxStatus)
                            <button class="btn bg-gradient-primary" disabled><i class="fas fa-download mr-2" ></i>Export Chapter List</button>
                        @else
                            <button class="btn bg-gradient-primary" onclick="startExport('chapter', 'Chapter List')"><i class="fas fa-download mr-2" ></i>Export Chapter List</button>
                        @endif
                    @endif
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

function showPrimary() {
var base_url = '{{ url("/chapter/chapterlist") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
