@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE)
    @section('page_title', $reportYearRange.' EOY Reports *ADMIN*')
    @section('breadcrumb', 'EOY Stataus Report')
@elseif ($eoyTestCondition && $displayEOYTESTING)
    @section('page_title', $reportYearRange.' EOY Reports *TESTING*')
    @section('breadcrumb', 'EOY Stataus Reports')
@else
    @section('page_title', $reportYearRange.' EOY Reports')
    @section('breadcrumb', 'EOY Stataus Reports')
@endif

@section('content')
 <!-- Main content -->
 <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex align-items-center">
                    <div class="dropdown d-flex align-items-center">
                        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Financial Overview Report
                        </h3>
                         <span class="ms-3">Chapters that were added after June 30, {{ $reportYearEnd }} will not be listed</span>
                        @include('layouts.dropdown_menus.menu_eoy')
                    </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                    <tr>
                        <th rowspan="2">Financial<br>Report</th>
                        <th rowspan="2">Conf/Reg</th>
                        <th rowspan="2">State</th>
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Extended/<br>Returned</th>
                        <th rowspan="2">Received</th>
                        <th colspan="3" class="text-center">Attachments</th>
                        <th rowspan="2">At Least<br>1 Project</th>
                        <th rowspan="2">M2M<br>Donation</th>
                        <th rowspan="2">Party%</th>
                        <th rowspan="2">Probation<br>Status</th>
                        <th rowspan="2">Report<br>Balanced</th>
                        <th rowspan="2">Review<br>Complete</th>

                        {{-- <th colspan="2" class="text-center">Service Projects</th>
                        <th colspan="2" class="text-center">Parties/Benefits</th>
                        <th colspan="2" class="text-center">Review</th> --}}
                    </tr>
                    <tr>
                        <th>Roster</th>
                        <th>Stmt</th>
                        <th>990N</th>
                        {{-- <th>M2M</th>
                        <th>Project</th>
                        <th>Party%</th>
                        <th>Status</th>
                        <th>Balanced</th>
                        <th>Complete</th> --}}
                    </tr>
                </thead>
                 <tbody>
                    @foreach($chapterList as $list)
                        <tr>
                            <td class="text-center align-middle">
                                <a href="{{ url("/eoyreports/editfinancialreview/{$list->id}") }}"><i class="bi bi-calculator-fill"></i></a>
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
                            <td @if($list->documentsEOY->report_extension == '1') style="background-color: #ffc107;" @else style="background-color: transparent;" @endif>
                                @if($list->documentsEOY->report_extension == '1') YES @else @endif
                            </td>
                            <td @if($list->documentsEOY->financial_report_received == '1') style="background-color:#28a745; color: #ffffff;"
                                @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                    @if($list->documentsEOY?->financial_report_received == '1') YES @else NO @endif
                            </td>

                            @if($list->documentsEOY->report_extension == '1' || $list->documentsEOY?->financial_report_received == '1')
                                <td @if($list->documentsEOY?->roster_path != null) style="background-color:#28a745; color: #ffffff;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                        @if($list->documentsEOY?->roster_path != null) YES @else NO @endif
                                </td>
                                <td @if($list->documentsEOY->statement_1_path != null) style="background-color:#28a745; color: #ffffff;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                        @if($list->documentsEOY->statement_1_path != null) YES @else NO @endif
                                </td>
                                <td @if($list->documentsIRS->irs_path != null) style="background-color:#28a745; color: #ffffff;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                        @if($list->documentsIRS->irs_path != null) YES @else NO @endif
                                </td>
                                <td @if($list->financialReport->service_project_array != null) style="background-color:#28a745; color: #ffffff;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                        @if($list->financialReport->service_project_array != null) YES @else NO @endif
                                </td>
                                <td @if($list->financialReport->service_project_expenses_m2m > '0') style="background-color:#28a745; color: #ffffff;" @else style="background-color: #ffc107;" @endif>
                                    ${{ number_format(($list->financialReport->service_project_expenses_m2m ?? 0) , 2) }}
                                </td>
                                <td @if($list->financialReport->party_percentage > 0.20) style="background-color:#dc3545; color: #ffffff;"
                                    @elseif($list->financialReport->party_percentage > 0.15) style="background-color: #ffc107;"
                                    @else style="background-color:#28a745; color: #ffffff;" @endif>
                                    {{ number_format(($list->financialReport->party_percentage ?? 0) * 100, 2) }}%
                                </td>
                                <td @if($list->financialReport->party_percentage > 0.20) style="background-color:#dc3545; color: #ffffff;"
                                    @elseif($list->financialReport->party_percentage > 0.15) style="background-color: #ffc107;"
                                    @else style="background-color:#28a745; color: #ffffff;" @endif>
                                        @if($list->financialReport->party_percentage > 0.20) PROBATION
                                        @elseif($list->financialReport->party_percentage > 0.15) WARNING
                                        @else @endif
                                </td>
                                <td @if($list->financialReport->ending_balance == $list->financialReport->reconciled_balance) style="background-color:#28a745; color: #ffffff;"
                                    @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                    @if($list->financialReport->ending_balance == $list->financialReport->reconciled_balance) YES @else NO @endif
                                </td>
                                <td @if($list->documentsEOY->financial_review_complete == '1') style="background-color:#28a745; color: #ffffff;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                    @if($list->documentsEOY->financial_review_complete == '1') YES @else NO @endif
                                </td>
                            @else
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                </table>
               </div>
            <!-- /.card-body -->

            <div class="card-body">
            <div class="col-sm-12">
                <div class="form-check form-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                    <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-check form-switch">
                    <input type="checkbox" name="showReviewer" id="showReviewer" class="form-check-input" {{$checkBox2Status ? 'checked' : '' }} onchange="showReviewer()" />
                    <label class="form-check-label" for="showReviewer">Only show chapters I am Assigned Reviewer for</label>
                </div>
            </div>
            @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
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
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{$checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters (Export Available)</label>
                        </div>
                    </div>
                @endif
                    </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
                @if ($coordinatorCondition && $conferenceCoordinatorCondition)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="confirmSendEOYRptReminder()">
                        <i class="bi bi-envelope-fill me-2"></i>Send EOY Late Notices
                    </button>
                {{-- @endif
                @if ($assistConferenceCoordinatorCondition) --}}
                    @if ($checkBox3Status)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="startExport('eoystatus', 'EOY Status Lis')"><i class="bi bi-download me-2"></i>Export EOY Status List</button>
                    @elseif ($checkBox51Status)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="startExport('inteoystatus', 'International EOY Status List')"><i class="bi bi-download me-2"></i>Export International EOY Status List</button>
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
