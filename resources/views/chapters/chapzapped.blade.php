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
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    @if ($ITCondition && ($checkBox5Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
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
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif

            <div class="card-body text-center">
                @if ($assistConferenceCoordinatorCondition)
                    @if ($checkBox5Status)
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('intzapchapter', 'International Zapped Chapter List')"><i class="fas fa-download"></i>&nbsp; Export International Zapped Chapter List</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('zapchapter', 'Zapped Chapter List')"><i class="fas fa-download mr-2" ></i>Export Zapped Chapter List</button>
                    @endif
                @endif
             </div>
          </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
