@extends('layouts.mimi_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'IRS Status Report')

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
                        IRS Status Report
                    </h3>
                    @include('layouts.dropdown_menus.menu_reports_chap')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>IRS<br>Details</th>
                    <th>Letter</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter On File</th>
                    <th>EIN Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                    <tr >
                        <td class="text-center align-middle">
                            @if ($conferenceCoordinatorCondition)
                                <a href="{{ url("/chapterreports/irsedit/{$list->id}") }}"><i class="bi bi-bank"></i></a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if ($list->documents->ein_letter_path != null)
                                <a href="{{ $list->documents->ein_letter_path }}"
                                    onclick="event.preventDefault(); openPdfViewer('{{ $list->documents->ein_letter_path }}');">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>
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
                        <td data-sort="{{ $list->start_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                            {{ $list->startMonth->month_short_name }} {{ $list->start_year }}
                        </td>
						<td>{{ $list->ein }}</td>
                        <td @if($list->documents->ein_letter_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td>{{ $list->documents->ein_notes }}</td>
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
                            <label class="form-check-label" for="showIntl">Show All International Chapters (Export & Fax Info Available)</label>
                        </div>
                    </div>
                @endif
                </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
                    @if ($assistConferenceCoordinatorCondition)
                        @if ($checkBox3Status)
                            <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('einstatus', 'EIN Status List')"><i class="bi bi-download me-2"></i></i>Export EIN Status List</button>
                        @elseif ($checkBox51Status)
                            <button class="btn btn-primary bg-gradient mb-2" onclick="startExport('inteinstatus', 'International EIN Status List')"><i class="bi bi-download me-2"></i>Export International EIN Status List</button>
                        {{-- @else
                            <button class="btn btn-primary bg-gradient mb-2 disabled" onclick="startExport('einstatus', 'EIN Status List')" disabled><i class="bi bi-download me-2"></i>Export EIN Status List</button> --}}
                        @endif
                    @endif
                <br>
                    @if ($einCondition || $ITCondition)
                        @if ($checkBox51Status)
                            <button class="btn btn-primary bg-gradient mb-2" onclick="showEODeptCoverSheetModal()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>EO Dept Fax Coversheet</button>
                            <button class="btn btn-primary bg-gradient mb-2" onclick="showIRSUpdatesModal()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>IRS Updates to EO Dept</button>
                            <button class="btn btn-primary bg-gradient mb-2" onclick="showSubordinateFilingModal()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Subordinate Filing PDF</button>
                        @endif
                    @endif
                    </div>
            <!-- /.card-body for buttons -->

             <div class="card-body text-center mt-3">
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

