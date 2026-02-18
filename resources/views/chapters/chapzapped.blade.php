@extends('layouts.coordinator_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Zapped Chapter List')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Zapped Chapter List
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
                    @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="bi bi-eye-fill"></i></a></td>
                    <td>
                        @if ($list->state->conference_id > 0)
                            {{ $list->state->conference->short_name }} / {{ $list->state->region->short_name }}
                        @else
                            {{ $list->state->conference->short_name }}
                        @endif
                    </td>
                    <td>
                        @if($list->state_id < 52)
                            {{$list->state->state_short_name}}
                        @else
                            {{$list->state->country?->short_name}}
                        @endif
                    </td>
                    <td>{{ $list->name }}</td>
                    <td>{{ $list->ein }}</td>
                    <td><span class="date-mask">{{ $list->zap_date }}</span></td>
                    <td>{{ $list->disband_reason }}</td>
                   @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
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

            <div class="card-body">
             <!-- /.card-body -->
                @if ($ITCondition || $einCondition)
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters (Export Available)</label>
                        </div>
                    </div>
                @endif
                  </div>
            <!-- /.card-body for checkboxes -->

            <div class="card-body text-center mt-3">
                @if ($assistConferenceCoordinatorCondition)
                    @if ($checkBox51Status)
                        <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('intzapchapter', 'International Zapped Chapter List')"><i class="bi bi-download me-2"></i>Export International Zapped Chapter List</button>
                    {{-- @else
                        <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('zapchapter', 'Zapped Chapter List')"><i class="bi bi-download me-2"></i>Export Zapped Chapter List</button> --}}
                    @endif
                @endif
             </div>
            <!-- /.card-body for buttons -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection
