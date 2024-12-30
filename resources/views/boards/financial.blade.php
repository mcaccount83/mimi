@extends('layouts.board_theme')

@section('content')

<div class="container" id="test">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
         <!-- Widget: user widget style 1 -->
         <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                        </div>
                        <div class="card-body">
                    @php
                        $thisDate = \Carbon\Carbon::now();
                    @endphp
                    <div class="col-md-12"><br><br></div>
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{ $stateShortName }} </h2>
                        <h4 class="text-center"> <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</h4>
                    <div class="col-md-12"><br></div>
                    <h4 class="text-center">
                    @if ($chDocuments->financial_report_received != '1')
                        Please complete the report below with finanacial information about your chapter.<br>
                        Reports are due by July 15th.
                    @else
                        <span style="color: #dc3545">Your chapter's Financial Report has been Submitted!<br>
                        Please save a copy of the PDF for your records.</span><br>
                        <br>
                        <button type="button" id="btn-download-pdf" class="btn btn-primary" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chDocuments->financial_pdf_path }}'"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</button>
                    @endif
                    </h4>
                        </div>
                    </div>
                </div>
            </div>

        <div class="container-fluid">
                @auth
                    <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.storefinancial", $chDetails->id) }}'>
                    @csrf
                    <input type="hidden" id="chapter_id" name="id" value="{{ Session::get('chapterid') }}">
                    {{-- <input type="hidden" name="ch_name" value="<?php echo $chDetails->name; ?>" /> --}}
                    {{-- <input type="hidden" name="ch_state" value="<?php echo $stateShortName; ?>" /> --}}
                    {{-- <input type="hidden" name="ch_pcid" value="<?php echo $chDetails->primary_coordinator_id; ?>" /> --}}
                    {{-- <input type="hidden" name="ch_conf" value="<?php echo $chDetails->conference_id; ?>" /> --}}
                    <input type="hidden" name="submitted" id="submitted" value="<?php echo $submitted; ?>" />
                    <input type="hidden" name="FurthestStep" id="FurthestStep" value="<?php if($chFinancialReport['farthest_step_visited'] > 0) echo $chFinancialReport['farthest_step_visited']; else echo "0"; ?>" />

            <div class="row">
                    <div class="col-12"  id="accordion">
                        <!------Start Step 1 ------>
                        <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '1' ? 'active' : '' }}">
                            <div class="card-header" id="accordion-header-members">
                                <h4 class="card-title w-100">
                                    <a class="d-block" data-toggle="collapse" href="#collapseOne" style="width: 100%;">CHAPTER DUES</a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="collapse {{ $chFinancialReport->farthest_step_visited == '1' ? 'show' : '' }}" data-parent="#accordion">
                                <div class="card-body">
                                    <section>
                        @if (!is_null($chDocuments['roster_path']))
                                <div class="col-md-12" id="RosterBlock">
                                        <label>Chapter Roster Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $chDocuments['roster_path'] }}">&nbsp; View Chapter Roster</a><br>
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace Roster File.<br>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Roster File</button>
                                </div>
                            @else
                                <div class="col-md-12" id="RosterBlock">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload Roster File.<br>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Roster File</button>
                                </div>
                            @endif
                                <input type="hidden" name="RosterPath" id="RosterPath" value="{{ $chDocuments->roster_path }}">
                                <div class="clearfix"></div>
                            <div class="col-md-12"><br></div>
                        <div class="col-md-12 ">
                            <div class="col-12 form-group row">
                                <label>Did your chapter change your dues this year?<span class="field-required">*</span></label>
                                <div class="col-md-12 row">
                                    <div class="form-check" style="margin-right: 20px;">
                                        <input class="form-check-input" type="radio" id="optChangeDuesYes" name="optChangeDues" value="1" {{ $chFinancialReport->changed_dues === 1 ? 'checked' : '' }} onchange="ChapterDuesQuestionsChange()">
                                        <label class="form-check-label" for="optChangeDuesYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="optChangeDuesNo" name="optChangeDues" value="0" {{ $chFinancialReport->changed_dues === 0 ? 'checked' : '' }} onchange="ChapterDuesQuestionsChange()">
                                        <label class="form-check-label" for="optChangeDuesNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 form-group row">
                                <label>Did your chapter charge different amounts for new and returning members?<span class="field-required">*</span></label>
                                <div class="col-md-12 row">
                                    <div class="form-check" style="margin-right: 20px;">
                                        <input class="form-check-input" type="radio" id="optNewOldDifferentYes" name="optNewOldDifferent" value="1" {{ $chFinancialReport->different_dues === 1 ? 'checked' : '' }} onchange="ChapterDuesQuestionsChange()">
                                        <label class="form-check-label" for="optNewOldDifferentYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="optNewOldDifferentNo" name="optNewOldDifferent" value="0" {{ $chFinancialReport->different_dues === 0 ? 'checked' : '' }} onchange="ChapterDuesQuestionsChange()">
                                        <label class="form-check-label" for="optNewOldDifferentNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 form-group row">
                                <label>Did your chapter have any members who didn't pay full dues?<span class="field-required">*</span></label>
                                <div class="col-md-12 row">
                                    <div class="form-check" style="margin-right: 20px;">
                                        <input class="form-check-input" type="radio" id="optNoFullDuesYes" name="optNoFullDues" value="1" {{ $chFinancialReport->not_all_full_dues === 1 ? 'checked' : '' }} onchange="ChapterDuesQuestionsChange()">
                                        <label class="form-check-label" for="optNoFullDuesYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="optNoFullDuesNo" name="optNoFullDues" value="0" {{ $chFinancialReport->not_all_full_dues === 0 ? 'checked' : '' }} onchange="ChapterDuesQuestionsChange()">
                                        <label class="form-check-label" for="optNoFullDuesNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12"><br></div>
                        <div class="form-row">
                           <div class="col-md-12">
                            <p><i>Note: Count all members who paid dues, even if they are not still members.</i></p>
                            </div>
                            <div class="col-md-6 float-left nopadding-l">
                                <div class="form-group">
                                    <label for="TotalNewMembers" id="lblTotalNewMembers">Total New Members (who paid dues)</label>
                                    <div class="">
                                    <input type="number" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalNewMembers" id="TotalNewMembers" value="{{ $chFinancialReport->total_new_members }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                <label for="TotalRenewedMembers" id="lblTotalRenewedMembers">
                                    Total Renewed Members (who paid dues)
                                </label>
                                <div class="">
                                <input type="number" class="form-control " oninput="ChangeMemberCount()" name="TotalRenewedMembers" id="TotalRenewedMembers" value="{{ $chFinancialReport->total_renewed_members }}">
                                </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="ifChangeDues" style="display:none">
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                        <label for="TotalNewMembersNewFee">Total New Members (who paid NEW dues)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control " oninput="ChangeMemberCount()" name="TotalNewMembersNewFee" id="TotalNewMembersNewFee" value="{{ $chFinancialReport->total_new_members_changed_dues }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding">
                                    <div class="form-group">
                                    <label for="TotalRenewedMembersNewFee">Total Renewed Members (who paid NEW dues)</label>
                                    <div class="input-group">
                                    <input type="number"  class="form-control" oninput="ChangeMemberCount()" name="TotalRenewedMembersNewFee" id="TotalRenewedMembersNewFee" value="{{ $chFinancialReport->total_renewed_members_changed_dues }}">
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="MemberDues" id="lblMemberDues">
                                        Member Dues
                                    </label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="MemberDues" oninput="ChangeMemberCount()" id="MemberDues" value="{{ $chFinancialReport->dues_per_member }}"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="ifChangedDues1" style="visibility:hidden">
                                <div class="form-group">
                                    <label for="NewMemberDues" id="lblNewMemberDues">
                                        Member Dues (New Amount)
                                    </label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="NewMemberDues" oninput="ChangeMemberCount()" id="NewMemberDues" value="{{ $chFinancialReport->dues_per_member_new_changed}}"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="ifChangedDuesDifferentPerMemberType" style="display:none">
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                        <label for="MemberDuesRenewal">Renewal Dues</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                        <input type="text" class="form-control " name="MemberDuesRenewal" oninput="ChangeMemberCount()" id="MemberDuesRenewal" value="{{ $chFinancialReport->dues_per_member_renewal }}"
                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding" id="ifChangedDuesDifferentPerMemberType1" style="visibility:hidden">
                                    <div class="form-group">
                                    <label for="NewMemberDuesRenewal">Renewal Dues (NEW Amount)</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="NewMemberDuesRenewal" oninput="ChangeMemberCount()" id="NewMemberDuesRenewal" value="{{ $chFinancialReport->dues_per_member_renewal_changed }}"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                    </div>
                                </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="ifMembersNoDues" style="display:none">
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                        <label for="MembersNoDues">Total Members Who Paid No Dues</label>
                                        <input type="text" class="form-control" name="MembersNoDues" id="MembersNoDues" oninput="ChangeMemberCount()" value="{{ $chFinancialReport->members_who_paid_no_dues }}">
                                    </div>
                                </div>
                                <div class="col-md-6 float-left " style="visibility:hidden"><div class="form-group">
                                        <label for="MembersNoDues">Hidden</label>
                                        <input type="number" class="form-control" value="0">
                                    </div></div>

                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                    <label for="TotalPartialDuesMembers">Total Members Who Paid Partial Dues</label>
                                    <input type="number" class="form-control" name="TotalPartialDuesMembers" id="TotalPartialDuesMembers" oninput="ChangeMemberCount()"value="{{ $chFinancialReport->members_who_paid_partial_dues }}">
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding">
                                    <div class="form-group">
                                    <label for="PartialDuesMemberDues">Total Partial Dues Amount Collected</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                    <input type="text" class="form-control " name="PartialDuesMemberDues" id="PartialDuesMemberDues" oninput="ChangeMemberCount()" value="{{ $chFinancialReport->total_partial_fees_collected }}"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                    </div>
                                </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                    <label for="TotalAssociateMembers">Total Associate Members</label>
                                    <input type="number" class="form-control" name="TotalAssociateMembers" id="TotalAssociateMembers" oninput="ChangeMemberCount()" value="{{ $chFinancialReport->total_associate_members }}">
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding">
                                    <div class="form-group">
                                    <label for="AssociateMemberDues">Associate Member Dues</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="AssociateMemberDues" id="AssociateMemberDues" oninput="ChangeMemberCount()" value="{{ $chFinancialReport->associate_member_fee }}"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                    </div>
                                </div>
                                    </div>
                                </div>
                                <p><small><i>Note: Associate Members are not dues-waived or reduced members. They are a separate category of members. Many chapters do not have any Associate Members, but if your
                                        chapter did have Associate Members this year, how many Associate Members did your chapter have?</i></small></p>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label for="TotalMembers">Total Members</label>
                                    <input type="number" class="form-control" name="TotalMembers" id="TotalMembers" readonly>
                                </div>
                            </div>
                            <div class="col-md-6"><br></div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label for="TotalDues">Total Dues Collected</label>
                                    <div class="form-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                    <input type="text" class="form-control " name="TotalDues" id="TotalDues"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <hr>
                        </div>

                        <div class="card-body text-center">
                                  <button type="button" id="btn-step-1" class="btn btn-primary"  ><i class="fas fa-save" ></i>&nbsp; Save</button>
                        </div>
                        </section>
                    </div>
                </div>
            </div>
                <!------End Step 1 ------>

                <!------Start Step 2 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '2' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTwo" style="width: 100%;">MONTHLY MEETING EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="collapse {{ $chFinancialReport->farthest_step_visited == '2' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                        <section>
                            <div class="col-12 form-row form-group">
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="ManditoryMeetingFeesPaid">Mandatory Meeting Room Fees Paid</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="text"  class="form-control" name="ManditoryMeetingFeesPaid" id="ManditoryMeetingFeesPaid" oninput="ChangeMeetingFees()" value="{{ $chFinancialReport->manditory_meeting_fees_paid }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="VoluntaryDonationsPaid">Voluntary Donations Paid</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="text"  class="form-control" name="VoluntaryDonationsPaid" id="VoluntaryDonationsPaid" oninput="ChangeMeetingFees()" value="{{ $chFinancialReport->voluntary_donations_paid }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="TotalMeetingRoomExpenses">Total Meeting Room Expenses</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" name="TotalMeetingRoomExpenses" id="TotalMeetingRoomExpenses"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>

                            <div class="col-12 form-row form-group">
                                <div class="col-md-12 float-left">
                                <label>13. Did you have speakers at any meetings?<span class="field-required">*</span></label>
                                <div class="col-md-12 form-row">
                                    <div class="form-check" >
                                        <input class="form-check-input" type="radio" id="MeetingSpeakersYes" name="MeetingSpeakers" value="1" {{ $chFinancialReport->meeting_speakers === 1 ? 'checked' : '' }} onchange="ToggleMeetingSpeakersExplanation()">
                                        <label class="form-check-label" for="MeetingSpeakersYes">Yes</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="MeetingSpeakersNo" name="MeetingSpeakers" value="0" {{ $chFinancialReport->meeting_speakers === 0 ? 'checked' : '' }} onchange="ToggleMeetingSpeakersExplanation()">
                                        <label class="form-check-label" for="MeetingSpeakersNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-row" style="margin-left: 15px;" id="divMeetingSpeakersTopics">
                                <label>If yes, check any of the topics that were covered:<span class="field-required">*</span></label>
                                <div class="col-md-12 form-row">
                                    @php
                                        $selectedValues = is_null($chFinancialReport->meeting_speakers_array)
                                            ? []
                                            : json_decode($chFinancialReport->meeting_speakers_array);
                                    @endphp
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="Speakers0" name="Speakers[]" value="0" {{ in_array('0', $selectedValues) ? 'checked' : '' }} >
                                        <label class="form-check-label" for="Speakers0">Child Rearing</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="Speakers1" name="Speakers[]" value="1" {{ in_array('1', $selectedValues) ? 'checked' : '' }} >
                                        <label class="form-check-label" for="Speakers1">Schools/Education</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="Speakers2" name="Speakers[]" value="2" {{ in_array('2', $selectedValues) ? 'checked' : '' }} >
                                        <label class="form-check-label" for="Speakers2">Home Management</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="Speakers3" name="Speakers[]" value="3" {{ in_array('3', $selectedValues) ? 'checked' : '' }} >
                                        <label class="form-check-label" for="Speakers3">Politics</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="Speakers4" name="Speakers[]" value="4" {{ in_array('4', $selectedValues) ? 'checked' : '' }} >
                                        <label class="form-check-label" for="Speakers4">Other Non-Profit</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="Speakers5" name="Speakers[]" value="5" {{ in_array('5', $selectedValues) ? 'checked' : '' }} onchange="ToggleMotherOutingsExplanation()">
                                        <label class="form-check-label" for="Speakers5">Other</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="col-12 form-row form-group">
                                <div class="col-md-12 float-left">
                                <label>Did you have any discussion topics at your meetings? If yes, how often?<span class="field-required">*</span></label>
                                <div class="col-md-12 form-row">
                                    <div class="col-md-12 row">
                                        <div class="form-check" >
                                            <input class="form-check-input" type="radio" id="SpeakerFrequency4" name="SpeakerFrequency" value="4" {{ $chFinancialReport->discussion_topic_frequency === 4 ? 'checked' : '' }} >
                                            <label class="form-check-label" for="SpeakerFrequency4">10+ Times</label>
                                        </div>
                                        <div class="form-check" style="margin-left: 20px;">
                                            <input class="form-check-input" type="radio" id="SpeakerFrequency3" name="SpeakerFrequency" value="3" {{ $chFinancialReport->discussion_topic_frequency === 3 ? 'checked' : '' }} >
                                            <label class="form-check-label" for="SpeakerFrequency3">7-9 Times</label>
                                        </div>
                                        <div class="form-check" style="margin-left: 20px;">
                                            <input class="form-check-input" type="radio" id="SpeakerFrequency2" name="SpeakerFrequency" value="2" {{ $chFinancialReport->discussion_topic_frequency === 2 ? 'checked' : '' }} >
                                            <label class="form-check-label" for="SpeakerFrequency2">4-6 Times</label>
                                        </div>
                                        <div class="form-check" style="margin-left: 20px;">
                                            <input class="form-check-input" type="radio" id="SpeakerFrequency1" name="SpeakerFrequency" value="1" {{ $chFinancialReport->discussion_topic_frequency === 1 ? 'checked' : '' }} >
                                            <label class="form-check-label" for="SpeakerFrequency1">1-3 Times</label>
                                        </div>
                                        <div class="form-check" style="margin-left: 20px;">
                                            <input class="form-check-input" type="radio" id="SpeakerFrequencyNo" name="SpeakerFrequency" value="0" {{ $chFinancialReport->discussion_topic_frequency === 0 ? 'checked' : '' }} >
                                            <label class="form-check-label" for="SpeakerFrequencyNo">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="col-12 form-row form-group">
                                <div class="col-md-12 float-left">
                                <label>Did you have a children’s room with babysitters?<span class="field-required">*</span></label>
                                <div class="col-md-12 form-row">
                                    <div class="form-check" >
                                        <input class="form-check-input" type="radio" id="ChildrensRoomPaid" name="ChildrensRoom" value="2" {{ $chFinancialReport->childrens_room_sitters === 2 ? 'checked' : '' }} >
                                        <label class="form-check-label" for="ChildrensRoomPaid">Yes, with Paid Sitters</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="ChildrensRoomVol" name="ChildrensRoom" value="1" {{ $chFinancialReport->childrens_room_sitters === 1 ? 'checked' : '' }} >
                                        <label class="form-check-label" for="ChildrensRoomVol">Yes, with Volunteer Members</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="ChildrensRoomNo" name="ChildrensRoom" value="0" {{ $chFinancialReport->childrens_room_sitters === 0 ? 'checked' : '' }} >
                                        <label class="form-check-label" for="ChildrensRoomNo">No</label>
                                    </div>
                                </div>
                            </div>
                            </div>

                            <div class="col-12 form-row form-group">
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="PaidBabySitters">Paid Babysitter Expenses (if any)</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" name="PaidBabySitters" id="PaidBabySitters" oninput="ChangeChildrensRoomExpenses()" value="{{ $chFinancialReport->paid_baby_sitters }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 float-left">
                                    {{-- <div class="form-group"> --}}
                                        {{-- <div class="col-md-12"> --}}
                                            <p>
                                                <strong>List all Children's Room Miscellaneous Expenses below.</strong> Briefly describe the expense and list any supplies or other expenses for each project.
                                              </p>
                                        {{-- <label for="childrens-room">
                                            Children's Room Miscellaneous Expenses
                                        </label> --}}
                                        {{-- </div> --}}
                                    {{-- </div> --}}
                                    <table width="100%" class="table table-bordered" id="childrens-room">
                                        <thead>
                                            <tr>
                                                <td>Description</td>
                                                <td>Supplies</td>
                                                <td>Other Expenses</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $childrens_room = null;
                                                $total_supplies = 0;
                                                $total_other_expenses = 0;

                                                if(isset($chFinancialReport['childrens_room_expenses'])){
                                                    $childrens_room = unserialize(base64_decode($chFinancialReport['childrens_room_expenses']));
                                                    $ChildrensExpenseRowCount = is_array($childrens_room) ? count($childrens_room) : 0;
                                                } else {
                                                    $ChildrensExpenseRowCount = 1;
                                                }
                                            @endphp
                                            @for ($row = 0; $row < $ChildrensExpenseRowCount; $row++)
                                                @php
                                                    $supplies = isset($childrens_room[$row]['childrens_room_supplies']) ? floatval(str_replace(['$', ','], '', $childrens_room[$row]['childrens_room_supplies'])) : 0;
                                                    $other_expenses = isset($childrens_room[$row]['childrens_room_other']) ? floatval(str_replace(['$', ','], '', $childrens_room[$row]['childrens_room_other'])) : 0;
                                                    $total_supplies += $supplies;
                                                    $total_other_expenses += $other_expenses;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="ChildrensRoomDesc{{ $row }}" id="ChildrensRoomDesc{{ $row }}" value="{{ $childrens_room[$row]['childrens_room_desc'] ?? '' }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span>
                                                                </div>
                                                                <input type="text" class="form-control" name="ChildrensRoomSupplies{{ $row }}" id="ChildrensRoomSupplies{{ $row }}" oninput="ChangeChildrensRoomExpenses()"
                                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="{{ $childrens_room[$row]['childrens_room_supplies'] ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span>
                                                                </div>
                                                                <input type="text" class="form-control" name="ChildrensRoomOther{{ $row }}" id="ChildrensRoomOther{{ $row }}" oninput="ChangeChildrensRoomExpenses()"
                                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="{{ $childrens_room[$row]['childrens_room_other'] ?? '' }}">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td><strong>Total</strong></td>
                                                <td>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control" value="{{ number_format($total_supplies, 2) }}" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">$</span>
                                                            </div>
                                                            <input type="text" class="form-control" value="{{ number_format($total_other_expenses, 2) }}" readonly>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12 float-left">
                                    <button type="button" class="btn btn-sm btn-success" onclick="AddChildrenExpenseRow()" ><i class="fas fa-plus" ></i>&nbsp; Add Row</button>
                                    <button type="button" class="btn btn-sm btn-danger " onclick="DeleteChildrenExpenseRow()" ><i class="fas fa-minus" ></i>&nbsp; Remove Row</button>
                                </div>
                                <div class="col-md-12"><br></div>
                                <div class="col-md-6 float-left">
                                    <div>
                                        <label for="ChildrensRoomTotal">Total Children's Room Expenses</label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="text"  class="form-control" value="0.00" name="ChildrensRoomTotal"  id="ChildrensRoomTotal"  readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <input type="hidden" name="ChildrensExpenseRowCount" id="ChildrensExpenseRowCount" value="{{ $ChildrensExpenseRowCount }}" />
                            <div class="card-body text-center">
                                <button type="submit" id="btn-step-2" class="btn btn-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
                <!------End Step 2 ------>

                <!------Start Step 3 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '3' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseThree" style="width: 100%;">SERVICE PROJECTS</a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="collapse {{ $chFinancialReport->farthest_step_visited == '3' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                    <section>
                        <div class="col-12 form-row form-group">
                          <p>
                            A Service Project is one that benefits others OUTSIDE your chapter. However, a Service Project may also be a project to benefit a member-in-distress or one who has special emergency needs, if the needs are the reason for the project. For example, a fundraiser may benefit the International MOMS Club’s Mother-to-Mother Fund or may be used to help pay extreme medical expenses for a life-threatening illness suffered by a member’s child. (Any fundraisers or projects that benefited your chapter or members who are not suffering emergency or devastating situations should not be listed here. Those should be listed in Step 7.)
                          </p>
                          <p>
                            Not all Service Projects are fundraisers! If you did a Service Project that was not a fundraiser, you will have expenses listed here, but no income for that project. If your chapter made a donation from the treasury to another charity and used treasury money collected as dues (instead of money raised by your chapter for the donation), you will have expenses listed (the donation), but no income for that project.
                          </p>
                        </div>

                          <div class="col-12 form-row form-group">
                            <label>Did your chapter perform at least one service project to benefit mothers or children?<span class="field-required">*</span></label>
                            <div class="col-md-12 form-row">
                                <div class="form-check" >
                                    <input class="form-check-input" type="radio" id="PerformServiceProjectYes" name="PerformServiceProject" value="1" {{ $chFinancialReport->at_least_one_service_project === 1 ? 'checked' : '' }} onchange="TogglePerformServiceProjectExplanation()">
                                    <label class="form-check-label" for="PerformServiceProjectYes">Yes</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="PerformServiceProjectNo" name="PerformServiceProject" value="0" {{ $chFinancialReport->at_least_one_service_project === 0 ? 'checked' : '' }} onchange="TogglePerformServiceProjectExplanation()">
                                    <label class="form-check-label" for="PerformServiceProjectNo">No</label>
                                </div>
                            </div>
                        <div class="col-md-12 "  id="divPerformServiceProjectExplanation" >
                            <div class="col-sm-12" >
                                <label for="PerformServiceProjectExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="2" name="PerformServiceProjectExplanation" id="PerformServiceProjectExplanation">{{ $chFinancialReport->at_least_one_service_project_explanation }}</textarea>
                            </div>
                        </div>
                </div>

                <div class="col-12 form-row form-group">
                            <label>Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity?<span class="field-required">*</span></label>
                            <div class="col-md-12 form-row">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="ContributionsNotRegNPYes" name="ContributionsNotRegNP" value="1" {{ $chFinancialReport->contributions_not_registered_charity === 1 ? 'checked' : '' }} onchange="ToggleContributionsNotRegNPExplanation()">
                                    <label class="form-check-label" for="ContributionsNotRegNPYes">Yes</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="ContributionsNotRegNPNo" name="ContributionsNotRegNP" value="0" {{ $chFinancialReport->contributions_not_registered_charity === 0 ? 'checked' : '' }} onchange="ToggleContributionsNotRegNPExplanation()">
                                    <label class="form-check-label" for="ContributionsNotRegNPNo">No</label>
                                </div>
                        </div>
                        <div class="col-md-12" id="divContributionsNotRegNPExplanation" >
                            <div class="col-sm-12" >
                                <label for="ContributionsNotRegNPExplanation">If yes, please explain who received the contributions and why you chose them:<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="2" name="ContributionsNotRegNPExplanation" id="ContributionsNotRegNPExplanation">{{ $chFinancialReport->contributions_not_registered_charity_explanation }}</textarea>
                            </div>
                        </div>
                        </div>

                        <div class="col-12 form-row form-group">
                          <p>
                            <strong>List all Service Projects below, even if there was no income or expense.</strong> Briefly describe the project and who was benefited by it. List any income and expenses for each project.
                          </p>
                        <div class="col-md-12 nopadding">
                            <table width="100%" class="table table-bordered" id="service-projects">
                                <thead>
                                    <tr>
                                        <td width="36%">Project Description<span class="field-required">*</span></td>
                                        <td width="16%">Income</td>
                                        <td width="16%">Supplies & Expenses</td>
                                        <td width="16%">Charity Donation</td>
                                        <td width="16%">M2M Donation</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $service_projects = null;
                                        $total_income = 0;
                                        $total_expenses = 0;
                                        $total_charity = 0;
                                        $total_m2m = 0;

                                        if(isset($chFinancialReport['service_project_array'])){
                                            $service_projects = unserialize(base64_decode($chFinancialReport['service_project_array']));
                                            $ServiceProjectRowCount = is_array($service_projects) ? count($service_projects) : 0;
                                        } else {
                                            $ServiceProjectRowCount = 1;
                                        }
                                    @endphp
                                    @for ($row = 0; $row < $ServiceProjectRowCount; $row++)
                                    @php
                                        $income = isset($service_projects[$row]['service_project_income']) ? floatval(str_replace(['$', ','], '', $service_projects[$row]['service_project_income'])) : 0;
                                        $expenses = isset($service_projects[$row]['service_project_supplies']) ? floatval(str_replace(['$', ','], '', $service_projects[$row]['service_project_supplies'])) : 0;
                                        $charity = isset($service_projects[$row]['service_project_charity']) ? floatval(str_replace(['$', ','], '', $service_projects[$row]['service_project_charity'])) : 0;
                                        $m2m = isset($service_projects[$row]['service_project_m2m']) ? floatval(str_replace(['$', ','], '', $service_projects[$row]['service_project_m2m'])) : 0;
                                        $total_income += $income;
                                        $total_expenses += $expenses;
                                        $total_charity += $charity;
                                        $total_m2m += $m2m;
                                    @endphp
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <textarea class="form-control" rows="2" name="ServiceProjectDesc{{ $row }}" id="ServiceProjectDesc{{ $row }}">{{ $service_projects[$row]['service_project_desc'] ?? '' }}</textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="ServiceProjectIncome{{ $row }}" id="ServiceProjectIncome{{ $row }}" oninput="ChangeServiceProjectExpenses()"
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="{{ $service_projects[$row]['service_project_income'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="ServiceProjectSupplies{{ $row }}" id="ServiceProjectSupplies{{ $row }}" oninput="ChangeServiceProjectExpenses()"
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="{{ $service_projects[$row]['service_project_supplies'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="ServiceProjectDonatedCharity{{ $row }}" id="ServiceProjectDonatedCharity{{ $row }}" oninput="ChangeServiceProjectExpenses()"
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="{{ $service_projects[$row]['service_project_charity'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="ServiceProjectDonatedM2M{{ $row }}" id="ServiceProjectDonatedM2M{{ $row }}" oninput="ChangeServiceProjectExpenses()"
                                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" value="{{ $service_projects[$row]['service_project_m2m'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><strong>Total</strong></td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ number_format($total_income, 2) }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ number_format($total_expenses, 2) }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ number_format($total_charity, 2) }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">$</span>
                                                    </div>
                                                    <input type="text" class="form-control" value="{{ number_format($total_m2m, 2) }}" readonly>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-12 float-left">
                            <button type="button" class="btn btn-sm btn-success" onclick="AddServiceProjectRow()" ><i class="fas fa-plus" ></i>&nbsp; Add Row</button>
                            <button type="button" class="btn btn-sm btn-danger " onclick="DeleteServiceProjectRow()" ><i class="fas fa-minus" ></i>&nbsp; Remove Row</button>
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-6 float-left">
                            <div class="form-group">
                                <label for="ServiceProjectIncomeTotal">
                                    Total Service Project Income
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectIncomeTotal"  id="ServiceProjectIncomeTotal" readonly>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6"><br></div>
                        <div class="col-md-6 float-left">
                            <div class="form-group">
                                <label for="ServiceProjectExpenseTotal">
                                    Total Service Project Expenses
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectExpenseTotal"  id="ServiceProjectExpenseTotal" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <input type="hidden" name="ServiceProjectRowCount" id="ServiceProjectRowCount" value="{{ $ServiceProjectRowCount }}" />

                    <div class="card-body text-center">
                          <button type="button" class="btn btn-primary" id="btn-step-3" ><i class="fas fa-save" ></i>&nbsp; Save</button>
                    </div>
                </section>
                </div>
                </div>
            </div>
                <!------End Step 3 ------>

                <!------Start Step 4 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '4' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFour" style="width: 100%;">PARTIES & MEMBER BENEFITS</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="collapse {{ $chFinancialReport->farthest_step_visited == '4' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                <section>
                    <div class="col-12 form-row form-group">
                      <p>
                        If your members paid to attend any parties or members-only fun activities organized by your chapter, enter the amounts they paid your chapter here. For example, if your members paid money to the chapter to attend a Museum Day or Halloween Party, include those payments here. Include all Year-End Chapter Banquet income in this section. Do not include any money paid to or from your chapter for reservations at International MOMS Club events like Regional/State Luncheons or Workshops – that is listed in Step 5.
                      </p>
                      <p>
                        <i>
                          (<strong>Note:</strong> If there are multiple entries for a party in your books, group all that one party’s income into one entry for this report – for example if your members were charged for your Holiday Party, add all the income from the Holiday Party together and write one entry for that party here.)
                        </i>
                      </p>
                      <p>
                        If your chapter had any expenses for <strong>parties and/or members-only fun activities or expenses,</strong> enter the amounts here. Group all expenses for any party/activity into one entry for each event. (For example, one amount for all expenses for Holiday Party, one amount for Year-End Banquet, etc…) If everything was donated for the event, you will not have any expense listed here. You may list parties/activities with no expense, but be sure to explain that everything was potluck or donated in the description column, and put $0 in the expense column for that event. Also list gifts bought for members, members-only crafts, and/or refreshments for members-only activities below (modest gifts for volunteer recognition can be listed under “Other” Expenses).
                      </p>
                      <table id="party-expenses" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Party Name/Description (include date)</td>
                                <td>Income</td>
                                <td>Expenses</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $party_expenses = null;
                                $total_income = 0;
                                $total_expenses = 0;

                                if (isset($chFinancialReport['party_expense_array'])) {
                                    $party_expenses = unserialize(base64_decode($chFinancialReport['party_expense_array']));
                                    $PartyExpenseRowCount = is_array($party_expenses) ? count($party_expenses) : 0;
                                } else {
                                    $PartyExpenseRowCount = 1;
                                }
                            @endphp
                            @for ($row = 0; $row < $PartyExpenseRowCount; $row++)
                            @php
                                $income = isset($party_expenses[$row]['party_expense_income']) ? floatval(str_replace(['$', ','], '', $party_expenses[$row]['party_expense_income'])) : 0;
                                $expenses = isset($party_expenses[$row]['party_expense_expenses']) ? floatval(str_replace(['$', ','], '', $party_expenses[$row]['party_expense_expenses'])) : 0;
                                $total_income += $income;
                                $total_expenses += $expenses;
                            @endphp

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="PartyDesc{{ $row }}" id="PartyDesc{{ $row }}" value="{{ $party_expenses[$row]['party_expense_desc'] ?? '' }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="PartyIncome{{ $row }}" id="PartyIncome{{ $row }}" oninput="ChangePartyExpenses()" value="{{ $party_expenses[$row]['party_expense_income'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="PartyExpenses{{ $row }}" id="PartyExpenses{{ $row }}" oinput="ChangePartyExpenses()" value="{{ $party_expenses[$row]['party_expense_expenses'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ number_format($total_income, 2) }}" readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ number_format($total_expenses, 2) }}" readonly>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                        <div class="col-md-12">
                            <button type="button" class="btn btn-sm btn-success" onclick="AddPartyExpenseRow()" ><i class="fas fa-plus" ></i>&nbsp; Add Row</button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="DeletePartyExpenseRow()" ><i class="fas fa-minus" ></i>&nbsp; Remove Row</button>
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-6 float-left">
                            <div class="form-group">
                                <label for="PartyIncomeTotal">
                                    Total Benefit Income
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="PartyIncomeTotal" id="PartyIncomeTotal"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6"><br></div>
                        <div class="col-md-6 float-left">
                            <div class="form-group">
                                <label for="PartyExpenseTotal">
                                    Total Benefit Expenses
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="PartyExpenseTotal" id="PartyExpenseTotal"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                            </div>
                        </div>
                        </div>
                        <hr>
                    </div>
                    <input type="hidden" name="PartyExpenseRowCount" id="PartyExpenseRowCount" value="{{ $PartyExpenseRowCount }}" />
                    <div class="card-body text-center">
                        <button type="submit" id="btn-step-4" class="btn btn-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                    </div>
                </section>
                </div>
                </div>
            </div>
                <!------End Step 4 ------>

                <!------Start Step 5 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '5' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFive" style="width: 100%;">OFFICE & OPERATING EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseFive" class="collapse {{ $chFinancialReport->farthest_step_visited == '5' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                    <section>
                    <div class="col-12 form-row form-group">
                      <p>
                        Use this section to list individually any Office Expenses or other Operating Expenses. Please include only one expense type per line (i.e. website hosting, advertising, etc.).
                      </p>
                    <div class="col-md-4 float-left">
                        <div class="form-group">
                            <label for="PrintingCosts">
                                Printing Costs
                            </label>
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                            <input type="text" class="form-control" name="PrintingCosts" id="PrintingCosts" oninput="ChangeOfficeExpenses()" value="{{ $chFinancialReport->office_printing_costs }}"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-4 float-left">
                        <div class="form-group">
                            <label for="PostageCosts">
                                Postage Costs
                            </label>
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                            <input type="text" class="form-control" name="PostageCosts" id="PostageCosts" oninput="ChangeOfficeExpenses()" value="{{ $chFinancialReport->office_postage_costs }}"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-4 float-left">
                        <div class="form-group">
                            <label for="MembershipPins">
                                Membership Pins
                            </label>
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                            <input type="text" class="form-control" name="MembershipPins" id="MembershipPins" oninput="ChangeOfficeExpenses()" value="{{ $chFinancialReport->office_membership_pins_cost }}"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                            </div>
                        </div>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <label for="office-expenses">
                            Other Office & Operating Expenses
                        </label>
                    </div>
                    <table id="office-expenses" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Description of Expense</td>
                                <td>Expenses</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $other_office_expenses = null;
                                $total_expenses = 0;

                                if (isset($chFinancialReport['office_other_expenses'])) {
                                    $other_office_expenses = unserialize(base64_decode($chFinancialReport['office_other_expenses']));
                                    $OfficeExpenseRowCount = is_array($other_office_expenses) ? count($other_office_expenses) : 0;
                                } else {
                                    $OfficeExpenseRowCount = 1;
                                }
                            @endphp
                            @for ($row = 0; $row < $OfficeExpenseRowCount; $row++)
                            @php
                                $expenses = isset($other_office_expenses[$row]['office_other_expense']) ? floatval(str_replace(['$', ','], '', $other_office_expenses[$row]['office_other_expense'])) : 0;
                                $total_expenses += $expenses;
                            @endphp
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="OfficeDesc{{ $row }}" id="OfficeDesc{{ $row }}" value="{{ $other_office_expenses[$row]['office_other_desc'] ?? '' }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="OfficeExpenses{{ $row }}" id="OfficeExpenses{{ $row }}" oninput="ChangeOfficeExpenses()" value="{{ $other_office_expenses[$row]['office_other_expense'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ number_format($total_expenses, 2) }}" readonly>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="col-md-12 float-left">
                        <button type="button" class="btn btn-sm btn-success " onclick="AddOfficeExpenseRow()" ><i class="fas fa-plus" ></i>&nbsp; Add Row</button>
                        <button type="button" class="btn btn-sm btn-danger " onclick="DeleteOfficeExpenseRow()" ><i class="fas fa-minus" ></i>&nbsp; Remove Row</button>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-6 float-left">
                        <div class="form-group">
                            <label for="TotalOperatingExpense">
                                Total Office & Operating Expenses
                            </label>
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                            <input type="text" class="form-control" name="TotalOperatingExpense" id="TotalOperatingExpense"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="OfficeExpenseRowCount" id="OfficeExpenseRowCount" value="{{ $OfficeExpenseRowCount }}" />
                <div class="card-body text-center">
                          <button type="button" id="btn-step-5" class="btn btn-primary" onSubmit="this.form.submit(); this.disabled=true;" ><i class="fas fa-save" ></i>&nbsp; Save</button>
                </div>
                </section>
                </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
        </div>
            <!------End Step 5 ------>

            <!------Start Step 6 ------>
            <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '6' ? 'active' : '' }}">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseSix" style="width: 100%;">INTERNATIONAL EVENTS & RE-REGISTRATION</a>
                    </h4>
                </div>
                <div id="collapseSix" class="collapse {{ $chFinancialReport->farthest_step_visited == '6' ? 'show' : '' }}" data-parent="#accordion">
                    <div class="card-body">
                    <section>
                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <p>
                                    Annual Chapter Registration Fee paid to International MOMS Club.  Due by the end of your chapter's anniversary month each year.
                                  </p>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label for="AnnualRegistrationFee">
                                        Chapter Registration Fee<span class="field-required">*</span>
                                    </label>
                                        <div class="form-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="AnnualRegistrationFee" id="AnnualRegistrationFee" oninput="ChangeReRegistrationExpense()" value="{{ $chFinancialReport->annual_registration_fee }}"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" >
                                </div>
                                </div>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12"><br></div>

                    <div class="col-12 form-row form-group">
                        <p>
                            International Events include any State/Regional/Conference Luncheons, Workshops or other events sponsored or organized by the International MOMS Club.  These events could be attended In Person or Virtually.
                        </p>
                        <p>
                        “Event income” includes all money paid to your treasury by members for their reservations at those events and any income from fundraisers held to help offset the expense of members attending an International event. Also include any donations to your chapter to help build your raffle basket or chapter display.
                    </p>
                    <p>
                        “Event expenses” includes all money paid by your treasury to International for reservations or for members attending the event, raffle basket and display expenses, and any travel costs. If your chapter paid for all reservations from its treasury and did not charge members for attending, it will have expenses, but no income, in this category.
                        </p>
                        </div>

                        <div class="col-12 form-row form-group">
                            <label>Did your chapter attend an International Event (in person or virtual)?<span class="field-required">*</span></label>
                            <div class="col-md-12 form-row">
                                <div class="form-check" >
                                    <input class="form-check-input" type="radio" id="InternationalEventYes" name="InternationalEvent" value="1" {{ $chFinancialReport->international_event === 1 ? 'checked' : '' }} onchange="ToggleInternationalEventExplanation()">
                                    <label class="form-check-label" for="InternationalEventYes">Yes</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="InternationalEventNo" name="InternationalEvent" value="0" {{ $chFinancialReport->international_event === 0 ? 'checked' : '' }} onchange="ToggleInternationalEventExplanation()">
                                    <label class="form-check-label" for="SInternationalEventNo">No</label>
                                </div>
                            </div>
                        </div>


                    <div class="col-12 form-row form-group">
                        <p>
                          <strong>List all Event Information.</strong> Briefly describe the event and list any income and expenses.
                        </p>
                    <div class="form-group">
                       <label for="">
                            International Event Registrations
                       </label>
                    </div>
                    <table id="international_events" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Description</td>
                                <td>Income</td>
                                <td>Expenses</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $international_event_array = null;
                                $total_income = 0;
                                $total_expenses = 0;

                                if (isset($chFinancialReport['international_event_array'])) {
                                    $international_event_array = unserialize(base64_decode($chFinancialReport['international_event_array']));
                                    $InternationalEventRowCount = is_array($international_event_array) ? count($international_event_array) : 0;
                                } else {
                                    $InternationalEventRowCount = 1;
                                }
                            @endphp
                            @for ($row = 0; $row < $InternationalEventRowCount; $row++)
                            @php
                                $income = isset($international_event_array[$row]['intl_event_income']) ? floatval(str_replace(['$', ','], '', $international_event_array[$row]['intl_event_income'])) : 0;
                                $expenses = isset($international_event_array[$row]['intl_event_expenses']) ? floatval(str_replace(['$', ','], '', $international_event_array[$row]['intl_event_expenses'])) : 0;
                                $total_income += $income;
                                $total_expenses += $expenses;
                            @endphp
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="InternationalEventDesc{{ $row }}" id="InternationalEventDesc{{ $row }}"
                                                value="{{ $international_event_array[$row]['intl_event_desc'] ?? '' }}"
                                                onkeypress="if(this.value.length==30) return false;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="InternationalEventIncome{{ $row }}" id="InternationalEventIncome{{ $row }}" oninput="ChangeInternationalEventExpense()" value="{{ $international_event_array[$row]['intl_event_income'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="InternationalEventExpense{{ $row }}" id="InternationalEventExpense{{ $row }}" oninput="ChangeInternationalEventExpense()" value="{{ $international_event_array[$row]['intl_event_expenses'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><strong>Total</strong></td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ number_format($total_income, 2) }}" readonly>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ number_format($total_expenses, 2) }}" readonly>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="col-md-12 float-left">
                    <button type="button" class="btn btn-sm btn-success" onclick="AddInternationalEventRow()" ><i class="fas fa-plus" ></i>&nbsp; Add Row</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="DeleteInternationalEventRow()" ><i class="fas fa-minus" ></i>&nbsp; Remove Row</button>
                    </div>
                    <hr>
                </div>
                <div class="col-md-12"><br></div>
                <div class="col-12 form-row form-group">
                <div class="col-md-6 float-left">
                    <div class="form-group">
                        <label for="InternationalEventIncomeTotal">
                            Total Event Registration Income
                        </label>
                        <div class="form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                        <input type="text" class="form-control" name="InternationalEventIncomeTotal" id="InternationalEventIncomeTotal"
                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                    </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6"><br></div>
                <div class="col-md-6 float-left">
                    <div class="form-group">
                        <label for="InternationalEventExpenseTotal">
                            Total Event Registration Expenses
                        </label>
                        <div class="form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                        <input type="text" class="form-control" name="InternationalEventExpenseTotal" id="InternationalEventExpenseTotal"
                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                    </div>
                    </div>
                </div>
                </div>
                <hr>
                </div>
                <input type="hidden" name="InternationalEventRowCount" name="InternationalEventRowCount" id="InternationalEventRowCount" value="{{ $InternationalEventRowCount }}" />

                <div class="card-body text-center">
                          <button type="button" id="btn-step-6" class="btn btn-primary" onSubmit="this.form.submit(); this.disabled=true;" ><i class="fas fa-save" ></i>&nbsp; Save</button>
                </div>
                </section>
                </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
        </div>
            <!------End Step 6 ------>

            <!------Start Step 7 ------>
            <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '7' ? 'active' : '' }}">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseSeven" style="width: 100%;">DONATIONS TO YOUR CHAPTER</a>
                    </h4>
                </div>
                <div id="collapseSeven" class="collapse {{ $chFinancialReport->farthest_step_visited == '7' ? 'show' : '' }}" data-parent="#accordion">
                    <div class="card-body">
                <section>
                <div class="col-12 form-row form-group">
                    <label for="donation-income">
                        Monetary Donations:
                    </label>
                    <p>For each donation of money (cash or checks), please list Donor Name, Address, Date of Donation and Amount. If the money was donated for a specific purpose, list that, too. If you received grants, include that income here.</p>
                    <table id="donation-income" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Purpose of Donation/How it was Used</td>
                                <td>Donor Name & Address</td>
                                <td>Date</td>
                                <td>Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $monetary_dontations_to_chapter = null;
                                $total_income = 0;

                                if (isset($chFinancialReport['monetary_donations_to_chapter'])) {
                                    $monetary_dontations_to_chapter = unserialize(base64_decode($chFinancialReport['monetary_donations_to_chapter']));
                                    $MonDonationRowCount = is_array($monetary_dontations_to_chapter) ? count($monetary_dontations_to_chapter) : 0;
                                } else {
                                    $MonDonationRowCount = 1;
                                }
                            @endphp
                            @for ($row = 0; $row < $MonDonationRowCount; $row++)
                            @php
                                $income = isset($monetary_dontations_to_chapter[$row]['mon_donation_amount']) ? floatval(str_replace(['$', ','], '', $monetary_dontations_to_chapter[$row]['mon_donation_amount'])) : 0;
                                $total_income += $income;
                            @endphp
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="DonationDesc{{ $row }}" id="DonationDesc{{ $row }}" value="{{ $monetary_dontations_to_chapter[$row]['mon_donation_desc'] ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="DonorInfo{{ $row }}" id="DonorInfo{{ $row }}" value="{{ $monetary_dontations_to_chapter[$row]['mon_donation_info'] ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="MonDonationDate{{ $row }}" id="MonDonationDate{{ $row }}" value="{{ $monetary_dontations_to_chapter[$row]['mon_donation_date'] ?? '' }}"
                                            data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask >
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" name="DonationAmount{{ $row }}" id="DonationAmount{{ $row }}" oninput="ChangeDonationAmount()" value="{{ $monetary_dontations_to_chapter[$row]['mon_donation_amount'] ?? '' }}"
                                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total</strong></td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input type="text" class="form-control" value="{{ number_format($total_income, 2) }}" readonly>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-success " onclick="AddMonDonationRow()" ><i class="fas fa-plus" ></i>&nbsp; Add Row</button>
                        <button type="button" class="btn btn-sm btn-danger " onclick="DeleteMonDonationRow()" ><i class="fas fa-minus" ></i>&nbsp; Remove Row</button>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-6 float-left">
                    <div class="form-group">
                        <label for="DonationTotal">Monetary Donation Total</label>
                        <div class="form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                        <input type="text" class="form-control"  name="DonationTotal" id="DonationTotal"
                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                    </div>
                    </div>
                    </div>
                    <input type="hidden" name="MonDonationRowCount" id="MonDonationRowCount" value="{{ $MonDonationRowCount }}" />
                </div>
                </div>
                <div class="col-md-12"><br></div>
                <div class="col-12 form-row form-group">
                    <label for="donation-goods">
                        Non-Monetary Donations:
                    </label>
                    <p>For each donation, please list Donor Name, Address, Date of Donation, Items Donated, and purpose of donation. Do not include a value for items donated except in the case of gift cards. Do list the value of any gift cards received in the description.</p>
                    <table id="donation-goods" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Item & Purpose of Donation/How it was Used</td>
                                <td>Donor Name & Address</td>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $non_monetary_dontations_to_chapter = null;
                                if (isset($chFinancialReport['non_monetary_donations_to_chapter'])) {
                                    $non_monetary_dontations_to_chapter = unserialize(base64_decode($chFinancialReport['non_monetary_donations_to_chapter']));
                                    $NonMonDonationRowCount = is_array($non_monetary_dontations_to_chapter) ? count($non_monetary_dontations_to_chapter) : 0;
                                } else {
                                    $NonMonDonationRowCount = 1;
                                }
                            @endphp

                            @for ($row = 0; $row < $NonMonDonationRowCount; $row++)
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="NonMonDonationDesc{{ $row }}" id="NonMonDonationDesc{{ $row }}" value="{{ $non_monetary_dontations_to_chapter[$row]['nonmon_donation_desc'] ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="NonMonDonorInfo{{ $row }}" id="NonMonDonorInfo{{ $row }}" value="{{ $non_monetary_dontations_to_chapter[$row]['nonmon_donation_info'] ?? '' }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="NonMonDonationDate{{ $row }}" id="NonMonDonationDate{{ $row }}" value="{{ $non_monetary_dontations_to_chapter[$row]['nonmon_donation_date'] ?? '' }}"
                                            data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask >
                                    </div>
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div class="col-md-12 float-left">
                        <button type="button" class="btn btn-sm btn-success " onclick="AddNonMonDonationRow()">
                            <i class="fas fa-plus"></i>&nbsp; Add Row
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="DeleteNonMonDonationRow()">
                            <i class="fas fa-minus"></i>&nbsp; Remove Row
                        </button>
                    </div>

                    <input type="hidden" name="NonMonDonationRowCount" id="NonMonDonationRowCount" value="{{ $NonMonDonationRowCount }}" />
                    <hr>
                    </div>
                    <div class="card-body text-center">
                              <button type="submit" id="btn-step-7" class="btn btn-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                    </div>
                </section>
                </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
            </div>
                <!------End Step 7 ------>

                <!------Start Step 8 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '8' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseEight" style="width: 100%;">OTHER INCOME & EXPENSES</a>
                        </h4>
                    </div>
                    <div id="collapseEight" class="collapse {{ $chFinancialReport->farthest_step_visited == '8' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                    <section>
                    <div class="col-12 form-row form-group">

                        <p>If your chapter had any other income not listed elsewhere, enter those amounts and descriptions here. (If there are multiple entries of one type of income in your books, please group them together as one total for that type of entry below. For example, if local businesses paid for advertising in your newsletter, enter one amount for all the advertising sold by your chapter during the year.)</p>
                        <p>Use this section to list any fundraisers your chapter may have had to benefit the chapter or the members. If your chapter participated in any programs offering rebates, matching contributions or bonus cards, include that information here.</p>

                        <table id="other-office-expenses" width="100%" class="table table-bordered">
                            <thead>
                                <tr>
                                    <td>Description of Expense/Income</td>
                                    <td>Income</td>
                                    <td>Expenses</td>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $other_income_and_expenses_array = null;
                                    $total_income = 0;
                                    $total_expenses = 0;

                                    if (isset($chFinancialReport['other_income_and_expenses_array'])) {
                                        $other_income_and_expenses_array = unserialize(base64_decode($chFinancialReport['other_income_and_expenses_array']));
                                        $OtherOfficeExpenseRowCount = is_array($other_income_and_expenses_array) ? count($other_income_and_expenses_array) : 0;
                                    } else {
                                        $OtherOfficeExpenseRowCount = 2;
                                    }
                                @endphp
                                @for ($row = 1; $row < $OtherOfficeExpenseRowCount; $row++)
                                @php
                                    $income = isset($other_income_and_expenses_array[$row]['other_income']) ? floatval(str_replace(['$', ','], '', $other_income_and_expenses_array[$row]['other_income'])) : 0;
                                    $expenses = isset($other_income_and_expenses_array[$row]['other_expenses']) ? floatval(str_replace(['$', ','], '', $other_income_and_expenses_array[$row]['other_expenses'])) : 0;
                                    $total_income += $income;
                                    $total_expenses += $expenses;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="OtherOfficeDesc{{ $row }}" id="OtherOfficeDesc{{ $row }}"
                                                value="{{ $other_income_and_expenses_array[$row]['other_desc'] ?? '' }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="OtherOfficeIncome{{ $row }}" id="OtherOfficeIncome{{ $row }}"
                                                    oninput="ChangeOtherOfficeExpenses()" value="{{ $other_income_and_expenses_array[$row]['other_income'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="OtherOfficeExpenses{{ $row }}" id="OtherOfficeExpenses{{ $row }}"
                                                    oninput="ChangeOtherOfficeExpenses()" value="{{ $other_income_and_expenses_array[$row]['other_expenses'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" value="{{ number_format($total_income, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" value="{{ number_format($total_expenses, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="col-md-12 float-left">
                            <button type="button" class="btn btn-sm btn-success" onclick="AddOtherOfficeExpenseRow()">
                                <i class="fas fa-plus"></i>&nbsp; Add Row
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="DeleteOtherOfficeExpenseRow()">
                                <i class="fas fa-minus"></i>&nbsp; Remove Row
                            </button>
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="OtherOfficeIncomeTotal">
                                    Total Other Income
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="OtherOfficeIncomeTotal" id="OtherOfficeIncomeTotal"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6"><br></div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="OtherOfficeExpenseTotal">
                                    Total Other Expenses
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="OtherOfficeExpenseTotal" id="OtherOfficeExpenseTotal"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                            </div>
                            </div>
                       </div>
                       <input type="hidden" name="OtherOfficeExpenseRowCount" id="OtherOfficeExpenseRowCount" value="{{ $OtherOfficeExpenseRowCount }}"/>
                        <hr>
                    </div>
                    <div class="card-body text-center">
                              <button type="submit" id="btn-step-8" class="btn btn-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                    </div>
                </section>
                </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
            </div>
                <!------End Step 8 ------>

                <!------Start Step 9 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '9' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseNine" style="width: 100%;">FINANCIAL SUMMARY</a>
                    </h4>
                </div>
                <div id="collapseNine" class="collapse {{ $chFinancialReport->farthest_step_visited == '9' ? 'show' : '' }}" data-parent="#accordion">
                    <div class="card-body">
                <section>
                <div class="col-12 form-row form-group">
                  <div class="col-sm-12">
                    <h3>July 1, <?php echo date('Y')-1 .' - June 30, '.date('Y');?></h3>
                  </div>
                    <div class="col-sm-12">
                        <div class="box-brd">
                        <h4>Income</h4>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Membership Dues Income:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumMembershipDuesIncome" id="SumMembershipDuesIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Service Project Income:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumServiceProjectIncome" id="SumServiceProjectIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Party Income:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumPartyIncome" id="SumPartyIncome"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Monetary Donations to Chapter:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumMonetaryDonationIncome" id="SumMonetaryDonationIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">International Event Reservation Income:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumInternationalEventIncome" id="SumInternationalEventIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Other Income:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumOtherIncome" id="SumOtherIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="bg-color">
                        <div class="col-sm-6 float-left">
                            <p>Total Income:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumTotalIncome" id="SumTotalIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="box-brd">
                        <h4>Expenses</h4>
                        <div class="col-sm-12 nopadding">
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Meeting Room Expense:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumMeetingRoomExpense" id="SumMeetingRoomExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-sm-12 nopadding">
                        <div class="col-sm-6">
                        <p style="margin-left:10%;">Children's Room Expenses:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Supplies:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                            <input type="text" class="form-control" name="SumChildrensSuppliesExpense" id="SumChildrensSuppliesExpense"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                        </div>
                    </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Paid Sitters:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumPaidSittersExpense" id="SumPaidSittersExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Other:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumChildrensOtherExpense" id="SumChildrensOtherExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Children's Room Expense Total:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumTotalChildrensRoomExpense" id="SumTotalChildrensRoomExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-sm-12 nopadding">
                        <div class="col-sm-6">
                        <p  style="margin-left:10%;">
                            Service Project Expenses:
                        </p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Supplies:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumServiceProjectExpense" id="SumServiceProjectExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Amount Donated to Charity/Recipients:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumDonationExpense" id="SumDonationExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Mother-to-Mother Fund Donation:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumM2MExpense" id="SumM2MExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Service Project Expense Total:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumTotalServiceProjectExpense" id="SumTotalServiceProjectExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Party/Members Only Expense:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumPartyExpense" id="SumPartyExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 nopadding">
                        <div class="col-sm-6">
                            <p style="margin-left:10%;">Office and Operating Expenses:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Printing:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumPrintingExpense" id="SumPrintingExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Postage:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumPostageExpense" id="SumPostageExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Membership Pins:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumPinsExpense" id="SumPinsExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Other:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumOtherOperatingExpense" id="SumOtherOperatingExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Office/Operating Expense Total:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumOperatingExpense" id="SumOperatingExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Annual Chapter Registration Fee:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumChapterReRegistrationExpense" id="SumChapterReRegistrationExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">International Event Registration:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumInternationalEventExpense" id="SumInternationalEventExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:10%;">Other Expense:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumOtherExpense" id="SumOtherExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="bg-color">
                        <div class="col-sm-6 float-left">
                            <p>Total Expenses:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="SumTotalExpense" id="SumTotalExpense"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="box-brd">
                            <h4>Net Income</h4>
                            <div class="col-sm-6 float-left">
                                <p style="margin-left:10%;">Profit/Loss:</p>
                            </div>
                            <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" name="SumTotalNetIncome" id="SumTotalNetIncome"
                                        data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                     </section>
            </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
        </div>
            <!------End Step 9 ------>

                <!------Start Step 10 ------>
                <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '10' ? 'active' : '' }}">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTen" style="width: 100%;">BANK RECONCILIATION</a>
                        </h4>
                    </div>
                    <div id="collapseTen" class="collapse {{ $chFinancialReport->farthest_step_visited == '10' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                    <section>
                        @if (!is_null($chDocuments['statement_1_path']))
                            <div class="col-md-12">
                                <label>Bank Statement Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['statement_1_path']; ?>" >&nbsp; View Bank Statement</a><br>
                            </div>
                        @endif
                        @if (!is_null($chDocuments['statement_2_path']))
                            <div class="col-md-12">
                                <label>Additional Statement Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['statement_2_path']; ?>" >&nbsp; View Additional Bank Statement</a><br>
                            </div>
                        @endif
                        <div class="col-md-12" id="StatementBlock">
                            <strong style="color:red">Please Note</strong><br>
                                This will refresh the screen - be sure to save all work before clicking button to Upload or Replace Bank Statement(s).<br>
                            @if (!is_null($chDocuments['statement_1_path']))
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Bank Statement</button>
                            @else
                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Bank Statement</button>
                            @endif
                        </div>
                            <input type="hidden" name="StatementFile" id="StatementPath" value="{{ $chDocuments->statement_1_path }}">
                        <div class="clearfix"></div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12" id="Statement2Block">
                            @if (!is_null($chDocuments['statement_2_path']))
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Additional Bank Statement</button>
                            @else
                                <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Additional Bank Statement</button>
                            @endif
                        </div>
                        <input type="hidden" name="Statement2File" id="Statement2Path" value="{{ $chDocuments->statement_2_path }}">
                        <div class="clearfix"></div>
                        <div class="col-md-12"><br></div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12 ">
                                <div class="form-group ">
                                <label>Is a copy of your chapter’s most recent bank statement included?<span class="field-required">*</span></label>
                                <div class="col-md-12 row">
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="BankStatementIncludedYes" name="BankStatementIncluded" value="1" {{ $chFinancialReport->bank_statement_included === 1 ? 'checked' : '' }} onchange="ToggleBankStatementIncludedExplanation()">
                                        <label class="form-check-label" for="BankStatementIncludedYes">Yes</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="BankStatementIncludedNo" name="BankStatementIncluded" value="0" {{ $chFinancialReport->bank_statement_included === 0 ? 'checked' : '' }} onchange="ToggleBankStatementIncludedExplanation()">
                                        <label class="form-check-label" for="BankStatementIncludedNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-bottom: 10px">
                                <div class="col-sm-12" id="divBankStatementIncludedExplanation">
                                    <label for="BankStatementIncludedExplanation">If no, briefly explain:</label>
                                    <textarea class="form-control" rows="2" name="BankStatementIncludedExplanation" id="BankStatementIncludedExplanation">{{ $chFinancialReport->bank_statement_included_explanation }}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-12" >
                                <div class="col-sm-12" id="WheresTheMoney">
                                    <label style="display: block;">If your group does not have any bank accounts, where is the chapter money kept?:</label>
                                    <textarea class="form-control" rows="2" name="WheresTheMoney" id="WheresTheMoney">{{ $chFinancialReport->wheres_the_money }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="col-12 form-row form-group">
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="AmountReservedFromLastYear">
                                    This Year's Beginning Balance (July 1, <?php echo date('Y')-1;?>):
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" oninput="TreasuryBalanceChange()" name="AmountReservedFromLastYear" id="AmountReservedFromLastYear" value="{{ $chFinancialReport->amount_reserved_from_previous_year }}"
                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="LastYearReportEnding">
                                    Last Year's Report Ending Balance (June 30, <?php echo date('Y')-1;?>):
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" name="LastYearReportEnding" id="LastYearReportEnding"
                                            value="{{$chFinancialReport['pre_balance'] }}?>"
                                            data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="TotalNetIncome">
                                    Profit/Loss:
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text"class="form-control" name="TotalNetIncome" id="TotalNetIncome"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <br>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="TreasuryBalanceNow">
                                    Ending Balance (Treasury Balance Now):
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="TreasuryBalanceNow" id="TreasuryBalanceNow"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <div class="form-group">
                                <label for="BankBalanceNow">
                                    Ending Bank Statement Balance (June 30, <?php echo date('Y');?>):
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="BankBalanceNow" id="BankBalanceNow" oninput="ChangeBankRec()" value="{{ $chFinancialReport->bank_balance_now }}"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                            </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                </div>
                <div class="col-12 form-row form-group">
                  <p>If your most recent bank statement’s ending balance does not match your “Treasury Balance Now”, you must reconcile your checking account using the worksheet below so that the balances match.</p>
                  <p>To balance your account, start with your bank statement’s ending balance, then list any deposits and any outstanding payments. When done, the new reconciled balance will match your treasury balance.</p>
                  <p>View a step by step instruction video <a href="https://momsclub.org/elearning/courses/annual-financial-report-bank-reconciliation/">HERE</a>.</p>
                  <br>
                <label>Bank Reconciliation:</label>
                    <table id="bank-rec" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Date</td>
                                <td>Check No.</td>
                                <td>Transaction Desc.</td>
                                <td>Payment Amount</td>
                                <td>Deposit Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $bank_rec_array = null;
                                if (isset($chFinancialReport['bank_reconciliation_array'])) {
                                    $bank_rec_array = unserialize(base64_decode($chFinancialReport['bank_reconciliation_array']));
                                    $BankRecRowCount = is_array($bank_rec_array) ? count($bank_rec_array) : 0;
                                } else {
                                    $BankRecRowCount = 1;
                                }
                            @endphp
                            @for ($row = 0; $row < $BankRecRowCount; $row++)
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <input type="date" class="form-control" name="BankRecDate{{ $row }}" id="BankRecDate{{ $row }}"
                                            data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $bank_rec_array[$row]['bank_rec_date'] ?? '' }}" >
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="BankRecCheckNo{{ $row }}" id="BankRecCheckNo{{ $row }}"
                                                value="{{ $bank_rec_array[$row]['bank_rec_check_no'] ?? '' }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="BankRecDesc{{ $row }}" id="BankRecDesc{{ $row }}"
                                                value="{{ $bank_rec_array[$row]['bank_rec_desc'] ?? '' }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="BankRecPaymentAmount{{ $row }}" id="BankRecPaymentAmount{{ $row }}"
                                                    oninput="ChangeBankRec()" value="{{ $bank_rec_array[$row]['bank_rec_payment_amount'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="text" class="form-control" name="BankRecDepositAmount{{ $row }}" id="BankRecDepositAmount{{ $row }}"
                                                    oninput="ChangeBankRec()" value="{{ $bank_rec_array[$row]['bank_rec_desposit_amount'] ?? '' }}"
                                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-success" onclick="AddBankRecRow()">
                            <i class="fas fa-plus"></i>&nbsp; Add Row
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="DeleteBankRecRow()">
                            <i class="fas fa-minus"></i>&nbsp; Remove Row
                        </button>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="TreasuryBalanceNowR">
                                    Ending Balance (Treasury Balance Now):
                                </label>
                                <div class="form-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                <input type="text" class="form-control" name="TreasuryBalanceNowR" id="TreasuryBalanceNowR"
                                    data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                                </div>
                            </div>
                            </div>
                        </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ReconciledBankBalance">
                                Reconciled Bank Balance:
                            </label>
                            <div class="form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                            <input type="text" class="form-control" name="ReconciledBankBalance" id="ReconciledBankBalance"
                                data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'" readonly>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div id="ReconciliationAlert" class="alert alert-warning" style="display: none;">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Alert!</h5>
                        <div id="ReconciledBankBalanceWarning" class="alert-message"></div>
                    </div>
                </div>
                    <input type="hidden" name="BankRecRowCount" id="BankRecRowCount" value="{{ $BankRecRowCount }}" />
                    <hr>
                </div>
                <div class="card-body text-center">
                              <button type="submit" id="btn-step-10" class="btn btn-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                </div>
            </section>
            </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
        </div>
            <!------End Step 10 ------>

            <!------Start Step 11 ------>
            <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '11' ? 'active' : '' }}">
                <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseEleven" style="width: 100%;">990N IRS FILING</a>
                        </h4>
                    </div>
                    <div id="collapseEleven" class="collapse {{ $chFinancialReport->farthest_step_visited == '11' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                    <section>
                        @if (!is_null($chDocuments['irs_path']))
                            <div class="col-md-12">
                                <label>990N Uploaded:</label><a href="https://drive.google.com/uc?export=download&id=<?php echo $chDocuments['irs_path']; ?>" >&nbsp; View 990N Confirmation</a><br>
                            </div>
                        @endif

                        <div class="col-12" id="FileIRSBlock">
                            <strong style="color:red">Please Note</strong><br>
                                This will refresh the screen - be sure to save all work before clicking button to Upload or Replace Bank Statement(s).<br>
                            @if (!is_null($chDocuments['irs_path']))
                                <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace 990N Confirmation</button>
                            @else
                                <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload 990N Confirmation</button>
                            @endif
                        </div>
                        <input type="hidden" name="IRSFiling" id="IRSFiling" value="{{ $chDocuments->irs_path }}">
                        <div class="clearfix"></div>
                        <div class="col-md-12"><br></div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12 ">
                                <div class="form-group ">
                                <label>Is a copy of your chpater's <?php echo date('Y')-1;?> 990N Filing included?<span class="field-required">*</span></label>
                                <div class="col-md-12 row">
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="FileIRSYes" name="FileIRS" value="1" {{ $chFinancialReport->file_irs === 1 ? 'checked' : '' }} onchange="ToggleFileIRSExplanation()">
                                        <label class="form-check-label" for="FileIRSYes">Yes</label>
                                    </div>
                                    <div class="form-check" style="margin-left: 20px;">
                                        <input class="form-check-input" type="radio" id="FileIRSNo" name="FileIRS" value="0" {{ $chFinancialReport->file_irs === 0 ? 'checked' : '' }} onchange="ToggleFileIRSExplanation()">
                                        <label class="form-check-label" for="FileIRSNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" style="margin-bottom: 10px">
                                <div class="col-sm-12" id="divFileIRSExplanation">
                                    <label for="FileIRSExplanation">If no, briefly explain:</label>
                                    <textarea class="form-control" rows="2" name="FileIRSExplanation" id="FileIRSExplanation">{{ $chFinancialReport->file_irs_explanation }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                <div class="col-12">
                    {{-- <strong><u>990N (e-Postcard) Information</u></strong><br> --}}
                    The 990N filing is an IRS requirement that all chapters must complete, but it cannot be filed before July 1st.  After filing, upload a copy of your chapter's filing confirmation here.  You can upload a copy of your confirmation email or screenshot after filing.  All chapters should file their 990N directly with the IRS and not through a third party. <span style="color:red"><i>The IRS does not charge a fee for 990N filings.</i></span><br>
                    <br>
                    @if($thisDate->month >= 1 && $thisDate->month <= 6)
                    <table>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><span class="text-danger">990N Filing Instructions will be available on July 1st. Since chapter cannot file until then, we are also unable to verify that instructions/screenshots have not changed since last year until that date, so please bear with us until we get them updated and posted.</span><br></td>
                        </tr>
                    </table>
                    @endif
                    @if($thisDate->month >= 7 && $thisDate->month <= 12)
                    <table>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><a href="https://www.irs.gov/charities-non-profits/annual-electronic-filing-requirement-for-small-exempt-organizations-form-990-n-e-postcard" target="_blank">990N IRS Website Link to File</a></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            @foreach($resources as $resourceItem)
                            @if ($resourceItem->name === '990N Filing Instructions')
                                <td><a href="{{ $resourceItem->file_path }}" target="_blank">990N Filing Instructions
                                </a></td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            @foreach($resources as $resourceItem)
                            @if ($resourceItem->name === '990N Filing FAQs')
                                <td><a href="{{ $resourceItem->file_path }}" target="_blank">990N Filing FAQs
                                </a></td>
                            @endif
                            @endforeach
                        </tr>
                    </table>
                    @endif
                    <br>
                </div>
                <div class="card-body text-center">
                    <button type="submit" id="btn-step-11" class="btn btn-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                </div>
            </section>
            </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
        </div>
            <!------End Step 11 ------>

            <!------Start Step 12 ------>
            <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '12' ? 'active' : '' }}">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseTwelve" style="width: 100%;">CHAPTER QUESTIONS</a>
                    </h4>
                </div>
                <div id="collapseTwelve" class="collapse {{ $chFinancialReport->farthest_step_visited == '12' ? 'show' : '' }}" data-parent="#accordion">
                    <div class="card-body">
                <section>
                <div id="form-step-8" role="form" data-toggle="validator" class="form-row form-group">
                    <p>During the last fiscal year (July 1, <?php echo date('Y')-1 .' - June 30, '.date('Y');?>)</p>

                <div class="col-md-12" style="margin-left: 10px">
                    <div class="form-group row">
                        <label>1. Did you make the Bylaws and/or manual available for any chapter members that requested them?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="ByLawsAvailableYes" name="ByLawsAvailable" value="1" {{ $chFinancialReport->bylaws_available === 1 ? 'checked' : '' }} onchange="ToggleByLawsAvailableExplanation()">
                                <label class="form-check-label" for="ByLawsAvailableYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="ByLawsAvailableNo" name="ByLawsAvailable" value="0" {{ $chFinancialReport->bylaws_available === 0 ? 'checked' : '' }} onchange="ToggleByLawsAvailableExplanation()">
                                <label class="form-check-label" for="ByLawsAvailableNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divByLawsAvailableExplanation">
                            <label for="ByLawsAvailableExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="ByLawsAvailableExplanation" id="ByLawsAvailableExplanation">{{ $chFinancialReport->bylaws_available_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>2. Did your chapter vote on all activities and expenditures during the fiscal year?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="VoteAllActivitiesYes" name="VoteAllActivities" value="1" {{ $chFinancialReport->vote_all_activities === 1 ? 'checked' : '' }} onchange="ToggleVoteAllActivitiesExplanation()">
                                <label class="form-check-label" for="VoteAllActivitiesYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="VoteAllActivitiesNo" name="VoteAllActivities" value="0" {{ $chFinancialReport->vote_all_activities === 0 ? 'checked' : '' }} onchange="ToggleVoteAllActivitiesExplanation()">
                                <label class="form-check-label" for="VoteAllActivitiesNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divVoteAllActivitiesExplanation">
                            <label for="VoteAllActivitiesExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="VoteAllActivitiesExplanation" id="VoteAllActivitiesExplanation">{{ $chFinancialReport->vote_all_activities_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>3. Did you have any child focused outings or activities?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="ChildOutingsYes" name="ChildOutings" value="1" {{ $chFinancialReport->child_outings === 1 ? 'checked' : '' }} onchange="ToggleChildOutingsExplanation()">
                                <label class="form-check-label" for="ChildOutingsYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="ChildOutingsNo" name="ChildOutings" value="0" {{ $chFinancialReport->child_outings === 0 ? 'checked' : '' }} onchange="ToggleChildOutingsExplanation()">
                                <label class="form-check-label" for="ChildOutingsNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divChildOutingsExplanation">
                            <label for="ChildOutingsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="ChildOutingsExplanation" id="ChildOutingsExplanation">{{ $chFinancialReport->child_outings_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>4. Did you have playgroups? If so, how were they arranged?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="col-md-12 row">
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="PlaygroupsMulti" name="Playgroups" value="2" {{ $chFinancialReport->playgroups === 2 ? 'checked' : '' }} onchange="TogglePlaygroupsExplanation()">
                                    <label class="form-check-label" for="PlaygroupsMulti">Yes, Multi-Aged Groups</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="PlaygroupsAge" name="Playgroups" value="1" {{ $chFinancialReport->playgroups === 1 ? 'checked' : '' }} onchange="TogglePlaygroupsExplanation()">
                                    <label class="form-check-label" for="PlaygroupsAge">Yes, Arranged by Age</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="PlaygroupsNo" name="Playgroups" value="0" {{ $chFinancialReport->playgroups === 0 ? 'checked' : '' }} onchange="TogglePlaygroupsExplanation()">
                                    <label class="form-check-label" for="PlaygroupsNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divPlaygroupsExplanation" style="display: {{ $chFinancialReport->playgroups === 0 ? 'block' : 'none' }}">
                            <label for="PlaygroupsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="PlaygroupsExplanation" id="PlaygroupsExplanation">{{ $chFinancialReport->playgroups_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>5. Did your chapter have scheduled park days? If yes, how often?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="col-md-12 row">
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="ParkDays4" name="ParkDays" value="4" {{ $chFinancialReport->park_day_frequency === 4 ? 'checked' : '' }} onchange="ToggleParkDaysExplanation()">
                                    <label class="form-check-label" for="ParkDays4">10+ Times</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="ParkDays3" name="ParkDays" value="3" {{ $chFinancialReport->park_day_frequency === 3 ? 'checked' : '' }} onchange="ToggleParkDaysExplanation()">
                                    <label class="form-check-label" for="ParkDays3">7-9 Times</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="ParkDays2" name="ParkDays" value="2" {{ $chFinancialReport->park_day_frequency === 2 ? 'checked' : '' }} onchange="ToggleParkDaysExplanation()">
                                    <label class="form-check-label" for="ParkDays2">4-6 Times</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="ParkDays1" name="ParkDays" value="1" {{ $chFinancialReport->park_day_frequency === 1 ? 'checked' : '' }} onchange="ToggleParkDaysExplanation()">
                                    <label class="form-check-label" for="ParkDays1">1-3 Times</label>
                                </div>
                                <div class="form-check" style="margin-left: 20px;">
                                    <input class="form-check-input" type="radio" id="ParkDaysNo" name="ParkDays" value="0" {{ $chFinancialReport->park_day_frequency === 0 ? 'checked' : '' }} onchange="ToggleParkDaysExplanation()">
                                    <label class="form-check-label" for="ParkDaysNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divParkDaysExplanation" style="display: {{ $chFinancialReport->park_day_frequency === 0 ? 'block' : 'none' }}">
                            <label for="ParkDaysExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="ParkDaysExplanation" id="ParkDaysExplanation">{{ $chFinancialReport->park_day_frequency_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>6. Did you have any mother focused outings or activities?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="MotherOutingsYes" name="MotherOutings" value="1" {{ $chFinancialReport->mother_outings === 1 ? 'checked' : '' }} onchange="ToggleMotherOutingsExplanation()">
                                <label class="form-check-label" for="MotherOutingsYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="MotherOutingsNo" name="MotherOutings" value="0" {{ $chFinancialReport->mother_outings === 0 ? 'checked' : '' }} onchange="ToggleMotherOutingsExplanation()">
                                <label class="form-check-label" for="MotherOutingsNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divMotherOutingsExplanation">
                            <label for="MotherOutingsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="MotherOutingsExplanation" id="MotherOutingsExplanation">{{ $chFinancialReport->mother_outings_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>7. Did your chapter have any of the following activity groups?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            @php
                                $selectedValues = is_null($chFinancialReport->activity_array)
                                    ? []
                                    : json_decode($chFinancialReport->activity_array);
                            @endphp
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="checkbox" id="Activity0" name="Activity[]" value="0" {{ in_array('0', $selectedValues) ? 'checked' : '' }} onchange="ToggleActivityOtherExplanation()">
                                <label class="form-check-label" for="Activity0">Cooking</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="checkbox" id="Activity1" name="Activity[]" value="1" {{ in_array('1', $selectedValues) ? 'checked' : '' }} onchange="ToggleActivityOtherExplanation()">
                                <label class="form-check-label" for="Activity1">Cost Cutting Tips</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="checkbox" id="Activity2" name="Activity[]" value="2" {{ in_array('2', $selectedValues) ? 'checked' : '' }} onchange="ToggleActivityOtherExplanation()">
                                <label class="form-check-label" for="Activity2">Mommy Playgroup</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="checkbox" id="Activity3" name="Activity[]" value="3" {{ in_array('3', $selectedValues) ? 'checked' : '' }} onchange="ToggleActivityOtherExplanation()">
                                <label class="form-check-label" for="Activity3">Babysitting Co-op</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="checkbox" id="Activity4" name="Activity[]" value="4" {{ in_array('4', $selectedValues) ? 'checked' : '' }} onchange="ToggleActivityOtherExplanation()">
                                <label class="form-check-label" for="Activity4">MOMS Night Out</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="checkbox" id="Activity5" name="Activity[]" value="5" {{ in_array('5', $selectedValues) ? 'checked' : '' }} onchange="ToggleActivityOtherExplanation()">
                                <label class="form-check-label" for="Activity5">Other</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divActivityOtherExplanation">
                           <label for="ActivityOtherExplanation">If other, briefly explain:</label>
                           <textarea class="form-control" rows="2" name="ActivityOtherExplanation" id="ActivityOtherExplanation">{{ $chFinancialReport->activity_other_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>8. Did you offer or inform your members about MOMS Club merchandise?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="OfferedMerchYes" name="OfferedMerch" value="1" {{ $chFinancialReport->offered_merch === 1 ? 'checked' : '' }} onchange="ToggleOfferedMerchExplanation()">
                                <label class="form-check-label" for="OfferedMerchYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="OfferedMerchNo" name="OfferedMerch" value="0" {{ $chFinancialReport->offered_merch === 0 ? 'checked' : '' }} onchange="ToggleOfferedMerchExplanation()">
                                <label class="form-check-label" for="OfferedMerchNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divOfferedMerchExplanation">
                            <label for="OfferedMerchExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="OfferedMerchExplanation" id="OfferedMerchExplanation">{{ $chFinancialReport->offered_merch_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>9. Did you purchase any merchandise from International other than pins?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="BoughtMerchYes" name="BoughtMerch" value="1" {{ $chFinancialReport->bought_merch === 1 ? 'checked' : '' }} onchange="ToggleBoughtMerchExplanation()">
                                <label class="form-check-label" for="BoughtMerchYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="BoughtMerchNo" name="BoughtMerch" value="0" {{ $chFinancialReport->bought_merch === 0 ? 'checked' : '' }} onchange="ToggleBoughtMerchExplanation()">
                                <label class="form-check-label" for="BoughtMerchNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divBoughtMerchExplanation">
                            <label for="BoughtMerchExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="BoughtMerchExplanation" id="BoughtMerchExplanation">{{ $chFinancialReport->bought_merch_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>10. Did you purchase pins from International?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="BoughtPinsYes" name="BoughtPins" value="1" {{ $chFinancialReport->purchase_pins === 1 ? 'checked' : '' }} onchange="ToggleBoughtPinsExplanation()">
                                <label class="form-check-label" for="BoughtPinsYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="BoughtPinsNo" name="BoughtPins" value="0" {{ $chFinancialReport->purchase_pins === 0 ? 'checked' : '' }} onchange="ToggleBoughtPinsExplanation()">
                                <label class="form-check-label" for="BoughtPinsNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px">
                        <div class="col-sm-12" id="divBoughtPinsExplanation">
                            <label for="BoughtPinsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="BoughtPinsExplanation" id="BoughtPinsExplanation">{{ $chFinancialReport->purchase_pins_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>11. Did anyone in your chapter receive any compensation or pay for their work with your chapter?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="ReceiveCompensationYes" name="ReceiveCompensation" value="1" {{ $chFinancialReport->changed_dues === 1 ? 'checked' : '' }} onchange="ToggleReceiveCompensationExplanation()">
                                <label class="form-check-label" for="ReceiveCompensationYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="ReceiveCompensationNo" name="ReceiveCompensation" value="0" {{ $chFinancialReport->changed_dues === 0 ? 'checked' : '' }} onchange="ToggleReceiveCompensationExplanation()">
                                <label class="form-check-label" for="ReceiveCompensationNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="divReceiveCompensationExplanation">
                        <div class="col-md-12" style="margin-bottom: 10px">
                            <label for="ReceiveCompensationExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="2" name="ReceiveCompensationExplanation" id="ReceiveCompensationExplanation">{{ $chFinancialReport->receive_compensation_explanation }}</textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>12. Did any officer, member or family of a member benefit financially in any way from the member’s position with your chapter?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="FinancialBenefitYes" name="FinancialBenefit" value="1" {{ $chFinancialReport->financial_benefit === 1 ? 'checked' : '' }} onchange="ToggleFinancialBenefitExplanation()">
                                <label class="form-check-label" for="FinancialBenefitYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="FinancialBenefitNo" name="FinancialBenefit" value="0" {{ $chFinancialReport->financial_benefit === 0 ? 'checked' : '' }} onchange="ToggleFinancialBenefitExplanation()">
                                <label class="form-check-label" for="FinancialBenefitNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="divFinancialBenefitExplanation">
                        <div class="col-md-12" style="margin-bottom: 10px">
                            <label for="FinancialBenefitExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="FinancialBenefitExplanation" id="FinancialBenefitExplanation">{{ $chFinancialReport->financial_benefit_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>13. Did your chapter attempt to influence any national, state/provincial, or local legislation, or did your chapter support any other organization that did?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="InfluencePoliticalYes" name="InfluencePolitical" value="1" {{ $chFinancialReport->influence_political === 1 ? 'checked' : '' }} onchange="ToggleInfluencePoliticalExplanation()">
                                <label class="form-check-label" for="InfluencePoliticalYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="InfluencePoliticalNo" name="InfluencePolitical" value="0" {{ $chFinancialReport->influence_political === 0 ? 'checked' : '' }} onchange="ToggleInfluencePoliticalExplanation()">
                                <label class="form-check-label" for="InfluencePoliticalNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="divInfluencePoliticalExplanation">
                        <div class="col-md-12" style="margin-bottom: 10px">
                            <label for="InfluencePoliticalExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="InfluencePoliticalExplanation" id="InfluencePoliticalExplanation" >{{ $chFinancialReport->influence_political_explanation }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label>14. Did your chapter sister another chapter?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="SisterChapterYes" name="SisterChapter" value="1" {{ $chFinancialReport->sister_chapter === 1 ? 'checked' : '' }} onchange="ToggleSisterChapterExplanation()">
                                <label class="form-check-label" for="SisterChapterYes">Yes</label>
                            </div>
                            <div class="form-check" style="margin-left: 20px;">
                                <input class="form-check-input" type="radio" id="SisterChapterNo" name="SisterChapter" value="0" {{ $chFinancialReport->sister_chapter === 0 ? 'checked' : '' }} onchange="ToggleSisterChapterExplanation()">
                                <label class="form-check-label" for="SisterChapterNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="divSisterChapterExplanation" style="display: {{ $chFinancialReport->sister_chapter === 1 ? 'block' : 'none' }}">
                        <div class="col-md-12" style="margin-bottom: 10px">
                            <label for="SisterChapterExplanation">If yes, which chapter?<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="SisterChapterExplanation" id="SisterChapterExplanation" >{{ $chFinancialReport->sister_chapter_explanation }}</textarea>
                        </div>
                    </div>
                </div>

                    </div>
                    <div class="card-body text-center">
                              <button type="button" class="btn btn-primary" id="btn-step-12" ><i class="fas fa-save" ></i>&nbsp; Save</button>
                    </div>
              </section>
          </div><!-- end of accordion body -->
          </div><!-- end of accordion item -->
        </div>
            <!------End Step 12 ------>

            <!------Begin Step 13 ------>
            <div class="card card-primary {{ $chFinancialReport->farthest_step_visited == '13' ? 'active' : '' }}">
                <div class="card-header" id="accordion-header-members">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapseThirteen" style="width: 100%;">AWARD NOMINATIONS</a>
                    </h4>
                </div>
                <div id="collapseThirteen" class="collapse {{ $chFinancialReport->farthest_step_visited == '13' ? 'show' : '' }}" data-parent="#accordion">
                    <div class="card-body">
                    <section>
                    <div class="col-12 form-row form-group">
                        <div class="box_brd_contentpad">
                            <div class="box_brd_title_box">
                                <h4>Instructions for Recognition Entry</h4>
                            </div>
                            <input type="hidden" id="TotalAwardNominations" name="TotalAwardNominations" value=<?php
                                    if (!empty($chFinancialReport)) {
                                        if ($chFinancialReport['award_nominations']>0){
                                            echo $chFinancialReport['award_nominations'];
                                        }
                                        else {
                                            echo "0";
                                        }
                                    }
                                    else {
                                        echo "0";
                                    } ?>>
                            <div class="award_acc_con">
                                <ul class="border_list">
                                    <li>Include with your entry a written description of your project/activity and any photos or newspaper clippings available. You may be contacted for more information, if necessary. Give enough information that someone who is not familiar with your project or activity can see how wonderful it was!</li>
                                    <li>Please submit a separate Recognition for each entry. If you have any questions, please contact your Coordinator.</li>
                                    <li>All entries must be submitted by the deadline in the instructions for your Conference.</li>
                                    <li>Keep a copy of your entry, photos and other information!</li>
                                </ul>

                                <h5 class="awrd_sub_head">Chapter Award Types</h5>
                                    <ul class="border_list">
                                        <li><strong>Outstanding Specific Service Project</strong> (one project only)</li>
                                        <li><strong>Outstanding Overall Service Program</strong> (multiple projects considered together)</li>
                                        <li><strong>Outstanding Children's Activity</strong></li>
                                        <li>
                                        <strong>Outstanding Spirit (formation of sister chapters)</strong><br>
                                        <em>Given to existing chapters who Sister new Chapters</em>
                                        </li>
                                        <li>
                                           <strong>Outstanding Chapter</strong> (for chapters started before July 1, <?php echo date('Y')-1;?>)<br>
                                           <em>Given for outstanding overall representation of MOMS Club goals, principles, and program (including program to members, community involvement, and support of International MOMS Club)</em>
                                        </li>
                                        <li>
                                           <strong>Outstanding New Chapter</strong> (for chapters started after July 1, <?php echo date('Y')-1;?>)<br>
                                           <em>Given for outstanding overall representation of MOMS Club goals, principles, and program (including program to members, community involvement, and support of International MOMS Club)</em>
                                        </li>
                                        <li>
                                           <strong>Other Outstanding Award</strong> (any entries not included in categories above)</li>
                                    </ul>

                                    <div class="award_sec_btn">
                                        <button type="button" id="btnAddAwardNomination" class="btn btn-sm btn-success " onclick="AddAwardNomination()" <?php if($submitted || $chFinancialReport['award_nominations']==5) echo "disabled"; ?>>
                                            <i class="fas fa-plus"></i>&nbsp; Add Nomination</button>
                                        <button type="button" id="btnDeleteAwardNomination" class="btn btn-sm btn-danger" onclick="DeleteAwardNomination()" <?php if($submitted || $chFinancialReport['award_nominations']<1) echo "disabled"; ?>>
                                            <i class="fas fa-minus"></i>&nbsp; Delete Nomination</button>
                                    </div>
                            </div>
                        </div>
                        <!-- Award 1 Start -->
                        <div class="col-12 box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_nominations']<1) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="col-sm-12"><br></div>
                                <h4>Award 1</h4>
                                <div class="col-12 form-group">
                                <label for="NominationType1">Select list:</label>
                                    <select class="form-control" id="NominationType1" name="NominationType1" onClick="ShowOutstandingCriteria(1)">
                                       <option style="display:none" disabled selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                        <div class="award_acc_con">
                                            <div id="OutstandingCriteria1" style="display: none;">
                                        <h4>Outstanding Chapter Criteria</h4>
                                    <div class="form-group">
                                        <label>Did you follow the Bylaws and all instructions from International?<span class="field-required">*</span></label>
                                        <select id="OutstandingFollowByLaws1" name="OutstandingFollowByLaws1" class="form-control select2" style="width: 25%;" required >
                                            <option value="" {{ is_null($chFinancialReport->award_1_outstanding_follow_bylaws) ? 'selected' : ''}} disabled>Please Select</option>
                                            <option value="0" {{$chFinancialReport->award_1_outstanding_follow_bylaws === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$chFinancialReport->award_1_outstanding_follow_bylaws == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    <div><br></div>
                                    <div class="form-group">
                                        <label>Did you run a well-rounded program for your members?<span class="field-required">*</span></label><br><p>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings),
                                            a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration.
                                            A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>
                                        <select id="OutstandingWellRounded1" name="OutstandingWellRounded1" class="form-control select2" style="width: 25%;" required >
                                            <option value="" {{ is_null($chFinancialReport->award_1_outstanding_well_rounded) ? 'selected' : ''}} disabled>Please Select</option>
                                            <option value="0" {{$chFinancialReport->award_1_outstanding_well_rounded === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$chFinancialReport->award_1_outstanding_well_rounded == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    <div><br></div>
                                    <div class="form-group">
                                        <label>Did you communicate with your Coordinator?<span class="field-required">*</span></label><br><p>Did you send in your newsletter regularly? Send updates? Return telephone calls?
                                            A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>
                                        <select id="OutstandingCommunicated1" name="OutstandingCommunicated1" class="form-control select2" style="width: 25%;" required >
                                            <option value="" {{ is_null($chFinancialReport->award_1_outstanding_communicated) ? 'selected' : ''}} disabled>Please Select</option>
                                            <option value="0" {{$chFinancialReport->award_1_outstanding_communicated === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$chFinancialReport->award_1_outstanding_communicated == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    <div><br></div>
                                    <div class="form-group">
                                        <label>Did you support the International MOMS Club?<span class="field-required">*</span></label><br><p>Indications of supporting International MAY include but are not limited to:
                                            <ul>
                                                <li>Providing MOMS Club pins to your members</li>
                                                <li>Purchasing MOMS Club merchandise from International</li>
                                                <li>Forming sister chapters (when possible)</li>
                                                <li>Donating to the Mother-to-Mother Fund</li>
                                                <li>Participating in Area, State and Regional events.</li>
                                            </ul></p>
                                        <select id="OutstandingSupportMomsClub1" name="OutstandingSupportMomsClub1" class="form-control select2" style="width: 25%;" required >
                                            <option value="" {{ is_null($chFinancialReport->award_1_outstanding_support_international) ? 'selected' : ''}} disabled>Please Select</option>
                                            <option value="0" {{$chFinancialReport->award_1_outstanding_support_international === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$chFinancialReport->award_1_outstanding_support_international == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                    <div>
                                        <div>
                                 <label>Description:</label>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc1" name="AwardDesc1"><?php if (!empty($chFinancialReport)) {echo $chFinancialReport['award_1_outstanding_project_desc'];}?></textarea>
                                 </div>
                                </div>
                                 <div class="col-md-12" id="Award1Block" <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_1_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload Award 1 Files.<br>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="showAward1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Award 1 Attachments</button>
                                        {{-- <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-award1" ><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Award 1 Files</button> --}}
                                    </div>
                                </div>
                                <input type="hidden" name="Award1Path" id="Award1Path" value="<?php echo $chFinancialReport['award_1_files']; ?>">
                                <div class="col-md-12 mar_bot_20" <?php if (!empty($chFinancialReport)) {if (!$chFinancialReport['award_1_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12" >
                                        <div>
                                            <label class="control-label" for="Award1Link">Award 1 Files:</label>
                                           <a href="<?php echo $chFinancialReport['award_1_files']; ?>" target="_blank">View Award 1 Attachments</a><br>
                                            <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace Award 1 Files.<br>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="showAward1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Award 1 Attachments</button>
                                        {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-award1" ><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Award 1 Files</button> --}}
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            </div>
                        </div>
                    </div>
                        <!-- Award 1 Stop -->
                        <!-- Award 2 Start -->
                        <div class="col-12 box_brd_contentpad" id="Award2Panel" style="display: <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_nominations']<2) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="col-sm-12"><br></div>
                                <h4>Award 2</h4>
                                <div class="col-12 form-group">
                                <label for="NominationType2">Select list:</label>
                                    <select class="form-control" id="NominationType2" name="NominationType2" onClick="ShowOutstandingCriteria(2)">
                                       <option style="display:none" disabled selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="award_acc_con">
                                    <div id="OutstandingCriteria2" style="display: none;">
                                <h4>Outstanding Chapter Criteria</h4>
                            <div class="form-group">
                                <label>Did you follow the Bylaws and all instructions from International?<span class="field-required">*</span></label>
                                <select id="OutstandingFollowByLaws2" name="OutstandingFollowByLaws2" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_2_outstanding_follow_bylaws) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_2_outstanding_follow_bylaws === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_2_outstanding_follow_bylaws == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you run a well-rounded program for your members?<span class="field-required">*</span></label><br><p>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings),
                                    a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration.
                                    A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>
                                <select id="OutstandingWellRounded2" name="OutstandingWellRounded2" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_2_outstanding_well_rounded) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_2_outstanding_well_rounded === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_2_outstanding_well_rounded == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you communicate with your Coordinator?<span class="field-required">*</span></label><br><p>Did you send in your newsletter regularly? Send updates? Return telephone calls?
                                    A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>
                                <select id="OutstandingCommunicated2" name="OutstandingCommunicated2" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_2_outstanding_communicated) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_2_outstanding_communicated === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_2_outstanding_communicated == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you support the International MOMS Club?<span class="field-required">*</span></label><br><p>Indications of supporting International MAY include but are not limited to:
                                    <ul>
                                        <li>Providing MOMS Club pins to your members</li>
                                        <li>Purchasing MOMS Club merchandise from International</li>
                                        <li>Forming sister chapters (when possible)</li>
                                        <li>Donating to the Mother-to-Mother Fund</li>
                                        <li>Participating in Area, State and Regional events.</li>
                                    </ul></p>
                                <select id="OutstandingSupportMomsClub2" name="OutstandingSupportMomsClub2" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_2_outstanding_support_international) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_2_outstanding_support_international === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_2_outstanding_support_international == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                            <div>
                                <div>
                                 <label>Description:</label>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc2" name="AwardDesc2"><?php if (!empty($chFinancialReport)) {echo $chFinancialReport['award_2_outstanding_project_desc'];}?></textarea>
                                 </div>
                                </div>
                                 <div class="col-md-12" id="Award2Block" <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_2_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload Award 2 Files.<br>
                                        <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-award2" ><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Award 2 Files</button>
                                    </div>
                                </div>
                                <input type="hidden" name="Award2Path" id="Award2Path" value="<?php echo $chFinancialReport['award_2_files']; ?>">
                                <div class="col-md-12 mar_bot_20" <?php if (!empty($chFinancialReport)) {if (!$chFinancialReport['award_2_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12" >
                                        <div>
                                            <label class="control-label" for="Award2Link">Award 2 Files:</label>
                                           <a href="<?php echo $chFinancialReport['award_2_files']; ?>" target="_blank">View Award 2 Files</a><br>
                                            <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace Award 2 Files.<br>
                                           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-award2" ><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Award 2 Files</button>
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                            </div>
                        </div>
                        <!-- Award 2 Stop -->
                        <!-- Award 3 Start -->
                        <div class="col-12 box_brd_contentpad" id="Award3Panel" style="display: <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_nominations']<3) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="col-sm-12"><br></div>
                                <h4>Award 3</h4>
                                <div class="col-12 form-group">
                                <label for="NominationType3">Select list:</label>
                                    <select class="form-control" id="NominationType3" name="NominationType3" onClick="ShowOutstandingCriteria(3)">
                                       <option style="display:none" disabled selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="award_acc_con">
                                    <div id="OutstandingCriteria3" style="display: none;">
                                <h4>Outstanding Chapter Criteria</h4>
                            <div class="form-group">
                                <label>Did you follow the Bylaws and all instructions from International?<span class="field-required">*</span></label>
                                <select id="OutstandingFollowByLaws3" name="OutstandingFollowByLaws3" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_3_outstanding_follow_bylaws) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_3_outstanding_follow_bylaws === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_3_outstanding_follow_bylaws == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you run a well-rounded program for your members?<span class="field-required">*</span></label><br><p>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings),
                                    a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration.
                                    A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>
                                <select id="OutstandingWellRounded3" name="OutstandingWellRounded3" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_3_outstanding_well_rounded) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_3_outstanding_well_rounded === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_3_outstanding_well_rounded == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you communicate with your Coordinator?<span class="field-required">*</span></label><br><p>Did you send in your newsletter regularly? Send updates? Return telephone calls?
                                    A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>
                                <select id="OutstandingCommunicated3" name="OutstandingCommunicated3" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_3_outstanding_communicated) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_3_outstanding_communicated === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_3_outstanding_communicated == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you support the International MOMS Club?<span class="field-required">*</span></label><br><p>Indications of supporting International MAY include but are not limited to:
                                    <ul>
                                        <li>Providing MOMS Club pins to your members</li>
                                        <li>Purchasing MOMS Club merchandise from International</li>
                                        <li>Forming sister chapters (when possible)</li>
                                        <li>Donating to the Mother-to-Mother Fund</li>
                                        <li>Participating in Area, State and Regional events.</li>
                                    </ul></p>
                                <select id="OutstandingSupportMomsClub3" name="OutstandingSupportMomsClub3" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_3_outstanding_support_international) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_3_outstanding_support_international === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_3_outstanding_support_international == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                            <div>
                                <div>
                                 <label>Description:</label>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc3" name="AwardDesc3"><?php if (!empty($chFinancialReport)) {echo $chFinancialReport['award_3_outstanding_project_desc'];}?></textarea>
                                 </div>
                                </div>
                                 <div class="col-md-12" id="Award3Block" <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_3_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload Award 3 Files.<br>
                                        <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-award3" ><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Award 3 Files</button>
                                    </div>
                                </div>
                                <input type="hidden" name="Award3Path" id="Award3Path" value="<?php echo $chFinancialReport['award_3_files']; ?>">
                                <div class="col-md-12 mar_bot_20" <?php if (!empty($chFinancialReport)) {if (!$chFinancialReport['award_3_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12" >
                                        <div>
                                            <label class="control-label" for="Award3Link">Award 3 Files:</label>
                                           <a href="<?php echo $chFinancialReport['award_3_files']; ?>" target="_blank">View Award 3 Files</a><br>
                                            <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace Award 3 Files.<br>
                                           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-award3" ><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Award 3 Files</button>
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            </div>
                            </div>
                        </div>
                        <!-- Award 3 Stop -->
                        <!-- Award 4 Start -->
                        <div class="col-12 box_brd_contentpad" id="Award4Panel" style="display: <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_nominations']<4) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="col-sm-12"><br></div>
                                <h4>Award 4</h4>
                                <div class="col-12 form-group">
                                <label for="NominationType4">Select list:</label>
                                    <select class="form-control" id="NominationType4" name="NominationType4" onClick="ShowOutstandingCriteria(4)">
                                       <option style="display:none" disabled selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="award_acc_con">
                                    <div id="OutstandingCriteria4" style="display: none;">
                                <h4>Outstanding Chapter Criteria</h4>
                            <div class="form-group">
                                <label>Did you follow the Bylaws and all instructions from International?<span class="field-required">*</span></label>
                                <select id="OutstandingFollowByLaws4" name="OutstandingFollowByLaws4" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_4_outstanding_follow_bylaws) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_4_outstanding_follow_bylaws === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_4_outstanding_follow_bylaws == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you run a well-rounded program for your members?<span class="field-required">*</span></label><br><p>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings),
                                    a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration.
                                    A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>
                                <select id="OutstandingWellRounded4" name="OutstandingWellRounded4" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_4_outstanding_well_rounded) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_4_outstanding_well_rounded === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_4_outstanding_well_rounded == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you communicate with your Coordinator?<span class="field-required">*</span></label><br><p>Did you send in your newsletter regularly? Send updates? Return telephone calls?
                                    A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>
                                <select id="OutstandingCommunicated4" name="OutstandingCommunicated4" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_4_outstanding_communicated) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_4_outstanding_communicated === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_4_outstanding_communicated == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you support the International MOMS Club?<span class="field-required">*</span></label><br><p>Indications of supporting International MAY include but are not limited to:
                                    <ul>
                                        <li>Providing MOMS Club pins to your members</li>
                                        <li>Purchasing MOMS Club merchandise from International</li>
                                        <li>Forming sister chapters (when possible)</li>
                                        <li>Donating to the Mother-to-Mother Fund</li>
                                        <li>Participating in Area, State and Regional events.</li>
                                    </ul></p>
                                <select id="OutstandingSupportMomsClub4" name="OutstandingSupportMomsClub4" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_4_outstanding_support_international) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_4_outstanding_support_international === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_4_outstanding_support_international == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                            <div>
                                <div>
                                 <label>Description:</label>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc4" name="AwardDesc4"><?php if (!empty($chFinancialReport)) {echo $chFinancialReport['award_4_outstanding_project_desc'];}?></textarea>
                                 </div>
                                </div>
                                 <div class="col-md-12" id="Award4Block" <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_4_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload Award 4 Files.<br>
                                        <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-award4" ><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Award 4 Files</button>
                                    </div>
                                </div>
                                <input type="hidden" name="Award1Path" id="Award4Path" value="<?php echo $chFinancialReport['award_4_files']; ?>">
                                <div class="col-md-12 mar_bot_20" <?php if (!empty($chFinancialReport)) {if (!$chFinancialReport['award_4_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12" >
                                        <div>
                                            <label class="control-label" for="Award4Link">Award 4 Files:</label>
                                           <a href="<?php echo $chFinancialReport['award_4_files']; ?>" target="_blank">View Award 4 Files</a><br>
                                            <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace Award 4 Files.<br>
                                           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-award4" ><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Award 4 Files</button>
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                            </div>
                        </div>
                        <!-- Award 4 Stop -->
                        <!-- Award 5 Start -->
                        <div class="col-12 box_brd_contentpad" id="Award5Panel" style="display: <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_nominations']<5) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="col-sm-12"><br></div>
                                <h4>Award 5</h4>
                                <div class="col-12 form-group">
                                <label for="NominationType5">Select list:</label>
                                    <select class="form-control" id="NominationType5" name="NominationType5" onClick="ShowOutstandingCriteria(5)">
                                       <option style="display:none" disabled selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="award_acc_con">
                                    <div id="OutstandingCriteria5" style="display: none;">
                                <h4>Outstanding Chapter Criteria</h4>
                            <div class="form-group">
                                <label>Did you follow the Bylaws and all instructions from International?<span class="field-required">*</span></label>
                                <select id="OutstandingFollowByLaws5" name="OutstandingFollowByLaws5" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_5_outstanding_follow_bylaws) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_5_outstanding_follow_bylaws === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_5_outstanding_follow_bylaws == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you run a well-rounded program for your members?<span class="field-required">*</span></label><br><p>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings),
                                    a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration.
                                    A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>
                                <select id="OutstandingWellRounded5" name="OutstandingWellRounded5" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_5_outstanding_well_rounded) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_5_outstanding_well_rounded === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_5_outstanding_well_rounded == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you communicate with your Coordinator?<span class="field-required">*</span></label><br><p>Did you send in your newsletter regularly? Send updates? Return telephone calls?
                                    A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>
                                <select id="OutstandingCommunicated5" name="OutstandingCommunicated5" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_5_outstanding_communicated) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_5_outstanding_communicated === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_5_outstanding_communicated == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            <div><br></div>
                            <div class="form-group">
                                <label>Did you support the International MOMS Club?<span class="field-required">*</span></label><br><p>Indications of supporting International MAY include but are not limited to:
                                    <ul>
                                        <li>Providing MOMS Club pins to your members</li>
                                        <li>Purchasing MOMS Club merchandise from International</li>
                                        <li>Forming sister chapters (when possible)</li>
                                        <li>Donating to the Mother-to-Mother Fund</li>
                                        <li>Participating in Area, State and Regional events.</li>
                                    </ul></p>
                                <select id="OutstandingSupportMomsClub5" name="OutstandingSupportMomsClub5" class="form-control select2" style="width: 25%;" required >
                                    <option value="" {{ is_null($chFinancialReport->award_5_outstanding_support_international) ? 'selected' : ''}} disabled>Please Select</option>
                                    <option value="0" {{$chFinancialReport->award_5_outstanding_support_international === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$chFinancialReport->award_5_outstanding_support_international == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                            <div>
                                <div>
                                 <label>Description:</label>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc5" name="AwardDesc5"><?php if (!empty($chFinancialReport)) {echo $chFinancialReport['award_5_outstanding_project_desc'];}?></textarea>
                                 </div>
                                </div>
                                 <div class="col-md-12" id="Award5Block" <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_5_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12">
                                        <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Upload Award 5 Files.<br>
                                        <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-award5" ><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Award 5 Files</button>
                                    </div>
                                </div>
                                <input type="hidden" name="Award5Path" id="Award5Path" value="<?php echo $chFinancialReport['award_5_files']; ?>">
                                <div class="col-md-12 mar_bot_20" <?php if (!empty($chFinancialReport)) {if (!$chFinancialReport['award_5_files']) echo "style=\"display: none;\"";} ?>>
                                    <div class="col-md-12" >
                                        <div>
                                            <label class="control-label" for="Award5Link">Award 1 Files:</label>
                                           <a href="<?php echo $chFinancialReport['award_5_files']; ?>" target="_blank">View Award 5 Files</a><br>
                                            <strong style="color:red">Please Note</strong><br>
                                            This will refresh the screen - be sure to save all work before clicking button to Replace Award 5 Files.<br>
                                           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-award5" ><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Award 5 Files</button>
                                    </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                            </div>
                        </div>
                        <!-- Award 5 Stop -->

                        <div class="box_brd_contentpad" id="AwardSignatureBlock" style="display: <?php if (!empty($chFinancialReport)) {if ($chFinancialReport['award_nominations']<1) echo "none;"; else echo "block;";} else echo "none;";?>">
                              <div class="box_brd_title_box">
                                <div class="col-sm-12"><br></div>
                                 <label>ALL ENTRIES MUST INCLUDE THIS SIGNED AGREEMENT</label>
                              </div>
                                <div class="award_acc_con form-check">
                                    <p>I, the undersigned, affirm that I have the right to submit the enclosed entry to the International MOMS Club for consideration in their Outstanding Chapter Recognitions,
                                        that the enclosed information is accurate and complete to the best of my ability and that I have received permission to enter this information from any other members
                                        who may have contributed to this entry or the original activity/project that is being considered.  I undeerstand that, whether or not my chapter receives a recognition,
                                        the enclosed entry will become the property of the International MOMS Club and that the information, pictures, clippings an/or other materials enclosed may be shared
                                        wtih other MOMS Club Chapters or used in any way the Internation MOMS Club sees fit, with no compensation to me, others involved in this project and/or the chapter(s).
                                        No entries or submitted materials will be returned and the International MOMS Club may reassign any entry to another category if it deems necessary.  Recognitions will
                                        be given in the various categories according to the decision of the International MOMS Club. The awarding of recognitions will be according to merit, and the International
                                        MOMS Club may decide not to give an award in any or all categories if it so chooses. All decisions of the International MOMS Club are final.  Any recognitions are officially
                                        presented to the local chapters, not the individuals, and recognitions will not be personalized with any individual's name. Replacement reccognitions may or may not be made
                                        available at International's discretion, adn if a replacement is made because of an error in the entry information, the cost will be pain in advance by the local chapter.
                                    </p>
                                    <div class="checkbox">
                                        <input class="form-check-input" type="checkbox" id="AwardsAgree" name="AwardsAgree" <?php if (isset($chFinancialReport['award_agree']) && $chFinancialReport['award_agree'] == 1) echo "checked"; ?>  required>
                                        <label class="form-check-label"><strong>I understand and agree to the above</strong></label>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-body text-center">
                             <button type="button" id="btn-step-13" class="btn btn-primary" ><i class="fas fa-save" ></i>&nbsp; Save</button>
                    </div>
                     </section>
                    </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
            </div>
                <!------End Step 13 ------>

                <!------Start Step 14 ------>
                @if ($user_type != 'coordinator')
                <div class="card card-primary <?php if($chFinancialReport['farthest_step_visited'] =='14') echo "active";?>">
                    <div class="card-header" id="accordion-header-members">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFourteen" style="width: 100%;">SUBMIT REPORT</a>
                        </h4>
                    </div>
                    <div id="collapseFourteen" class="collapse {{ $chFinancialReport->farthest_step_visited == '14' ? 'show' : '' }}" data-parent="#accordion">
                        <div class="card-body">
                        <section>
                            <div class="form-row form-group">
                                <div class="col-sm-12">
                                    <strong>Contact information for the person who completed the report.</strong></div>
                                    <div class="col-md-12 float-left">
                                    <strong>Name: </strong>{{ $userName }}
                                    </div>
                                    <div class="col-md-12 float-left">
                                    <strong>Email: </strong><a href="mailto:{{ $userEmail }}">{{ $userEmail }}</a>
                                    </div>
                            </div>
                            <div class="card-body text-center">
                                <button type="button" class="btn btn-primary" id="btn-step-14" ><i class="fas fa-save" ></i>&nbsp; Save</button>
                                @if($thisDate->month >= 3 && $thisDate->month <= 12)
                                <button type="button" class="btn btn-success" id="final-submit" ><i class="fas fa-share-square" ></i>&nbsp; Submit</button>
                                @endif
                            </div>
                        </section>
                    </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
            </div>
            @endif
                <!------End Step 14 ------>

                </div><!-- end of accordion -->
            </form>
            @else
                <p>Your session has expired. Please <a href="{{ url('/login') }}">log in</a> again.</p>
            @endif

            <div class="card-body text-center">
                @if($user_type === 'board')
                    <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
                @endif
                @if($user_type === 'coordinator')
                    <a href="{{ route('viewas.viewchapterpresident', $chFinancialReport['chapter_id']) }}" class="btn btn-primary" id="btn-back"><i class="fas fa-reply"></i>&nbsp; Back</a>
                @endif
                @if($submitted !='1')
                    <button type="button" id="btn-save" class="btn btn-primary"><i class="fas fa-save"></i>&nbsp; Save</button>
                @endif
                @if($submitted =='1')
                    <button type="button" id="btn-download-pdf" class="btn btn-primary" onclick="window.location.href='https://drive.google.com/uc?export=download&id={{ $chFinancialReport['financial_pdf_path'] }}'"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</button>
                    {{-- <a id="btn-download-pdf" href="https://drive.google.com/uc?export=download&id={{ $chFinancialReport['financial_pdf_path'] }}" class="btn btn-primary"><i class="fas fa-file-pdf"></i>&nbsp; Download PDF</a> --}}
                @endif
            </div>


        <div class="modal fade" id="modal-award1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Award 1 Files</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/storeAward1/'. $chFinancialReport['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                        <button type="submit" class="btn btn-sm btn-success" id="btn-award1"><i class="fas fa-upload" ></i>&nbsp; Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-award2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Award 2 Files</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/storeAward2/'. $chFinancialReport['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                        <button type="submit" class="btn btn-sm btn-success" id="btn-award2"><i class="fas fa-upload" ></i>&nbsp; Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-award3">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Award 3 Files</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/storeAward3/'. $chFinancialReport['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                        <button type="submit" class="btn btn-sm btn-success" id="btn-award3"><i class="fas fa-upload" ></i>&nbsp; Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-award4">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Award 4 Files</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/storeAward4/'. $chFinancialReport['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                        <button type="submit" class="btn btn-sm btn-success" id="btn-award4"><i class="fas fa-upload" ></i>&nbsp; Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-award5">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Award 5 Files</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/storeAward5/'. $chFinancialReport['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="fas fa-times" ></i>&nbsp; Close</button>
                        <button type="submit" class="btn brn-sm btn-success" id="btn-award5"><i class="fas fa-upload" ></i>&nbsp; Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Popups -->
    </div>
</div>
</div>

@endsection
@section('customscript')
<script>
/* Disable fields and buttons  */
    $(document).ready(function () {
        var submitted = @json($submitted);
        var userType = @json($user_type);

    if (userType === 'coordinator') {
        // Disable all input fields, select elements, textareas, and buttons
        $('button').not('#btn-back').prop('disabled', true);
        $('input, select, textarea').prop('disabled', true);

    } else if (submitted == '1') {
            $('button').not('#btn-back, #btn-download-pdf').prop('disabled', true);
            $('input, select, textarea').prop('disabled', true);
        } else {
            $('button, input, select, textarea').prop('disabled', false);
        }
        var allDisabled = true;
        $('input, select, textarea').each(function() {
            if (!$(this).prop('disabled')) {
                allDisabled = false;
                return false;
            }
        });

        if (allDisabled) {
            $('.description').show();
        } else {
            $('.description').hide();
        }
    });

/* Curency Mask */
    document.addEventListener("DOMContentLoaded", function() {
        Inputmask().mask(document.querySelectorAll('[data-inputmask]'));

        document.querySelector('form').addEventListener('submit', function(event) {
            const inputs = document.querySelectorAll('[data-inputmask]');
        });
    });

    function debounce(func, delay) {
    let debounceTimer;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => func.apply(context, args), delay);
    }
}

document.querySelectorAll('.input-field-selector').forEach(function(element) {
    element.addEventListener('input', debounce(ChangeMemberCount, 300));
});


</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        ChapterDuesQuestionsChange();
    });

    // Initial function calculation functions
        ChangeChildrensRoomExpenses();
        ChangeMemberCount();
        ChapterDuesQuestionsChange();
        ChangeMeetingFees();
        ChangeServiceProjectExpenses();
        ChangePartyExpenses();
        ChangeOfficeExpenses();
        ChangeInternationalEventExpense();
        ChangeReRegistrationExpense();
        ChangeDonationAmount();
        ChangeOtherOfficeExpenses();
        ChangeBankRec();
        TreasuryBalanceChange();

    function ChapterDuesQuestionsChange(){
        var ChangedMeetingFees=false;
        var ChargedMembersDifferently=false;
        var MembersReducedDues=false;

        var optChangeDuesValue = document.querySelector('input[name="optChangeDues"]:checked')?.value;
        ChangedMeetingFees = optChangeDuesValue === "1";

        var optNewOldDifferentValue = document.querySelector('input[name="optNewOldDifferent"]:checked')?.value;
        ChargedMembersDifferently = optNewOldDifferentValue === "1";

        var optNoFullDuesValue = document.querySelector('input[name="optNoFullDues"]:checked')?.value;
        MembersReducedDues = optNoFullDuesValue === "1";

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        if(ChangedMeetingFees){
            document.getElementById("ifChangeDues").style.display = 'block';
            document.getElementById("ifChangedDues1").style.visibility = 'visible';

            document.getElementById("lblTotalNewMembers").innerHTML = "Total New Members (who paid OLD dues amount)"
            document.getElementById("lblTotalRenewedMembers").innerHTML = "Total Renewed Members (who paid OLD dues amount)"
        }
        else{
            document.getElementById("ifChangeDues").style.display = 'none';
            document.getElementById("ifChangedDues1").style.visibility = 'hidden';

            document.getElementById("TotalNewMembersNewFee").value = 0;
            document.getElementById("TotalRenewedMembersNewFee").value = 0;

            document.getElementById("lblTotalNewMembers").innerHTML = "Total New Members (who paid dues)"
            document.getElementById("lblTotalRenewedMembers").innerHTML = "Total Renewed Members (who paid dues)"
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        if(ChargedMembersDifferently){
            document.getElementById("ifChangedDuesDifferentPerMemberType").style.display = 'block';

            document.getElementById("lblMemberDues").innerHTML  = "Dues collected per New Member"
            document.getElementById("lblNewMemberDues").innerHTML = "Dues collected per New Member (NEW Amount)"

            if(ChangedMeetingFees){
                document.getElementById("ifChangedDuesDifferentPerMemberType1").style.visibility = 'visible';
            }
            else{
                document.getElementById("ifChangedDuesDifferentPerMemberType1").style.visibility = 'hidden';
            }
        }
        else{
            document.getElementById("ifChangedDuesDifferentPerMemberType").style.display = 'none';
            document.getElementById("lblMemberDues").innerHTML = "Dues collected per Member"
            document.getElementById("lblNewMemberDues").innerHTML = "Dues collected per Member (NEW Amount)"
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////////
        if(MembersReducedDues){
            document.getElementById("ifMembersNoDues").style.display = 'block';
        }
        else{
            document.getElementById("ifMembersNoDues").style.display = 'none';
            document.getElementById("MembersNoDues").value = 0;
            document.getElementById("TotalPartialDuesMembers").value = 0;
            document.getElementById("TotalAssociateMembers").value = 0;
            document.getElementById("PartialDuesMemberDues").value = 0;
            document.getElementById("AssociateMemberDues").value = 0;
        }

        ChangeMemberCount();
    }

    function ChangeMeetingFees(){
        var ManditoryFees;
        var VoluntaryFees;
        var TotalFees;

        ManditoryFees = Number(document.getElementById("ManditoryMeetingFeesPaid").value);
        VoluntaryFees = Number(document.getElementById("VoluntaryDonationsPaid").value);

        TotalFees = (ManditoryFees + VoluntaryFees).toFixed(2);

        document.getElementById("TotalMeetingRoomExpenses").value = TotalFees;
        document.getElementById("SumMeetingRoomExpense").value = TotalFees;

        ReCalculateSummaryTotal();
    }

    function ChangeMemberCount(){
        var ChangedMeetingFees = document.querySelector('input[name="optChangeDues"]:checked') && document.querySelector('input[name="optChangeDues"]:checked').value === "1";
        var ChargedMembersDifferently = document.querySelector('input[name="optNewOldDifferent"]:checked') && document.querySelector('input[name="optNewOldDifferent"]:checked').value === "1";
        var MembersReducedDues = document.querySelector('input[name="optNoFullDues"]:checked') && document.querySelector('input[name="optNoFullDues"]:checked').value === "1";

        var NewMembers = Number(document.getElementById("TotalNewMembers") ? document.getElementById("TotalNewMembers").value : 0);
        var RenewedMembers = Number(document.getElementById("TotalRenewedMembers") ? document.getElementById("TotalRenewedMembers").value : 0);
        var NewMembers2 = Number(document.getElementById("TotalNewMembersNewFee") ? document.getElementById("TotalNewMembersNewFee").value : 0);
        var RenewedMembers2 = Number(document.getElementById("TotalRenewedMembersNewFee") ? document.getElementById("TotalRenewedMembersNewFee").value : 0);

        var MemberDues = Number(document.getElementById("MemberDues") ? document.getElementById("MemberDues").value.replace(/[^0-9.-]+/g,"") : 0);
        var NewMemberDues = Number(document.getElementById("NewMemberDues") ? document.getElementById("NewMemberDues").value.replace(/[^0-9.-]+/g,"") : 0);
        var MemberDuesRenewal = Number(document.getElementById("MemberDuesRenewal") ? document.getElementById("MemberDuesRenewal").value.replace(/[^0-9.-]+/g,"") : 0);
        var NewMemberDuesRenewal = Number(document.getElementById("NewMemberDuesRenewal") ? document.getElementById("NewMemberDuesRenewal").value.replace(/[^0-9.-]+/g,"") : 0);

        var MembersNoDues = Number(document.getElementById("MembersNoDues") ? document.getElementById("MembersNoDues").value : 0);
        var PartialDuesMembers = Number(document.getElementById("TotalPartialDuesMembers") ? document.getElementById("TotalPartialDuesMembers").value : 0);
        var AssociateMembers = Number(document.getElementById("TotalAssociateMembers") ? document.getElementById("TotalAssociateMembers").value : 0);

        var TotalMembers = NewMembers + RenewedMembers + MembersNoDues + AssociateMembers + PartialDuesMembers + NewMembers2 + RenewedMembers2;

        document.getElementById("TotalMembers").value = TotalMembers;

        var newMembersDues = NewMembers * MemberDues;
        var renewalMembersDues = RenewedMembers * MemberDues;
        var renewalMembersDuesDiff = RenewedMembers * MemberDuesRenewal;
        var newMembersDuesNew = NewMembers2 * NewMemberDues;
        var renewMembersDuesNew = RenewedMembers2 * NewMemberDues;
        var renewMembersNewDuesDiff = RenewedMembers2 * NewMemberDuesRenewal;
        var partialMembersDues = PartialDuesMembers * Number(document.getElementById("PartialDuesMemberDues").value.replace(/[^0-9.-]+/g,""));
        var associateMembersDues = AssociateMembers * Number(document.getElementById("AssociateMemberDues").value.replace(/[^0-9.-]+/g,""));

        if (ChangedMeetingFees && ChargedMembersDifferently) {
            TotalFees = newMembersDues + renewalMembersDuesDiff + newMembersDuesNew + renewMembersNewDuesDiff + associateMembersDues + partialMembersDues;
        } else if (ChargedMembersDifferently) {
            TotalFees = newMembersDues + renewalMembersDuesDiff + associateMembersDues + partialMembersDues;
        } else if (ChangedMeetingFees) {
            TotalFees = newMembersDues + renewalMembersDues + newMembersDuesNew + renewMembersDuesNew + associateMembersDues + partialMembersDues;
        } else {
            TotalFees = newMembersDues + renewalMembersDues + associateMembersDues + partialMembersDues;
        }

        TotalFees = TotalFees.toFixed(2);

        document.getElementById("TotalDues").value = TotalFees;
        document.getElementById("SumMembershipDuesIncome").value = TotalFees;

        ReCalculateSummaryTotal();
    }

    function ChangeChildrensRoomExpenses(){
        var SupplyTotal = 0;
        var OtherTotal = 0;

        var table = document.getElementById("childrens-room");
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var supplyValue = Number(rows[i].cells[1].querySelector('input').value.replace(/,/g, '')) || 0;
            SupplyTotal += supplyValue;

            var otherValue = Number(rows[i].cells[2].querySelector('input').value.replace(/,/g, '')) || 0;
            OtherTotal += otherValue;
        }

        var TotalMisc = (SupplyTotal + OtherTotal).toFixed(2);
        SupplyTotal = SupplyTotal.toFixed(2);
        OtherTotal = OtherTotal.toFixed(2);

        // Update totals in the footer
        var footer = table.getElementsByTagName('tfoot')[0];
        footer.getElementsByTagName('input')[0].value = SupplyTotal;
        footer.getElementsByTagName('input')[1].value = OtherTotal;

        // Update other totals
        document.getElementById("SumChildrensOtherExpense").value = OtherTotal;
        document.getElementById("SumChildrensSuppliesExpense").value = SupplyTotal;

        var SumPaidSittersExpense = Number(document.getElementById("PaidBabySitters").value.replace(/,/g, '')).toFixed(2);
        document.getElementById("SumPaidSittersExpense").value = SumPaidSittersExpense;

        var TotalChildrensFees = (Number(TotalMisc) + Number(SumPaidSittersExpense)).toFixed(2);
        document.getElementById("SumTotalChildrensRoomExpense").value = TotalChildrensFees;
        document.getElementById("ChildrensRoomTotal").value = TotalChildrensFees;

        ReCalculateSummaryTotal();
    }

    function AddChildrenExpenseRow() {
        var ExpenseCount = parseInt(document.getElementById("ChildrensExpenseRowCount").value, 10);

        var table = document.getElementById("childrens-room");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="ChildrensRoomDesc${ExpenseCount}" id="ChildrensRoomDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ChildrensRoomSupplies${ExpenseCount}" id="ChildrensRoomSupplies${ExpenseCount}" oninput="ChangeChildrensRoomExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ChildrensRoomOther${ExpenseCount}" id="ChildrensRoomOther${ExpenseCount}" oninput="ChangeChildrensRoomExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('ChildrensExpenseRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#childrens-room .form-control'));
    }

    function DeleteChildrenExpenseRow() {
        var ExpenseCount = parseInt(document.getElementById("ChildrensExpenseRowCount").value, 10);

        if (ExpenseCount > 1) {
            var table = document.getElementById("childrens-room");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('ChildrensExpenseRowCount').value = ExpenseCount;

            ChangeChildrensRoomExpenses();
        }
    }

    function ChangeServiceProjectExpenses() {
        var ExpenseTotal = 0;
        var IncomeTotal = 0;
        var CharityTotal = 0;
        var M2MTotal = 0;

        var table = document.getElementById("service-projects");
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var incomeValue = Number(rows[i].cells[1].querySelector('input').value.replace(/,/g, '')) || 0;
            IncomeTotal += incomeValue;

            var expenseValue = Number(rows[i].cells[2].querySelector('input').value.replace(/,/g, '')) || 0;
            ExpenseTotal += expenseValue;

            var charityValue = Number(rows[i].cells[3].querySelector('input').value.replace(/,/g, '')) || 0;
            CharityTotal += charityValue;

            var m2mValue = Number(rows[i].cells[4].querySelector('input').value.replace(/,/g, '')) || 0;
            M2MTotal += m2mValue;
        }

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);
        CharityTotal = CharityTotal.toFixed(2);
        M2MTotal = M2MTotal.toFixed(2);

        var footer = table.getElementsByTagName('tfoot')[0];
        footer.getElementsByTagName('input')[0].value = IncomeTotal;
        footer.getElementsByTagName('input')[1].value = ExpenseTotal;
        footer.getElementsByTagName('input')[2].value = CharityTotal;
        footer.getElementsByTagName('input')[3].value = M2MTotal;

        document.getElementById("ServiceProjectIncomeTotal").value = IncomeTotal;
        document.getElementById("SumServiceProjectIncome").value = IncomeTotal;

        // document.getElementById("ServiceProjectSuppliesTotal").value = ExpenseTotal;
        // document.getElementById("ServiceProjectDonationTotal").value = CharityTotal;
        // document.getElementById("ServiceProjectM2MDonationTotal").value = M2MTotal;
        document.getElementById("SumServiceProjectExpense").value = ExpenseTotal;
        document.getElementById("SumDonationExpense").value = CharityTotal;
        document.getElementById("SumM2MExpense").value = M2MTotal;

        var TotalServiceProjectFees = parseFloat(ExpenseTotal) + parseFloat(CharityTotal) + parseFloat(M2MTotal);
        TotalServiceProjectFees = TotalServiceProjectFees.toFixed(2);
        document.getElementById("ServiceProjectExpenseTotal").value = TotalServiceProjectFees;
        document.getElementById("SumTotalServiceProjectExpense").value = TotalServiceProjectFees;

        ReCalculateSummaryTotal();
    }

    function AddServiceProjectRow() {
        var ExpenseCount = parseInt(document.getElementById("ServiceProjectRowCount").value);
        var table = document.getElementById("service-projects");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        cell1.innerHTML = `<div class="form-group"><textarea class="form-control" rows="4" name="ServiceProjectDesc${ExpenseCount}" id="ServiceProjectDesc${ExpenseCount}"></textarea></div>`;
        cell2.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectIncome${ExpenseCount}" id="ServiceProjectIncome${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectSupplies${ExpenseCount}" id="ServiceProjectSupplies${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell4.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectDonatedCharity${ExpenseCount}" id="ServiceProjectDonatedCharity${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell5.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectDonatedM2M${ExpenseCount}" id="ServiceProjectDonatedM2M${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('ServiceProjectRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#service-projects .form-control'));
    }

    function DeleteServiceProjectRow() {
        var ExpenseCount = parseInt(document.getElementById("ServiceProjectRowCount").value, 10);

        if (ExpenseCount > 1) {
            var table = document.getElementById("service-projects");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('ServiceProjectRowCount').value = ExpenseCount;

            ChangeServiceProjectExpenses();
        }
    }

    function ChangePartyExpenses() {
        var IncomeTotal = 0;
        var ExpenseTotal = 0;

        var table = document.getElementById("party-expenses");
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var incomeValue = Number(rows[i].cells[1].querySelector('input').value.replace(/,/g, '')) || 0;
            IncomeTotal += incomeValue;

            var expenseValue = Number(rows[i].cells[2].querySelector('input').value.replace(/,/g, '')) || 0;
            ExpenseTotal += expenseValue;
        }

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);

        // Update totals in the footer
        var footer = table.getElementsByTagName('tfoot')[0];
        footer.getElementsByTagName('input')[0].value = IncomeTotal;
        footer.getElementsByTagName('input')[1].value = ExpenseTotal;

        // Update other totals
        document.getElementById("PartyIncomeTotal").value = IncomeTotal;
        document.getElementById("PartyExpenseTotal").value = ExpenseTotal;
        document.getElementById("SumPartyIncome").value = IncomeTotal;
        document.getElementById("SumPartyExpense").value = ExpenseTotal;

        ReCalculateSummaryTotal();
    }

    function AddPartyExpenseRow() {
        var ExpenseCount = parseInt(document.getElementById("PartyExpenseRowCount").value);
        var table = document.getElementById("party-expenses");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="PartyDesc${ExpenseCount}" id="PartyDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="PartyIncome${ExpenseCount}" id="PartyIncome${ExpenseCount}" oninput="ChangePartyExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="PartyExpenses${ExpenseCount}" id="PartyExpenses${ExpenseCount}" oninput="ChangePartyExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('PartyExpenseRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#party-expenses .form-control'));
    }

    function DeletePartyExpenseRow() {
        var ExpenseCount = parseInt(document.getElementById("PartyExpenseRowCount").value);

        if (ExpenseCount > 1) {
            var table = document.getElementById("party-expenses");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('PartyExpenseRowCount').value = ExpenseCount;

            ChangePartyExpenses();
        }
    }

    function ChangeOfficeExpenses(){
    var totalExpenses = 0;  // Initialize totalExpenses
    var table = document.getElementById("office-expenses");
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    // Sum up all the expenses
    for (var i = 0; i < rows.length; i++) {
        var expenseValue = Number(rows[i].cells[1].querySelector('input').value.replace(/,/g, '')) || 0;
        totalExpenses += expenseValue;
    }

    // Update totals in the footer
    var footer = table.getElementsByTagName('tfoot')[0];
    footer.getElementsByTagName('input')[0].value = totalExpenses.toFixed(2);

    // Get other expenses and format them correctly
    var SumPrintingExpense = Number(document.getElementById("PrintingCosts").value.replace(/,/g, '')) || 0;
    var SumPostageExpense = Number(document.getElementById("PostageCosts").value.replace(/,/g, '')) || 0;
    var SumPinsExpense = Number(document.getElementById("MembershipPins").value.replace(/,/g, '')) || 0;

    // Calculate OperatingTotal (before formatting)
    var OperatingTotal = totalExpenses + SumPrintingExpense + SumPostageExpense + SumPinsExpense;

    // Update the fields with formatted values
    document.getElementById("SumOtherOperatingExpense").value = totalExpenses.toFixed(2);
    document.getElementById("SumPrintingExpense").value = SumPrintingExpense.toFixed(2);
    document.getElementById("SumPostageExpense").value = SumPostageExpense.toFixed(2);
    document.getElementById("SumPinsExpense").value = SumPinsExpense.toFixed(2);

    // Set the OperatingTotal
    document.getElementById("SumTotalChildrensRoomExpense").value = OperatingTotal.toFixed(2);
    document.getElementById("TotalOperatingExpense").value = OperatingTotal.toFixed(2);

    // Call summary recalculation
    ReCalculateSummaryTotal();
}

    function AddOfficeExpenseRow() {
        var ExpenseCount = document.getElementById("OfficeExpenseRowCount").value;
        var table = document.getElementById("office-expenses");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="OfficeDesc${ExpenseCount}" id="OfficeDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="OfficeExpenses${ExpenseCount}" id="OfficeExpenses${ExpenseCount}" oninput="ChangeOfficeExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('OfficeExpenseRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#office-expenses .form-control'));
    }

    function DeleteOfficeExpenseRow() {
        var ExpenseCount = document.getElementById("OfficeExpenseRowCount").value;

        if (ExpenseCount > 1) {
            var table = document.getElementById("office-expenses");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('OfficeExpenseRowCount').value = ExpenseCount;

            ChangeOfficeExpenses();
        }
    }

    function ChangeReRegistrationExpense(){
        var ReRegistrationFee=0;

        ReRegistrationFee = Number(document.getElementById("AnnualRegistrationFee").value);

        document.getElementById("SumChapterReRegistrationExpense").value = ReRegistrationFee.toFixed(2);

        ReCalculateSummaryTotal();
    }

    function ChangeInternationalEventExpense(){
        var ExpenseTotal=0;
        var IncomeTotal=0;

        var table=document.getElementById("international_events");
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var incomeValue = Number(rows[i].cells[1].querySelector('input').value.replace(/,/g, '')) || 0;
            IncomeTotal += incomeValue;

            var expenseValue = Number(rows[i].cells[2].querySelector('input').value.replace(/,/g, '')) || 0;
            ExpenseTotal += expenseValue;
        }

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);

        var footer = table.getElementsByTagName('tfoot')[0];
        footer.getElementsByTagName('input')[0].value = IncomeTotal;
        footer.getElementsByTagName('input')[1].value = ExpenseTotal;

        document.getElementById("InternationalEventIncomeTotal").value = IncomeTotal;
        document.getElementById("InternationalEventExpenseTotal").value = ExpenseTotal;

        document.getElementById("SumInternationalEventIncome").value = IncomeTotal;
        document.getElementById("SumInternationalEventExpense").value = ExpenseTotal;

        ReCalculateSummaryTotal();
    }

    function AddInternationalEventRow() {
        var ExpenseCount = document.getElementById("InternationalEventRowCount").value;
        var table = document.getElementById("international_events");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="InternationalEventDesc${ExpenseCount}" id="InternationalEventDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="InternationalEventIncome${ExpenseCount}" id="InternationalEventIncome${ExpenseCount}" oninput="ChangeInternationalEventExpense()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="InternationalEventExpense${ExpenseCount}" id="InternationalEventExpense${ExpenseCount}" oninput="ChangeInternationalEventExpense()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('InternationalEventRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#international_events .form-control'));
    }

    function DeleteInternationalEventRow() {
        var ExpenseCount = document.getElementById("InternationalEventRowCount").value;

        if (ExpenseCount > 1) {
            var table = document.getElementById("international_events");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('InternationalEventRowCount').value = ExpenseCount;

            ChangeInternationalEventExpense();
        }
    }

    function ChangeDonationAmount() {
    var IncomeTotal = 0;
    var table = document.getElementById("donation-income");
    var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (var i = 0; i < rows.length; i++) {
        var incomeValue = Number(rows[i].cells[3].querySelector('input').value.replace(/,/g, '')) || 0;
        IncomeTotal += incomeValue;
    }

    var footer = table.getElementsByTagName('tfoot')[0];
    footer.getElementsByTagName('input')[0].value = IncomeTotal.toFixed(2);

    document.getElementById("DonationTotal").value = IncomeTotal;
    document.getElementById("SumMonetaryDonationIncome").value = IncomeTotal;
}

    function AddMonDonationRow() {
        var ExpenseCount = document.getElementById("MonDonationRowCount").value;
        var table = document.getElementById("donation-income");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="DonationDesc${ExpenseCount}" id="DonationDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="DonorInfo${ExpenseCount}" id="DonorInfo${ExpenseCount}"></div>`;
        cell3.innerHTML = `<div class="form-group"><input type="date" class="form-control" name="MonDonationDate${ExpenseCount}" id="MonDonationDate${ExpenseCount}"></div>`;
        cell4.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="DonationAmount${ExpenseCount}" id="DonationAmount${ExpenseCount}" oninput="ChangeDonationAmount()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('MonDonationRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#donation-income .form-control'));
    }

    function DeleteMonDonationRow() {
        var ExpenseCount = document.getElementById("MonDonationRowCount").value;

        if (ExpenseCount > 1) {
            var table = document.getElementById("donation-income");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('MonDonationRowCount').value = ExpenseCount;

            ChangeDonationAmount();
        }
    }

    function AddNonMonDonationRow() {
        var ExpenseCount = document.getElementById("NonMonDonationRowCount").value;
        var table = document.getElementById("donation-goods");
        var row = table.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="NonMonDonationDesc${ExpenseCount}" id="NonMonDonationDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="NonMonDonorInfo${ExpenseCount}" id="NonMonDonorInfo${ExpenseCount}"></div>`;
        cell3.innerHTML = `<div class="form-group"><input type="date" class="form-control" name="NonMonDonationDate${ExpenseCount}" id="NonMonDonationDate${ExpenseCount}"></div>`;

        ExpenseCount++;
        document.getElementById('NonMonDonationRowCount').value = ExpenseCount;
    }

    function DeleteNonMonDonationRow() {
        var ExpenseCount = document.getElementById("NonMonDonationRowCount").value;

        if (ExpenseCount > 1) {
            document.getElementById("donation-goods").deleteRow(ExpenseCount - 1);
            ExpenseCount--;        // Update the expense count
            document.getElementById('NonMonDonationRowCount').value = ExpenseCount;

            if (ExpenseCount === 1) {
                document.querySelector('.btn-danger').setAttribute('disabled', 'disabled');
            }
        }
    }

    function ChangeOtherOfficeExpenses() {
        var ExpenseTotal = 0;
        var IncomeTotal = 0;

        var table = document.getElementById("other-office-expenses");
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (var i = 0; i < rows.length; i++) {
            var incomeValue = Number(rows[i].cells[1].querySelector('input').value.replace(/,/g, '')) || 0;
            IncomeTotal += incomeValue;

            var expenseValue = Number(rows[i].cells[2].querySelector('input').value.replace(/,/g, '')) || 0;
            ExpenseTotal += expenseValue;
        }

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);

        var footer = table.getElementsByTagName('tfoot')[0];
        footer.getElementsByTagName('input')[0].value = IncomeTotal;
        footer.getElementsByTagName('input')[1].value = ExpenseTotal;

        document.getElementById("OtherOfficeExpenseTotal").value = ExpenseTotal;
        document.getElementById("OtherOfficeIncomeTotal").value = IncomeTotal;
        document.getElementById("SumOtherIncome").value = IncomeTotal;
        document.getElementById("SumOtherExpense").value = ExpenseTotal;

        ReCalculateSummaryTotal();
    }

    function AddOtherOfficeExpenseRow() {
        var ExpenseCount = document.getElementById("OtherOfficeExpenseRowCount").value;
        var table = document.getElementById("other-office-expenses");
        var tbody = table.getElementsByTagName('tbody')[0];
        var row = tbody.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);

        cell1.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="OtherOfficeDesc${ExpenseCount}" id="OtherOfficeDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="OtherOfficeIncome${ExpenseCount}" id="OtherOfficeIncome${ExpenseCount}" oninput="ChangeOtherOfficeExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="OtherOfficeExpenses${ExpenseCount}" id="OtherOfficeExpenses${ExpenseCount}" oninput="ChangeOtherOfficeExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('#other-office-expenses .form-control'));
    }

    function DeleteOtherOfficeExpenseRow() {
        var ExpenseCount = document.getElementById("OtherOfficeExpenseRowCount").value;

        if (ExpenseCount > 1) {
            var table = document.getElementById("other-office-expenses");
            var tbody = table.getElementsByTagName('tbody')[0];
            tbody.deleteRow(-1);

            ExpenseCount--;
            document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;

            ChangeOtherOfficeExpenses();

        }
    }

    function TreasuryBalanceChange() {
        var TreasuryBalance = parseFloat(document.getElementById("AmountReservedFromLastYear").value.replace(/,/g, '')) || 0;
        document.getElementById("AmountReservedFromLastYear").value = TreasuryBalance.toFixed(2);

        ReCalculateSummaryTotal();
    }

    function ChangeBankRec() {
        var PaymentTotal = 0;
        var DepositTotal = 0;

        var table = document.getElementById("bank-rec");

        for (var i = 1, row; row = table.rows[i]; i++) {
            // Payment Amount
            var paymentInput = row.querySelector('input[name^="BankRecPaymentAmount"]');
            var paymentValue = paymentInput ? parseFloat(paymentInput.value.replace(/,/g, '')) || 0 : 0;
            PaymentTotal += paymentValue;

            // Deposit Amount
            var depositInput = row.querySelector('input[name^="BankRecDepositAmount"]');
            var depositValue = depositInput ? parseFloat(depositInput.value.replace(/,/g, '')) || 0 : 0;
            DepositTotal += depositValue;
        }

        var BankBalanceNow = parseFloat(document.getElementById("BankBalanceNow").value.replace(/,/g, '')) || 0;

        var TotalFees = (BankBalanceNow - PaymentTotal + DepositTotal).toFixed(2);
        document.getElementById("ReconciledBankBalance").value = TotalFees;

        var TreasuryBalanceNow = parseFloat(document.getElementById("TreasuryBalanceNow").value.replace(/,/g, '')) || 0;

        var alertDiv = document.getElementById("ReconciliationAlert");
        var warningDiv = document.getElementById("ReconciledBankBalanceWarning");

        if (TotalFees != TreasuryBalanceNow) {
            alertDiv.style.display = "block";
            warningDiv.innerText = "Reconciled Bank Balance does not match treasury balance now. These numbers must match for your report to be in balance";
            warningDiv.style.borderStyle = "none";
        } else {
            alertDiv.style.display = "none";
        }
    }

    function AddBankRecRow(){
        var ExpenseCount = document.getElementById("BankRecRowCount").value;

        var table = document.getElementById("bank-rec");
        var row = table.insertRow(-1);

        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        cell1.innerHTML = `<div class="form-group"><input type="date" class="form-control" name="BankRecDate${ExpenseCount}" id="BankRecDate${ExpenseCount}" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $bank_rec_array[$row]['bank_rec_date'] ?? '' }}"></div>`;
        cell2.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="BankRecCheckNo${ExpenseCount}" id="BankRecCheckNo${ExpenseCount}"  oninput="ChangeBankRec()"></div>`;
        cell3.innerHTML = `<div class="form-group"><input type="text" class="form-control" name="BankRecDesc${ExpenseCount}" id="BankRecDesc${ExpenseCount}"></div>`;
        cell4.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="BankRecPaymentAmount${ExpenseCount}" id="BankRecPaymentAmount${ExpenseCount}" oninput="ChangeBankRec()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell5.innerHTML = `<div class="form-group"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="BankRecDepositAmount${ExpenseCount}" id="BankRecDepositAmount${ExpenseCount}" oninput="ChangeBankRec()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

        ExpenseCount++;
        document.getElementById('BankRecRowCount').value = ExpenseCount;

        Inputmask().mask(document.querySelectorAll('[data-inputmask]'));
    }

    function DeleteBankRecRow() {
        var ExpenseCount = document.getElementById("BankRecRowCount").value;

        if (ExpenseCount > 1) {
            var table = document.getElementById("bank-rec");
            table.deleteRow(ExpenseCount - 1);
            ExpenseCount--;
            document.getElementById('BankRecRowCount').value = ExpenseCount;
            ChangeBankRec();

            if (ExpenseCount === 1) {
                document.querySelector('.btn-danger').setAttribute('disabled', 'disabled');
            }
        }
    }

    function ReCalculateSummaryTotal() {
        // Helper function to remove commas and convert to number
        function parseNumber(value) {
            return Number(value.replace(/,/g, ''));
        }

        // Initialize summary items
        var SumOtherIncome = 0;
        var SumMeetingRoomExpense = 0;
        var SumTotalChildrensRoomExpense = 0;
        var ServiceIncomeTotal = 0;
        var ServiceExpenseTotal = 0;
        var SumOtherExpense = 0;
        var SumOperatingExpense = 0;
        var SumTotalExpense = 0;
        var SumTotalIncome = 0;
        var SumTotalNetIncome = 0;
        var SumPartyExpense = 0;
        var SumPartyIncome = 0;
        var SumInternationalEventExpense = 0;
        var SumInternationalEventIncome = 0;
        var SumMonetaryDonationIncome = 0;
        var SumChapterReRegistrationExpense = 0;
        var TreasuryBalance = 0;
        var TreasuryBalanceNow = 0;

        // Retrieve and sanitize input values
        SumMeetingRoomExpense = parseNumber(document.getElementById("SumMeetingRoomExpense").value);
        SumMembershipDuesIncome = parseNumber(document.getElementById("SumMembershipDuesIncome").value);
        SumTotalChildrensRoomExpense = parseNumber(document.getElementById("SumTotalChildrensRoomExpense").value);
        ServiceIncomeTotal = parseNumber(document.getElementById("SumServiceProjectIncome").value);
        ServiceExpenseTotal = parseNumber(document.getElementById("SumTotalServiceProjectExpense").value);
        SumPartyIncome = parseNumber(document.getElementById("SumPartyIncome").value);
        SumPartyExpense = parseNumber(document.getElementById("SumPartyExpense").value);
        SumOtherIncome = parseNumber(document.getElementById("SumOtherIncome").value);
        SumOtherExpense = parseNumber(document.getElementById("SumOtherExpense").value);
        SumOperatingExpense = parseNumber(document.getElementById("SumOperatingExpense").value);
        SumInternationalEventExpense = parseNumber(document.getElementById("SumInternationalEventExpense").value);
        SumInternationalEventIncome = parseNumber(document.getElementById("SumInternationalEventIncome").value);
        SumMonetaryDonationIncome = parseNumber(document.getElementById("SumMonetaryDonationIncome").value);
        SumChapterReRegistrationExpense = parseNumber(document.getElementById("SumChapterReRegistrationExpense").value);
        // TreasuryBalance = parseNumber(document.getElementById("SumAmountReservedFromPreviousYear").value);
        TreasuryBalance = parseNumber(document.getElementById("AmountReservedFromLastYear").value);


        // Perform calculations
        SumTotalExpense = SumTotalChildrensRoomExpense + SumMeetingRoomExpense + ServiceExpenseTotal + SumOtherExpense + SumPartyExpense + SumOperatingExpense + SumInternationalEventExpense + SumChapterReRegistrationExpense;
        SumTotalIncome = ServiceIncomeTotal + SumOtherIncome + SumPartyIncome + SumMembershipDuesIncome + SumInternationalEventIncome + SumMonetaryDonationIncome;

        TreasuryBalanceNow = TreasuryBalance - SumTotalExpense + SumTotalIncome;
        SumTotalNetIncome = SumTotalIncome - SumTotalExpense;

        // Update values in the DOM
        document.getElementById("SumTotalExpense").value = SumTotalExpense.toFixed(2);
        document.getElementById("SumTotalIncome").value = SumTotalIncome.toFixed(2);
        document.getElementById("TotalNetIncome").value = SumTotalNetIncome.toFixed(2);
        document.getElementById("SumTotalNetIncome").value = SumTotalNetIncome.toFixed(2);
        document.getElementById("TreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);
        document.getElementById("TreasuryBalanceNowR").value = TreasuryBalanceNow.toFixed(2);
        // document.getElementById("SumTreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);

        // Call other functions if necessary
        ChangeBankRec();
    }

// Sectiom 10 Questions - Explainations Rquired.
document.addEventListener("DOMContentLoaded", function() {

    // Add event listeners for each radio button group
    document.querySelectorAll('input[name="ReceiveCompensation"]').forEach(function(el) {
        el.addEventListener("change", ToggleReceiveCompensationExplanation);
    });
    document.querySelectorAll('input[name="FinancialBenefit"]').forEach(function(el) {
        el.addEventListener("change", ToggleFinancialBenefitExplanation);
    });
    document.querySelectorAll('input[name="InfluencePolitical"]').forEach(function(el) {
        el.addEventListener("change", ToggleInfluencePoliticalExplanation);
    });
    document.querySelectorAll('input[name="VoteAllActivities"]').forEach(function(el) {
        el.addEventListener("change", ToggleVoteAllActivitiesExplanation);
    });
    document.querySelectorAll('input[name="BoughtPins"]').forEach(function(el) {
        el.addEventListener("change", ToggleBoughtPinsExplanation);
    });
    document.querySelectorAll('input[name="BoughtMerch"]').forEach(function(el) {
        el.addEventListener("change", ToggleBoughtMerchExplanation);
    });
    document.querySelectorAll('input[name="OfferedMerch"]').forEach(function(el) {
        el.addEventListener("change", ToggleOfferedMerchExplanation);
    });
    document.querySelectorAll('input[name="ByLawsAvailable"]').forEach(function(el) {
        el.addEventListener("change", ToggleByLawsAvailableExplanation);
    });
    document.querySelectorAll('input[name="ChildOutings"]').forEach(function(el) {
        el.addEventListener("change", ToggleChildOutingsExplanation);
    });
    document.querySelectorAll('input[name="MotherOutings"]').forEach(function(el) {
        el.addEventListener("change", ToggleMotherOutingsExplanation);
    });
    document.querySelectorAll('input[name="MeetingSpeakers"]').forEach(function(el) {
        el.addEventListener("change", ToggleMeetingSpeakersExplanation);
    });
    document.querySelectorAll('input[name="Activity[]"]').forEach(function(el) {
        el.addEventListener("change", ToggleActivityOtherExplanation);
    });
    document.querySelectorAll('input[name="ContributionsNotRegNP"]').forEach(function(el) {
        el.addEventListener("change", ToggleContributionsNotRegNPExplanation);
    });
    document.querySelectorAll('input[name="PerformServiceProject"]').forEach(function(el) {
        el.addEventListener("change", TogglePerformServiceProjectExplanation);
    });
    document.querySelectorAll('input[name="FileIRS"]').forEach(function(el) {
        el.addEventListener("change", ToggleFileIRSExplanation);
    });
    document.querySelectorAll('input[name="BankStatementIncluded"]').forEach(function(el) {
        el.addEventListener("change", ToggleBankStatementIncludedExplanation);
    });
    document.querySelectorAll('input[name="BankStatementDiff"]').forEach(function(el) {
        el.addEventListener("change", ToggleBankStatementDiffExplanation);
    });
    document.querySelectorAll('input[name="Playgroups"]').forEach(function(el) {
        el.addEventListener("change", TogglePlaygroupsExplanation);
    });
    document.querySelectorAll('input[name="ParkDays"]').forEach(function(el) {
        el.addEventListener("change", ToggleParkDaysExplanation);
    });
    document.querySelectorAll('input[name="SisterChapter"]').forEach(function(el) {
        el.addEventListener("change", ToggleSisterChapterExplanation);
    });

// Initial function calls to ensure the correct sections are displayed based on pre-selected values
    ToggleReceiveCompensationExplanation();
    ToggleFinancialBenefitExplanation();
    ToggleInfluencePoliticalExplanation();
    ToggleVoteAllActivitiesExplanation();
    ToggleBoughtPinsExplanation();
    ToggleBoughtMerchExplanation();
    ToggleOfferedMerchExplanation();
    ToggleByLawsAvailableExplanation();
    ToggleChildOutingsExplanation();
    ToggleMotherOutingsExplanation();
    ToggleMeetingSpeakersExplanation();
    ToggleActivityOtherExplanation();
    ToggleContributionsNotRegNPExplanation();
    TogglePerformServiceProjectExplanation();
    ToggleFileIRSExplanation();
    ToggleBankStatementIncludedExplanation();
    ToggleBankStatementDiffExplanation();
    TogglePlaygroupsExplanation();
    ToggleParkDaysExplanation();
    ToggleSisterChapterExplanation();
});

    function ToggleReceiveCompensationExplanation() {
        var selectedRadio = document.querySelector('input[name="ReceiveCompensation"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 1 */

        if (selectedValue === "1") {
            $('#ReceiveCompensationExplanation').addClass('tx-cls');
            document.getElementById("divReceiveCompensationExplanation").style.display = 'block'; // If "Yes" is selected
        } else {
            $('#ReceiveCompensationExplanation').removeClass('tx-cls');
            document.getElementById("divReceiveCompensationExplanation").style.display = 'none'; // If "No" is selected
        }
    }

    function ToggleFinancialBenefitExplanation() {
        var selectedRadio = document.querySelector('input[name="FinancialBenefit"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 2 */

        if (selectedValue === "1") {
            $('#FinancialBenefitExplanation').addClass('tx-cls');
            document.getElementById("divFinancialBenefitExplanation").style.display = 'block'; // If "Yes" is selected
        } else {
            $('#FinancialBenefitExplanation').removeClass('tx-cls');
            document.getElementById("divFinancialBenefitExplanation").style.display = 'none'; // If "No" is selected
        }
    }

    function ToggleInfluencePoliticalExplanation() {
        var selectedRadio = document.querySelector('input[name="InfluencePolitical"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 3 */

        if (selectedValue === "1") {
            $('#InfluencePoliticalExplanation').addClass('tx-cls');
            document.getElementById("divInfluencePoliticalExplanation").style.display = 'block'; // If "Yes" is selected
        } else {
            $('#InfluencePoliticalExplanation').removeClass('tx-cls');
            document.getElementById("divInfluencePoliticalExplanation").style.display = 'none'; // If "No" is selected
        }
    }

    function ToggleVoteAllActivitiesExplanation() {
        var selectedRadio = document.querySelector('input[name="VoteAllActivities"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 4 */

        if (selectedValue === "0") {
            $('#VoteAllActivitiesExplanation').addClass('tx-cls');
            document.getElementById("divVoteAllActivitiesExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#VoteAllActivitiesExplanation').removeClass('tx-cls');
            document.getElementById("divVoteAllActivitiesExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleBoughtPinsExplanation() {
        var selectedRadio = document.querySelector('input[name="BoughtPins"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 5 */

        if (selectedValue === "0") {
            $('#BoughtPinsExplanation').addClass('tx-cls');
            document.getElementById("divBoughtPinsExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#BoughtPinsExplanation').removeClass('tx-cls');
            document.getElementById("divBoughtPinsExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleBoughtMerchExplanation() {
        var selectedRadio = document.querySelector('input[name="BoughtMerch"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 6 */

        if (selectedValue === "0") {
            $('#BoughtMerchExplanation').addClass('tx-cls');
            document.getElementById("divBoughtMerchExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#BoughtMerchExplanation').removeClass('tx-cls');
            document.getElementById("divBoughtMerchExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleOfferedMerchExplanation() {
        var selectedRadio = document.querySelector('input[name="OfferedMerch"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 7 */

        if (selectedValue === "0") {
            $('#OfferedMerchExplanation').addClass('tx-cls');
            document.getElementById("divOfferedMerchExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#OfferedMerchExplanation').removeClass('tx-cls');
            document.getElementById("divOfferedMerchExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleByLawsAvailableExplanation() {
        var selectedRadio = document.querySelector('input[name="ByLawsAvailable"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 8 */

        if (selectedValue === "0") {
            $('#ByLawsAvailableExplanation').addClass('tx-cls');
            document.getElementById("divByLawsAvailableExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#ByLawsAvailableExplanation').removeClass('tx-cls');
            document.getElementById("divByLawsAvailableExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleChildOutingsExplanation() {
        var selectedRadio = document.querySelector('input[name="ChildOutings"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 11 */

        if (selectedValue === "0") {
            $('#ChildOutingsExplanation').addClass('tx-cls');
            document.getElementById("divChildOutingsExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#ChildOutingsExplanation').removeClass('tx-cls');
            document.getElementById("divChildOutingsExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleMotherOutingsExplanation() {
        var selectedRadio = document.querySelector('input[name="MotherOutings"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 12 */

        if (selectedValue === "0") {
            $('#MotherOutingsExplanation').addClass('tx-cls');
            document.getElementById("divMotherOutingsExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#MotherOutingsExplanation').removeClass('tx-cls');
            document.getElementById("divMotherOutingsExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleMeetingSpeakersExplanation() {
        var selectedRadio = document.querySelector('input[name="MeetingSpeakers"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null;

        if (selectedValue === null) {
            document.getElementById("divMeetingSpeakersTopics").style.display = 'none';
            return;
        }

        if (selectedValue === "0") {
            $('#MeetingSpeakersExplanation').addClass('tx-cls');
            document.getElementById("divMeetingSpeakersTopics").style.display = 'none';
        } else {
            $('#MeetingSpeakersExplanation').removeClass('tx-cls');
            document.getElementById("divMeetingSpeakersTopics").style.display = 'block';
        }
    }

    function ToggleSisterChapterExplanation() {
        var selectedRadio = document.querySelector('input[name="SisterChapter"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null;

        if (selectedValue === "0") {
            $('#SisterChapterExplanation').addClass('tx-cls');
            document.getElementById("divSisterChapterExplanation").style.display = 'none';
        } else {
            $('#SisterChapterExplanation').removeClass('tx-cls');
            document.getElementById("divSisterChapterExplanation").style.display = 'block';
        }
    }

    function TogglePlaygroupsExplanation() {
        var selectedRadio = document.querySelector('input[name="Playgroups"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null;

        if (selectedValue === "0") {
            $('#PlaygroupsExplanation').addClass('tx-cls');
            document.getElementById("divPlaygroupsExplanation").style.display = 'block';
        } else {
            $('#PlaygroupsExplanation').removeClass('tx-cls');
            document.getElementById("divPlaygroupsExplanation").style.display = 'none';
        }
    }

    function ToggleParkDaysExplanation() {
        var selectedRadio = document.querySelector('input[name="ParkDays"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null;

        if (selectedValue === "0") {
            $('#ParkDaysExplanation').addClass('tx-cls');
            document.getElementById("divParkDaysExplanation").style.display = 'block';
        } else {
            $('#ParkDaysExplanation').removeClass('tx-cls');
            document.getElementById("divParkDaysExplanation").style.display = 'none';
        }
    }

    function ToggleActivityOtherExplanation() {
        var otherCheckbox = document.querySelector('input[name="Activity[]"][value="5"]'); /* Questions 16 */

        if (otherCheckbox?.checked) {
            document.getElementById("divActivityOtherExplanation").style.display = 'block'; // If "Other" is selected
        } else {
            document.getElementById("divActivityOtherExplanation").style.display = 'none';
        }
    }

    function ToggleContributionsNotRegNPExplanation() {
        var selectedRadio = document.querySelector('input[name="ContributionsNotRegNP"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 17 */

        if (selectedValue === "1") {
            $('#ContributionsNotRegNPExplanation').addClass('tx-cls');
            document.getElementById("divContributionsNotRegNPExplanation").style.display = 'block'; // If "Yes" is selected
        } else {
            $('#ContributionsNotRegNPExplanation').removeClass('tx-cls');
            document.getElementById("divContributionsNotRegNPExplanation").style.display = 'none'; // If "No" is selected
        }
    }

    function TogglePerformServiceProjectExplanation() {
        var selectedRadio = document.querySelector('input[name="PerformServiceProject"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 18 */

        if (selectedValue === "0") {
            $('#PerformServiceProjectExplanation').addClass('tx-cls');
            document.getElementById("divPerformServiceProjectExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#PerformServiceProjectExplanation').removeClass('tx-cls');
            document.getElementById("divPerformServiceProjectExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleFileIRSExplanation() {
        var selectedRadio = document.querySelector('input[name="FileIRS"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 21 */

        if (selectedValue === "0") {
            $('#FileIRSExplanation').addClass('tx-cls');
            document.getElementById("divFileIRSExplanation").style.display = 'block'; // If "No" is selected
        } else {
            $('#FileIRSExplanation').removeClass('tx-cls');
            document.getElementById("divFileIRSExplanation").style.display = 'none'; // If "Yes" is selected
        }
    }

    function ToggleBankStatementIncludedExplanation() {
        var selectedRadio = document.querySelector('input[name="BankStatementIncluded"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 21 */

        if (selectedValue === "0") {
            $('#BankStatementIncludedExplanation').addClass('tx-cls');
            document.getElementById("divBankStatementIncludedExplanation").style.display = 'block'; // If "No" is selected
            document.getElementById("WheresTheMoney").style.display = 'block'; // If "No" is selected
        } else {
            $('#BankStatementIncludedExplanation').removeClass('tx-cls');
            document.getElementById("divBankStatementIncludedExplanation").style.display = 'none'; // If "Yes" is selected
            document.getElementById("WheresTheMoney").style.display = 'none'; // If "Yes" is selected
        }
    }

    function AddAwardNomination(){
        // Did they say no, if so, we need to mark the explanation field as required
        if (document.getElementById("Award1Panel").style.display === 'none'){
            document.getElementById("Award1Panel").style.display = 'block';
            document.getElementById("AwardSignatureBlock").style.display = 'block';
            document.getElementById("btnDeleteAwardNomination").disabled = false;
            document.getElementById("NominationType1").required = true;
            document.getElementById("AwardsAgree").required = true;
        }
        else if (document.getElementById("Award2Panel").style.display === 'none'){
            document.getElementById("Award2Panel").style.display = 'block';
            document.getElementById("NominationType2").required = true;
        }
        else if (document.getElementById("Award3Panel").style.display === 'none'){
            document.getElementById("Award3Panel").style.display = 'block';
            document.getElementById("NominationType3").required = true;
        }
        else if (document.getElementById("Award4Panel").style.display === 'none'){
            document.getElementById("Award4Panel").style.display = 'block';
            document.getElementById("NominationType4").required = true;
        }
        else if (document.getElementById("Award5Panel").style.display === 'none'){
            document.getElementById("Award5Panel").style.display = 'block';
            document.getElementById("btnAddAwardNomination").disabled = true;
            document.getElementById("NominationType5").required = true;
        }
        document.getElementById("TotalAwardNominations").value = Number(document.getElementById("TotalAwardNominations").value) + 1;
    }

    function DeleteAwardNomination(){
        // Did they say no, if so, we need to mark the explanation field as required
        if (document.getElementById("Award5Panel").style.display === 'block'){
            document.getElementById("Award5Panel").style.display = 'none';
            document.getElementById("btnAddAwardNomination").disabled = false;
            document.getElementById("NominationType5").required = false;
        }
        else if (document.getElementById("Award4Panel").style.display === 'block'){
            document.getElementById("Award4Panel").style.display = 'none';
            document.getElementById("NominationType4").required = false;
        }
        else if (document.getElementById("Award3Panel").style.display === 'block'){
            document.getElementById("Award3Panel").style.display = 'none';
            document.getElementById("NominationType3").required = false;
        }
        else if (document.getElementById("Award2Panel").style.display === 'block'){
            document.getElementById("Award2Panel").style.display = 'none';
            document.getElementById("NominationType2").required = false;
        }
        else if (document.getElementById("Award1Panel").style.display === 'block'){
            document.getElementById("Award1Panel").style.display = 'none';
            document.getElementById("AwardSignatureBlock").style.display = 'none';
            document.getElementById("btnDeleteAwardNomination").disabled = true;
            document.getElementById("NominationType1").required = false;
            document.getElementById("AwardsAgree").required = false;
        }
        document.getElementById("TotalAwardNominations").value = Number(document.getElementById("TotalAwardNominations").value) - 1;
    }

    window.addEventListener('load', function() {
            var awardNumbers = [1, 2, 3, 4, 5]; // Array of AwardNumbers

            // Loop through each AwardNumber and call ShowOutstandingCriteria function
            awardNumbers.forEach(function(AwardNumber) {
                ShowOutstandingCriteria(AwardNumber);
            });
        });

        function ShowOutstandingCriteria(AwardNumber){
            var NominationElementName = "NominationType" + AwardNumber;
            var CriteriaElementName = "OutstandingCriteria" + AwardNumber;

            if (document.getElementById(NominationElementName).value == 5 || document.getElementById(NominationElementName).value == 6){
                document.getElementById(CriteriaElementName).style.display = 'block';
            }
            else{
                document.getElementById(CriteriaElementName).style.display = 'none';
            }
        }

</script>

<script>
function showRosterUploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Chapter Roster',
        html: `
            <form id="uploadRosterForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadRosterForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeRoster', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Roster uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showStatement1UploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Statement',
        html: `
            <form id="uploadStatement1Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement1Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement1', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showStatement2UploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Additional Statement',
        html: `
            <form id="uploadStatement2Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement2Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement2', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Additional Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function show990NUploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload 990N',
        html: `
            <form id="upload990NForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('upload990NForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/store990n', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: '990N uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showAward1UploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Award Attachments',
        html: `
            <form id="uploadAward1Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadAward1Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeAward1', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Award attachments uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showAward2UploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Award Attachments',
        html: `
            <form id="uploadAward2Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadAward2Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeAward2', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Award attachments uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showAward1Up3oadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Award Attachments',
        html: `
            <form id="uploadAward3Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadAward3Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeAward3', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Award attachments uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showAward4UploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Award Attachments',
        html: `
            <form id="uploadAward4Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadAward4Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeAward4', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Award attachments uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}

function showAward5UploadModal() {
    var chapter_id = document.getElementById('chapter_id').value;

    Swal.fire({
        title: 'Upload Award Attachments',
        html: `
            <form id="uploadAward5Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadAward5Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeAward5', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Award attachments uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
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
}





    /* Save & Submit Verification */
    $(document).ready(function() {
        function submitFormWithStep(step) {
            $("#FurthestStep").val(step);
            $("#financial_report").submit();
        }

        $("#btn-step-1").click(function() {
            if (!EnsureRoster()) return false;
            if (!EnsureMembers()) return false;
            submitFormWithStep(1);
        });
        $("#btn-step-2").click(function() {
            submitFormWithStep(2);
        });
        $("#btn-step-3").click(function() {
            if (!EnsureServiceProject()) return false;
            submitFormWithStep(3);
        });
        $("#btn-step-4").click(function() {
            submitFormWithStep(4);
        });
        $("#btn-step-5").click(function() {
            submitFormWithStep(5);
        });
        $("#btn-step-6").click(function() {
            if (!EnsureReRegistration()) return false;
            submitFormWithStep(6);
        });
        $("#btn-step-7").click(function() {
            submitFormWithStep(7);
        });
        $("#btn-step-8").click(function() {
            submitFormWithStep(8);
        });
        $("#btn-step-9").click(function() {
            submitFormWithStep(9);
        });
        $("#btn-step-10").click(function() {
            if (!EnsureStatement()) return false;
            if (!EnsureReconciliation()) return false;
            submitFormWithStep(10);
        });
        $("#btn-step-11").click(function() {
            submitFormWithStep(11);
        });
        $("#btn-step-12").click(function() {
            if (!EnsureQuestions()) return false;
            submitFormWithStep(12);
        });
        $("#btn-step-13").click(function() {
            submitFormWithStep(13);
        });
        $("#btn-step-14").click(function() {
            // if (!EnsureSubmitInformation()) return false;
            submitFormWithStep(14);
        });
        $("#btn-save").click(function() {
            submitFormWithStep(15);
        });
    });

    $("#final-submit").click(async function() {
        if (!EnsureRoster()) return false;
        if (!EnsureMembers()) return false;
        if (!EnsureServiceProject()) return false;
        if (!EnsureReRegistration()) return false;
        if (!EnsureQuestions()) return false;
        if (!EnsureReconciliation()) return false;

        // Await EnsureBalance if it is an async function
        if (!await EnsureBalance()) return false;

        // Use SweetAlert2 for the final confirmation
        Swal.fire({
            title: 'Final Confirmation',
            text: "This will finalize and submit your report. You will no longer be able to edit this report. Do you wish to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit Report',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show processing spinner
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    }
                });

                // Proceed with form submission
                $("#submitted").val('1');
                $("#FurthestStep").val('16');
                $("#financial_report").submit();
            } else {
                // Optionally handle the case where the user cancels
                $(this).prop('disabled', false);
            }
        });
    });

        function isValidEmail(email) {
            // Regular expression for email validation
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function EnsureRoster() {
            var rosterPath = document.getElementById('RosterPath');
            var message = `<p>Your chapter's roster was not uploaded in CHAPTER DUES section.</p>
                   <p>Please upload Roster to Continue.</p>`;
            if (!rosterPath || rosterPath.value == "") {
                customWarningAlert(message);
                // accordion.openAccordionItem('accordion-header-members');
                return false;
            }
            return true;
        }

        function EnsureMembers() {
            var missingQuestions = [];

            // Check each required question
            if (!document.querySelector('input[name="optChangeDues"]:checked')) {
                missingQuestions.push("Did you change dues this year?");
            }
            if (!document.querySelector('input[name="optNewOldDifferent"]:checked')) {
                missingQuestions.push("Did you charge different dues for new and returning?");
            }
            if (!document.querySelector('input[name="optNoFullDues"]:checked')) {
                missingQuestions.push("Did you have members who didn't pay full dues?");
            }

            // Display the missing questions if any
            if (missingQuestions.length > 0) {
                var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
                var message = `<p>The following questions in the CHAPTER DUES section are required, please answer the required questions to continue.</p>
                        <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                            ${missingQuestionsText}
                        </ul>
                        `;
                        customWarningAlert(message);
                // accordion.openAccordionItem('accordion-header-members');
                return false;
            }

            return true;
        }


        function EnsureServiceProject() {
            var serviceProjectDesc0 = document.getElementById('ServiceProjectDesc0');
            var message = `<p>At least one Service Project is required in the SERVICE PROJECT section, please enter the required information to continue.</p>`;
            if (!serviceProjectDesc0 || serviceProjectDesc0.value == "") {
                customWarningAlert(message);
                // accordion.openAccordionItem('accordion-header-service');
                // $("#ServiceProjectDesc0").focus();
                return false;
            }
            return true;
        }

        function EnsureReRegistration() {
            var annualRegistrationFee = document.getElementById('AnnualRegistrationFee');
            var message = `<p>Chapter Re-registration is required in the INTERNATIONAL EVENTS & RE-REGISTRATION section, please enter the required information to continue.</p>`;
            if (!annualRegistrationFee || annualRegistrationFee.value == "") {
                customWarningAlert(message);
                // accordion.openAccordionItem('accordion-header-rereg');
                // $("#AnnualRegistrationFee").focus();
                return false;
            }
            return true;
        }

        function EnsureStatement() {
            var bankStatementIncluded = document.getElementById('BankStatementIncluded');
            var statementPath = document.getElementById('StatementPath');
            var message = `<p>Your chapter's Bank Statement was not uploaded in the BANK RECONCILIATION section, but you indicated the file was attached.</p>
                <p>Please upload Bank Statement to Continue.</p>`;
            if (bankStatementIncluded && bankStatementIncluded.value == "1" && (!statementPath || statementPath.value == "")) {
                // accordion.openAccordionItem('accordion-header-reconciliation');
                customWarningAlert(message);
                return false;
            }
            return true;
        }

        function EnsureReconciliation() {
            var amountReservedFromLastYear = document.getElementById('AmountReservedFromLastYear').value.trim();
            var bankBalanceNow = document.getElementById('BankBalanceNow').value.trim();
            var missingFields = [];

            // Check for missing fields and add to the list
            if (amountReservedFromLastYear === '' || amountReservedFromLastYear === null) {
                missingFields.push("This Year's Beginning Balance");
            }
            if (bankBalanceNow === '' || bankBalanceNow === null) {
                missingFields.push("Last Bank Statement Balance");
            }

            // Display the missing fields if any
            if (missingFields.length > 0) {
                var missingFieldsText = missingFields.map(field => `<li>${field}</li>`).join('');
                var message = `<p>The following fields are required in the BANK RECONCILIATION Section, please answer the required questions to continue.</p>
                   <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                     ${missingFieldsText}
                   </ul>
                   `;
                   customWarningAlert(message);
                return false;
            }

            return true;
        }

        async function EnsureBalance() {
            var PaymentTotal = 0;
            var DepositTotal = 0;

            var table = document.getElementById("bank-rec");

            for (var i = 1, row; row = table.rows[i]; i++) {
                // Payment Amount
                var paymentInput = row.querySelector('input[name^="BankRecPaymentAmount"]');
                var paymentValue = paymentInput ? parseFloat(paymentInput.value.replace(/,/g, '')) || 0 : 0;
                PaymentTotal += paymentValue;

                // Deposit Amount
                var depositInput = row.querySelector('input[name^="BankRecDepositAmount"]');
                var depositValue = depositInput ? parseFloat(depositInput.value.replace(/,/g, '')) || 0 : 0;
                DepositTotal += depositValue;
            }

            var BankBalanceNow = parseFloat(document.getElementById("BankBalanceNow").value.replace(/,/g, '')) || 0;
            var TotalFees = (BankBalanceNow - PaymentTotal + DepositTotal).toFixed(2);
            var TreasuryBalanceNow = parseFloat(document.getElementById("TreasuryBalanceNow").value.replace(/,/g, '')) || 0;

            if (TotalFees != TreasuryBalanceNow) {
                // Use await to wait for the SweetAlert result
                const result = await Swal.fire({
                    title: 'Report Does Not Balance',
                    text: "Your report does not balance. Your Treasury Balance Now and Reconciled Bank Balance should match before submitting your report.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Submit Anyway',
                    cancelButtonText: 'Return to Report',
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                        cancelButton: 'btn-sm btn-danger'
                    }
                });

                if (result.isConfirmed) {
                    return true; // User wants to submit anyway
                } else {
                    // Optionally open the accordion or perform other actions
                    // accordion.openAccordionItem('accordion-header-reconciliation');
                    return false; // User does not want to submit
                }
            }

            // If balanced, allow form submission
            return true;
        }

        function EnsureQuestions() {
            var requiredQuestions = [
                'ReceiveCompensation', 'FinancialBenefit', 'InfluencePolitical', 'VoteAllActivities',
                'BoughtPins', 'BoughtMerch', 'OfferedMerch', 'ByLawsAvailable', 'ChildrensRoom',
                'Playgroups', 'ChildOutings', 'MotherOutings', 'MeetingSpeakers', 'SpeakerFrequency',
                'ParkDays', 'ContributionsNotRegNP', 'Activity[]', 'PerformServiceProject', 'SisterChapter',
                'InternationalEvent', 'FileIRS', 'BankStatementIncluded'
            ];

            // Mapping of internal names to user-friendly labels
            var questionLabels = {
                'ReceiveCompensation': 'Member compensation received for work with chapter?',
                'FinancialBenefit': 'Member benefit financially from position in chapter?',
                'InfluencePolitical': 'Infuence or support political legislation or org?',
                'VoteAllActivities': 'Did the chapter vote on all activites?',
                'BoughtPins': 'Did the chapter purchase MOMS Club pins?',
                'BoughtMerch': 'Did the chapter purchase MOMS Club merchandise?',
                'OfferedMerch': 'Was MOMS Club merchandise offered to members?',
                'ByLawsAvailable': 'Were By-Laws made available to members?',
                'ChildrensRoom': 'Did the chapter have a children\'s room?',
                'Playgroups': 'Did the chapter have playgroups?',
                'ChildOutings': 'Did the chapter have child focused outings?',
                'MotherOutings': 'Did the chapter have mother focused outings?',
                'MeetingSpeakers': 'Did the chapter have meeting speakers?',
                'SpeakerFrequency': 'Did the chapter have discussion topics at meetings?',
                'ParkDays': 'Did the chapter have scheuled park days?',
                'Activity[]': 'Did the chapter have any actifity groups?',
                'ContributionsNotRegNP': 'Did the chapter make contributions to non-charities?',
                'PerformServiceProject': 'Did the chapter perform at least one service project?',
                'SisterChapter': 'Did the chapter Sister a New Chapter?',
                'InternationalEvent': 'Did the chapter atend an International event?',
                'FileIRS': 'Is the 990N filed with the IRS?',
                'BankStatementIncluded': 'Is the most recent Bank Statment Attached?'
            };

            var missingQuestions = [];

            // Check for unanswered questions
            for (var i = 0; i < requiredQuestions.length; i++) {
                var questionName = requiredQuestions[i];
                var isAnswered = document.querySelector('input[name="' + questionName + '"]:checked');
                if (!isAnswered) {
                    missingQuestions.push(questionLabels[questionName] || questionName);
                }
            }

            // Display the missing questions if any
            if (missingQuestions.length > 0) {
                var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
                var message = `<p>The following questions in the CHAPTER QUESTIONS section are required, please answer the required questions to continue.</p>
                                <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                                    ${missingQuestionsText}
                                </ul>
                                `;
                                customWarningAlert(message);
                accordion.openAccordionItem('accordion-header-questions');
                return false;
            }

            return true;
        }

        function Ensure990() {
            var fileIRS = document.getElementById('FileIRS');
            var path990N = document.getElementById('990NPath');
            var message = `<p>Your chapter's 990N filing confirmation was not uploaded in the 990N IRS Filing section, but you indicated the file was attached.</p>
                <p>Please upload 990 Confirmation to Continue.</p>`;
            if (fileIRS && fileIRS.value == "1" && path990N && path990N.value == "") {
                customWarningAlert(message);
                // accordion.openAccordionItem('accordion-header-questions');
                return false;
            }
            return true;
        }

    </script>
@endsection
