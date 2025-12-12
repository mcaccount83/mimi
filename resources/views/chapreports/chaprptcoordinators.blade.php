@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Coordinator Report')

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
                            Chapter Coordinator Report
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_chap')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Details</th>
                  <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
				   <th>CC</th>
				   <th>ACC</th>
				  <th>RC</th>
				  <th>ARC</th>
				  <th>SC</th>
				  <th>AC</th>
				  <th>BS</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chaptersData as $data)
                        @php
                            $chapter = $data['chapter'];
                            $coordinatorArray = $data['coordinatorArray'];
                        @endphp
                        <tr>
                            <td class="text-center align-middle">
                                <a href="{{ url('/chapter/details/' . $chapter->id) }}"><i class="fas fa-eye"></i></a>
                            </td>
                            <td>
                                @if ($chapter->region->short_name != "None")
                                    {{ $chapter->conference->short_name }} / {{ $chapter->region->short_name }}
                                @else
                                    {{ $chapter->conference->short_name }}
                                @endif
                            </td>
                            <td>
                                @if($chapter->state_id < 52)
                                    {{$chapter->state->state_short_name}}
                                @else
                                    {{$chapter->country->short_name}}
                                @endif
                            </td>
                            <td>{{ $chapter->name }}</td>
                            @for ($posRow = 7; $posRow > 0; $posRow--)
                                @php $positionFound = false; @endphp
                                @foreach ($coordinatorArray as $coordinator)
                                    @if ($coordinator && $coordinator->position == $positionCodes[$posRow - 1])
                                        <td>{{ $coordinator->first_name }} {{ $coordinator->last_name }}</td>
                                        @php $positionFound = true; @endphp
                                        @break
                                    @endif
                                @endforeach
                                @if (!$positionFound)
                                    <td></td>
                                @endif
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showChPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                 @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showChAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Chapters</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                <div class="card-body text-center">
                    @if ($checkBox3Status)
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('chaptercoordinator', 'Chapter/Coordinator List')"><i class="fas fa-download mr-2" ></i>Export Chapter/Coordinator List</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3 disabled" onclick="startExport('chaptercoordinator', 'Chapter/Coordinator List')" disabled><i class="fas fa-download mr-2" ></i>Export Chapter/Coordinator List</button>
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
