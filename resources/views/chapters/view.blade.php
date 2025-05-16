@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Chapter Details')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                <br>
                EIN: {{$chDetails->ein}}
                @if ( $chDetails->ein == null && $conferenceCoordinatorCondition)
                    <br>
                    Apply for an EIN:
                    <button class="btn bg-gradient-primary btn-xs ml-1" type="button" id="irs-ein" onclick="window.open('https://sa.www4.irs.gov/modiein/individual/index.jsp', '_blank')">Link to IRS</button>
                    @foreach($resources as $resourceItem)
                    @if ($resourceItem->name === 'Applying for a Chapter EIN')
                        <button class="btn bg-gradient-primary btn-xs ml-1" type="button" id="apply-ein" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">Instuctions</button>
                    @endif
                    @endforeach
                @endif
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">

                <b>IRS Notes:</b> {{$chDocuments->irs_notes}}
                    </li>
                    <li class="list-group-item">
                        <b>Re-Registration Dues:</b><span class="float-right">
                            @if ($chPayments->rereg_members)
                                <b>{{ $chPayments->rereg_members }} Members</b> on <b><span class="date-mask">{{ $chPayments->rereg_date }}</span></b>
                            @else
                                No Payment Recorded
                            @endif
                        </span><br>
                        <b>M2M Donation:</b><span class="float-right">
                            @if ($chPayments->m2m_donation)
                                <b>${{ $chPayments->m2m_donation }}</b> on <b><span class="date-mask">{{ $chPayments->m2m_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                        <b>Sustaining Chapter Donation: </b><span class="float-right">
                            @if ($chPayments->sustaining_donation)
                                <b>${{ $chPayments->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chPayments->sustaining_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                    </li>
                    <li class="list-group-item">
                        <b>Founded:</b> <span class="float-right">{{ $startMonthName }} {{ $chDetails->start_year }}</span>
                        <br>
                        <b>Formerly Known As:</b> <span class="float-right">{{ $chDetails->former_name }}</span>
                        <br>
                        <b>Sistered By:</b> <span class="float-right">{{ $chDetails->sistered_by }}</span>
                    </li>
                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                </ul>
                <div class="text-center">
                    @if ($chDetails->active_status == 1 )
                        <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                    @elseif ($chDetails->active_status == 2)
                      <b><span style="color: #ffc107;">Chapter is PENDING</span></b>
                    @elseif ($chDetails->active_status == 3)
                      <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b>
                    @elseif ($chDetails->active_status == 0)
                        <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                        Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                        {{ $chDetails->disband_reason }}
                    @endif
                </div>
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
                  <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">General</a></li>
                  <li class="nav-item"><a class="nav-link" href="#com" data-toggle="tab">Documents</a></li>
                  <li class="nav-item"><a class="nav-link" href="#eoy" data-toggle="tab">End of Year</a></li>
                  <li class="nav-item"><a class="nav-link" href="#pre" data-toggle="tab">President</a></li>
                  <li class="nav-item"><a class="nav-link" href="#avp" data-toggle="tab">Administrative VP</a></li>
                  <li class="nav-item"><a class="nav-link" href="#mvp" data-toggle="tab">Membership VP</a></li>
                  <li class="nav-item"><a class="nav-link" href="#trs" data-toggle="tab">Treasurer</a></li>
                  <li class="nav-item"><a class="nav-link" href="#sec" data-toggle="tab">Secretary</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="general">
                    <div class="general-field">
                        <h3 class="profile-username">General Information
                            <button class="btn bg-gradient-primary btn-xs ml-2" onclick="window.location.href='{{ route('board.editpresident', ['id' => $chDetails->id]) }}'">View Chapter Profile As President</button>
                            {{-- <button class="btn bg-gradient-primary btn-xs ml-2" onclick="window.location.href='{{ route('viewas.viewchapterpresident', ['id' => $chDetails->id]) }}'">View Chapter Profile As President</button> --}}
                    </h3>
                    <div class="row">
                            <div class="col-md-12">
                                <label>Boundaries:</label> {{ $chDetails->territory}}
                        <br>
                        <label>Status:</label> {{$chapterStatus}}
                        @if ($chDetails->status_id != 1)
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
                                <label>Chpater Email Address:</label> <a href="mailto:{{ $chDetails->email}}">{{ $chDetails->email}}</a>
                        <br>
                        <label>Email used for Inquiries:</label> <a href="mailto:{{ $chDetails->inquiries_contact}}">{{ $chDetails->inquiries_contact}}</a>
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
                            <label>Website:</label> <a href="{{$chDetails->website_url}}" target="_blank">{{$chDetails->website_url}}</a>
                        <br>
                        <label>Webiste Link Status:</label> {{$websiteLink}}
                        <br>
                        <label>Webiste Notes (not visible to board members):</label><br>
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

                    <br><br>
                    </div>
                  </div>
                   <!-- /.tab-pane -->
                   <div class="tab-pane" id="com">
                    <div class="com-field">
                        <div class="row">
                            <div class="col-md-6">

                        <h3 class="profile-username">PDF Letters</h3>
                        @if($chDetails->active_status == '0')
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Disband Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDocuments->disband_letter_path != null)
                                        <button class="btn bg-gradient-primary btn-sm" type="button" id="disband-letter" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">Disband Letter</button>
                                        {{-- <button class="btn bg-gradient-primary btn-sm" type="button" id="disband-letter" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->disband_letter_path }}'">Disband Letter</button> --}}
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm disabled">No Disband Letter on File</button>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final Financial Report:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDocuments->final_financial_pdf_path != null)
                                        <button class="btn bg-gradient-primary btn-sm" type="button" id="final-pdf" onclick="openPdfViewer('{{ $chDocuments->final_financial_pdf_path }}')">Final Financial Report</button>
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm disabled">Final Report Not Filed</button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>EIN Letter:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @if($chDocuments->ein_letter_path != null)
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter from IRS</button>
                                    {{-- <button class="btn bg-gradient-primary btn-sm" type="button" id="ein-letter" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->ein_letter_path }}'">EIN Letter from IRS</button> --}}
                                @else
                                    <button class="btn bg-gradient-primary btn-sm disabled">No EIN Letter on File</button>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>Chapter Roster:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @if($chDocuments->roster_path != null)
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="roster-file" onclick="openPdfViewer('{{ $chDocuments->roster_path }}')">Most Current Roster</button>
                                    {{-- <button class="btn bg-gradient-primary btn-sm" type="button" id="roster-file" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->roster_path }}'">Most Current Roster</button> --}}
                                @else
                                    <button class="btn bg-gradient-primary btn-sm disabled">No Roster on File</button>
                                @endif
                            </div>
                        </div>

                        @if($chDetails->active_status == '1')
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Chaper in Good Standing Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button id="GoodStanding" type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}', '_blank')">Good Standing Chapter Letter</button><br>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDocuments->probation_path != null)
                                        <button class="btn bg-gradient-primary btn-sm" type="button" id="probation-file" onclick="openPdfViewer('{{ $chDocuments->probation_path }}')">Probation Letter</button>
                                        {{-- <button class="btn bg-gradient-primary btn-sm" type="button" id="probation-file" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->probation_path }}'">Probation Letter</button> --}}
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm disabled">No Probation Letter on File</button>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDocuments->probation_release_path != null)
                                        <button class="btn bg-gradient-primary btn-sm" type="button" id="probaton-release-file" onclick="openPdfViewer('{{ $chDocuments->probation_release_path }}')">Probation Release Letter</button>
                                        {{-- <button class="btn bg-gradient-primary btn-sm" type="button" id="roster-file" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->probation_release_path }}'">Probation Release Letter</button> --}}
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm disabled">No Probation Release Letter on File</button>
                                    @endif
                                </div>
                            </div>

                            @if($chDocuments->name_change_letter_path != null)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Name Change Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="name-change-file" onclick="openPdfViewer('{{ $chDocuments->name_change_letter_path }}')">Name Change Letter</button>
                                </div>
                            </div>
                            @endif

                        @endif

                        </div>

                        @if($chDetails->active_status == '1')
                        <div class="col-md-6">
                            <h3 class="profile-username">Preset Emails</h3>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Custom Message:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="showChapterEmailModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Board in MIMI</button>
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
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chDetails->name . ', ' . $stateShortName) }}'">Blank Email to Board</button>
                                </div>
                            </div>
                            @if ($startDate->greaterThanOrEqualTo($threeMonthsAgo))
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <label>New Chapter Email:</label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        @if ($chDetails->ein != null)
                                            <button id="NewChapter" type="button" class="btn bg-primary mb-1 btn-sm" onclick="showNewChapterEmailModal()">Send New Chapter Email</button>
                                        @else
                                            <button type="button" class="btn bg-primary mb-1 btn-sm" disabled>Must have EIN Number</button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Re-Registration Reminder:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="showChapterReRegModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Re-Registration</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Re-Registration Late Reminder:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="showChapterReRegLateModal('{{ $chDetails->name }}', {{ $chDetails->id }})">Email Late Notice</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation/Warning Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="showProbationLetterModal()">Email Probation/Warning</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="showProbationReleaseModal()">Email Probation Release</button>
                                </div>
                            </div>

                        </div>
                        @endif

                        @if($chDetails->active_status == '0')
                        <div class="col-md-6">
                            <h3 class="profile-username">Disband Checklist
                                @if (isset($chDisbanded))
                                    <button id="viewdisband" class="btn bg-gradient-primary btn-xs ml-2" onclick="window.location.href='{{ route('viewas.viewchapterdisbandchecklist', ['id' => $chDetails->id]) }}'">View Checklist/Report As President</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-xs ml-2" disabled>View Checklist/Report As President</button>
                                @endif
                        </h3>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final Re-Reg Payment:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    {{ $chDisbanded?->final_payment == 1 ? 'YES' : 'NO' }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Funds Donated:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    {{ $chDisbanded?->donate_funds == 1 ? 'YES' : 'NO' }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Manual Returned/Destroyed:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    {{ $chDisbanded?->destroy_manual == 1 ? 'YES' : 'NO' }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Online Accounts Removed:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    {{ $chDisbanded?->remove_online == 1 ? 'YES' : 'NO' }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final 990N Filed:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    {{ $chDisbanded?->file_irs == 1 ? 'YES' : 'NO' }}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final Financial Report Submitted:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    {{ $chDisbanded?->file_financial == 1 ? 'YES' : 'NO' }}
                                </div>
                            </div>

                        </div>
                        @endif

                    </div>

                    </div>
                </div>

                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="eoy">
                    <div class="eoy-field">
                        <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} End of Year Information
                            @if ($userAdmin && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                            @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                        </h3>
                        @if($userAdmin || $eoyTestCondition && $displayTESTING || $displayLIVE)
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Boundary Issues:</label>
                                </div>
                                @if ($chDetails->boundary_issues != null)
                                    <div class="col-sm-5">
                                        Chapter has reported boundary issues.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mr-2">Resolved:</label>{{ $chDetails->boundary_issue_resolved == 1 ? 'YES' : 'NO' }}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Chapter has not reported any boundary issues.
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Board Report:</label>
                                </div>
                                @if ($chDocuments->new_board_submitted == 1)
                                    <div class="col-sm-5">
                                        Board Election Report has been received.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mr-2">Activated:</label>{{ $chDocuments->new_board_active == 1 ? 'YES' : 'NO' }}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Board Election Report has not been submitted.
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Financial Report:</label>
                                </div>
                                @if ($chDocuments->financial_report_received == 1)
                                    <div class="col-sm-5">
                                        Financial Report has been received.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mr-2">Review Complete:</label>{{ $chDocuments->financial_review_complete == 1 ? 'YES' : 'NO' }}
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        Financial Report has not been submitted.
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Attachments:</label>
                                </div>
                                <div class="col-sm-9">
                                    @php
                                    // Check if $chFinancialReport is null before proceeding
                                    $attachments = $chDocuments ? [
                                        'Roster' => $chDocuments->roster_path ?? null,
                                        'Statement' => $chDocuments->statement_1_path ?? null,
                                        'Additional Statement' => $chDocuments->statement_2_path ?? null,
                                        '990N Confirmation' => $chDocuments->irs_path ?? null,
                                    ] : [];

                                    $included = array_keys(array_filter($attachments, fn($path) => $path !== null));
                                    $excluded = array_keys(array_filter(
                                        $attachments,
                                        fn($path, $label) => $path === null && $label !== 'Additional Statement',
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


                            <div class="row">
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

                            <div class="row">
                                <div class="col-sm-3">
                                    <label>990N Filing:</label>
                                </div>
                                @if ($chFinancialReport?->check_current_990N_verified_IRS == 1)
                                    <div class="col-sm-9">
                                        990N Filing was verified on the IRS website.
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        990N Filing has not been verified on the IRS website. {{ $chFinancialReport?->check_current_990N_notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="row ">
                                <div class="col-sm-3">
                                    <label>Chapter Awards:</label>
                                </div>
                                    @if(($chFinancialReport?->award_1_nomination_type != null)  || ($chFinancialReport?->award_2_nomination_type != null) || ($chFinancialReport?->award_3_nomination_type != null)
                                        || ($chFinancialReport?->award_4_nomination_type != null) || ($chFinancialReport?->award_5_nomination_type != null))
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
                        <br><br>
                    </div>
                </div>
                  <!-- /.tab-pane -->

                  <div class="tab-pane" id="pre">
                    @if ($chIsActive == '1')
                        <div class="pre-field">
                                <h3 class="profile-username">{{$PresDetails->first_name}} {{$PresDetails->last_name}}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="mailto:{{ $PresDetails->email }}">{{ $PresDetails->email }}</a>
                                        <br>
                                        <span class="phone-mask">{{$PresDetails->phone }}</span>
                                        <br>
                                        {{$PresDetails->street_address}}
                                        <br>
                                        {{$PresDetails->city}},{{$PresDetails->state}}&nbsp;{{$PresDetails->zip}}
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                                    <div class="row mt-3">
                                        @php
                                            $Subscriptions = $PresDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                        @endphp
                                        <dt class="col-sm-3">Public Announcements</dt>
                                        <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                                            <dd class="col-sm-6">
                                                @if (in_array(1, $Subscriptions))
                                                    <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(1, {{ $PresDetails->user_id }})">Unsubscribe</button>
                                                @else
                                                    <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(1, {{ $PresDetails->user_id }})">Subscribe</button>
                                                @endif
                                            </dd>
                                        <div class="col-md-12">
                                    <p>This will reset password to default "TempPass4You" for this user only.
                                    <br>
                                    <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $PresDetails->user_id }}">Reset President Password</button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="pre-field">
                            <h3 class="profile-username">{{$PresDisbandedDetails->first_name}} {{$PresDisbandedDetails->last_name}}</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="mailto:{{ $PresDisbandedDetails->email }}">{{ $PresDisbandedDetails->email }}</a>
                                    <br>
                                    <span class="phone-mask">{{$PresDisbandedDetails->phone }}</span>
                                    <br>
                                    {{$PresDisbandedDetails->street_address}}
                                    <br>
                                    {{$PresDisbandedDetails->city}},{{$PresDisbandedDetails->state}}&nbsp;{{$PresDisbandedDetails->zip}}
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="avp">
                    @if ($chIsActive == '1')
                        @if ($AVPDetails->user_id == '')
                            <div class="avp-field-vacant">
                                <h3 class="profile-username">Administrative Vice President Position is Vacant</h3>
                                <br><br>
                            </div>
                        @else
                            <div class="avp-field">
                                <h3 class="profile-username">{{$AVPDetails->first_name}} {{$AVPDetails->last_name}}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="mailto:{{ $AVPDetails->email }}">{{ $AVPDetails->email }}</a>
                                        <br>
                                        <span class="phone-mask">{{$AVPDetails->phone}}</span>
                                        <br>
                                        {{$AVPDetails->street_address}}
                                        <br>
                                        {{$AVPDetails->city}},{{$AVPDetails->state}}&nbsp;{{$AVPDetails->zip}}
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                        @php
                                            $Subscriptions = $AVPDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                        @endphp
                                    <dt class="col-sm-3">Public Announcements</dt>
                                    <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                                    <dd class="col-sm-6">
                                        @if (in_array(1, $Subscriptions))
                                            <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(1, {{ $AVPDetails->user_id }})">Unsubscribe</button>
                                        @else
                                            <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(1, {{ $AVPDetails->user_id }})">Subscribe</button>
                                        @endif
                                    </dd>
                                <div class="col-md-12">
                                    <p>This will reset password to default "TempPass4You" for this user only.
                                    <br>
                                    <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $AVPDetails->user_id }}">Reset AVP Password</button>
                                    </p>
                                </div>
                                </div>
                            </div>
                        @endif
                        @else
                        @if ($AVPDetails->user_id == '')
                            <div class="avp-field-vacant">
                                <h3 class="profile-username">Administrative Vice President Position is Vacant</h3>
                                <br><br>
                            </div>
                        @else
                            <div class="avp-field">
                                <h3 class="profile-username">{{$AVPDetails->first_name}} {{$AVPDetails->last_name}}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="mailto:{{ $AVPDetails->email }}">{{ $AVPDetails->email }}</a>
                                        <br>
                                        <span class="phone-mask">{{$AVPDetails->phone}}</span>
                                        <br>
                                        {{$AVPDetails->street_address}}
                                        <br>
                                        {{$AVPDetails->city}},{{$AVPDetails->state}}&nbsp;{{$AVPDetails->zip}}
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="mvp">
                    @if ($chIsActive == '1')
                        @if ($MVPDetails->user_id == '')
                            <div class="mvp-field-vacant">
                                <h3 class="profile-username">Membership Vice President Position is Vacant</h3>
                                <br><br>
                            </div>
                        @else
                            <div class="mvp-field">
                                <h3 class="profile-username">{{$MVPDetails->first_name}} {{$MVPDetails->last_name}}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="mailto:{{ $MVPDetails->email }}">{{ $MVPDetails->email }}</a>
                                        <br>
                                        <span class="phone-mask">{{$MVPDetails->phone}}</span>
                                        <br>
                                        {{$MVPDetails->street_address}}
                                        <br>
                                        {{$MVPDetails->city}},{{$MVPDetails->state}}&nbsp;{{$MVPDetails->zip}}
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    @php
                                        $Subscriptions = $MVPDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                    @endphp
                                    <dt class="col-sm-3">Public Announcements</dt>
                                    <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                                        <dd class="col-sm-6">
                                            @if (in_array(1, $Subscriptions))
                                                <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(1, {{ $MVPDetails->user_id }})">Unsubscribe</button>
                                            @else
                                                <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(1, {{ $MVPDetails->user_id }})">Subscribe</button>
                                            @endif
                                        </dd>
                                    <div class="col-md-12">
                                        <p>This will reset password to default "TempPass4You" for this user only.
                                        <br>
                                        <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $MVPDetails->user_id }}">Reset MVP Password</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        @if ($MVPDisbandedDetails->user_id == '')
                            <div class="mvp-field-vacant">
                                <h3 class="profile-username">Membership Vice President Position is Vacant</h3>
                                <br><br>
                            </div>
                        @else
                            <div class="mvp-field">
                                <h3 class="profile-username">{{$MVPDisbandedDetails->first_name}} {{$MVPDisbandedDetails->last_name}}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="mailto:{{ $MVPDisbandedDetails->email }}">{{ $MVPDisbandedDetails->email }}</a>
                                        <br>
                                        <span class="phone-mask">{{$MVPDisbandedDetails->phone}}</span>
                                        <br>
                                        {{$MVPDisbandedDetails->street_address}}
                                        <br>
                                        {{$MVPDisbandedDetails->city}},{{$MVPDisbandedDetails->state}}&nbsp;{{$MVPDisbandedDetails->zip}}
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    </div>
                  <!-- /.tab-pane -->
                    <div class="tab-pane" id="trs">
                        @if ($chIsActive == '1')
                        @if ($TRSDetails->user_id == '')
                          <div class="trs-field-vacant">
                              <h3 class="profile-username">Treasurer Position is Vacant</h3>
                              <br><br>
                          </div>
                        @else
                          <div class="trs-field">
                              <h3 class="profile-username">{{$TRSDetails->first_name}} {{$TRSDetails->last_name}}</h3>
                              <div class="row">
                                <div class="col-md-6">
                                    <a href="mailto:{{ $TRSDetails->email }}">{{ $TRSDetails->email }}</a>
                                    <br>
                                    <span class="phone-mask">{{$TRSDetails->phone}}</span>
                                    <br>
                                    {{$TRSDetails->street_address}}
                                    <br>
                                    {{$TRSDetails->city}},{{$TRSDetails->state}}&nbsp;{{$TRSDetails->zip}}
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                            <div class="row mt-3">
                                    @php
                                        $Subscriptions = $TRSDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                    @endphp
                                    <dt class="col-sm-3">Public Announcements</dt>
                                    <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                                        <dd class="col-sm-6">
                                            @if (in_array(1, $Subscriptions))
                                                <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(1, {{ $TRSDetails->user_id }})">Unsubscribe</button>
                                            @else
                                                <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(1, {{ $TRSDetails->user_id }})">Subscribe</button>
                                            @endif
                                        </dd>
                                    <div class="col-md-12">
                               <p>This will reset password to default "TempPass4You" for this user only.
                              <br>
                              <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $TRSDetails->user_id }}">Reset Treasurer Password</button>
                            </p>
                        </div>
                    </div>
                </div>
                        @endif
                        @else
                        @if ($TRSDisbandedDetails->user_id == '')
                            <div class="trs-field-vacant">
                                <h3 class="profile-username">Treasurer Position is Vacant</h3>
                                <br><br>
                            </div>
                        @else
                            <div class="trs-field">
                                <h3 class="profile-username">{{$TRSDisbandedDetails->first_name}} {{$TRSDisbandedDetails->last_name}}</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="mailto:{{ $TRSDisbandedDetails->email }}">{{ $TRSDisbandedDetails->email }}</a>
                                        <br>
                                        <span class="phone-mask">{{$TRSDisbandedDetails->phone}}</span>
                                        <br>
                                        {{$TRSDisbandedDetails->street_address}}
                                        <br>
                                        {{$TRSDisbandedDetails->city}},{{$TRSDisbandedDetails->state}}&nbsp;{{$TRSDisbandedDetails->zip}}
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                        </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="sec">
                    @if ($chIsActive == '1')
                  @if ($SECDetails->user_id == '')
                    <div class="sec-field-vacant">
                        <h3 class="profile-username">Secretary Position is Vacant</h3>
                        <br><br>
                    </div>
                  @else
                    <div class="sec-field">
                        <h3 class="profile-username">{{$SECDetails->first_name}} {{$SECDetails->last_name}}</h3>
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                        </div>
                        <div class="row mt-3">
                                <a href="mailto:{{ $SECDetails->email }}">{{ $SECDetails->email }}</a>
                                <br>
                                <span class="phone-mask">{{$SECDetails->phone}}</span>
                                <br>
                                {{$SECDetails->street_address}}
                                <br>
                                {{$SECDetails->city}},{{$SECDetails->state}}&nbsp;{{$SECDetails->zip}}
                            </div>
                            <div class="col-md-6">
                                @php
                                    $Subscriptions = $SECDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                <dt class="col-sm-3">Public Announcements</dt>
                                <dd class="col-sm-2">{{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}</dd>
                                    <dd class="col-sm-6">
                                        @if (in_array(1, $Subscriptions))
                                            <button class="btn bg-gradient-primary btn-sm" onclick="unsubscribe(1, {{ $SECDetails->user_id }})">Unsubscribe</button>
                                        @else
                                            <button class="btn bg-gradient-primary btn-sm" onclick="subscribe(1, {{ $SECDetails->user_id }})">Subscribe</button>
                                        @endif
                                    </dd>
                                <div class="col-md-12">
                        <p>This will reset password to default "TempPass4You" for this user only.
                        <br>
                        <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $SECDetails->user_id }}">Reset Secretary Password</button>
                        </p>
                    </div>
                </div>
            </div>
            @endif
            @else
            @if ($SECDisbandedDetails->user_id == '')
                <div class="sec-field-vacant">
                    <h3 class="profile-username">Secretary Position is Vacant</h3>
                    <br><br>
                </div>
            @else
                <div class="sec-field">
                    <h3 class="profile-username">{{$SECDisbandedDetails->first_name}} {{$SECDisbandedDetails->last_name}}</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="mailto:{{ $SECDisbandedDetails->email }}">{{ $SECDisbandedDetails->email }}</a>
                            <br>
                            <span class="phone-mask">{{$SECDisbandedDetails->phone}}</span>
                            <br>
                            {{$SECDisbandedDetails->street_address}}
                            <br>
                            {{$SECDisbandedDetails->city}},{{$SECDisbandedDetails->state}}&nbsp;{{$SECDisbandedDetails->zip}}
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                </div>
            @endif
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
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                @php
                    $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($chDetails->id);
                    $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                    $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string
                @endphp
                        <button type="button" class="btn bg-gradient-primary mb-3"
                            onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chDetails->name . ', ' . $stateShortName) }}'">
                            <i class="fas fa-envelope mr-2"></i>Email Board</button>
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.edit', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Chapter Information</button>
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.editboard', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Board Information</button>
                @endif

                @if ( $userAdmin || $eoyTestCondition && $displayTESTING || $regionalCoordinatorCondition && $displayLIVE )
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-edit mr-2"></i>Update EOY Information
                        @if ($userAdmin && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                        @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                    </button>
                @endif
                @if($conferenceCoordinatorCondition)
                    <br>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.editpayment', ['id' => $chDetails->id]) }}'"><i class="fas fa-dollar-sign mr-2"></i>Enter Payment/Donation</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="updateEIN()"><i class="fas fa-university mr-2"></i>Update EIN Number</button>
                @endif
                @if($regionalCoordinatorCondition)
                    <button class="btn bg-gradient-primary mb-3 showFileUploadModal" data-ein-letter="{{ $chDocuments->ein_letter_path }}"><i class="fas fa-upload mr-2"></i>Update EIN Letter</button>
                    @if($chIsActive == 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="showDisbandChapterModal()"><i class="fas fa-ban mr-2"></i>Disband Chapter</button>
                    @elseif($chIsActive != 1)
                        <button type="button" id="unzap" class="btn bg-gradient-primary mb-3" onclick="unZapChapter()"><i class="fas fa-undo mr-2"></i>UnZap Chapter</button>
                    @endif
                @endif
                <br>
                @if($coordinatorCondition)
                    @if ($confId == $chConfId)
                        @if ($chIsActive == 1)
                            @if ($inquiriesCondition  && ($coorId != $chPcId))
                                <button type="button" id="back-inquiries" class="btn bg-gradient-primary mb-3" onclick="window.location.window.location.href='{{ route('chapters.chapinquiries') }}'"><i class="fas fa-reply mr-2"></i>Back to Inquiries Chapter List</button>
                            @else
                                <button type="button" id="back" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter List</button>
                            @endif
                        @else
                            @if ($inquiriesCondition  && ($coorId != $chPcId))
                                <button type="button" id="back-inquiries-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapinquiries') }}'"><i class="fas fa-reply mr-2"></i>Back to Inquiries Zapped Chapter List</button>
                            @else
                                <button type="button" id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapzapped') }}'"><i class="fas fa-reply mr-2"></i>Back to Zapped Chapter List</button>
                            @endif
                        @endif
                    @elseif ($einCondition && ($confId != $chConfId) || $inquiriesCondition  && ($confId != $chConfId) || $userAdmin  && ($confId != $chConfId))
                        @if ($chIsActive == 1)
                            <button type="button" id="back-international"class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchapter') }}'"><i class="fas fa-reply mr-2"></i>Back to International Chapter List</button>
                        @else
                            <button type="button" id="back-international-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchapterzapped') }}'"><i class="fas fa-reply mr-2"></i>Back to International Zapped Chapter List</button>
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
<script>
    var $chIsActive = @json($chIsActive);
    var $einCondition = @json($einCondition);
    var $inquiriesCondition = @json($inquiriesCondition);
    var $chPcId = @json($chPcId);
    var $coorId = @json($coorId);
    var $confId = @json($confId);

$(document).ready(function () {
    // Disable fields for chapters that are not active or EIN & Inquiries Coordinators who are not PC for the Chapter
    if (($chIsActive != 1) || ($inquiriesCondition && ($coorId != $chPCid)) || ($einCondition && ($coorId != $chPCid))) {
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });

        // Re-enable the specific "Back" buttons
        $('#disband-letter').prop('disabled', false);
        $('#final-pdf').prop('disabled', false);
        $('#ein-letter').prop('disabled', false);
        $('#roster-file').prop('disabled', false);
        $('#back-zapped').prop('disabled', false);
        $('#back-inquiries').prop('disabled', false);
        $('#back-inquiries-zapped').prop('disabled', false);
        $('#back-international').prop('disabled', false);
        $('#back-international-zapped').prop('disabled', false);
        $('#unzap').prop('disabled', false);
        $('#viewdisband').prop('disabled', false);
    }
});

$(document).ready(function() {
    function loadCoordinatorList(coorId) {
        if (coorId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + coorId,
                type: "GET",
                success: function(result) {
                    $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                    console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    var selectedCorId = $("#ch_primarycor").val();
    loadCoordinatorList(selectedCorId);

    $("#ch_primarycor").change(function() {
        var selectedValue = $(this).val();
        loadCoordinatorList(selectedValue);
    });
});

document.querySelectorAll('.reset-password-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const userId = this.getAttribute('data-user-id');
        const newPassword = "TempPass4You";

        $.ajax({
            url: '{{ route('updatepassword') }}',
            type: 'PUT',
            data: {
                user_id: userId,
                new_password: newPassword,
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message.replace('<br>', '\n'),
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    }
                });
            },
            error: function(jqXHR, exception) {
                console.log(jqXHR.responseText); // Log error response
            }
        });
    });
});

function subscribe(categoryId, userId) {
    Swal.fire({
        title: 'Subscribe to List',
        html: `
            <p>User will be subscribed to the selected list. Please confirm by pressing OK.</p>
            <input type="hidden" id="user_id" name="user_id" value="${userId}">
            <input type="hidden" id="category_id" name="category_id" value="${categoryId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const userId = Swal.getPopup().querySelector('#user_id').value;
            const categoryId = Swal.getPopup().querySelector('#category_id').value;

            return {
                user_id: userId,
                category_id: categoryId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('forum.subscribecategory') }}',
                type: 'POST',
                data: {
                    user_id: data.user_id,
                        category_id: data.category_id,
                        _token: '{{ csrf_token() }}'
                    },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

function unsubscribe(categoryId, userId) {
    Swal.fire({
        title: 'Subscribe to List',
        html: `
            <p>Coordinator will be subscribed to the selected list. Please confirm by pressing OK.</p>

            <input type="hidden" id="user_id" name="user_id" value="${userId}">
            <input type="hidden" id="category_id" name="category_id" value="${categoryId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const userId = Swal.getPopup().querySelector('#user_id').value;
            const categoryId = Swal.getPopup().querySelector('#category_id').value;

            return {
                user_id: userId,
                category_id: categoryId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('forum.unsubscribecategory') }}',
                type: 'POST',
                data: {
                        user_id: data.user_id,
                        category_id: data.category_id,
                        _token: '{{ csrf_token() }}'
                    },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

function updateEIN() {
    const chapterId = '{{ $chDetails->id }}'; // Get the chapter ID from the Blade variable

    // Check if the chapter already has an EIN
    $.ajax({
        url: '{{ route('chapters.checkein') }}',
        type: 'GET',
        data: {
            chapter_id: chapterId
        },
        success: function(response) {
            if (response.ein) {
                // Show a warning if an EIN already exists
                Swal.fire({
                    title: 'Warning!',
                    text: 'This chapter already has an EIN. Do you want to replace it?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, replace it',
                    cancelButtonText: 'No',
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                        cancelButton: 'btn-sm btn-danger'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed to input the new EIN
                        promptForNewEIN(chapterId);
                    }
                });
            } else {
                // No existing EIN, proceed directly
                promptForNewEIN(chapterId);
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error!',
                text: 'Unable to check the existing EIN. Please try again later.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                }
            });
        }
    });
}

function promptForNewEIN() {
    Swal.fire({
        title: 'Enter EIN',
        html: `
            <p>Please enter the EIN for the chapter.</p>
            <div style="display: flex; align-items: center; ">
                <input type="text" id="ein" name="ein" class="swal2-input" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask placeholder="Enter EIN" required style="width: 100%;">
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
            <br>
            <div class="custom-control custom-switch">
                <input type="checkbox" id="chapter_ein" class="custom-control-input">
                <label class="custom-control-label" for="chapter_ein">Send EIN Notification to Chapter</label>
            </div>
            <br>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const ein = Swal.getPopup().querySelector('#ein').value;
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const chapterEIN = Swal.getPopup().querySelector('#chapter_ein').checked;

            if (!ein) {
                Swal.showValidationMessage('Please enter the new EIN.');
                return false;
            }

            return {
                ein: ein,
                chapter_id: chapterId,
                chapter_ein: chapterEIN,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.updateein') }}',
                        type: 'POST',
                        data: {
                            ein: data.ein,
                            notify: data.chapter_ein ? '1' : '0',
                            chapterid: data.chapter_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}


document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.showFileUploadModal').addEventListener('click', function(e) {
        e.preventDefault();

        const einLetter = this.getAttribute('data-ein-letter');

        Swal.fire({
            title: 'Upload EIN Letter',
            html: `
                <form id="uploadEINForm" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" required>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            cancelButtonText: 'Close',
            preConfirm: () => {
                const formData = new FormData(document.getElementById('uploadEINForm'));

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we upload your file.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        $.ajax({
                            url: '{{ url('/files/storeEIN/'. $id) }}',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'File uploaded successfully!',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(jqXHR, exception) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong, please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });

                return false;
            },
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        });
    });
});

function showProbationLetterModal() {
    Swal.fire({
        title: 'Generate Probation Letter',
        html: `
            <p>This will generate a Probation/Warning letter to be emailed to the full board and all coordinators for the chapter.</p>
            <p>Select the type of letter to generate and send:</p>
            <select id="letterType" class="form-control">
                <option value="no_report">Probation Letter - No EOY Reports</option>
                <option value="no_payment">Probation Letter - No Re-Reg Payment</option>
                <option value="probation_party">Probation Letter - Excess Party Expenses</option>
                <option value="warning_party">Warning Letter - Excess Party Expenses</option>
            </select>
            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Generate Letter',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const letterType = Swal.getPopup().querySelector('#letterType').value;
            return { chapterId, letterType };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we generate your letter.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ route('pdf.generateProbationLetter') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapterId,
                            letterType: data.letterType,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}

function showProbationReleaseModal() {
    Swal.fire({
        title: 'Generate Probation Letter',
        html: `
            <p>This will generate a Probation Release letter to be emailed to the full board and all coordinators for the chapter.</p>

            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Generate Letter',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const letterType = "probation_release";

            return { chapterId, letterType };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we generate your letter.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ route('pdf.generateProbationLetter') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapterId,
                            letterType: data.letterType,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}

function showDisbandChapterModal() {
    Swal.fire({
        title: 'Chapter Disband Reason',
        html: `
            <p>Marking a chapter as disbanded will remove the logins for all board members and remove the chapter. Please enter the reason for disbanding and press OK.</p>
            <div style="display: flex; align-items: center; ">
                <input type="text" id="disband_reason" name="disband_reason" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
            <br>
            <div class="custom-control custom-switch">
                <input type="checkbox" id="disband_letter" class="custom-control-input">
                <label class="custom-control-label" for="disband_letter">Send Disband Letter to Chapter</label>
            </div>
            <br>
            <div id="letterTypeContainer" style="display: none;">
                <p>Select the type of letter to generate and send:</p>
                <select id="letterType" class="form-control">
                    <option value="general">Disband Letter - General</option>
                    <option value="did_not_start">Disband Letter - Did Not Start</option>
                    <option value="no_report">Disband Letter - No EOY Reports</option>
                    <option value="no_payment">Disband Letter - No Re-Reg Payment</option>
                    <option value="no_communication">Disband Letter - No Communication</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        didOpen: () => {
            // Add event listener to checkbox
            document.getElementById('disband_letter').addEventListener('change', function() {
                const letterTypeContainer = document.getElementById('letterTypeContainer');
                letterTypeContainer.style.display = this.checked ? 'block' : 'none';
            });
        },
        preConfirm: () => {
            const disbandReason = Swal.getPopup().querySelector('#disband_reason').value;
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const disbandLetter = Swal.getPopup().querySelector('#disband_letter').checked;
            const letterType = Swal.getPopup().querySelector('#letterType').value;

            if (!disbandReason) {
                Swal.showValidationMessage('Please enter the reason for disbanding.');
                return false;
            }

            return {
                disband_reason: disbandReason,
                chapter_id: chapterId,
                disband_letter: disbandLetter,
                letterType
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.updatechapdisband') }}',
                        type: 'POST',
                        data: {
                            reason: data.disband_reason,
                            letter: data.disband_letter ? '1' : '0',
                            chapterid: data.chapter_id,
                            letterType: data.letterType,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}

// Function to unzap Chapter via AJAX
function unZapChapter(chapterid) {
    Swal.fire({
        title: 'UnZap Chapter',
        html: `
            <p>Unzapping a chapter will reactivate the logins for all board members and readd the chapter.</p>

            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">

        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('chapters.updatechapterunzap') }}',
                type: 'POST',
                data: {
                    chapterid: data.chapter_id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        showConfirmButton: false,  // Automatically close without "OK" button
                        timer: 1500,
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                },
                error: function(jqXHR, exception) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn-sm btn-success'
                        }
                    });
                }
            });
        }
    });
}

function showNewChapterEmailModal() {
    Swal.fire({
        title: 'New Chapter Email',
        html: `
            <p>This will automatically send the New Chapter Email to the full board and coordinator team. It will include their Letter of Good Standing and Group Exemption Letter.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chDetails->id }}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Send',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.sendnewchapter') }}',
                        type: 'POST',
                        data: {
                            chapterid: data.chapter_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}

function showChapterReRegModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Re-Registration Reminder',
        html: `
            <p>This will send the regular re-registration reminder for <b>${chapterName}</b> to the full board and all coordinators.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.sendchapterrereg') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapter_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}

function showChapterReRegLateModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Re-Registration Late Notice',
        html: `
            <p>This will send the regular re-registration late notice for <b>${chapterName}</b> to the full board and all coordinators.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.sendchapterrereglate') }}',
                        type: 'POST',
                        data: {
                            chapterId: data.chapter_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    });
}


</script>
@endsection
