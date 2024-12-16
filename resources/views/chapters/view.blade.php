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
                <h3 class="profile-username text-center">MOMS Club of {{ $chapterList->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                <br>
                EIN: {{$chapterList->ein}}
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">

                <b>IRS Notes:</b> {{$allDocuments->ein_notes}}
                    </li>
                    <li class="list-group-item">
                        <b>Re-Registration Dues:</b><span class="float-right">
                            @if ($chapterList->members_paid_for)
                                <b>{{ $chapterList->members_paid_for }} Members</b> on <b><span class="date-mask">{{ $chapterList->dues_last_paid }}</span></b>
                            @else
                                No Payment Recorded
                            @endif
                        </span><br>
                        <b>M2M Donation:</b><span class="float-right">
                            @if ($chapterList->m2m_payment)
                                <b>${{ $chapterList->m2m_payment }}</b> on <b><span class="date-mask">{{ $chapterList->m2m_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                        <b>Sustaining Chapter Donation: </b><span class="float-right">
                            @if ($chapterList->sustaining_donation)
                                <b>${{ $chapterList->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chapterList->sustaining_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                    </li>
                    <li class="list-group-item">
                        <b>Founded:</b> <span class="float-right">{{ $startMonthName }} {{ $chapterList->start_year }}</span>
                        <br>
                        <b>Formerly Known As:</b> <span class="float-right">{{ $chapterList->former_name }}</span>
                        <br>
                        <b>Sistered By:</b> <span class="float-right">{{ $chapterList->sistered_by }}</span>
                    </li>
                    <input type="hidden" id="ch_primarycor" value="{{ $chapterList->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                </ul>
                <div class="text-center">
                    @if ($chapterList->is_active == 1 )
                        <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                    @else
                        <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                        Disband Date: <span class="date-mask">{{ $chapterList->zap_date }}</span><br>
                        {{ $chapterList->disband_reason }}
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
                        <button class="btn bg-gradient-primary btn-xs ml-2" onclick="window.location.href='{{ route('viewas.viewchapterpresident', ['id' => $chapterList->id]) }}'">View Chapter Profile As President</button>
                    </h3>
                    <div class="row">
                            <div class="col-md-12">
                                <label>Boundaries:</label> {{ $chapterList->territory}}
                        <br>
                        <label>Status:</label> {{$allStatuses[0]->chapter_status}}
                        <br>
                        <label>Status Notes (not visible to board members):</label> {{ $chapterList->notes}}
                        <br><br>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Chpater Email Address:</label> <a href="mailto:{{ $chapterList->email}}">{{ $chapterList->email}}</a>
                        <br>
                        <label>Email used for Inquiries:</label> <a href="mailto:{{ $chapterList->inquiries_contact}}">{{ $chapterList->inquiries_contact}}</a>
                        <br>
                        <label>Inquiries Notes (not visible to board members):</label><br>
                        {{ $chapterList->inquiries_note}}
                        <br><br>
                    </div>

                        <div class="col-md-6">
                            <label>PO Box/Mailing Address:</label> {{ $chapterList->po_box }}
                        <br>
                        <label>Additional Information (not visible to board members):</label><br>
                        {!! nl2br(e($chapterList->additional_info)) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Website:</label> <a href="{{$chapterList->website_url}}" target="_blank">{{$chapterList->website_url}}</a>
                        <br>
                        <label>Webiste Link Status:</label> {{ $allWebLinks[0]->link_status}}
                        <br>
                        <label>Webiste Notes (not visible to board members):</label><br>
                        {{ $chapterList->website_notes }}
                    </div>
                    <div class="col-md-6">
                        <label>Forum/Group/App:</label> {{ $chapterList->egroup}}
                        <br>
                        <label>Facebook:</label> {{ $chapterList->social1}}
                        <br>
                        <label>Twitter:</label> {{ $chapterList->social2}}
                        <br>
                        <label>Instagram:</label> {{ $chapterList->social3}}
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
                        @if($chapterList->is_active != '1')
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Disband Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    @if($allDocuments->disband_letter_path != null)
                                        <button class="btn bg-gradient-primary btn-sm" type="button" id="disband-letter" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $allDocuments->disband_letter_path }}'">Disband Letter</button>
                                    @else
                                        <button class="btn bg-gradient-primary btn-sm disabled">No Disband Letter on File</button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-sm-6 mb-2">
                                <label>EIN Letter:</label>
                            </div>
                            <div class="col-sm-6 mb-2">
                                @if($allDocuments->ein_letter_path != null)
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="ein-letter" onclick="window.open('{{ $allDocuments->ein_letter_path }}', '_blank')">EIN Letter from IRS</button>
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
                                @if($allDocuments->roster_path != null)
                                    <button class="btn bg-gradient-primary btn-sm" type="button" id="roster-file" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $allDocuments->roster_path }}'">Most Current Roster</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm disabled">No Roster on File</button>
                                @endif
                            </div>
                        </div>

                        @if($chapterList->is_active == '1')
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Chaper in Good Standig Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button id="GoodStanding" type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.open('{{ route('pdf.chapteringoodstanding', ['id' => $chapterList->id]) }}', '_blank')">Good Standing Chapter Letter</button><br>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release Letter:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
                                </div>
                            </div>

                        @endif

                        </div>

                        @if($chapterList->is_active == '1')
                        <div class="col-md-6">
                            <h3 class="profile-username">Preset Emails</h3>
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Blank Email:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chapterList->name . ', ' . $chapterList->statename) }}'">Email Board</button>
                                </div>
                            </div>

                            @php
                                $mimiUrl = 'https://example.com/mimi';
                                $reRegMessage = "Your chapter's re-registration payment is due at this time and has not yet been received.\n\n";
                                $reRegMessage .= "Calculate your payment:\n";
                                $reRegMessage .= "- Determine how many people paid dues to your chapter since your last re-registration payment through today.\n";
                                $reRegMessage .= "- Add in any people who paid reduced dues or had their dues waived due to financial hardship.\n";
                                $reRegMessage .= "- If this total amount of members is less than 10, make your check for the amount of $50.\n";
                                $reRegMessage .= "- If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due.\n";
                                $reRegMessage .= "- Payments received after the last day of your renewal month should include a late fee of $10.\n\n";
                                $reRegMessage .= "Make your payment:\n";
                                $reRegMessage .= "- Pay Online: $mimiUrl\n";
                                $reRegMessage .= "- Pay via Mail to: Chapter Re-Registration, 208 Hewitt Dr. Ste 103 #328, Waco, TX 76712\n";
                            @endphp
                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Re-Registration Reminder:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                    <button type="button" class="btn bg-primary mb-1 btn-sm" onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('Re-Registration Payment Reminder | MOMS Club of ' . $chapterList->name . ', ' . $chapterList->statename) }}&body={{ rawurlencode($reRegMessage) }}'">Email Re-Registration</button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation for No Payment:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation for No Reports:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Warning for Party Expense:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation for Party Expense:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6 mb-2">
                                    <label>Probation Release:</label>
                                </div>
                                <div class="col-sm-6 mb-2">
                                        **Coming Soon - In Production
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
                        <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} End of Year Information</h3>
                        @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
                            <div class="row">
                                <div class="col-sm-3">
                                    <label>Boundary Issues:</label>
                                </div>
                                @if ($chapterList->boundary_issues != null)
                                    <div class="col-sm-5">
                                        Chapter has reported boundary issues.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mr-2">Resolved:</label>{{ $chapterList->boundary_issue_resolved == 1 ? 'YES' : 'NO' }}
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
                                @if ($allDocuments->new_board_submitted == 1)
                                    <div class="col-sm-5">
                                        Board Election Report has been received.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mr-2">Activated:</label>{{ $allDocuments->new_board_active == 1 ? 'YES' : 'NO' }}
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
                                @if ($allDocuments->financial_report_received == 1)
                                    <div class="col-sm-5">
                                        Financial Report has been received.
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="mr-2">Review Complete:</label>{{ $allDocuments->financial_report_complete == 1 ? 'YES' : 'NO' }}
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
                                    // Check if $allFinancialReport is null before proceeding
                                    $attachments = $allFinancialReport ? [
                                        'Roster' => $allFinancialReport->roster_path ?? null,
                                        'Statement' => $allFinancialReport->bank_statement_included_path ?? null,
                                        'Additional Statement' => $allFinancialReport->bank_statement_2_included_path ?? null,
                                        '990N Confirmation' => $allFinancialReport->file_irs_path ?? null,
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
                                @if ($allDocuments->report_extension == 1)
                                    <div class="col-sm-9">
                                        Extension was granted. {{ $allDocuments->extension_notes}}
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
                                @if ($allFinancialReport?->check_current_990N_verified_IRS == 1)
                                    <div class="col-sm-9">
                                        990N Filing was verified on the IRS website.
                                    </div>
                                @else
                                    <div class="col-sm-9">
                                        990N Filing has not been verified on the IRS website. {{ $allFinancialReport?->check_current_990N_notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="row ">
                                <div class="col-sm-3">
                                    <label>Chapter Awards:</label>
                                </div>
                                    @if(($allFinancialReport?->award_1_nomination_type != null)  || ($allFinancialReport?->award_2_nomination_type != null) || ($allFinancialReport?->award_3_nomination_type != null)
                                        || ($allFinancialReport?->award_4_nomination_type != null) || ($allFinancialReport?->award_5_nomination_type != null))
                                 <div class="col-sm-9">
                                    Chapter was nominated for one or more awards.
                                </div>
                                    @else
                                <div class="col-sm-9">
                                    Chatper was not nominated for any awards.
                                </div>
                                    @endif
                            </div>



                        @else
                        <h3><strong>{{ (date('Y') - 1) . '-' . date('Y') }} Report Status/Links are not available at this time.</strong></h3>
                        @endif
                        <br><br>
                    </div>
                </div>

                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="pre">
                      <div class="pre-field">
                          <h3 class="profile-username">{{$PresDetails->first_name}} {{$PresDetails->last_name}}</h3>
                          <a href="mailto:{{ $PresDetails->email }}">{{ $PresDetails->email }}</a>
                          <br>
                          <span class="phone-mask">{{$PresDetails->phone }}</span>
                          <br><br>
                          {{$PresDetails->street_address}}
                          <br>
                          {{$PresDetails->city}},&nbsp;{{$PresDetails->state}}&nbsp;{{$PresDetails->zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $PresDetails->user_id }}">Reset President Password</button>
                          </p>
                      </div>
                    </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="avp">
                    @if ($AVPDetails->user_id == '')
                      <div class="avp-field-vacant">
                          <h3 class="profile-username">Administrative Vice President Position is Vacant</h3>
                          <br><br>
                      </div>
                    @else
                      <div class="avp-field">
                          <h3 class="profile-username">{{$AVPDetails->first_name}} {{$AVPDetails->last_name}}</h3>
                          <a href="mailto:{{ $AVPDetails->email }}">{{ $AVPDetails->email }}</a>
                          <br>
                          <span class="phone-mask">{{$AVPDetails->phone}}</span>
                          <br><br>
                          {{$AVPDetails->street_address}}
                          <br>
                          {{$AVPDetails->city}},&nbsp;{{$AVPDetails->state}}&nbsp;{{$AVPDetails->zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $AVPDetails->user_id }}">Reset AVP Password</button>
                        </p>
                    </div>
                    @endif
                    </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="mvp">
                    @if ($MVPDetails->user_id == '')
                      <div class="mvp-field-vacant">
                          <h3 class="profile-username">Membership Vice President Position is Vacant</h3>
                          <br><br>
                      </div>
                    @else
                      <div class="mvp-field">
                          <h3 class="profile-username">{{$MVPDetails->first_name}} {{$MVPDetails->last_name}}</h3>
                          <a href="mailto:{{ $MVPDetails->email }}">{{ $MVPDetails->email }}</a>
                          <br>
                          <span class="phone-mask">{{$MVPDetails->phone}}</span>
                          <br><br>
                          {{$MVPDetails->street_address}}
                          <br>
                          {{$MVPDetails->city}},&nbsp;{{$MVPDetails->state}}&nbsp;{{$MVPDetails->zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $MVPDetails->user_id }}">Reset MVP Password</button>
                        </p>
                    </div>
                    @endif
                    </div>
                  <!-- /.tab-pane -->
                    <div class="tab-pane" id="trs">
                        @if ($TRSDetails->user_id == '')
                          <div class="trs-field-vacant">
                              <h3 class="profile-username">Treasury Position is Vacant</h3>
                              <br><br>
                          </div>
                        @else
                          <div class="trs-field">
                              <h3 class="profile-username">{{$TRSDetails->first_name}} {{$TRSDetails->last_name}}</h3>
                              <a href="mailto:{{ $TRSDetails->email }}">{{ $TRSDetails->email }}</a>
                              <br>
                              <span class="phone-mask">{{$TRSDetails->phone}}</span>
                              <br><br>
                              {{$TRSDetails->street_address}}
                              <br>
                              {{$TRSDetails->city}},&nbsp;{{$TRSDetails->state}}&nbsp;{{$TRSDetails->zip}}
                              <br><br>
                              <p>This will reset password to default "TempPass4You" for this user only.
                              <br>
                              <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $TRSDetails->user_id }}">Reset Treasurer Password</button>
                            </p>
                        </div>
                        @endif
                        </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="sec">
                  @if ($SECDetails->user_id == '')
                    <div class="sec-field-vacant">
                        <h3 class="profile-username">Secretary Position is Vacant</h3>
                        <br><br>
                    </div>
                  @else
                    <div class="sec-field">
                        <h3 class="profile-username">{{$SECDetails->first_name}} {{$SECDetails->last_name}}</h3>
                        <a href="mailto:{{ $SECDetails->email }}">{{ $SECDetails->email }}</a>
                        <br>
                        <span class="phone-mask">{{$SECDetails->phone}}</span>
                        <br><br>
                        {{$SECDetails->street_address}}
                        <br>
                        {{$SECDetails->city}},&nbsp;{{$SECDetails->state}}&nbsp;{{$SECDetails->zip}}
                        <br><br>
                        <p>This will reset password to default "TempPass4You" for this user only.
                        <br>
                        <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $SECDetails->user_id }}">Reset Secretary Password</button>
                        </p>
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
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                        <button type="button" class="btn bg-gradient-primary mb-3"
                            onclick="window.location.href='mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('MOMS Club of ' . $chapterList->name . ', ' . $chapterList->statename) }}'">
                            <i class="fas fa-envelope mr-2"></i>Email Board</button>
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.edit', ['id' => $chapterList->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Chapter Information</button>
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.editboard', ['id' => $chapterList->id]) }}'"><i class="fas fa-edit mr-2"></i>Update Board Information</button>
                @endif
                @if($regionalCoordinatorCondition)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chapterList->id]) }}'"><i class="fas fa-edit mr-2"></i>Update EOY Information</button>
                @endif
                @if($conferenceCoordinatorCondition)
                    <br>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.editpayment', ['id' => $chapterList->id]) }}'"><i class="fas fa-dollar-sign mr-2"></i>Enter Payment/Donation</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="updateEIN()"><i class="fas fa-university mr-2"></i>Update EIN Number</button>
                    <button class="btn bg-gradient-primary mb-3 showFileUploadModal" data-ein-letter="{{ $chapterList->ein_letter_path }}"><i class="fas fa-upload mr-2"></i>Update EIN Letter</button>
                @endif
                @if($regionalCoordinatorCondition)
                    @if($chIsActive == 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="showDisbandChapterModal()"><i class="fas fa-ban mr-2"></i>Disband Chapter</button>
                    @elseif($chIsActive != 1)
                        <button type="button" id="unzap" class="btn bg-gradient-primary mb-3" onclick="unZapChapter()"><i class="fas fa-undo mr-2"></i>UnZap Chapter</button>
                    @endif
                @endif
                <br>
                @if($coordinatorCondition)
                    @if ($corConfId == $chConfId)
                        @if ($chIsActive == 1)
                            @if ($inquiriesCondition  && ($coordId != $chPCid))
                                <button type="button" id="back-inquiries" class="btn bg-gradient-primary mb-3" onclick="window.location.window.location.href='{{ route('chapters.chapinquiries') }}'"><i class="fas fa-reply mr-2"></i>Back to Inquiries Chapter List</button>
                            @else
                                <button type="button" id="back" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplist') }}'"><i class="fas fa-reply mr-2"></i>Back to Chapter List</button>
                            @endif
                        @else
                            @if ($inquiriesCondition  && ($coordId != $chPCid))
                                <button type="button" id="back-inquiries-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapinquiries') }}'"><i class="fas fa-reply mr-2"></i>Back to Inquiries Zapped Chapter List</button>
                            @else
                                <button type="button" id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapzapped') }}'"><i class="fas fa-reply mr-2"></i>Back to Zapped Chapter List</button>
                            @endif
                        @endif
                    @elseif ($einCondition && ($corConfId != $chConfId) || $inquiriesCondition  && ($corConfId != $chConfId) || $adminReportCondition  && ($corConfId != $chConfId))
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
    var $chPCid = @json($chPCid);
    var $coordId = @json($coordId);
    var $corConfId = @json($corConfId);

$(document).ready(function () {
    // Disable fields for chapters that are not active or EIN & Inquiries Coordinators who are not PC for the Chapter
    if (($chIsActive != 1) || ($inquiriesCondition && ($coordId != $chPCid)) || ($einCondition && ($coordId != $chPCid))) {
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
        $('#ein-letter').prop('disabled', false);
        $('#roster-file').prop('disabled', false);
        $('#back-zapped').prop('disabled', false);
        $('#back-inquiries').prop('disabled', false);
        $('#back-inquiries-zapped').prop('disabled', false);
        $('#back-international').prop('disabled', false);
        $('#back-international-zapped').prop('disabled', false);
        $('#unzap').prop('disabled', false);

    }
});

$(document).ready(function() {
    function loadCoordinatorList(corId) {
        if (corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
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

function updateEIN() {
    const chapterId = '{{ $chapterList->id }}'; // Get the chapter ID from the Blade variable

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

// Function to prompt the user for a new EIN
function promptForNewEIN(chapterId) {
    Swal.fire({
        title: 'Enter EIN',
        html: `
            <p>Please enter the EIN for the chapter.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="ein" name="ein" class="swal2-input" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask placeholder="Enter EIN" required style="width: 100%;">
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
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

            return {
                chapter_id: chapterId,
                ein: ein,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Perform the AJAX request to update the EIN
            $.ajax({
                url: '{{ route('chapters.updateein') }}',
                type: 'POST',
                data: {
                    chapter_id: data.chapter_id,
                    ein: data.ein,
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

function showDisbandChapterModal() {
    Swal.fire({
        title: 'Chapter Disband Reason',
        html: `
            <p>Marking a chapter as disbanded will remove the logins for all board members and remove the chapter. Please enter the reason for disbanding and press OK.</p>
            <div style="display: flex; align-items: center; ">
                <input type="text" id="disband_reason" name="disband_reason" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chapterList->id }}">
            <br>
            <div class="custom-control custom-switch">
                <input type="checkbox" id="disband_letter" class="custom-control-input">
                <label class="custom-control-label" for="disband_letter">Send Standard Disband Letter to Chapter</label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const disbandReason = Swal.getPopup().querySelector('#disband_reason').value;
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const disbandLetter = Swal.getPopup().querySelector('#disband_letter').checked;

            if (!disbandReason) {
                Swal.showValidationMessage('Please enter the reason for disbanding.');
                return false;
            }

            return {
                disband_reason: disbandReason,
                chapter_id: chapterId,
                disband_letter: disbandLetter
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
    });
}

// Function to unzap Chapter via AJAX
function unZapChapter(chapterid) {
    Swal.fire({
        title: 'UnZap Chapter',
        html: `
            <p>Unzapping a chapter will reactivate the logins for all board members and readd the chapter.</p>

            <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chapterList->id }}">

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

</script>
@endsection
