@extends('layouts.mimi_theme')

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
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
				  <th>Chapter<br>Details</th>
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
                                <a href="{{ url('/chapter/details/' . $chapter->id) }}"><i class="bi bi-house-fill"></i></a>
                            </td>
                            <td>
                                @if ($chapter->state->conference_id > 0)
                                    {{ $chapter->state->conference->short_name }} / {{ $chapter->state->region->short_name }}
                                @else
                                    {{ $chapter->state->conference->short_name }}
                                @endif
                            </td>
                            <td>
                                @if($chapter->state_id < 52)
                                    {{$chapter->state->state_short_name}}
                                @else
                                    {{$chapter->state->country?->short_name}}
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

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{ $checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                        <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                 @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{ $checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                                @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Conference (Export Available)</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Region (Export Available)</label>
                                @endif
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                     </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
                    @if ($checkBox3Status)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="startExport('chaptercoordinator', 'Chapter/Coordinator List')"><i class="bi bi-download me-2"></i>Export Chapter/Coordinator List</button>
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
