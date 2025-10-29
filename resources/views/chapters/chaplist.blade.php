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
                    @if ($ITCondition && ($checkBox5Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                            <td class="text-center align-middle">
                                <a onclick="showChapterEmailModal('{{ $list->name }}', {{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="far fa-envelope text-primary"></i></a>
                           </td>
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
                            <td>{{ $list->ein }}</td>
                            <td>{{ $list->president->first_name }} {{ $list->president->last_name }}</td>
                            <td class="email-column">
                                <a href="mailto:{{ $list->president->email }}">{{ $list->president->email }}</a>
                            </td>
                            <td><span class="phone-mask">{{ $list->president->phone }}</span></td>
                            <td>{{ $list->primaryCoordinator?->first_name }} {{ $list->primaryCoordinator?->last_name }}</td>
                           @if ($ITCondition && ($checkBox5Status ?? '') == 'checked')
                        <td class="text-center align-middle"><i class="fa fa-ban"
                            onclick="showDeleteChapterModal({{ $list->id }}, '{{ $list->name }}', '{{ $list->activeStatus->active_status }}')"
                            style="cursor: pointer; color: #dc3545;"></i>
                        </td>
                    @endif
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
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Chapters</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                <div class="card-body text-center">
                    @if ($coordinatorCondition && $regionalCoordinatorCondition)
                        <a class="btn bg-gradient-primary mb-3" href="{{ route('chapters.chaplistpending') }}"><i class="fas fa-share mr-2" ></i>New Chapters Pending</a>

                        @if ($checkBox3Status)
                            <button class="btn bg-gradient-primary mb-3" onclick="startExport('chapter', 'Chapter List')"><i class="fas fa-download mr-2" ></i>Export Chapter List</button>
                        @elseif ($checkBox5Status)
                            <button class="btn bg-gradient-primary mb-3" onclick="startExport('intchapter', 'International Chapter List')"><i class="fas fa-download"></i>&nbsp; Export International Chapter List</button>
                        @else
                            <button class="btn bg-gradient-primary mb-3" onclick="startExport('chapter', 'Chapter List')" disabled><i class="fas fa-download mr-2" ></i>Export Chapter List</button>
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

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});

function showPrimary() {
    var base_url = '{{ url("/chapter/chapterlist") }}';
    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::PRIMARY_COORDINATOR }}=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAllConf() {
    var base_url = '{{ url("/chapter/chapterlist") }}';
    if ($("#showAllConf").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::CONFERENCE_REGION }}=yes';
    } else {
        window.location.href = base_url;
    }
}

function showAll() {
    var base_url = '{{ url("/chapter/chapterlist") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\ChapterCheckbox::INTERNATIONAL }}=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
