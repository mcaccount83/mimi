@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

@section('content')
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

           <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">General</a></li>
                    <li class="nav-item"><a class="nav-link" href="#board" data-bs-toggle="tab">Executive Board</a></li>
                    <li class="nav-item"><a class="nav-link" href="#online" data-bs-toggle="tab">Online/Social Media</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rereg" data-bs-toggle="tab">Re-Registration</a></li>
                    <li class="nav-item"><a class="nav-link" href="#donation" data-bs-toggle="tab">Donations</a></li>
                    <li class="nav-item"><a class="nav-link" href="#documents" data-bs-toggle="tab">Documents</a></li>
                    <li class="nav-item"><a class="nav-link" href="#eoy" data-bs-toggle="tab">End of Year</a></li>
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
                                <div class="col-12 mb-3">
                                    <label class="me-2">Name:</label>MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}<br>
                                    <label class="me-2">Conference:</label>{{ $conferenceDescription }}<br>
                                    <label class="me-2">Region:</label>{{ $regionLongName }}<br>
                                    <label class="me-2">EIN:</label>{{$chDetails->ein}}<br>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="me-2">Boundaries:</label>{{ $chDetails->territory}}<br>
                                    <label class="me-2">Status:</label>{{$chapterStatus}}<br>
                                    @if ($chDetails->status_id != \App\Enums\OperatingStatusEnum::OK)
                                        <label class="me-2">Probation Reason:</label>{{$probationReason}}<br>
                                    @endif
                                    <label class="me-2">Founded:</label>{{ $startMonthName }} {{ $chDetails->start_year }}<br>
                                </div>
                                <div class="col-4 mb-3">
                                     @include('boards-new.partials.coordinatorlist')
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="board">
                    <div class="board-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Executive Board</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">President:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards-new.partials.presinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">AVP:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards-new.partials.avpinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">MVP:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards-new.partials.mvpinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">Treasurer:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards-new.partials.trsinfo')
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-3">
                                    <label class="me-2">Secretary:</label>
                                </div>
                                <div class="col-12 col-md-9">
                                    @include('boards-new.partials.secinfo')
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="card-body text-center mt-3">
                                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.editboard', ['id' => $chDetails->id]) }}'"><i class="bi bi-people-fill me-2"></i>Edit Board Information</button>
                                </div>
                            </div>


                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="online">
                    <div class="online-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Online Information</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <div class="row">
                              <div class="col-12 mb-3">
                                <label class="me-2">Website:</label>
                                    @if($chDetails->website_url == 'http://' || empty($chDetails->website_url))
                                        No Website<br>
                                    @else
                                        <a href="{{$chDetails->website_url}}" target="_blank">{{$chDetails->website_url}}</a><br>
                                    @endif
                                <label class="me-2">Webiste Link Status:</label> {{$websiteLink}}
                            </div>
                            <div class="col-12 mb-3">
                                <label class="me-2">Forum/Group/App:</label> {{ $chDetails->egroup}}<br>
                                <label class="me-2">Facebook:</label> {{ $chDetails->social1}}<br>
                                <label class="me-2">Twitter:</label> {{ $chDetails->social2}}<br>
                                <label class="me-2">Instagram:</label> {{ $chDetails->social3}}<br>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.editonline', ['id' => $chDetails->id]) }}'"><i class="bi bi-laptop me-2"></i>Edit Online Information</button>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.tab-pane -->

                <div class="tab-pane" id="rereg">
                    <div class="rereg-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Re-Registration History</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                              <div class="col-12 mb-3">
                                    <label class="me-2">Last Re-Registration Payment:</label>
                                        @if ($chPayments->rereg_members)
                                            ${{ number_format($chPayments->rereg_payment, 2) }} for {{ $chPayments->rereg_members }} Members on <span class="date-mask">{{ $chPayments->rereg_date }}</span>
                                        @else
                                            No Payment Recorded
                                        @endif
                                        <br>
                                    <label class="me-2">Next Re-Registration Payment:</label>
                                    @if ($currentDate->gte($dueDate))
                                        @if ($chDetails->start_month_id == $currentMonth)
                                            <span class="badge bg-success fs-7">Due Now (<span class="date-mask">{{ $renewalDate }})</span></span>
                                        @else
                                            <span class="badge bg-danger fs-7">Overdue (<span class="date-mask">{{ $renewalDate }})</span></span>
                                        @endif
                                    @else
                                        Due on <span class="date-mask">{{ $renewalDate }}</span>
                                    @endif
                                    <br>
                                </div>

                              <div class="col-12 mb-3">
                                    <label>All Re-Registration Payments:</label><br>
                                    {{-- Check if there are ANY payments (current OR historical) --}}
                                    @if($chPayments->rereg_date || $reregHistory->count() > 0)

                                        {{-- Current Payment --}}
                                        @if($chPayments->rereg_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->rereg_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->rereg_payment, 2) }}<br>
                                                Members: {{ $chPayments->rereg_members }}<br>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Payments --}}
                                        @if($reregHistory->count() > 0)
                                            @foreach($reregHistory as $payment)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($payment->payment_date)) }}<br>
                                                    Amount: ${{ number_format($payment->payment_amount, 2) }}<br>
                                                    Members: {{ $payment->rereg_members }}<br>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                    @else
                                        {{-- Only show this if BOTH current and history are empty --}}
                                        <p class="text-muted">No re-registration payments</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.editreregpayment', ['id' => $chDetails->id]) }}'"><i class="bi bi-credit-card-fill me-2"></i>Make a Payment</button>
                            </div>
                        </div>

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="donation">
                    <div class="donation-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Donation History</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                             <div class="row">

                              <div class="col-md-6 mb-3">
                                     <h4 class="profile-username">Mother-to-Mother Fund Donations</h4>
                            <div class="row mb-4">
                                <div class="col-12">
                                    {{-- Check if there are ANY donations (current OR historical) --}}
                                    @if($chPayments->m2m_date || $m2mHistory->count() > 0)

                                        {{-- Current Donation --}}
                                        @if($chPayments->m2m_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->m2m_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->m2m_donation, 2) }}<br>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Donations --}}
                                        @if($m2mHistory->count() > 0)
                                            @foreach($m2mHistory as $payment)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($payment->payment_date)) }}<br>
                                                    Amount: ${{ number_format($payment->payment_amount, 2) }}<br>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                    @else
                                        {{-- Only show this if BOTH current and history are empty --}}
                                        <p class="text-muted">No M2M Fund donations</p>
                                    @endif
                                </div>
                            </div>
                                </div>

                              <div class="col-md-6 mb-3">
                                <h4 class="profile-username">Sustaining Chapter Donations</h4>
                            <div class="row mb-4">
                                <div class="col-12">
                                    {{-- Check if there are ANY donations (current OR historical) --}}
                                    @if($chPayments->sustaining_date || $sustainingHistory->count() > 0)

                                        {{-- Current Donation --}}
                                        @if($chPayments->sustaining_date)
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                Date: {{ date('m/d/Y', strtotime($chPayments->sustaining_date)) }}<br>
                                                Amount: ${{ number_format($chPayments->sustaining_donation, 2) }}<br>
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Historical Donations --}}
                                        @if($sustainingHistory->count() > 0)
                                            @foreach($sustainingHistory as $payment)
                                            <div class="card mb-2">
                                                <div class="card-body">
                                                    Date: {{ date('m/d/Y', strtotime($payment->payment_date)) }}<br>
                                                    Amount: ${{ number_format($payment->payment_amount, 2) }}<br>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif

                                    @else
                                        {{-- Only show this if BOTH current and history are empty --}}
                                        <p class="text-muted">No sustaining chapter donations</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                            </div>

                            <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.editdonate', ['id' => $chDetails->id]) }}'"><i class="bi bi-currency-dollar me-2"></i>Make a Donation</button>
                            </div>
                        </div>

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="documents">
                    <div class="documents-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>Chapter Documents</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                            <div class="col-md-6">
                                 <div class="card-header bg-transparent border-0">
                                        <h3>PDF Documents/Letters</h3>
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
                                        <button class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="disband-letter" onclick="openPdfViewer('{{ $chDocuments->disband_letter_path }}')">Disband Letter</button>
                                    @else
                                        <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No Disband Letter on File</button>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Final Financial Report:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($chDisbanded?->file_financial == 1 && $chEOYDocuments->final_financial_pdf_path != null)
                                        <button class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="final-pdf" onclick="openPdfViewer('{{ $chEOYDocuments->final_financial_pdf_path }}')">Final Financial PDF</button>
                                    @else
                                        <button class="btn btn-primary bg-gradient btn-sm disabled" type="button" disabled>Final PDF Not Available</button>
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
                                    <button class="btn btn-primary bg-gradient btn-sm keep-enabled" type="button" id="ein-letter" onclick="openPdfViewer('{{ $chDocuments->ein_letter_path }}')">EIN Letter from IRS</button>
                                @else
                                    <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No EIN Letter on File</button>
                                @endif
                            </div>
                        </div>

                        @if($chDetails->active_status == \App\Enums\ChapterStatusEnum::ACTIVE)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Chapter in Good Standing Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button id="GoodStanding" type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chDetails->id]) }}', '_blank')">Good Standing Chapter Letter</button><br>
                                </div>
                            </div>

                            @if($chDetails->active_status != \App\Enums\OperatingStatusEnum::OK)
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <label>Probation Letter:</label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        @if($chDocuments->probation_path != null)
                                            <button class="btn btn-primary bg-gradient btn-sm" type="button" id="probation-file" onclick="openPdfViewer('{{ $chDocuments->probation_path }}')">Probation Letter</button>
                                        @else
                                            <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No Probation Letter on File</button>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($chDetails->active_status == \App\Enums\OperatingStatusEnum::OK && $chDocuments->probation_release_path != null)
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button class="btn btn-primary bg-gradient btn-sm" type="button" id="probaton-release-file" onclick="openPdfViewer('{{ $chDocuments->probation_release_path }}')">Probation Release Letter</button>
                                </div>
                            </div>
                            @endif

                            @if($chDocuments->name_change_letter_path != null)
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label>Name Change Letter:</label>
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="btn btn-primary bg-gradient btn-sm" type="button" id="name-change-file" onclick="openPdfViewer('{{ $chDocuments->name_change_letter_path }}')">Name Change Letter</button>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($financialReportPdfs))
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <label>Financial Reports:</label>
                                    </div>
                                    <div class="col-sm-6">
                                        @foreach ($financialReportPdfs as $year => $path)
                                            <button type="button" class="btn btn-primary bg-gradient btn-sm me-1 mb-1"
                                                onclick="openPdfViewer('{{ $path }}')">
                                                {{ $year - 1 }}-{{ $year }} Financial Report
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif


                        @endif
                        </div>
                        </div>

                    </div>
                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->

                <div class="tab-pane" id="eoy">
                    <div class="eoy-field">
                        <div class="card-header bg-transparent border-0">
                            <h3>{{ $fiscalYear }} End of Year Information
                            @if ($ITCondition && !$displayTESTING && !$displayLIVE) *ADMIN*@endif
                            @if ($eoyTestCondition && $displayTESTING) *TESTING*@endif
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                            <div class="col-md-6 mb-3">
                                @if ($displayTESTING == '1' || $displayLIVE == '1' || $ITCondition)
                                    <div class="row mb-2">
                                        <div class="col-sm-4">
                                            <label>Board Report:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            @if($chEOYDocuments->new_board_active != '1')
                                                <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='{{ route('board-new.editboardreport', ['id' => $chDetails->id]) }}'">View Board Election Report</button>
                                            @else
                                                <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>Not available after Activation</button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-sm-4">
                                            <label>Financial Report:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="window.location.href='{{ route('board-new.editfinancialreport', ['id' => $chDetails->id]) }}'">View Financial Report</button>
                                                @if (!empty($chEOYDocuments->$yearColumnName))
                                                <button type="button" class="btn btn-primary bg-gradient btn-sm" onclick="openPdfViewer('{{ $chEOYDocuments->$yearColumnName }}')">View/Download Financial PDF</button>
                                            @endif
                                        </div>
                                    </div>
                                     <div class="row mb-3">
                                <div class="col-sm-4">
                                    <label>Awards:</label>
                                </div>
                                <div class="col-sm-8">
                                            @php
                                                $chapter_awards = null;

                                                if (isset($chFinancialReport['chapter_awards']) && !empty($chFinancialReport['chapter_awards'])) {
                                                    $blobData = base64_decode($chFinancialReport['chapter_awards']);
                                                    $chapter_awards = unserialize($blobData);
                                                }
                                            @endphp

                                            @if ($chapter_awards === false)
                                                @elseif (is_array($chapter_awards) && count($chapter_awards) > 0)
                                                    @foreach ($chapter_awards as $row)
                                                        @php
                                                            $awardType = "Unknown";
                                                            foreach($allAwards as $award) {
                                                                if($award->id == $row['awards_type']) {
                                                                    $awardType = $award->award_type;
                                                                    break;
                                                                }
                                                            }
                                                            $approved = $row['awards_approved'];
                                                        @endphp

                                                        <label class="me-2">{{ $awardType }}:</label>
                                                        <span class="badge {{ is_null($approved) ? 'bg-secondary' : ($approved == 1 ? 'bg-success' : 'bg-danger') }} fs-7">
                                                            {{ is_null($approved) ? 'Not Reviewed' : ($approved == 1 ? 'Approved' : 'Not Approved') }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    No awards applied for.
                                                @endif
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                            </div>
                              <div class="col-md-6 mb-3">
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label>Chapter Roster File:</label>
                                    </div>
                                    <div class="col-sm-8">
                                            @if (!empty($chEOYDocuments->roster_path))
                                                <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-roster" onclick="openPdfViewer('{{ $chEOYDocuments->roster_path }}')">View Chapter Roster</button>
                                            @else
                                                <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                            @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label>Primary Bank Statement:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        @if (!empty($chEOYDocuments->statement_1_path))
                                            <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-statement-1" onclick="openPdfViewer('{{ $chEOYDocuments->statement_1_path }}')">View Bank Statement</button>
                                        @else
                                            <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label>Additional Bank Statement:</label>
                                    </div>
                                    <div class="col-sm-8">
                                            @if (!empty($chEOYDocuments->statement_2_path))
                                                <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-statement-2" onclick="openPdfViewer('{{ $chEOYDocuments->statement_2_path }}')">View Additional Bank Statement</button>
                                            @else
                                                <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                            @endif
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4">
                                        <label>990N Submission:</label>
                                    </div>
                                    <div class="col-sm-8">
                                            @if (!empty($chEOYDocuments->irs_path))
                                                <button class="btn btn-primary bg-gradient btn-sm" type="button" id="eoy-irs" onclick="openPdfViewer('{{ $chEOYDocuments->irs_path }}')">View 990N Confirmation</button>
                                            @else
                                                <button class="btn btn-primary bg-gradient btn-sm disabled" disabled>No file attached</button>
                                                    @if($displayEINInstructionsLIVE == true)
                                                        <a href="https://www.irs.gov/charities-non-profits/annual-electronic-filing-requirement-for-small-exempt-organizations-form-990-n-e-postcard" target="_blank"  class="btn btn-primary bg-gradient btn-sm">990N IRS Website Link to File</a></td>
                                                        @foreach($resources as $resourceItem)
                                                            @if ($resourceItem->name == '990N Filing Instructions')
                                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')" class="btn btn-primary bg-gradient btn-sm">990N Filing Instructions</a>
                                                            @endif
                                                        @endforeach

                                                        @foreach($resources as $resourceItem)
                                                            @if ($resourceItem->name == '990N Filing FAQs')
                                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')" class="btn btn-primary bg-gradient btn-sm">990N Filing FAQs</a>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @else
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <label>Board Report:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        Report will be available on May 1st.
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <label>Financial Report:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        Report will be available on June 1st.
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <label>990N Submission:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        Links/Instructions will be available on July 1st.
                                    </div>
                                </div>
                            @endif

                              <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.viewendofyear', ['id' => $chDetails->id]) }}'"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Filing Instructions & Information</button>
                            </div>
                        </div>

                             </div>
                        <!-- /.card-body -->
                    </div>
                </div>
                <!-- /.tab-pane -->



                </div>
                <!-- /.tab-content -->
                </div>
              </div>
              <!-- /.card-body -->
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
@section('customscript')
<script>

</script>

@endsection
