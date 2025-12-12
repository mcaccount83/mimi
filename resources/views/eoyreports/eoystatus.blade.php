@extends('layouts.coordinator_theme')

@section('page_title', $title)
@section('breadcrumb', $breadcrumb)

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
                        EOY Status Report
                    </h3>
                    @include('layouts.dropdown_menus.menu_eoy')
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
                <th>Extension Given or<br>Returned to Chapter</th>
                <th>Board Report Received</th>
                <th>Board Report Activated</th>
				<th>Financial Report Received</th>
				<th>Financial Review Completed</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    @php
                        $mailData = [
                            'chapterName' => $list->name,
                            'chapterState' => $list->state,
                            'boardElectionReportReceived' => $list->documentsEOY->new_board_submitted,
                            'financialReportReceived' => $list->documentsEOY->financial_report_received,
                            '990NSubmissionReceived' => $list->documentsEOY->irs_path,
                            'einLetterCopyReceived' => $list->documentsEOY->ein_letter,
                        ];

                        $renderedHtml = View::make('emails.endofyear.latereportreminder', ['mailData' => $mailData])->render();
                        $renderedPlainText = strip_tags($renderedHtml);
                    @endphp

                        <tr>
                            <td class="text-center align-middle">
                                @if($coordinatorCondition)
                                    <a href="{{ url("/eoy/editstatus/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($list->documentsEOY->new_board_submitted == null || $list->documentsEOY->financial_report_received == null || $list->documentsEOY->new_board_submitted == 0 || $list->documentsEOY->financial_report_received == 0)
                                   <a href="#" class="email-link" data-chapter-name="{{ $list->name }}" data-chapter-id="{{ $list->id }}" data-user-name="{{ $userName }}"
                                    data-user-position="{{ $userPosition }}" data-user-conf-name="{{ $userConfName }}" data-user-conf-desc="{{ $userConfDesc }}"
                                    data-predefined-subject="End of Year Reports Late Notice" data-message-id="msg-{{ $list->id }}"> <i class="far fa-envelope text-primary"></i></a>
                                    <textarea id="msg-{{ $list->id }}" class="d-none">{{ $renderedHtml = View::make('emails.endofyear.latereportreminder',
                                        ['mailData' => $mailData, 'minimal' => true, ])->render(); }}</textarea>
                                @endif
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
                            <td @if($list->documentsEOY->report_extension == '1') style="background-color: #ffc107;" @else style="background-color: transparent;" @endif>
                                @if($list->documentsEOY->report_extension == '1') YES @else @endif
                            </td>
                            <td @if($list->documentsEOY->new_board_submitted == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documentsEOY->new_board_submitted == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documentsEOY->new_board_active == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documentsEOY->new_board_active == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documentsEOY->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documentsEOY->financial_report_received == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documentsEOY->financial_review_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documentsEOY->financial_review_complete == '1') YES @else NO @endif
                            </td>
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
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showReviewer" id="showReviewer" class="custom-control-input" {{$checkBox2Status}} onchange="showChReviewer()" />
                    <label class="custom-control-label" for="showReviewer">Only show chapters I am Assigned Reviewer for</label>
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
                @if ($regionalCoordinatorCondition)
                    <a href="{{ route('eoyreports.eoystatusreminder') }}" onclick="return confirmSendEOYRptReminder();"> <button class="btn bg-gradient-primary mb-3"><i class="fas fa-envelope"></i>&nbsp;&nbsp;&nbsp;Send EOY Late Notices </button> </a>
                @endif
                @if ($assistConferenceCoordinatorCondition)
                    @if ($checkBox3Status)
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('eoystatus', 'EOY Status Lis')"><i class="fas fa-download mr-2" ></i>Export EOY Status List</button>
                    @elseif ($checkBox5Status)
                        <button class="btn bg-gradient-primary mb-3" onclick="startExport('inteoystatus', 'International EOY Status List')"><i class="fas fa-download"></i>&nbsp; Export International EOY Status List</button>
                    @else
                        <button class="btn bg-gradient-primary mb-3 disabled" onclick="startExport('eoystatus', 'EOY Status Lis')" disabled><i class="fas fa-download mr-2" ></i>Export EOY Status List</button>
                    @endif
                @endif
                </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
</div>
    </section>
    <!-- /.content -->
@endsection
