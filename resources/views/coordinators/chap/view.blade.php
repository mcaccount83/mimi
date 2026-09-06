@extends('layouts.mimi_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Chapter Details')

@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                    <br>
                EIN: {{$chDetails->ein}}
                @if ( $chDetails->ein == null && $conferenceCoordinatorCondition)
                    <br>
                    Apply for an EIN:
                    <span class="badge bg-primary badge-btn badge-inherit-size" style="cursor: pointer;" id="irs-ein" onclick="window.open('https://sa.www4.irs.gov/modiein/individual/index.jsp', '_blank')">Link to IRS</span>
                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-xs ms-1" type="button" id="irs-ein" onclick="window.open('https://sa.www4.irs.gov/modiein/individual/index.jsp', '_blank')">Link to IRS</button> --}}
                    @foreach($resources as $resourceItem)
                    @if ($resourceItem->name == 'Applying for a Chapter EIN')
                        <span class="badge bg-primary badge-btn badge-inherit-size" style="cursor: pointer;" id="apply-ein" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">Instuctions</span>
                        {{-- <button type="button" class="btn btn-primary bg-gradient btn-xs ms-1" type="button" id="apply-ein" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">Instuctions</button> --}}
                    @endif
                    @endforeach
                @endif
                @if($chDetails->ein != null && ($coordinatorCondition && $conferenceCoordinatorCondition))
                    <br>
                    <span class="badge bg-primary badge-btn badge-inherit-size" style="cursor: pointer;" onclick="updateEIN('{{ $chDetails->id }}')">Update EIN Number</span>
                    <span class="badge bg-primary badge-btn badge-inherit-size" style="cursor: pointer;" onclick="showFileUploadModal('{{ $chDetails->id }}')">Update EIN Letter</span>

                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-xs ms-1" onclick="updateEIN('{{ $chDetails->id }}')">
                        Update EIN Number
                    </button>
                    <button type="button" class="btn btn-primary bg-gradient btn-xs ms-1" onclick="showFileUploadModal('{{ $chDetails->id }}')">
                        Update EIN Letter
                    </button> --}}
                @endif
                 </p>
                 </div>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-auto"><label>EIN Notes:</label></div>
                                <div class="col text-end">
                                    {{$chDocuments->ein_notes}}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            @include('coordinators.partials.paymentinfo')
                            @include('coordinators.partials.donationinfo')

                             <div class="row">
                                <div class="text-center">
                                    <span class="badge bg-primary badge-btn badge-inherit-size" style="cursor: pointer;" onclick="window.location.href='{{ route('payment.editpayment', ['id' => $chDetails->id]) }}'">Enter Payment/Donation</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-xs ms-1" onclick="window.location.href='{{ route('payment.editpayment', ['id' => $chDetails->id]) }}'">
                                        Enter Payment/Donation
                                    </button> --}}
                                    @if($coordinatorCondition && $conferenceCoordinatorCondition)
                                        <span class="badge bg-primary badge-btn badge-inherit-size" style="cursor: pointer;" onclick="window.location.href='{{ route('payment.paymenthistory', ['id' => $chDetails->id]) }}'">View Payment History</span>
                                        {{-- <button type="button" class="btn btn-primary bg-gradient btn-xs ms-1" onclick="window.location.href='{{ route('payment.paymenthistory', ['id' => $chDetails->id]) }}'">
                                            View Payment History
                                        </button> --}}
                                    @endif
                                </div>
                            </div>

                        </li>
                        <li class="list-group-item">
                            @include('coordinators.partials.founderhistory')
                            @include('coordinators.partials.sisterhistory')
                        </li>
                        <li class="list-group-item">
                            @include('coordinators.partials.coordinatorlist')
                        </li>
                        <li class="list-group-item mt-3">
                            @include('coordinators.partials.chapterstatus')
                        </li>
                  </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">General</a></li>
                    <li class="nav-item"><a class="nav-link" href="#com" data-bs-toggle="tab">Documents</a></li>
                    @isset($chDisbanded)
                        <li class="nav-item"><a class="nav-link" href="#disband" data-bs-toggle="tab">Disband Checklist</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="#eoy" data-bs-toggle="tab">End of Year</a></li>
                    @endisset
                    <li class="nav-item"><a class="nav-link" href="#pre" data-bs-toggle="tab">President</a></li>
                    <li class="nav-item"><a class="nav-link" href="#avp" data-bs-toggle="tab">Administrative VP</a></li>
                    <li class="nav-item"><a class="nav-link" href="#mvp" data-bs-toggle="tab">Membership VP</a></li>
                    <li class="nav-item"><a class="nav-link" href="#trs" data-bs-toggle="tab">Treasurer</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sec" data-bs-toggle="tab">Secretary</a></li>
                </ul>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>General Information</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Boundaries:</label> {{ $chDetails->territory}}
                                    <br>
                                    <label>Status:</label> {{$chapterStatus}}
                                @if ($chDetails->status_id != \App\Enums\OperatingStatusEnum::OK)
                                    <br>
                                    <label>Probation Reason:</label> {{$probationReason}}
                                @endif
                                <br>
                                <label>Status Notes (not visible to board members):</label> {{ $chDetails->notes}}
                                <br><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Chapter Email Address:</label> @mailto($chDetails->email)
                                <br>
                                <label>Email used for Inquiries:</label> @mailto($chDetails->inquiries_contact)
                                <br>
                                <label>Inquiries Notes (not visible to board members):</label><br>
                                {{ $chDetails->inquiries_note}}
                                <br><br>
                            </div>
                            <div class="col-md-6">
                                <label>PO Box/Mailing Address:</label> {{ $chDetails->po_box }}
                                <br>
                                <label>Additional Information (not visible to board members):</label><br>
                                {!! nl2br(e($chDetails->additional_info)) !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">

                                <label>Website:</label>
                                    @if($chDetails->website_url == 'http://' || empty($chDetails->website_url))
                                        No Website
                                    @else
                                        <a href="{{$chDetails->website_url}}" target="_blank">{{$chDetails->website_url}}</a>
                                    @endif
                                <br>
                                <label>Website Link Status:</label> {{$websiteLink}}
                                <br>
                                <label>Website Notes (not visible to board members):</label><br>
                                {{ $chDetails->website_notes }}
                            </div>
                            <div class="col-md-6">
                                <label>Forum/Group/App:</label> {{ $chDetails->egroup}}
                                <br>
                                <label>Facebook:</label> {{ $chDetails->social1}}
                                <br>
                                <label>Twitter:</label> {{ $chDetails->social2}}
                                <br>
                                <label>Instagram:</label> {{ $chDetails->social3}}
                                <br><br>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

                   <div class="tab-pane" id="com">
                    <div class="com-field">
                        <div class="row">
                            <div class="col-md-6">
                                 <div class="card-header bg-transparent border-0">
                                    <h3>PDF Letters</h3>
                                </div>
                                <!-- /.card-header -->
                            <div class="card-body">
                        @if($chDetails->active_status == \App\Enums\ChapterStatusEnum::ZAPPED)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Disband Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDocuments->disband_letter_path != null)
                                        <span class="badge bg-primary badge-inherit-size badge-btn keep-enabled" style="cursor: pointer;" id="disband-letter" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">Disband Letter</span>
                                    @else
                                        <span class="badge bg-secondary badge-btn badge-inherit-size keep-enabled">No Disband Letter on File</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final Financial Report:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDisbanded?->file_financial == 1 && $chReportDocuments->final_financial_pdf_path != null)
                                        <span class="badge bg-primary badge-inherit-size badge-btn keep-enabled" style="cursor: pointer;" id="final-pdf" onclick="openPdfViewer('{{ $chReportDocuments->final_financial_pdf_path }}')">Final Financial PDF</span>
                                    @else
                                        <span class="badge bg-secondary badge-btn badge-inherit-size keep-enabled">Final PDF Not Available</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($chDetails->ein == null && ($conferenceCoordinatorCondition || $einCondition))
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>EIN Fax Coversheet:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" id="GoodStanding" onclick="openPdfViewer('{{ route('pdf.newchapfaxcover', ['id' => $chDetails->id]) }}')">EIN Fax Coversheet</span>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>EIN Letter:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @if($chDocuments->ein_letter_path != null)
                                    <span class="badge bg-primary badge-inherit-size badge-btn keep-enabled" style="cursor: pointer;" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter from IRS</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter from IRS</button> --}}
                                @else
                                    <span class="badge bg-primary badge-btn badge-inherit-size badge-disabled">No EIN Letter on File</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>No EIN Letter on File</button> --}}
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>Chapter Roster:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @if($chEOYDocuments->roster_path != null)
                                    <span class="badge bg-primary badge-inherit-size badge-btn keep-enabled" style="cursor: pointer;" id="roster-file" onclick="openPdfViewer('{{ $chEOYDocuments->roster_path }}')">Most Current Roster</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="roster-file" onclick="openPdfViewer('{{ $chEOYDocuments->roster_path }}')">Most Current Roster</button> --}}
                                @else
                                    <span class="badge bg-primary badge-btn badge-inherit-size badge-disabled">No Roster on File</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>No Roster on File</button> --}}
                                @endif
                            </div>
                        </div>

                        @if($chDetails->active_status == \App\Enums\ChapterStatusEnum::ACTIVE)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Good Standing Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" id="GoodStanding" onclick="openPdfViewer('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}')">Good Standing Chapter Letter</span>
                                    {{-- <button type="button" id="GoodStanding" class="btn btn-primary bg-gradient btn-sm" onclick="openPdfViewer('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}')">Good Standing Chapter Letter</button><br> --}}
                                </div>
                            </div>

                            @if($chDocuments->probation_path != null)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" id="probation-file" onclick="openPdfViewer('{{ $chDocuments->probation_path }}')">Probation Letter</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="probation-file" onclick="openPdfViewer('{{ $chDocuments->probation_path }}')">Probation Letter</button> --}}
                                </div>
                            </div>
                            @endif

                            @if($chDocuments->probation_release_path != null)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" id="probaton-release-file" onclick="openPdfViewer('{{ $chDocuments->probation_release_path }}')">Probation Release Letter</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="probaton-release-file" onclick="openPdfViewer('{{ $chDocuments->probation_release_path }}')">Probation Release Letter</button> --}}
                                </div>
                            </div>
                            @endif

                            @if($chDocuments->name_change_letter_path != null)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Name Change Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" id="name-change-file" onclick="openPdfViewer('{{ $chDocuments->name_change_letter_path }}')">Name Change Letter</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" type="button" id="name-change-file" onclick="openPdfViewer('{{ $chDocuments->name_change_letter_path }}')">Name Change Letter</button> --}}
                                </div>
                            </div>
                            @endif

                            @if (count($financialReportPdfs) > 0)
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <label>Financial Reports:</label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        @foreach ($financialReportPdfs as $year => $path)
                                            <span class="badge bg-primary badge-inherit-size badge-btn keep-enabled" style="cursor: pointer;" onclick="openPdfViewer('{{ $path }}')">{{ $year - 1 }}-{{ $year }} Financial Report</span>
                                            {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm me-1 mb-1 keep-enabled" onclick="openPdfViewer('{{ $path }}')">{{ $year - 1 }}-{{ $year }} Financial Report</button><br> --}}
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        @endif
                        </div>
                        </div>

                        @if($chDetails->active_status == \App\Enums\ChapterStatusEnum::ACTIVE)
                        <div class="col-md-6">
                            <div class="card-header bg-transparent border-0">
                                        <h3>Preset Emails</h3>
                                </div>
                                <!-- /.card-header -->
                            <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Custom Message:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;"
                                        onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">Email Board in MIMI</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">Email Board in MIMI</button> --}}
                                </div>
                            </div>
                            @php
                                $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($chDetails->id);
                                $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                                $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string
                            @endphp
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Blank Email:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn keep-enabled" style="cursor: pointer;"
                                        onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chDetails->name . ', ' . $stateShortName) }}'">Blank Email to Board</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chDetails->name . ', ' . $stateShortName) }}'">Blank Email to Board</button> --}}
                                </div>
                            </div>
                            @if ($startDate->greaterThanOrEqualTo($threeMonthsAgo))
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <label>New Chapter Email:</label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        @if ($chDetails->ein != null)
                                            <button type="button" id="NewChapter" class="btn btn-primary bg-gradient btn-sm" onclick="showNewChapterEmailModal({{ $chDetails->id }})">Send New Chapter Email</button>
                                        @else
                                            <button type="button" class="btn btn-primary bg-gradient btn-sm disabled" disabled>Must have EIN Number</button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Re-Reg Reminder:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" onclick="showChapterReRegEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Re-Registration</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showChapterReRegEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Re-Registration</button> --}}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Re-Reg Late Reminder:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" onclick="showChapterReRegLateEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Late Notice</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showChapterReRegLateEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Late Notice</button> --}}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation/Warning Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" onclick="showProbationLetterModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Probation/Warning</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showProbationLetterModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Probation/Warning</button> --}}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <span class="badge bg-primary badge-inherit-size badge-btn" style="cursor: pointer;" onclick="showProbationReleaseModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Probation Release</span>
                                    {{-- <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="showProbationReleaseModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Probation Release</button> --}}
                                </div>
                            </div>
                        </div>
                        </div>
                        @endif

                 </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="eoy">
                    <div class="eoy-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>{{ $reportYearRange }} End of Year Information
                            @if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE) *ADMIN*@endif
                            @if ($eoyTestCondition && $displayEOYTESTING) *TESTING*@endif
                            </h3>
                                </div>

                                <!-- /.card-header -->
                            <div class="card-body">
                        @if($ITCondition || $eoyTestCondition && $displayEOYTESTING || $displayEOYLIVE)
                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>Boundary Issues:</label>
                                </div>
                                @if ($chDetails->boundary_issues == 1)
                                    <div class="col-sm-5">
                                        Chapter has reported boundary issues.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="me-2">Resolved:</label>{{ $chDetails->boundary_issue_resolved == 1 ? 'YES' : 'NO' }}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Chapter has not reported any boundary issues.
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>Board Report:</label>
                                </div>
                                @if ($chEOYDocuments->new_board_submitted == 1)
                                    <div class="col-sm-5">
                                        Board Election Report has been received.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="me-2">Activated:</label>{{ $chEOYDocuments->new_board_active == 1 ? 'YES' : 'NO' }}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Board Election Report has not been submitted.
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>Financial Report:</label>
                                </div>
                                @if ($chEOYDocuments->financial_report_received == 1)
                                    <div class="col-sm-5">
                                        Financial Report has been received.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="me-2">Review Complete:</label>{{ $chEOYDocuments->financial_review_complete == 1 ? 'YES' : 'NO' }}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Financial Report has not been submitted.
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>Attachments:</label>
                                </div>
                                <div class="col-sm-9">
                                    @php
                                    // Check if $chFinancialReport is null before proceeding
                                    $attachments = $chEOYDocuments ? [
                                        'Roster' => $chEOYDocuments->roster_path ?? null,
                                        'Statement' => $chEOYDocuments->statement_1_path ?? null,
                                        'Additional Statement' => $chEOYDocuments->statement_2_path ?? null,
                                        '990N Confirmation' => $chIRSDocuments->irs_path ?? null,
                                    ] : [];

                                    $included = array_keys(array_filter($attachments, fn($path) => $path != null));
                                    $excluded = array_keys(array_filter(
                                        $attachments,
                                        fn($path, $label) => $path == null && $label != 'Additional Statement',
                                        ARRAY_FILTER_USE_BOTH
                                    ));

                                    // Anonymous function to format the list with "and"
                                    $formatListWithAnd = function($items) {
                                        if (count($items) > 1) {
                                            return implode(', ', array_slice($items, 0, -1)) . ' and ' . end($items);
                                        }
                                        return implode('', $items);
                                    };
                                @endphp

                                    @if(count($included) > 0)
                                        {{ $formatListWithAnd($included) }} are attached.
                                        @if(count($excluded) > 0)
                                        <br>
                                        @endif
                                    @endif

                                    @if(count($excluded) > 0)
                                        {{ $formatListWithAnd($excluded) }} are not attached.
                                    @endif
                                </div>
                            </div>


                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>Extension:</label>
                                </div>
                                @if ($chDetails->report_extension == 1)
                                    <div class="col-sm-9">
                                        Extension was granted. {{ $chDetails->extension_notes}}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        No extension has been granted at this time.
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>990N Filing:</label>
                                </div>
                                @if ($chIRSDocuments?->irs_verified == 1)
                                    <div class="col-sm-9">
                                        990N Filing was verified on the IRS website.
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        990N Filing has not been verified on the IRS website. {{ $chFinancialReport?->current_990N_notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="row mb-2">
                                <div class="col-sm-3">
                                    <label>Chapter Awards:</label>
                                </div>
                                @php
                                    $chapter_awards_check = [];
                                    if (!empty($chFinancialReportQuestions['chapter_awards'])) {
                                        $decoded = unserialize(base64_decode($chFinancialReportQuestions['chapter_awards']));
                                        if (is_array($decoded)) {
                                            $chapter_awards_check = array_filter($decoded, fn ($a) => !empty($a['awards_type']));
                                        }
                                    }
                                @endphp
                                @if (count($chapter_awards_check) > 0)
                                    <div class="col-sm-9">
                                        Chapter was nominated for one or more awards.
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Chapter was not nominated for any awards.
                                    </div>
                                @endif
                            </div>

                        @else
                            <strong>Report Status/Links are not available at this time.</strong>
                        @endif

                         <div class="row mb-2">
                            <div class="col-sm-3">
                                <label>Chapter Awards History:</label>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge bg-primary badge-inherit-size" style="cursor: pointer;" onclick="window.location.href='{{ route('eoyreports.awardhistory', ['id' => $chDetails->id]) }}'">View Award History</span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-3">
                                <label>Financial Report History:</label>
                            </div>
                            <div class="col-sm-9">
                                <span class="badge bg-primary badge-inherit-size" style="cursor: pointer;" onclick="window.location.href='{{ route('eoyreports.eoyreporthistory', ['id' => $chDetails->id]) }}'">View Report History</span>
                            </div>
                        </div>

                      </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

            <!-- /.tab-pane -->
            <div class="tab-pane" id="disband">
                <div class="disband-field">
                    <div class="card-header bg-transparent border-0">
                        <h3>Disband Checklist</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-6 mb-2">
                                <label>Final Re-Reg Payment:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                {{ $chDisbanded?->final_payment == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-6 mb-2">
                                <label>Funds Donated:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                {{ $chDisbanded?->donate_funds == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-6 mb-2">
                                <label>Manual Returned/Destroyed:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                {{ $chDisbanded?->destroy_manual == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-6 mb-2">
                                <label>Online Accounts Removed:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                {{ $chDisbanded?->remove_online == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-6 mb-2">
                                <label>Final 990N Filed:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                {{ $chDisbanded?->file_irs == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-6 mb-2">
                                <label>Final Financial Report Submitted:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                {{ $chDisbanded?->file_financial == 1 ? 'YES' : 'NO' }}
                                @if ( $chDisbanded?->file_financial == 1)
                                <button type="button" class="btn btn-danger bg-gradient btn-sm ms-2 keep-enabled keep-enabled" id="unsubmit">UnSubmit Report</button>
                                @else
                                <button type="button" class="btn btn-danger bg-gradient btn-sm ms-2" disabled>UnSubmit Report</button>
                                @endif
                            </div>
                        </div>
                        <br><br>
                     </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="pre">
                <div class="pre-field">
                    <!-- /.card-header -->
                    <div class="card-body">
                        @include('coordinators.partials.board_member_details', ['Details' => $PresDetails])
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="avp">
                @if ($AVPDetails->user_id == '')
                    <div class="avp-field-vacant">
                        <div class="card-header bg-transparent border-0">
                            <h3>Administrative Vice President Position is Vacant</h3>
                        </div>
                        <br><br>
                    </div>
                @else
                    <div class="avp-field">
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('coordinators.partials.board_member_details', ['Details' => $AVPDetails])
                        </div>
                        <!-- /.card-body -->
                    </div>
                @endif
            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="mvp">
                @if ($MVPDetails->user_id == '')
                    <div class="mvp-field-vacant">
                        <div class="card-header bg-transparent border-0">
                            <h3>Membership Vice President Position is Vacant</h3>
                        </div>
                        <br><br>
                    </div>
                @else
                    <div class="mvp-field">
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('coordinators.partials.board_member_details', ['Details' => $MVPDetails])
                        </div>
                        <!-- /.card-body -->
                    </div>
                @endif
            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="trs">
                @if ($TRSDetails->user_id == '')
                    <div class="trs-field-vacant">
                        <div class="card-header bg-transparent border-0">
                            <h3>Treasurer Position is Vacant</h3>
                        </div>
                        <br><br>
                    </div>
                @else
                    <div class="trs-field">
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('coordinators.partials.board_member_details', ['Details' => $TRSDetails])
                        </div>
                        <!-- /.card-body -->
                    </div>
                @endif
            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="sec">
                @if ($SECDetails->user_id == '')
                    <div class="sec-field-vacant">
                        <div class="card-header bg-transparent border-0">
                            <h3>Secretary Position is Vacant</h3>
                        </div>
                        <br><br>
                    </div>
                @else
                    <div class="sec-field">
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('coordinators.partials.board_member_details', ['Details' => $SECDetails])
                        </div>
                        <!-- /.card-body -->
                    </div>
                @endif
            </div>
            <!-- /.tab-pane -->

            </div>
        <!-- /.tab-content -->
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->

          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                @if ($coordinatorCondition)
                @php
                    $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($chDetails->id);
                    $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                    $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string
                @endphp
                    <button type="button" class="btn btn-primary bg-gradient mb-2" type="button" id="email-chapter" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                        <i class="bi bi-envelope-fill me-2"></i>Email Board</button>
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.edit', ['id' => $chDetails->id]) }}'"><i class="bi bi-house-fill me-2"></i>Update Chapter Information</button>
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.editboard', ['id' => $chDetails->id]) }}'"><i class="bi bi-person-bounding-box me-2"></i>Update Board Information</button>
                    @endif
                <br>
                @if ( $ITCondition || $eoyTestCondition && $displayEOYTESTING || $regionalCoordinatorCondition && $displayEOYLIVE )
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Update EOY Information
                        @if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE) *ADMIN*@endif
                        @if ($eoyTestCondition && $displayEOYTESTING) *TESTING*@endif
                    </button>
                @endif
                {{-- @if($coordinatorCondition && $conferenceCoordinatorCondition)                     --}}
                    {{-- <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('payment.editpayment', ['id' => $chDetails->id]) }}'"><i class="bi bi-currency-dollar me-2"></i>Enter Payment/Donation</button> --}}
                    {{-- <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="updateEIN('{{ $chDetails->id }}')"><i class="bi bi-bank me-2"></i>Update EIN Number</button> --}}
                {{-- @endif --}}
                @if($coordinatorCondition && $regionalCoordinatorCondition)
                    {{-- <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="showFileUploadModal('{{ $chDetails->id }}')"><i class="bi bi-upload me-2"></i>Update EIN Letter</button> --}}
                    @if($chActiveId == 1)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="showDisbandChapterModal({{ $chDetails->id }})"><i class="bi bi-ban me-2"></i>Disband Chapter</button>
                    @elseif($chActiveId != 1)
                        <button type="button" id="unzap" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="unZapChapter({{ $chDetails->id }})"><i class="bi bi-arrow-counterclockwise me-2"></i>UnZap Chapter</button>
                    @endif
                @endif
                <br>
                @if($coordinatorCondition)
                    @if ($confId == $chConfId)
                            @if ($chActiveId == \App\Enums\ChapterStatusEnum::ACTIVE)
                                <button type="button" id="back-list" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Active Chapter List</button>
                            @elseif ($chActiveId == \App\Enums\ChapterStatusEnum::ZAPPED)
                                <button type="button" id="back-zapped" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chapzapped') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-slash-fill me-2"></i>Back to Zapped Chapter List</button>
                            @endif
                            @if ($inquiriesCondition || $assistConferenceCoordinatorCondition)
                                @if ($chActiveId == \App\Enums\ChapterStatusEnum::ACTIVE)
                                    <button type="button" id="back-inquiries" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chapinquiries', [\App\Enums\CheckboxFilterEnum::INQUIRIES => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to Active Inquiries List</button>
                                @elseif ($chActiveId == \App\Enums\ChapterStatusEnum::ZAPPED)
                                    <button type="button" id="back-inquiries-zapped" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chapinquirieszapped', [\App\Enums\CheckboxFilterEnum::INQUIRIES => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to Zapped Inquiries List</button>
                                @endif
                            @endif
                     @elseif ($confId != $chConfId)
                        @if ($einCondition || $inquiriesInternationalCondition || $ITCondition)
                            @if ($chActiveId == \App\Enums\ChapterStatusEnum::ACTIVE)
                                <button type="button" id="back-list" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chaplist', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to International Active Chapter List</button>
                            @elseif ($chActiveId == \App\Enums\ChapterStatusEnum::ZAPPED)
                                <button type="button" id="back-zapped" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chapzapped', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-slash-fill me-2"></i>Back to International Zapped Chapter List</button>
                            @endif
                        @endif
                         @if ($inquiriesInternationalCondition || $ITCondition)
                            @if ($chActiveId == \App\Enums\ChapterStatusEnum::ACTIVE)
                                <button type="button" id="back-inquiries" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chapinquiries', [\App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to International Active Inquiries List</button>
                            @elseif ($chActiveId == \App\Enums\ChapterStatusEnum::ZAPPED)
                                <button type="button" id="back-inquiries-zapped" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('chapters.chapinquirieszapped', [\App\Enums\CheckboxFilterEnum::INTERNATIONALINQUIRIES => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-pin-map-fill me-2"></i>Back to International Zapped Inquiries List</button>
                            @endif
                        @endif
                    @endif
                @endif
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unsubmitButton = document.getElementById('unsubmit');

    if (!unsubmitButton) {
        return;
    }

    document.getElementById('unsubmit').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "Unsubmitting this report will make it editable by the chapter again.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Unsubmit',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
                cancelButton: 'btn btn-sm btn-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ url('/eoyreports/unsubmitfinal/' . $chDetails->id) }}";
            }
        });
    });
});
</script>
@endsection

