@extends('layouts.coordinator_theme')
<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')


  <!-- Contains page content -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Chapter Details</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
              <li class="breadcrumb-item active">Chapter Details</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterList[0]->statename}}</h3>
                <p class="text-center">{{ $chapterList[0]->confname }} Conference, {{ $chapterList[0]->regname }} Region
                <br>
                EIN: {{$chapterList[0]->ein}}
                <br>
                @if($chapterList[0]->ein_letter_path != null)
                    <button class="btn bg-gradient-primary btn-sm" onclick="window.open('{{ $chapterList[0]->ein_letter_path }}', '_blank')">View/Download EIN Letter</button>
                @else
                    <button class="btn bg-gradient-primary btn-sm disabled">No EIN Letter on File</button>
                @endif
                </p>
                <p class="text-center">
                    <button class="btn bg-gradient-primary btn-sm updateEINBtn" data-ein="{{ $chapterList[0]->ein }}" data-chapter-id="{{ $id }}">Update EIN Number</button>
                    <button class="btn bg-gradient-primary btn-sm showFileUploadModal" data-ein-letter="{{ $chapterList[0]->ein_letter_path }}">Update EIN Letter</button>
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Re-Registration Dues:</b><span class="float-right">
                            @if ($chapterList[0]->members_paid_for)
                                <b>{{ $chapterList[0]->members_paid_for }} Members</b> on <b><span class="date-mask">{{ $chapterList[0]->dues_last_paid }}</span></b>
                            @else
                                No Payment Recorded
                            @endif
                        </span><br>
                        <b>M2M Donation:</b><span class="float-right">
                            @if ($chapterList[0]->m2m_payment)
                                <b>${{ $chapterList[0]->m2m_payment }}</b> on <b><span class="date-mask">{{ $chapterList[0]->m2m_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                        <b>Sustaining Chapter Donation: </b><span class="float-right">
                            @if ($chapterList[0]->sustaining_donation)
                                <b>${{ $chapterList[0]->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chapterList[0]->sustaining_date }}</span></b>
                            @else
                                No Donation Recorded
                            @endif
                        </span><br>
                        @if ($conferenceCoordinatorCondition)
                            <div class="col-md-12 text-center">
                                <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('chapters.chapreregpayment', ['id' => $chapterList[0]->id]) }}'">Enter Payment</button>
                                <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('chapreports.chaprptdonationsview', ['id' => $chapterList[0]->id]) }}'">Enter Donation</button>
                            </div>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <b>Founded:</b> <span class="float-right">{{ $chapterList[0]->start_month_id }} {{ $chapterList[0]->start_year }}</span>
                        <br>
                        <b>Formerly Known As:</b> <span class="float-right">{{ $chapterList[0]->former_name }}</span>
                        <br>
                        <b>Sistered By:</b> <span class="float-right">{{ $chapterList[0]->sistered_by }}</span>
                    </li>
                    <input type="hidden" id="ch_primarycor" value="{{ $chapterList[0]->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                </ul>
                <div class="text-center">
                    @if ($chapterList[0]->is_active == 1 )
                        <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                    @else
                        <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                        Disband Date: <span class="date-mask">{{ $chapterList[0]->zap_date }}</span><br>
                        {{ $chapterList[0]->disband_reason }}
                    @endif
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">General</a></li>
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
                        <button class="btn bg-gradient-primary btn-xs" onclick="window.location.href='{{ route('viewas.viewchapterpresident', ['id' => $chapterList[0]->id]) }}'">View Chapter Profile As President</button>
                    </h3>
                    <div class="row">
                            <div class="col-md-12">
                        Boundaries: {{ $chapterList[0]->territory}}
                        <br>
                        Status: {{$chapterStatusinWords}}
                        <br>
                        Status Notes (not visible to board members): {{ $chapterList[0]->notes}}
                        <br><br>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-md-6">
                        Chpater Email Address: <a href="mailto:{{ $chapterList[0]->email}}">{{ $chapterList[0]->email}}</a>
                        <br>
                        Email used for Inquiries: <a href="mailto:{{ $chapterList[0]->inquiries_contact}}">{{ $chapterList[0]->inquiries_contact}}</a>
                        <br>
                        Inquiries Notes (not visible to board members):<br>
                        {{ $chapterList[0]->inquiries_note}}
                        <br><br>
                    </div>

                        <div class="col-md-6">
                        PO Box/Mailing Address: {{ $chapterList[0]->po_box }}
                        <br>
                        Additional Information (not visible to board members):<br>
                        {!! nl2br(e($chapterList[0]->additional_info)) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        Website: <a href="{{$chapterList[0]->website_url}}" target="_blank">{{$chapterList[0]->website_url}}</a>
                        <br>
                        Webiste Link Status: {{$webStatusinWords}}
                        <br>
                        <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('chapters.chapwebsiteview', ['id' => $chapterList[0]->id]) }}'">Update Website Link Status</button>
                    </div>
                    <div class="col-md-6">
                        Forum/Group/App: {{ $chapterList[0]->egroup}}
                        <br>
                        Facebook: {{ $chapterList[0]->social1}}
                        <br>
                        Twitter: {{ $chapterList[0]->social2}}
                        <br>
                        Instagram: {{ $chapterList[0]->social3}}
                        <br><br>
                    </div>
                </div>

                    <br><br>
                    </div>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="eoy">
                    <div class="eoy-field">
                        <h3 class="profile-username">End of Year Information</h3>
                        @if ($eoyReportConditionDISABLED || ($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes))
                        <div class="row">
                            <div class="col-md-12">
                                Extenstion Given: {{ $chapterList[0]->report_extension == 1 ? 'YES' : 'NO' }}
                                <br>
                                Extension Notes: {{ $chapterList[0]->extension_notes}}
                                @if (($eoyReportCondition && $eoyTestCondition && $testers_yes) || ($eoyReportCondition && $coordinators_yes && $regionalCoordinatorCondition))
                                <br>
                                    <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('eoyreports.eoystatusview', ['id' => $chapterList[0]->id]) }}'">Update EOY Report Status</button>
                                @endif
                                <br><br>
                            </div>
                            <div class="col-md-6">
                                {{ date('Y') . '-' . (date('Y') + 1) }} Board Info Received: {{ $chapterList[0]->new_board_submitted == 1 ? 'YES' : 'NO' }}
                                <br>
                                {{ date('Y') . '-' . (date('Y') + 1) }} Board Activated: {{ $chapterList[0]->new_board_active == 1 ? 'YES' : 'NO' }}
                                <br>
                                @if($chapterList[0]->new_board_active != '1')
                                    <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('eoyreports.eoyboardreportview', ['id' => $chapterList[0]->id]) }}'">View Board Election Report</button>
                                @else
                                    <button class="btn bg-gradient-primary btn-sm disabled">Election Report Not Available</button>
                                @endif
                            </div>
                            <div class="col-md-6">
                                {{ (date('Y') - 1) . '-' . date('Y') }} Financial Report Received: {{ $chapterList[0]->financial_report_received == 1 ? 'YES' : 'NO' }}
                                <br>
                                {{ (date('Y') - 1) . '-' . date('Y') }} Financial Review Completed: {{ $chapterList[0]->financial_report_complete == 1 ? 'YES' : 'NO' }}
                                <br>
                                    <button class="btn bg-gradient-primary btn-sm" onclick="window.location.href='{{ route('eoyreports.eoyfinancialreportview', ['id' => $chapterList[0]->id]) }}'">View Financial Report</button>
                            </div>
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
                          <h3 class="profile-username">{{$chapterList[0]->first_name}} {{$chapterList[0]->last_name}}</h3>
                          <a href="mailto:{{ $chapterList[0]->bd_email }}">{{ $chapterList[0]->bd_email }}</a>
                          <br>
                          <span class="phone-mask">{{$chapterList[0]->phone }}</span>
                          <br><br>
                          {{$chapterList[0]->street_address}}
                          <br>
                          {{$chapterList[0]->city}},{{$chapterList[0]->bd_state}}&nbsp;{{$chapterList[0]->zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $chapterList[0]->user_id }}">Reset President Password</button>
                          </p>
                      </div>
                    </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="avp">
                    @if ($AVPDetails[0]->user_id == '')
                      <div class="avp-field-vacant">
                          <h3 class="profile-username">Administrative Vice President Position is Vacant</h3>
                          <br><br>
                      </div>
                    @else
                      <div class="avp-field">
                          <h3 class="profile-username">{{$AVPDetails[0]->avp_fname}} {{$AVPDetails[0]->avp_lname}}</h3>
                          <a href="mailto:{{ $AVPDetails[0]->avp_email }}">{{ $AVPDetails[0]->avp_email }}</a>
                          <br>
                          <span class="phone-mask">{{$AVPDetails[0]->avp_phone}}</span>
                          <br><br>
                          {{$AVPDetails[0]->avp_addr}}
                          <br>
                          {{$AVPDetails[0]->avp_city}},{{$AVPDetails[0]->avp_state}}&nbsp;{{$AVPDetails[0]->avp_zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $AVPDetails[0]->user_id }}">Reset AVP Password</button>
                        </p>
                    </div>
                    @endif
                    </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="mvp">
                    @if ($MVPDetails[0]->user_id == '')
                      <div class="mvp-field-vacant">
                          <h3 class="profile-username">Membership Vice President Position is Vacant</h3>
                          <br><br>
                      </div>
                    @else
                      <div class="mvp-field">
                          <h3 class="profile-username">{{$MVPDetails[0]->mvp_fname}} {{$MVPDetails[0]->mvp_lname}}</h3>
                          <a href="mailto:{{ $MVPDetails[0]->mvp_email }}">{{ $MVPDetails[0]->mvp_email }}</a>
                          <br>
                          <span class="phone-mask">{{$MVPDetails[0]->mvp_phone}}</span>
                          <br><br>
                          {{$MVPDetails[0]->mvp_addr}}
                          <br>
                          {{$MVPDetails[0]->mvp_city}},{{$MVPDetails[0]->mvp_state}}&nbsp;{{$MVPDetails[0]->mvp_zip}}
                          <br><br>
                          <p>This will reset password to default "TempPass4You" for this user only.
                          <br>
                          <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $MVPDetails[0]->user_id }}">Reset MVP Password</button>
                        </p>
                    </div>
                    @endif
                    </div>
                  <!-- /.tab-pane -->
                    <div class="tab-pane" id="trs">
                        @if ($TRSDetails[0]->user_id == '')
                          <div class="trs-field-vacant">
                              <h3 class="profile-username">Treasury Position is Vacant</h3>
                              <br><br>
                          </div>
                        @else
                          <div class="trs-field">
                              <h3 class="profile-username">{{$TRSDetails[0]->trs_fname}} {{$TRSDetails[0]->trs_lname}}</h3>
                              <a href="mailto:{{ $TRSDetails[0]->trs_email }}">{{ $TRSDetails[0]->trs_email }}</a>
                              <br>
                              <span class="phone-mask">{{$TRSDetails[0]->trs_phone}}</span>
                              <br><br>
                              {{$TRSDetails[0]->trs_addr}}
                              <br>
                              {{$TRSDetails[0]->trs_city}},{{$TRSDetails[0]->trs_state}}&nbsp;{{$TRSDetails[0]->trs_zip}}
                              <br><br>
                              <p>This will reset password to default "TempPass4You" for this user only.
                              <br>
                              <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $TRSDetails[0]->user_id }}">Reset Treasurer Password</button>
                            </p>
                        </div>
                        @endif
                        </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="sec">
                  @if ($SECDetails[0]->user_id == '')
                    <div class="sec-field-vacant">
                        <h3 class="profile-username">Secretary Position is Vacant</h3>
                        <br><br>
                    </div>
                  @else
                    <div class="sec-field">
                        <h3 class="profile-username">{{$SECDetails[0]->sec_fname}} {{$SECDetails[0]->sec_lname}}</h3>
                        <a href="mailto:{{ $SECDetails[0]->sec_email }}">{{ $SECDetails[0]->sec_email }}</a>
                        <br>
                        <span class="phone-mask">{{$SECDetails[0]->sec_phone}}</span>
                        <br><br>
                        {{$SECDetails[0]->sec_addr}}
                        <br>
                        {{$SECDetails[0]->sec_city}},{{$SECDetails[0]->sec_state}}&nbsp;{{$SECDetails[0]->sec_zip}}
                        <br><br>
                        <p>This will reset password to default "TempPass4You" for this user only.
                        <br>
                        <button type="button" class="btn bg-gradient-primary btn-sm reset-password-btn" data-user-id="{{ $SECDetails[0]->user_id }}">Reset Secretary Password</button>
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
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='mailto:{{ $emailListChap }}?cc={{ $emailListCoord }}&subject=MOMS Club of {{ $chapterList[0]->name }}, {{ $chapterList[0]->statename }}'">E-mail Board</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.edit', ['id' => $chapterList[0]->id]) }}'">Update Chapter Information</button>
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.editboard', ['id' => $chapterList[0]->id]) }}'">Update Board Information</button>
                    @if($assistConferenceCoordinatorCondition && $chIsActive == 1)
                        <button type="button" class="btn bg-gradient-primary mb-3" onclick="showDisbandChapterModal()">Disband Chapter</button>
                    @endif
                    @if($assistConferenceCoordinatorCondition && $chIsActive != 1)
                        <button type="button" id="unzap" class="btn bg-gradient-primary mb-3" onclick="unZapChapter()">UnZap Chapter</button>
                    @endif
                    <br>
                    @if ($corConfId == $chConfId)
                        @if ($chIsActive == 1)
                            @if ($inquiriesCondition  && ($coordId != $chPCid))
                                <button type="button" id="back-inquiries" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapinquiries') }}'">Back to Inquiries Chapter List</button>
                            @else
                                <button type="button" id="back" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chaplist') }}'">Back to Chapter List</button>
                            @endif
                        @else
                            @if ($inquiriesCondition  && ($coordId != $chPCid))
                                <button type="button" id="back-inquiries-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapinquiries') }}'">Back to Inquiries Zapped Chapter List</button>
                            @else
                                <button type="button" id="back-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('chapters.chapzapped') }}'">Back to Zapped Chapter List</button>
                            @endif
                        @endif
                    @elseif ($einCondition && ($corConfId != $chConfId) || $inquiriesCondition  && ($corConfId != $chConfId) || $adminReportCondition  && ($corConfId != $chConfId))
                        @if ($chIsActive == 1)
                            <button type="button" id="back-international"class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchapter') }}'">Back to International Chapter List</button>
                        @else
                            <button type="button" id="back-international-zapped" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('international.intchapterzapped') }}'">Back to International Zapped Chapter List</button>
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


document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.updateEINBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const ein = this.getAttribute('data-ein');
        const id = this.getAttribute('data-chapter-id');

        if (!ein) {
            // Prompt for EIN if not already filled
            Swal.fire({
                title: 'Enter EIN',
                input: 'text',
                inputLabel: 'Please enter the EIN for the chapter',
                inputPlaceholder: 'Enter EIN',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    const einValue = result.value;
                    console.log('Entered EIN:', einValue);
                    submitEIN(einValue, id);
                }
            });
        } else {
            Swal.fire({
                title: 'Confirm EIN Change',
                text: 'This chapter already has an assigned EIN. Are you REALLY sure you want to change it?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Change EIN',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Enter New EIN',
                        input: 'text',
                        inputLabel: 'Please enter the new EIN for the chapter',
                        inputPlaceholder: 'Enter New EIN',
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn-sm btn-success',
                            cancelButton: 'btn-sm btn-danger'
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const newEinValue = result.value;
                            console.log('New EIN:', newEinValue);
                            submitEIN(newEinValue, id);
                        }
                    });
                }
            });
        }
    });
});


// Function to submit EIN via AJAX
function submitEIN(ein, chapterId) {
    $.ajax({
        url: `/chapterdetails/updateEIN/${chapterId}`, // Ensure the correct URL is being used
        type: 'POST', // Change PUT to POST
        data: {
            ch_ein: ein,
            _token: '{{ csrf_token() }}' // Pass the CSRF token
        },
        success: function(response) {
            Swal.fire({
                title: 'Success!',
                text: response.message || 'EIN updated successfully.',
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-success my-custom-btn' // Add your custom button class here
                }
            }).then(() => {
                location.reload(); // Reload the page to reflect changes
            });
        },
        error: function(jqXHR, exception) {
            console.log(jqXHR.responseText); // Log error response
            Swal.fire({
                title: 'Error!',
                text: 'Something went wrong. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-danger my-custom-btn' // Add your custom button class here
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
                <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chapterList[0]->id }}">
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
            title: 'Chapter Disband Reason',
            html: `
                <p>Unzapping a chapter will reactivate the logins for all board members and readd the chapter.</p>

                <input type="hidden" id="chapter_id" name="chapter_id" value="{{ $chapterList[0]->id }}">

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






//     $.ajax({
//         url: `/chapter/unzap/${chapterid}`,
//         type: 'POST',
//         data: {
//             _token: '{{ csrf_token() }}' // Pass the CSRF token
//         },
//         success: function(response) {
//             Swal.fire({
//                 title: 'Success!',
//                 text: response.message || 'Chapter Unzapped successfully.',
//                 icon: 'success',
//                 // confirmButtonText: 'OK',
//                 // customClass: {
//                 //     confirmButton: 'btn-sm btn-success my-custom-btn' // Add your custom button class here
//                 // }
//             }).then(() => {
//                 location.reload(); // Reload the page to reflect changes
//             });
//         },
//         error: function(jqXHR, exception) {
//             console.log(jqXHR.responseText); // Log error response
//             Swal.fire({
//                 title: 'Error!',
//                 text: 'Something went wrong. Please try again.',
//                 icon: 'error',
//                 // confirmButtonText: 'OK',
//                 // customClass: {
//                 //     confirmButton: 'btn-sm btn-danger my-custom-btn' // Add your custom button class here
//                 // }
//             });
//         }
//     });
// }



</script>
@endsection
