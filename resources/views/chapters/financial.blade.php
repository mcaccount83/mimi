@extends('layouts.coordinator_theme')

@section('content')

<div class="content">
<div>
	@if ($message = Session::get('success'))
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
		</div>
    @endif
	@if ($message = Session::get('fail'))
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
		</div>
    @endif
</div>
	<h4 class="moms-c">MOMS Club of {{ $chapterDetails[0]->chapter_name }}, {{$chapterDetails[0]->state}} Financial Report</h4>
    <h4><?php if(!$submitted) echo "<br><font color=\"red\">REPORT NOT YET SUBMITTED FOR REVIEW</font>"; ?></h4>
    <?php if ($submitted): ?>
    <h4>
        <?php $selectedReviewer = null; ?>
        <?php foreach ($reviewerList as $pcl): ?>
            <?php if ($financial_report_array['reviewer_id'] == $pcl['cid']): ?>
                <?php $selectedReviewer = $pcl['cname']; ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($selectedReviewer !== null): ?>
            <label for="AssignedReviewer" style="display: inline; font-weight: normal;">Assigned Reviewer: <?= $selectedReviewer ?></label>
        <?php else: ?>
            <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer in Section 13 before continuing to prevent errors.</span>
        <?php endif; ?>
    </h4>
<?php endif; ?>





	<h4 class="hide-on-print">Have some questions about reviewing this?  <a href="https://momsclub.org/reviewing-reports-faq/"   target="_blank">Check out our FAQ!</a></h4>

    <div class="row">
		<div class="col-md-12">
			<form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST"  action='{{ route("chapter.storefinancial",$chapterid) }}' novalidate>
			@csrf
			<input type="hidden" name="submitted" id="submitted" value="<?php echo $submitted; ?>" />
			<input type="hidden" name="FurthestStep" id="FurthestStep" value="<?php if($financial_report_array['farthest_step_visited_coord'] > 0) echo $financial_report_array['farthest_step_visited_coord']; else echo "0"; ?>" />
			<input type="hidden" name="submit_type" id="submit_type" value="" />
            <div class="accordion js-accordion">
				<!------Start Step 1 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] <='1') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 1 - Chapter Dues</div>
					<div class="accordion-body js-accordion-body">
						<section>
						<div class="form-row">
							<div class="col-md-6 form-row mar_bot_20">
								<div class="col-md-12">
									<label for="">
										Did your chapter change your dues this year?
									</label>
								</div>
								<div class="col-md-6">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
										<input type="radio" class="form-check-input" name="optChangeDues" id="optChangeDuesNo" value="no" onchange="ChapterDuesQuestionsChange()" <?php if (!is_null($financial_report_array['changed_dues'])) {if ($financial_report_array['changed_dues'] == false || $financial_report_array['changed_dues'] != true) echo "checked";} ?> disabled>
										<span class="form-check-sign"></span>
											No
										</label>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="optChangeDues" id="optChangeDuesYes" value="yes" onchange="ChapterDuesQuestionsChange()" <?php if (!is_null($financial_report_array['changed_dues'])) {if ($financial_report_array['changed_dues'] == true) echo "checked";} ?> disabled>
											<span class="form-check-sign"></span>
											Yes
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6 form-row mar_bot_20">
								<div class="col-md-12">
									<label for="">
										Did your chapter charge different amounts for new and returning members?
									</label>
								</div>
								<div class="col-md-6">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="optNewOldDifferent" id="optNewOldDifferentNo" value="no" onchange="ChapterDuesQuestionsChange()" <?php if (!is_null($financial_report_array['different_dues'])) {if ($financial_report_array['different_dues'] == false || $financial_report_array['different_dues'] != true) echo "checked";} ?> disabled>
											<span class="form-check-sign"></span>
											No
										</label>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="optNewOldDifferent" id="optNewOldDifferentYes" value="yes" onchange="ChapterDuesQuestionsChange()" <?php if (!is_null($financial_report_array['different_dues'])) {if ($financial_report_array['different_dues'] == true) echo "checked";} ?> disabled>
											<span class="form-check-sign"></span>
											Yes
										</label>
									</div>
								</div>
							</div>

							<div class="col-md-6 form-row mar_bot_20">
								<div class="col-md-12">
									<label for="">
										Did your chapter have any members who didn't pay full dues? <br>
										<span>(Associate members or members whose dues were reduced or waived)</span>
									</label>
								</div>
								<div class="col-md-6">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="optNoFullDues" id="optNoFullDuesNo" value="no" onchange="ChapterDuesQuestionsChange()" <?php if (!is_null($financial_report_array['not_all_full_dues'])) {if ($financial_report_array['not_all_full_dues'] == false || $financial_report_array['not_all_full_dues'] != true) echo "checked";} ?> disabled>
											<span class="form-check-sign"></span>
											No
										</label>

									</div>
								</div>

								<div class="col-md-6">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="optNoFullDues" id="optNoFullDuesYes" value="yes" onchange="ChapterDuesQuestionsChange()" <?php if (!is_null($financial_report_array['not_all_full_dues'])) {if ($financial_report_array['not_all_full_dues'] == true) echo "checked";} ?> disabled>
											<span class="form-check-sign"></span>
											Yes
										</label>
									</div>
								</div>
							</div>
						</div>
						<!--<hr>
							<div class="" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['roster_path']) echo "style=\"display: none;\"";} else echo "style=\"display: none;\""; ?>>
							<div class="col-md-12">
								<label class="control-label" for="RosterLink">Chapter Roster</label>
								<div>
									<p class="form-control-static"><a href="<?php echo $financial_report_array['roster_path']; ?>" target="_blank">Chapter Roster</a></p>

								</div>

							</div>
						</div>
						<input type="hidden" name="RosterPath" id="RosterPath" value="<?php echo $financial_report_array['roster_path']; ?>">-->
						<hr>

						<div class="form-row">
							<div class="col-md-12">
								<!--<h4>What dues did your chapter charge its members this year?  Count all members who paid full dues, even if they are not still members.</h4>-->
                            <p><i>Note: Count all members who paid dues, even if they are not still members.</i></p>
							</div>
							<div class="col-md-6 float-left">
								<div class="form-group">
									<label for="TotalNewMembers" id="lblTotalNewMembers">Total New Members (who paid dues)</label>
									<input type="number" class="form-control" oninput="ChangeMemberCount()" name="TotalNewMembers" id="TotalNewMembers" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_new_members'] ?>" disabled>
								</div>
							</div>
							<div class="col-md-6 float-left">
								<div class="form-group">
								<label for="TotalRenewedMembers" id="lblTotalRenewedMembers">
									Total Renewed Members (who paid dues)
								</label>
								<input type="number" class="form-control" oninput="ChangeMemberCount()" name="TotalRenewedMembers" id="TotalRenewedMembers" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_renewed_members'] ?>" disabled>
								</div>
							</div>
							<div class="col-md-12" id="ifChangeDues" style="display:none">
								<div class="form-row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="TotalNewMembersNewFee">Total New Members (who paid NEW dues amount)</label>
											<input type="number" class="form-control" oninput="ChangeMemberCount()" name="TotalNewMembersNewFee" id="TotalNewMembersNewFee" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_new_members_changed_dues'] ?>" disabled>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
										<label for="TotalRenewedMembersNewFee">Total Renewed Members (who paid NEW dues amount)</label>
										<input type="number" class="form-control" oninput="ChangeMemberCount()" name="TotalRenewedMembersNewFee" id="TotalRenewedMembersNewFee" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_renewed_members_changed_dues'] ?>" disabled>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
                                <div class="form-group">
                                    <label for="MemberDues" id="lblMemberDues">
                                        Dues collected per Member
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDues" oninput="ChangeMemberCount()" id="MemberDues" step="0.01" min=0 aria-describedby="sizing-addon1" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="ifChangedDuesDifferentPerMemberType" style="display:none">
                                    <div class="form-group">
                                        <label for="MemberDuesRenewal">Dues collected per Renewal Member</label>
                                        <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDuesRenewal"  oninput="ChangeMemberCount()" id="MemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal'] ?>">
                                        </div>
                                    </div>
                                </div>
                            <div class="col-md-12" id="ifChangedDues1" style="visibility:hidden">
                                <div class="col-md-6 float-left nopadding-l">
                                <div class="form-group">
                                    <label for="NewMemberDues" id="lblNewMemberDues">
                                        Dues collected per Member (New Amount)
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="NewMemberDues" oninput="ChangeMemberCount()" id="NewMemberDues" step="0.01" min=0 aria-describedby="sizing-addon1" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_new_changed'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="ifChangedDuesDifferentPerMemberType" style="display:none">
                                    <div class="form-group">
                                        <label for="MemberDuesRenewal">Dues collected per Renewal Member</label>
                                        <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDuesRenewal"  oninput="ChangeMemberCount()" id="MemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding" id="ifChangedDuesDifferentPerMemberType1" style="visibility:hidden">
                                    <div class="form-group">
                                    <label for="NewMemberDuesRenewal">Dues collected per Renewal Member (NEW Amount)</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="NewMemberDuesRenewal" oninput="ChangeMemberCount()" id="NewMemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal_changed'] ?>">
                                    </div>
                                    </div>
                                </div>
                            </div>

							<div class="col-md-12" id="ifMembersNoDues" style="display:none">
								<div class="form-row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="MembersNoDues">Members Who Paid No Dues</label>
										<input type="number" class="form-control" name="MembersNoDues" id="MembersNoDues"  min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['members_who_paid_no_dues'] ?>" disabled>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
									<label for="TotalPartialDuesMembers">Total Members Who Paid Partial Dues</label>
									<input type="number" class="form-control" name="TotalPartialDuesMembers" id="TotalPartialDuesMembers" min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['members_who_paid_partial_dues'] ?>" disabled>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									<label for="PartialDuesMemberDues">Total Partial Dues Collected</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="number" class="form-control" name="PartialDuesMemberDues" id="PartialDuesMemberDues" min="0" step="0.01" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_partial_fees_collected'] ?>" disabled>
									</div>
									</div>
								</div>
								<div class="col-md-12">
								<p><i>Note: Associate Members are not dues-waived or reduced members. They are a separate category of members. Many chapters do not have any Associate Members, but if your
chapter did have Associate Members this year, how many Associate Members did your chapter have?</i></p></div>
								<div class="col-md-6">
									<div class="form-group">
									<label for="TotalAssociateMembers">Total Associate Members</label>
									<input type="number" class="form-control" name="TotalAssociateMembers" id="TotalAssociateMembers" min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_associate_members'] ?>" disabled>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									<label for="AssociateMemberDues">Associate Member Dues</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="number" class="form-control" name="AssociateMemberDues" id="AssociateMemberDues" min="0" step="0.01" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['associate_member_fee'] ?>" disabled>
									</div>
									</div>
								</div>
							</div>
							</div>


							<div class="col-md-6 float-left">
								<div class="form-group">
									<label for="TotalMembers">Total Members</label>
									<input type="number" class="form-control" name="TotalMembers" id="TotalMembers" disabled>
								</div>
							</div>
							<div class="col-md-6 float-left">
								<div class="form-group">
									<label for="TotalDues">Total Dues Collected</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="number" class="form-control" name="TotalDues" id="TotalDues" step="0.01" aria-describedby="sizing-addon1" disabled>
									</div>
								</div>
							</div>
							<hr>
							<div class="col-md-12 float-left">
								<div class="form-group">
									<label for="AnnualRegistrationFee">Annual Chapter Registration Fee paid to International MOMS Club:<br><i>(Brought over from Step 5)</i></label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input type="number" class="form-control"  min="0"  step="0.01" readonly value="<?php if(!empty($financial_report_array)) echo $financial_report_array['annual_registration_fee'] ?>">
									</div>
								</div>
							</div>
						</div>
						<hr>
						<!-- start:report_review -->
						<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
							<div class="col-md-12">
								<h2>Annual Report Review</h2>
							</div>

							<div class="form-row">
								<div class="col-md-12 mar_bot_20" id="RosterBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['roster_path']) echo "style=\"display: none;\"";} ?>>
									<div class="col-md-12">
										<label class="control-label" for="RosterFile">Attach the chapter's current roster spreadsheet:</label>
										<input name="RosterFile" id="RosterFile" type="file" accept=".xls, .xlsx" class="demo1 form-control" />
									</div>
								</div>
								<input type="hidden" name="RosterPath" id="RosterPath" value="<?php echo $financial_report_array['roster_path']; ?>">

								<div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['roster_path']) echo "style=\"display: none;\"";} ?>>
									<div class="col-md-12" >
										<div>
										   <p class="form-control-static"><a href="<?php echo $financial_report_array['roster_path']; ?>" target="_blank">Chapter Roster</a></p>
									</div>

									<div class="col-md-6">
										<button type="button" class="btn btn-info btn-primary btn-sm hide-on-print" onclick="ReplaceRoster()">Replace</button>
									</div>
</div>
								</div>
								<div class="clearfix"></div>


								<div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
										<label for="">
											Excel roster attached and complete:
										</label>
									</div>
									<div class="col-md-6">
										<div class="form-check form-check-radio">
											<label class="form-check-label">
											<input id="checkRosterAttachedNo" name="checkRosterAttached" type="radio" class="form-check-input" value="no" onchange="UpdateRelatedControl('sumcheckRosterAttachedNo')"  <?php if (!is_null($financial_report_array['check_roster_attached'])) {if ($financial_report_array['check_roster_attached'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
											<span class="form-check-sign"></span>
												No
											</label>
										<label class="form-check-label">
												<input id="checkRosterAttachedYes" name="checkRosterAttached" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckRosterAttachedYes')"  <?php if (!is_null($financial_report_array['check_roster_attached'])) {if ($financial_report_array['check_roster_attached'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
												Yes
											</label>
										</div>
									</div>
								</div>

								<div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
										<label for="">
											Number of members listed, dues received, and renewal paid "seem right":
										</label>
									</div>
									<div class="col-md-6">
										<div class="form-check form-check-radio">
											<label class="form-check-label">
											<input type="radio" value="no" id="checkRenewalSeemsRightNo" name="checkRenewalSeemsRight" class="form-check-input" onchange="UpdateRelatedControl('sumcheckRenewalSeemsRightNo')" <?php if (!is_null($financial_report_array['check_renewal_seems_right'])) {if ($financial_report_array['check_renewal_seems_right'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
											<span class="form-check-sign"></span>
												No
											</label>
										<label class="form-check-label">
												<input type="radio" id="checkRenewalSeemsRightYes" name="checkRenewalSeemsRight" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckRenewalSeemsRightYes')" <?php if (!is_null($financial_report_array['check_renewal_seems_right'])) {if ($financial_report_array['check_renewal_seems_right'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
												Yes
											</label>
										</div>
									</div>
								</div>


								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step1_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" rows="10" name="Step1_Log" id="Step1_Log" readonly style="width:100%"><?php echo $financial_report_array['step_1_notes_log']; ?></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step1_Note">Note:</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="5" name="Step1_Note" id="Step1_Note" oninput="EnableNoteLogButton(1)" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<button type="button" id="AddNote1" class="btn btn-large btn-success" onclick="AddNote(1)" disabled>Add Note to Log</button>
									</div>
								</div>

							</div>
						</div>
						<!-- end:report_review -->

						<div class="form-row form-group">
							<div class="card-body">
								<div class="col-md-12 text-center">
								  <button type="submit" id="btn-step-1" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
								</div>
							</div>
						</div>
						</section>
					</div>
				</div>
				<!------End Step 1 ------>

				<!------Start Step 2 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='2') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 2 - Monthly Meeting Expenses</div>
					<div class="accordion-body js-accordion-body">
						<section>
							<div class="form-row form-group">
								<div class="col-md-6 float-left">
									<div class="form-group">
										<label for="ManditoryMeetingFeesPaid">
											Mandatory Meeting Room Fees Paid
										</label>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="ManditoryMeetingFeesPaid" id="ManditoryMeetingFeesPaid" oninput="ChangeMeetingFees()" min="0"  step="0.01" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['manditory_meeting_fees_paid']; else echo "0"; ?>" readonly>
										</div>
									</div>
								</div>
								<div class="col-md-6 float-left">
									<div class="form-group">
										<label for="VoluntaryDonationsPaid">
											Voluntary Donations Paid
										</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" name="VoluntaryDonationsPaid" id="VoluntaryDonationsPaid" oninput="ChangeMeetingFees()" min="0"  step="0.01" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['voluntary_donations_paid']; else echo "0"; ?>" readonly>
										</div>
									</div>
								</div>
								<div class="col-md-6 float-left">
									<div class="form-group">
										<label for="TotalMeetingRoomExpenses">
											Total Meeting Room Expenses
										</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" name="TotalMeetingRoomExpenses" id="TotalMeetingRoomExpenses" disabled>
										</div>
									</div>
								</div>
								<hr>
							</div>
							<div class="form-row form-group">
								<div class="col-md-12"><p>Use this section to list individually any Children’s Room expenses. Examples include craft supplies and snacks.</p></div>
								<div class="col-md-6 float-left">
									<div class="form-group">
										<label for="PaidBabySitters">Paid Babysitter Expenses (if any)</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" name="PaidBabySitters" id="PaidBabySitters"  min="0"  step="0.01" oninput="ChangeChildrensRoomExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['paid_baby_sitters'] ?>" readonly>
										</div>
									</div>
								</div>
								<div class="col-md-12 float-left">
									<div class="form-group">
										<div class="col-md-12">
										<label for="childrens-room">
											Children's Room Miscellaneous Expenses
										</label>
										</div>
									</div>
									<table width="100%" class="table table-bordered" id="childrens-room">
										<thead>
										<tr>
										  <td>Description</td>
										  <td>Supplies</td>
										  <td>Other Expenses</td>
										</tr>
										</thead>
										<tbody>
										<?php
											$childrens_room = null;
											if(isset($financial_report_array['childrens_room_expenses'])){
												$childrens_room=unserialize(base64_decode($financial_report_array['childrens_room_expenses']));
												//$ChildrensExpenseRowCount = count($childrens_room);
												$ChildrensExpenseRowCount = is_array($childrens_room) ? count($childrens_room) : 0;
											}
											else{
												$ChildrensExpenseRowCount = 1;
											}

											for ($row = 0; $row < $ChildrensExpenseRowCount; $row++){
												echo "<tr>";
												echo "<td>
												<div class=\"form-group\">
												<p class=\"form-group\" name=\"ChildrensRoomDesc" . $row . "\" id=\"ChildrensRoomDesc" . $row . "\" readonly>" . $childrens_room[$row]['childrens_room_desc'] . "</p>

												</div>
												</td>";

												echo "<td>
												<div class=\"form-group\">
												<div class=\"input-group\">";
												echo "<span class = \"input-group-addon\">$</span>";
												echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\"  name=\"ChildrensRoomSupplies" . $row . "\" id=\"ChildrensRoomSupplies" . $row . "\" oninput=\"ChangeChildrensRoomExpenses()\" readonly value=\"" . $childrens_room[$row]['childrens_room_supplies'] . "\">";
												echo "</div>
												</div>
												</td>";

												echo "<td>
												<div class=\"form-group\">
												<div class=\"input-group\">";
												echo "<span class = \"input-group-addon\">$</span>";
												echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"ChildrensRoomOther" . $row . "\" id=\"ChildrensRoomOther" . $row . "\" oninput=\"ChangeChildrensRoomExpenses()\" readonly value=\"" . $childrens_room[$row]['childrens_room_other'] . "\">";
												echo "</div>
												</div>
												</td>";

												echo "</tr>";
											}
										?>

										</tbody>
									</table>
								</div>

								<div class="col-md-6 float-left">
									<div>
										<label for="ChildrensRoomTotal">
											Children's Room Miscellaneous Total
										</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" value="0.00"name="ChildrensRoomTotal"  id="ChildrensRoomTotal"  step="0.01" disabled>
										</div>
									</div>
								</div>

							</div>
							<input type="hidden" name="ChildrensExpenseRowCount" id="ChildrensExpenseRowCount" value="<?php echo $ChildrensExpenseRowCount; ?>" />

							<hr>

							<!-- start:report_review -->
								<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
									<div class="col-md-12">
										<h2>Annual Report Review</h2>
									</div>
									<div class="form-row">
										<div class="clearfix"></div>
										<div class="col-md-12 mar_bot_20">
											<div class="col-md-12">
												<label for="Step2_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
											</div>
											<div class="col-md-12">
												<textarea class="form-control" style="width:100%" rows="10" name="Step2_Log" id="Step2_Log" readonly><?php echo $financial_report_array['step_2_notes_log']; ?></textarea>
											</div>
										</div>

										<div class="col-md-12 mar_bot_20">
											<div class="col-md-12">
												<label for="Step2_Note">Note:</label>
											</div>
											<div class="col-md-12">
												<textarea class="form-control" style="width:100%" rows="5" name="Step2_Note" id="Step2_Note" oninput="EnableNoteLogButton(2)" ></textarea>
											</div>
										</div>

										<div class="col-md-12 mar_bot_20">
											<div class="col-md-12">
												<button type="button" id="AddNote2" class="btn btn-large btn-success" onclick="AddNote(2)" disabled>Add Note to Log</button>
											</div>
										</div>
									</div>
								</div>
								<!-- end:report_review -->

							<div class="form-row form-group">
								<div class="card-body">
                                    <div class="col-md-12 text-center">
										<button type="submit" id="btn-step-2" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
                                    </div>
                                </div>
                            </div>
						</section>
					</div>
				</div>
				<!------End Step 2 ------>

				<!------Start Step 3 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='3') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 3 - Service Projects</div>
					<div class="accordion-body js-accordion-body">
					<section>
						<div class="form-row form-group">
						  <p>
							A Service Project is one that benefits others OUTSIDE your chapter. However, a Service Project may also be a project to benefit a member-in-distress or one who has special emergency needs, if the needs are the reason for the project. For example, a fundraiser may benefit the International MOMS Club’s Mother-to-Mother Fund or may be used to help pay extreme medical expenses for a life-threatening illness suffered by a member’s child. (Any fundraisers or projects that benefited your chapter or members who are not suffering emergency or devastating situations should not be listed here. Those should be listed in Step 6.)
						  </p>
						  <p>
							Not all Service Projects are fundraisers! If you did a Service Project that was not a fundraiser, you will have expenses listed here, but no income for that project. If your chapter made a donation from the treasury to another charity and used treasury money collected as dues (instead of money raised by your chapter for the donation), you will have expenses listed (the donation), but no income for that project.
						  </p>
						  <p>
							<strong>List all Service Projects below, even if there was no income or expense.</strong> Briefly describe the project and who was benefited by it. List any income and expenses for each project.
						  </p>
						<div class="col-md-12 nopadding">
							<table width="100%" class="table table-bordered" id="service-projects">
								<thead>
								<tr>
								  <td width="36%">Project Description</td>
								  <td width="16%">Income</td>
								  <td width="16%">Supplies & Expenses</td>
								  <td width="16%">Charity Donation</td>
								  <td width="16%">M2M & Sustaining Chapter Donation</td>
								</tr>
								</thead>
								<tbody>
								<?php
										$service_projects = null;
										if(isset($financial_report_array['service_project_array'])){
											$service_projects=unserialize(base64_decode($financial_report_array['service_project_array']));
											//$ServiceProjectRowCount = count($service_projects);
											$ServiceProjectRowCount = is_array($service_projects) ? count($service_projects) : 0;
										}
										else{
											$ServiceProjectRowCount = 1;
										}

										for ($row = 0; $row < $ServiceProjectRowCount; $row++){
											echo "<tr>";
											echo "<td>
											<div class=\"form-group\">
											<p class=\"form-group\" name=\"ServiceProjectDesc" . $row . "\" id=\"ServiceProjectDesc" . $row . "\" readonly>" . $service_projects[$row]['service_project_desc'] . "</p>

											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectIncome" . $row . "\" id=\"ServiceProjectIncome" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" readonly value=\"" . ltrim(rtrim($service_projects[$row]['service_project_income'])) . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectSupplies" . $row . "\" id=\"ServiceProjectSupplies" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" readonly value=\"" . ltrim(rtrim($service_projects[$row]['service_project_supplies'])) . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectDonatedCharity" . $row . "\" id=\"ServiceProjectDonatedCharity" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" readonly value=\"" . ltrim(rtrim($service_projects[$row]['service_project_charity'])) . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectDonatedM2M" . $row . "\" id=\"ServiceProjectDonatedM2M" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" readonly value=\"" . ltrim(rtrim($service_projects[$row]['service_project_m2m'])) . "\">";
											echo "</div>
											</div>
											</td>";
											echo "</tr>";
										}
									?>
								</tbody>
							</table>
						</div>

						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="ServiceProjectIncomeTotal">
									Service Project Income Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectIncomeTotal"  id="ServiceProjectIncomeTotal" disabled>
								</div>
							</div>
						</div>
						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="ServiceProjectSuppliesTotal">
									Service Project Supply & Expense Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectSuppliesTotal" id="ServiceProjectSuppliesTotal" disabled>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="ServiceProjectDonationTotal">
									Charity Donation Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectDonationTotal" id="ServiceProjectDonationTotal" disabled>
								</div>
							</div>
						</div>
						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="ServiceProjectM2MDonationTotal">
									M2M/Sustaining Chapter Donation Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectM2MDonationTotal" id="ServiceProjectM2MDonationTotal" disabled>
								</div>
							</div>
						</div>

					</div>
					<input type="hidden" name="ServiceProjectRowCount" id="ServiceProjectRowCount" value="<?php echo $ServiceProjectRowCount; ?>" />
					<hr>
					<!-- start:report_review -->
						<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
							<div class="col-md-12">
								<h2>Annual Report Review</h2>
							</div>
							<div class="form-row">
								<div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
										<label for="">
											Minimum of one service project completed:
										</label>
									</div>
									<div class="col-md-6">
										<div class="form-check form-check-radio">
											<label class="form-check-label">
											<input type="radio" id="checkServiceProjectNo" name="checkServiceProject" class="form-check-input" value="no" onchange="UpdateRelatedControl('sumcheckServiceProjectNo')"  <?php if (!is_null($financial_report_array['check_minimum_service_project'])) {if ($financial_report_array['check_minimum_service_project'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
											<span class="form-check-sign"></span>
												No
											</label>
									<label class="form-check-label">
												<input type="radio" id="checkServiceProjectYes" name="checkServiceProject" class="form-check-input" value="yes" onchange="UpdateRelatedControl('sumcheckServiceProjectYes')"  <?php if (!is_null($financial_report_array['check_minimum_service_project'])) {if ($financial_report_array['check_minimum_service_project'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
												Yes
											</label>
										</div>
									</div>
								</div>
								<div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
										<label for="">
											Made a donation to the M2M or General Fund (sustaining chapter):
										</label>
									</div>
									<div class="col-md-6">
										<div class="form-check form-check-radio">
											<label class="form-check-label">
											<input type="radio" id="checkM2MDonationNo" name="checkM2MDonation" class="form-check-input" value="no" onchange="UpdateRelatedControl('sumcheckM2MDonationNo')"  <?php if (!is_null($financial_report_array['check_m2m_donation'])) {if ($financial_report_array['check_m2m_donation'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
											<span class="form-check-sign"></span>
												No
											</label>
										<label class="form-check-label">
												<input type="radio" id="checkM2MDonationYes" name="checkM2MDonation" class="form-check-input" value="yes" onchange="UpdateRelatedControl('sumcheckM2MDonationYes')"  <?php if (!is_null($financial_report_array['check_m2m_donation'])) {if ($financial_report_array['check_m2m_donation'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
												Yes
											</label>
										</div>
									</div>
								</div>
								<!--<div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
										<label for="">
											Donation to MOMS Club General Fund (sustaining chapter):
										</label>
									</div>
									<div class="col-md-6">
										<div class="form-check form-check-radio">
											<label class="form-check-label">
											<input type="radio" id="checkMCGeneralFundNo" name="checkMCGeneralFund" class="form-check-input" value="no" onchange="UpdateRelatedControl('sumcheckMCGeneralFundNo')"  <?php if ($financial_report_array['check_mc_general_fund'] == false) echo "checked"; ?>>
											<span class="form-check-sign"></span>
												No
											</label>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input type="radio" id="checkMCGeneralFundYes" name="checkMCGeneralFund" class="form-check-input" value="yes" onchange="UpdateRelatedControl('sumcheckMCGeneralFundYes')"  <?php if ($financial_report_array['check_mc_general_fund'] == true) echo "checked"; ?>>
												<span class="form-check-sign"></span>
												Yes
											</label>
										</div>
									</div>
								</div>-->

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step3_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="10" name="Step3_Log" id="Step3_Log" readonly><?php echo $financial_report_array['step_3_notes_log']; ?></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step3_Note">Note:</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(3)" name="Step3_Note" id="Step3_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<button type="button" id="AddNote3" class="btn btn-large btn-success" onclick="AddNote(3)" disabled>Add Note to Log</button>
									</div>
								</div>
							</div>
						</div>
						<!-- end:report_review -->

					<div class="form-row form-group">
						<div class="card-body">
						<div class="col-md-12 text-center">
						  <button type="submit" id="btn-step-3" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
						</div>
						</div>
					</div>
				</section>
				</div>
				</div>
				<!------End Step 3 ------>

				<!------Start Step 4 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='4') echo "active";?>">
				<div class="accordion-header js-accordion-header">Section 4 - Parties and Member Benefits</div>
				<div class="accordion-body js-accordion-body">
				<section>
					<div class="form-row form-group">
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
							<?php
								$party_expenses = null;
								if(isset($financial_report_array['party_expense_array'])){
									$party_expenses=unserialize(base64_decode($financial_report_array['party_expense_array']));
									//$PartyExpenseRowCount = count($party_expenses);
									$PartyExpenseRowCount = is_array($party_expenses) ? count($party_expenses) : 0;
								}
								else{
									$PartyExpenseRowCount = 1;
								}

								for ($row = 0; $row < $PartyExpenseRowCount; $row++){
									echo "<tr>";
									echo "<td>
									<div class=\"form-group\">

									<p class=\"form-group\" name=\"PartyDesc" . $row . "\" id=\"PartyDesc" . $row . "\" readonly>" . $party_expenses[$row]['party_expense_desc'] . "</p>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"PartyIncome" . $row . "\" id=\"PartyIncome" . $row . "\" oninput=\"ChangePartyExpenses()\" readonly value=\"" . $party_expenses[$row]['party_expense_income'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"PartyExpenses" . $row . "\" id=\"PartyExpenses" . $row . "\" oninput=\"ChangePartyExpenses()\" readonly value=\"" . $party_expenses[$row]['party_expense_expenses'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "</tr>";
								}
							?>
							</tbody>
                        </table>

						<div class="col-md-12">
							<h4>Party/Member Benefit</h4>
						</div>
						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="PartyIncomeTotal">
									Income Total:
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0"  step="0.01" name="PartyIncomeTotal" id="PartyIncomeTotal" disabled>
								</div>
							</div>
						</div>
						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="PartyExpenseTotal">
									Expense Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0"  step="0.01" name="PartyExpenseTotal" id="PartyExpenseTotal" disabled>
								</div>
							</div>
						</div>

					</div>
					<input type="hidden" name="PartyExpenseRowCount" id="PartyExpenseRowCount" value="<?php echo $PartyExpenseRowCount; ?>" />
					<hr>
					<!-- start:report_review -->
					<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
						<div class="col-md-12">
							<h2>Annual Report Review</h2>
						</div>
						<div class="form-row">
							<div class="col-md-6 float-left">
								<div class="col-md-12">
									<div class="form-group">
										<label for="PartyDuesIncomeReview">
											Dues Income:
										</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" min="0"  step="0.01" name="PartyDuesIncomeReview" id="PartyDuesIncomeReview" readonly>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6 float-left">
								<div class="col-md-12">
									<div class="form-group">
										<label for="PartyIncomeReview">
											Party Income:
										</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" min="0"  step="0.01" name="PartyIncomeReview" id="PartyIncomeReview" readonly>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6 float-left">
								<div class="col-md-12">
									<div class="form-group">
										<label for="PartyExpenseReview">
											Party Expenses:
										</label>
										<div class="input-group">
										<span class="input-group-addon">$</span>
										<input type="number" class="form-control" min="0"  step="0.01" name="PartyExpenseReview" id="PartyExpenseReview" readonly>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-6 float-left">
								<div class="col-md-12">
									<div class="form-group">
										<label for="PartyExpensePercent">
											Expense Percentage:
										</label>
										<div class="input-group">
										<span class="input-group-addon">%</span>
										<input type="number" class="form-control" min="0"  step="0.01" name="PartyExpensePercent" id="PartyExpensePercent" readonly>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step4_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="10" name="Step4_Log" id="Step4_Log" readonly><?php echo $financial_report_array['step_4_notes_log']; ?></textarea>
								</div>
							</div>

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step4_Note">Note:</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(4)" name="Step4_Note" id="Step4_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
								</div>
							</div>

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<button type="button" id="AddNote4" class="btn btn-large btn-success" onclick="AddNote(4)" disabled>Add Note to Log</button>
								</div>
							</div>
						</div>
					</div>
					<!-- end:report_review -->

					<div class="form-row form-group">
						<div class="card-body">
							<div class="col-md-12 text-center">
							  <button type="submit" id="btn-step-4" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
							</div>
						</div>
					</div>
				</section>
				</div>
				</div>
				<!------End Step 4 ------>

				<!------Start Step 5 ------>
			    <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='5') echo "active";?>">
				<div class="accordion-header js-accordion-header">Section 5 - Office and Operating Expenses</div>
				<div class="accordion-body js-accordion-body">
				<div class="form-row form-group">
				  <div class="col-md-12">
				  <p>
					Use this section to list individually any Office Expenses or other Operating Expenses. Please include only one expense type per line (i.e. website hosting, advertising, etc.).
				  </p>
				  </div>
				<div class="col-md-4 float-left">
					<div class="form-group">
						<label for="PrintingCosts">
							Printing Costs
						</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="number" class="form-control" min="0"  step="0.01" name="PrintingCosts" id="PrintingCosts" oninput="ChangeOfficeExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['office_printing_costs']?>" readonly>
						</div>
					</div>
				</div>
				<div class="col-md-4 float-left">
					<div class="form-group">
						<label for="PostageCosts">
							Postage Costs
						</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="number" class="form-control" min="0"  step="0.01" name="PostageCosts" id="PostageCosts" oninput="ChangeOfficeExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['office_postage_costs'] ?>" readonly>
						</div>
					</div>
				</div>
				<div class="col-md-4 float-left">
					<div class="form-group">
						<label for="MembershipPins">
							Membership Pins
						</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="number" class="form-control" min="0"  step="0.01" name="MembershipPins" id="MembershipPins" oninput="ChangeOfficeExpenses()" value="<?php echo $financial_report_array['office_membership_pins_cost']; ?>" readonly>
						</div>
					</div>
				</div>
				<div class="col-md-12 float-left">
					<label for="">
						Other Office & Operating Expenses
					</label>
				</div>
				<div class="col-md-12">
				<table id="office-expenses" width="100%" class="table table-bordered">
					<thead>
					<tr>
					  <td>Description of Expense</td>
					  <td>Expenses</td>
					</tr>
					</thead>
					<tbody>
					<?php
						$other_office_expenses = null;
						if(isset($financial_report_array['office_other_expenses'])){
							$other_office_expenses=unserialize(base64_decode($financial_report_array['office_other_expenses']));
							//$OfficeExpenseRowCount = count($other_office_expenses);
							$OfficeExpenseRowCount = is_array($other_office_expenses) ? count($other_office_expenses) : 0;
						}
						else{
							$OfficeExpenseRowCount = 1;
						}
						for ($row = 0; $row < $OfficeExpenseRowCount; $row++){
							echo "<tr>";
							echo "<td>
							<div class=\"form-group\">
							<p class=\"form-group\" name=\"OfficeDesc" . $row . "\" id=\"OfficeDesc" . $row . "\" readonly>" . $other_office_expenses[$row]['office_other_desc'] . "</p>
							</div>
							</td>";

							echo "<td>
							<div class=\"form-group\">
							<div class=\"input-group\">";
							echo "<span class = \"input-group-addon\">$</span>";
							echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"OfficeExpenses" . $row . "\" id=\"OfficeExpenses" . $row . "\" oninput=\"ChangeOfficeExpenses()\" readonly value=\"" . ltrim(rtrim($other_office_expenses[$row]['office_other_expense'])) . "\">";
							echo "</div>
							</div>
						  </td>";

							echo "</tr>";
						}
					?>
					</tbody>
                </table>
                </div>

				<div class="col-md-4 float-left">
					<div class="form-group">
						<label for="OfficeExpenseTotal">
							Other Office & Operating Expenses Total
						</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="number" class="form-control"  min="0"  step="0.01" name="OfficeExpenseTotal" id="OfficeExpenseTotal" disabled>
						</div>
					</div>
				</div>
				<hr>
			</div>
			<input type="hidden" name="OfficeExpenseRowCount" id="OfficeExpenseRowCount" value="<?php echo $OfficeExpenseRowCount; ?>" />
			<div class="form-row form-group">
				<div class="col-md-12">
				  <h4>
					International Event Registration
				  </h4>
				</div>

				<div class="col-md-12">
				<p>
				  International Events include any State/Regional/Conference Luncheons, Workshops or other events sponsored or organized by the International MOMS Club. “Event income” includes all money paid to your treasury by members for their reservations at those events and any income from fundraisers held to help offset the expense of members attending an International event. Also include any donations to your chapter to help build your raffle basket or chapter display. “Event expenses” includes all money paid by your treasury to International for reservations or for members attending the event, raffle basket and display expenses, and any travel costs. If your chapter paid for all reservations from its treasury and did not charge members for attending, it will have expenses, but no income, in this category.
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
					<?php
						$international_event_array = null;
						if(isset($financial_report_array['international_event_array'])){
							$international_event_array=unserialize(base64_decode($financial_report_array['international_event_array']));
							//$InternationalEventRowCount = count($international_event_array);
							$InternationalEventRowCount = is_array($international_event_array) ? count($international_event_array) : 0;
						}
						else{
							$InternationalEventRowCount = 1;
						}

						for ($row = 0; $row < $InternationalEventRowCount; $row++){
							echo "<tr>";
							echo "<td>
							<div class=\"form-group\">

							<p class=\"form-group\" name=\"InternationalEventDesc" . $row . "\" id=\"InternationalEventDesc" . $row . "\" readonly>" . $international_event_array[$row]['intl_event_desc'] . "</p>
							</div>
							</td>";

							echo "<td>
							<div class=\"form-group\">
							<div class=\"input-group\">";
							echo "<span class = \"input-group-addon\">$</span>";
							echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"InternationalEventIncome" . $row . "\" id=\"InternationalEventIncome" . $row . "\" oninput=\"ChangeInternationalEventExpense()\" readonly value=\"" . $international_event_array[$row]['intl_event_income'] . "\">";
							echo "</div>
							</div>
							</td>";

							echo "<td>
							<div class=\"form-group\">
							<div class=\"input-group\">";
							echo "<span class = \"input-group-addon\">$</span>";
							echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"InternationalEventExpense" . $row . "\" id=\"InternationalEventExpense" . $row . "\" oninput=\"ChangeInternationalEventExpense()\" readonly value=\"" . $international_event_array[$row]['intl_event_expenses'] . "\">";
							echo "</div>
							</div>
							</td>";

							echo "</tr>";
						}
					?>
					</tbody>
                </table>
            </div>
			<hr>
			</div>

			<div class="form-row form-group">
			<div class="col-md-4 float-left">
				<div class="form-group">
					<label for="InternationalEventIncomeTotal">
						International Event Registration Income
					</label>
					<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="number" class="form-control" min="0"  step="0.01" name="InternationalEventIncomeTotal" id="InternationalEventIncomeTotal" disabled>
					</div>
				</div>
			</div>
			<div class="col-md-4 float-left">
				<div class="form-group">
					<label for="InternationalEventExpenseTotal">
						International Event Registration Expenses
					</label>
					<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="number" class="form-control" min="0"  step="0.01" name="InternationalEventExpenseTotal" id="InternationalEventExpenseTotal" disabled>
					</div>
				</div>
			</div>
			<hr>
			</div>
			<input type="hidden" name="InternationalEventRowCount" name="InternationalEventRowCount" id="InternationalEventRowCount" value="<?php echo $InternationalEventRowCount; ?>" />
			<div class="form-row form-group">
			<div class="col-md-12">
			  <h4>
				Chapter Re-registration
			  </h4>
			</div>
			<div class="col-md-6 float-left">
				<div class="form-group">
					<label for="AnnualRegistrationFee">
						Annual Chapter Registration Fee paid to International MOMS Club
					</label>
					<div class="input-group">
					<span class="input-group-addon">$</span>
					<input type="number" class="form-control" min="0"  step="0.01" name="AnnualRegistrationFee" id="AnnualRegistrationFee" oninput="ChangeReRegistrationExpense()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['annual_registration_fee'] ?>" readonly>
					</div>
				</div>
			</div>

			</div>

			<hr>

			<!-- start:report_review -->
				<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
					<div class="col-md-12">
						<h2>Annual Report Review</h2>
					</div>

					<div class="form-row">






						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step5_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="10" name="Step5_Log" id="Step5_Log" readonly><?php echo $financial_report_array['step_5_notes_log']; ?></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step5_Note">Note:</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(5)" name="Step5_Note" id="Step5_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<button type="button" id="AddNote5" class="btn btn-large btn-success" onclick="AddNote(5)" disabled>Add Note to Log</button>
							</div>
						</div>

					</div>
				</div>
				<!-- end:report_review -->

			<div class="form-row form-group">
				<div class="card-body">
					<div class="col-md-12 text-center">
					  <button type="submit" id="btn-step-5" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
					</div>
				</div>
			</div>
			</div><!-- end of accordion body -->
			</div><!-- end of accordion item -->
			<!------End Step 5 ------>

			<!------Start Step 6 ------>
		    <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='6') echo "active";?>">
				<div class="accordion-header js-accordion-header">Section 6 - Donations to Your Chapter</div>
				<div class="accordion-body js-accordion-body">
				<section>
				<div class="form-row form-group">
					<div class="col-md-12">
					<label for="donation-income">
						Monetary:
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
							<?php
								$monetary_dontations_to_chapter = null;
								if(isset($financial_report_array['monetary_donations_to_chapter'])){
									$monetary_dontations_to_chapter=unserialize(base64_decode($financial_report_array['monetary_donations_to_chapter']));
									//$MonDonationRowCount = count($monetary_dontations_to_chapter);
									$MonDonationRowCount = is_array($monetary_dontations_to_chapter) ? count($monetary_dontations_to_chapter) : 0;
								}
								else{
									$MonDonationRowCount = 1;
								}

									for ($row = 0; $row < $MonDonationRowCount; $row++){
										echo "<tr>";
										echo "<td>
										<div class=\"form-group\">

										<p class=\"form-group\" name=\"DonationDesc" . $row . "\" id=\"DonationDesc" . $row . "\" readonly>" . $monetary_dontations_to_chapter[$row]['mon_donation_desc'] . "</p>

										</div>
										</td>";

										echo "<td>
										<div class=\"form-group\">

										<p class=\"form-group\" name=\"DonorInfo" . $row . "\" id=\"DonorInfo" . $row . "\" readonly>" . $monetary_dontations_to_chapter[$row]['mon_donation_info'] . "</p>
										</div>
										</td>";

										echo "<td>
										<div class=\"form-group\">
										<input type=\"date\" class=\"form-control\" name=\"MonDonationDate" . $row . "\" id=\"MonDonationDate" . $row . "\" readonly value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_date'] . "\">
										</div>
										</td>";

										echo "<td>
										<div class=\"form-group\">
										<div class=\"input-group\">";
										echo "<span class = \"input-group-addon\">$</span>";
										echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"DonationAmount" . $row . "\" id=\"DonationAmount" . $row . "\" oninput=\"ChangeDonationAmount()\" readonly value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_amount'] . "\">";
										echo "</div>
										</div>
										</td>";

										echo "</tr>";
									}
							?>
						</tbody>
					</table>
				</div>

					<div class="col-md-4 float-left">
					<div class="form-group">
						<label for="DonationTotal">Monetary Donation Total</label>
						<div class="input-group">
						<span class="input-group-addon">$</span>
						<input type="number" class="form-control"  min="0"  step="0.01" name="DonationTotal" id="DonationTotal" disabled>
						</div>
					</div>
					<input type="hidden" name="MonDonationRowCount" id="MonDonationRowCount" value="<?php echo $MonDonationRowCount; ?>" />
				</div>
				<hr>
				</div>
				<div class="form-row form-group">
					<div class="col-md-12">
					<label for="">
						Non-Monetary:
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
							<?php
							$non_monetary_dontations_to_chapter = null;
							if(isset($financial_report_array['non_monetary_donations_to_chapter'])){
								$non_monetary_dontations_to_chapter=unserialize(base64_decode($financial_report_array['non_monetary_donations_to_chapter']));
								//$NonMonDonationRowCount = count($non_monetary_dontations_to_chapter);
								$NonMonDonationRowCount = is_array($non_monetary_dontations_to_chapter) ? count($non_monetary_dontations_to_chapter) : 0;
							}
							else{
								$NonMonDonationRowCount = 1;
							}

							for ($row = 0; $row < $NonMonDonationRowCount; $row++){
								echo "<tr>";
								echo "<td>
								<div class=\"form-group\">

								<p class=\"form-group\" name=\"NonMonDonationDesc" . $row . "\" id=\"NonMonDonationDesc" . $row . "\" readonly>" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_desc'] . "</p>
								</div>
								</td>";

								echo "<td>
								<div class=\"form-group\">

								<p class=\"form-group\" name=\"NonMonDonorInfo" . $row . "\" id=\"NonMonDonationDesc" . $row . "\" readonly>" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_info'] . "</p>
								</div>
								</td>";

								echo "<td>
								<div class=\"form-group\">
								<input type=\"date\" class=\"form-control\" name=\"NonMonDonationDate" . $row . "\" id=\"NonMonDonationDate" . $row . "\" readonly value=\"" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_date'] . "\">
								</div>
								</td>";

								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</div>

					<input type="hidden" name="NonMonDonationRowCount" id="NonMonDonationRowCount" value="<?php echo $NonMonDonationRowCount; ?>" />

					</div>

					<hr>

					<!-- start:report_review -->
						<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
							<div class="col-md-12">
								<h2>Annual Report Review</h2>
							</div>

							<div class="form-row">

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step6_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="10" name="Step6_Log" id="Step6_Log" readonly><?php echo $financial_report_array['step_6_notes_log']; ?></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step6_Note">Note:</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(6)" name="Step6_Note" id="Step6_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<button type="button" id="AddNote6" class="btn btn-large btn-success" onclick="AddNote(6)" disabled>Add Note to Log</button>
									</div>
								</div>

							</div>
						</div>
						<!-- end:report_review -->

					<div class="form-row form-group">
						<div class="card-body">
							<div class="col-md-12 text-center">
							  <button type="submit" id="btn-step-6" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
							</div>
						</div>
					</div>
				</section>
				</div><!-- end of accordion body -->
				</div><!-- end of accordion item -->
				<!------End Step 6 ------>

				<!------Start Step 7 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='7') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 7 - Other Income & Expenses</div>
					<div class="accordion-body js-accordion-body">
					<section>
					<div class="form-row form-group">
						<div class="col-md-12">

						<p>If your chapter had any other income not listed elsewhere, enter those amounts and descriptions here. (<i>If there are multiple entries of one type of income in your books, please group them together as one total for that type of entry below. For example, if local businesses paid for advertising in your newsletter, enter one amount for all the advertising sold by your chapter during the year.</i>)</p>
						<p>Use this section to list any fundraisers your chapter may have had to benefit the chapter or the members. If your chapter participated in any programs offering rebates, matching contributions or bonus cards, include that information here.</p>
						<table id="other-office-expenses" width="100%" class="table table-bordered">
							<thead>
							<tr>
							  <td>Description of Expense/Income</td>
							  <td>Income</td>
							  <td>Expenses</td>
							</tr>
							</thead>
							<?php
								$other_income_and_expenses_array = null;
								if(isset($financial_report_array['other_income_and_expenses_array'])){
									$other_income_and_expenses_array=unserialize(base64_decode($financial_report_array['other_income_and_expenses_array']));
									//$OtherOfficeExpenseRowCount = count($other_income_and_expenses_array);
									$OtherOfficeExpenseRowCount = is_array($other_income_and_expenses_array) ? count($other_income_and_expenses_array) : 0;
								}
								else{
									$OtherOfficeExpenseRowCount = 2;
								}
							?>

							<tbody>

								<tr>
								<td>
								<div class="form-group">
								<input type="text" class="form-control" name="OtherOfficeDesc0" id="OtherOfficeDesc0" value="Outgoing Board Gifts" readonly>
								</div>
								</td>

								<td>
								<div class="form-group">
								<div class="input-group">
								<span class = "input-group-addon">$</span>
								<input type="number" class="form-control" min="0" step="0.01" name="OtherOfficeIncome0" id="OtherOfficeIncome0" oninput="ChangeOtherOfficeExpenses()" value="<?php echo ltrim(rtrim($other_income_and_expenses_array[0]['other_income']));?>" readonly>
								</div>
								</div>
								</td>

								<td>
								<div class="form-group">
								<div class="input-group">
								<span class = "input-group-addon">$</span>
								<input type="number" class="form-control" min="0" step="0.01" name="OtherOfficeExpenses0" id="OtherOfficeExpenses0" oninput="ChangeOtherOfficeExpenses()" value="<?php echo ltrim(rtrim($other_income_and_expenses_array[0]['other_expenses']));?>" readonly>
								</div>
								</div>
								</td>

								</tr>

								<?php

										for ($row = 1; $row < $OtherOfficeExpenseRowCount; $row++){
											echo "<tr>";
											echo "<td>
											<div class=\"form-group\">

											<p class=\"form-group\" name=\"OtherOfficeDesc" . $row . "\" id=\"OtherOfficeDesc" . $row . "\" readonly>" . $other_income_and_expenses_array[$row]['other_desc'] . "</p>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"OtherOfficeIncome" . $row . "\" id=\"OtherOfficeIncome" . $row . "\" oninput=\"ChangeOtherOfficeExpenses()\" readonly value=\"" . ltrim(rtrim($other_income_and_expenses_array[$row]['other_income'])) . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"OtherOfficeExpenses" . $row . "\" id=\"OtherOfficeExpenses" . $row . "\" oninput=\"ChangeOtherOfficeExpenses()\" readonly value=\"" . ltrim(rtrim($other_income_and_expenses_array[$row]['other_expenses'])) . "\">";
											echo "</div>
											</div>
											</td>";

											echo "</tr>";
										}

								?>
							</tbody>
                        </table>
                    </div>

						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="OtherOfficeIncomeTotal">
									Other Income Total:
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control"min="0" step="0.01" name="OtherOfficeIncomeTotal" id="OtherOfficeIncomeTotal" disabled>
								</div>
							</div>
						</div>
						<div class="col-md-6 float-left">
							<div class="form-group">
								<label for="OtherOfficeExpenseTotal">
									Other Expense Total:
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="number" class="form-control" min="0" step="0.01" name="OtherOfficeExpenseTotal" id="OtherOfficeExpenseTotal" disabled>
								</div>
							</div>
					   </div>
					   <input type="hidden" name="OtherOfficeExpenseRowCount" id="OtherOfficeExpenseRowCount" value="<?php echo $OtherOfficeExpenseRowCount; ?>" />

					</div>

					<hr>

					<!-- start:report_review -->
					<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
						<div class="col-md-12">
							<h2>Annual Report Review</h2>
						</div>

						<div class="form-row">

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step7_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="10" name="Step7_Log" id="Step7_Log" readonly><?php echo $financial_report_array['step_7_notes_log']; ?></textarea>
								</div>
							</div>

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step7_Note">Note:</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="5" oninput="EnableNoteLogButton(7)"  name="Step7_Note" id="Step7_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
								</div>
							</div>

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<button type="button" id="AddNote7" class="btn btn-large btn-success" onclick="AddNote(7)" disabled>Add Note to Log</button>
								</div>
							</div>

						</div>
					</div>
					<!-- end:report_review -->

					<div class="form-row form-group">
						<div class="card-body">
							<div class="col-md-12 text-center">
							  <button type="submit" id="btn-step-7" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
							</div>
						</div>
					</div>
				</section>
			  </div><!-- end of accordion body -->
			  </div><!-- end of accordion item -->
				<!------End Step 7 ------>

				<!------Start Step 8 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='8') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 8 - Bank Reconciliation</div>
					<div class="accordion-body js-accordion-body">
					<section>
					<div class="form-row form-group">
                    <div class="col-md-6 float-left">
						<div class="form-group">
							<label for="AmountReservedFromLastYear">
								This Year's Beginning Balance (July 1, <?php echo date('Y')-1;?>):
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" oninput="TreasuryBalanceChange()" min="0" step="0.01" name="AmountReservedFromLastYear" id="AmountReservedFromLastYear" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['amount_reserved_from_previous_year'] ?>" readonly>
							</div>
						</div>
					</div>
					<div class="col-md-6 float-left">
						<div class="form-group">
							<label for="BankBalanceNow">
								Last Bank Statement Balance (June 30, <?php echo date('Y');?>):
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="BankBalanceNow" id="BankBalanceNow" oninput="ChangeBankRec()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['bank_balance_now'] ?>" readonly>
							</div>
						</div>
					</div>
					<div class="col-md-6 float-left" style="display:none">
						<div class="form-group">
							<label for="PettyCash">
								Petty Cash on Hand (if any):
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="PettyCash" id="PettyCash" oninput="ChangeBankRec()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['petty_cash'] ?>" readonly>
							</div>
						</div>
				   </div>
					<div class="col-md-6 float-left">
						<div class="form-group">
							<label for="TreasuryBalanceNow">
								Treasury Balance Now:
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="TreasuryBalanceNow" id="TreasuryBalanceNow" disabled>
							</div>
						</div>
					</div>
					<hr>
				</div>
				<div class="form-row form-group">
					<div class="col-md-12">
				  <p>If your most recent bank statement’s ending balance does not match your “Treasury Balance Now”, you must reconcile your checking account using the worksheet below so that the balances match.</p>
				  <p>To balance your account, start with your bank statement’s ending balance, then list any deposits and any outstanding payments. When done, the new reconciled balance will match your treasury balance.</p>
				  <p>View a step by step instruction video <a href="https://momsclub.org/elearning/courses/annual-financial-report-bank-reconciliation/">HERE</a>.</p>
				  <br>
				  <label for="bank-rec">
                  Bank Reconciliation:
				</label>
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

						<?php
							$bank_rec_array = null;
							if(isset($financial_report_array['bank_reconciliation_array'])){
								$bank_rec_array=unserialize(base64_decode($financial_report_array['bank_reconciliation_array']));
								//$BankRecRowCount = count($bank_rec_array);
								$BankRecRowCount = is_array($bank_rec_array) ? count($bank_rec_array) : 0;
							}
							else{
								$BankRecRowCount = 1;
							}

								for ($row = 0; $row < $BankRecRowCount; $row++){
									echo "<tr>";
									echo "<td>
									<div class=\"form-group\">
									<input type=\"date\" class=\"form-control\" name=\"BankRecDate" . $row . "\" id=\"BankRecDate" . $row . "\" readonly value=\"" . $bank_rec_array[$row]['bank_rec_date'] . "\">
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<input type=\"text\" class=\"form-control\" name=\"BankRecCheckNo" . $row . "\" id=\"BankRecCheckNo" . $row . "\" readonly value=\"" . $bank_rec_array[$row]['bank_rec_check_no'] . "\">
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<input type=\"text\" class=\"form-control\" name=\"BankRecDesc" . $row . "\" id=\"BankRecDesc" . $row . "\" readonly value=\"" . $bank_rec_array[$row]['bank_rec_desc'] . "\">
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"BankRecPaymentAmount" . $row . "\" id=\"BankRecPaymentAmount" . $row . "\" oninput=\"ChangeBankRec()\" readonly value=\"" . $bank_rec_array[$row]['bank_rec_payment_amount'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input type=\"number\" class=\"form-control\" min=\"0\"  step=\"0.01\" name=\"BankRecDepositAmount" . $row . "\" id=\"BankRecDepositAmount" . $row . "\" oninput=\"ChangeBankRec()\" readonly value=\"" . $bank_rec_array[$row]['bank_rec_desposit_amount'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "</tr>";
								}

						?>
						</tbody>
                    </table>
                </div>

					<div class="col-md-6 float-left">
						<div class="form-group">
							<label for="ReconciledBankBalance">
								Reconciled Bank Balance:
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="ReconciledBankBalance" id="ReconciledBankBalance" disabled>
							</div>
						</div>
					</div>

					<input type="hidden" name="BankRecRowCount" id="BankRecRowCount" value="<?php echo $BankRecRowCount; ?>" />

				</div>

				<hr>


				<!-- start:report_review -->
				<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
					<div class="col-md-12">
						<h2>Annual Report Review</h2>
					</div>

									<div class="form-row">
								<div class="col-md-12 mar_bot_20" id="StatementBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} ?>>
									<div class="col-md-12">
										<label class="control-label" for="StatementFile">Attach primary bank statement:</label>
										<input name="StatementFile" id="StatementFile" type="file" accept=".pdf, .jpg, .jpeg" class="demo1 form-control" />
									</div>
								</div>
								<input type="hidden" name="StatementPath" id="StatementPath" value="<?php echo $financial_report_array['bank_statement_included_path']; ?>">

								<div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} ?>>
									<div class="col-md-12" >
										<div>
										<p class="form-control-static"><a href="<?php echo $financial_report_array['bank_statement_included_path']; ?>" target="_blank">Primary Bank Statement</a></p>
										</div>
									</div>

									<div class="col-md-6 mar_bot_20">
										<button type="button" class="btn btn-info btn-primary btn-sm hide-on-print" onclick="ReplaceStatement1()">Replace</button>
									</div>
								<div class="clearfix"></div>


                                <div class="form-row">
								<div class="col-md-12 mar_bot_20" id="Statement2Block" <?php if (!empty($financial_report_array)) {if ($financial_report_array['bank_statement_2_included_path']) echo "style=\"display: none;\"";} ?>>
									<div class="col-md-12">
										<label class="control-label" for="Statement2File">If chapter has muliple bank accounts, attach additional statement:</label>
										<input name="Statement2File" id="Statement2File" type="file" accept=".pdf, .jpg, .jpeg" class="demo1 form-control" />
									</div>
								</div>
								<input type="hidden" name="Statement2Path" id="Statement2Path" value="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>">

								<div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['bank_statement_2_included_path']) echo "style=\"display: none;\"";} ?>>
									<div class="col-md-12" >
										<div>
										    <p class="form-control-static"><a href="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>" target="_blank">Additional Bank Statement</a></p>
										</div>
									</div>

									<div class="col-md-6">
										<button type="button" class="btn btn-info btn-primary btn-sm hide-on-print" onclick="ReplaceStatement2()">Replace</button>
									</div>
								</div>
								<div class="clearfix"></div>


						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									Current bank statement included and balance matches chapter entry:
								</label>
							</div>

							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input type="radio" id="checkBankStatementIncludedNo" name="checkBankStatementIncluded" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckBankStatementIncludedNo')" <?php if (!is_null($financial_report_array['check_bank_statement_included'])) {if ($financial_report_array['check_bank_statement_included'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>
								<label class="form-check-label">
										<input id="checkBankStatementIncludedYes" name="checkBankStatementIncluded" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckBankStatementIncludedYes')" <?php if (!is_null($financial_report_array['check_bank_statement_included'])) {if ($financial_report_array['check_bank_statement_included'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>



						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									Treasury Balance Now matches Reconciled Bank Balance:
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkBankStatementMatchesNo" name="checkBankStatementMatches" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckBankStatementMatchesNo')" <?php if (!is_null($financial_report_array['check_bank_statement_matches'])) {if ($financial_report_array['check_bank_statement_matches'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>

							<label class="form-check-label">
										<input id="checkBankStatementMatchesYes" name="checkBankStatementMatches" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckBankStatementMatchesYes')" <?php if (!is_null($financial_report_array['check_bank_statement_matches'])) {if ($financial_report_array['check_bank_statement_matches'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>

												<div class="col-md-6 mar_bot_20">

						<div class="col-md-12">
								<div class="form-group">
							<label for="post_balance">
								Beginning Balance (entered from last year's report):
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="pre_balance" id="pre_balance" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['pre_balance'] ?>" readonly >
							</div>
						</div>
							</div>
							</div>



						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step8_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="10" name="Step8_Log" id="Step8_Log" readonly><?php echo $financial_report_array['step_8_notes_log']; ?></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step8_Note">Note:</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(8)" name="Step8_Note" id="Step8_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<button type="button" id="AddNote8" class="btn btn-large btn-success" onclick="AddNote(8)" disabled>Add Note to Log</button>
							</div>
						</div>

					</div>
				</div>
				<!-- end:report_review -->

				 <div class="form-row form-group">
						<div class="card-body">
							<div class="col-md-12 text-center">
							  <button type="submit" id="btn-step-8" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
							</div>
						</div>
				</div>
			</section>
			</div><!-- end of accordion body -->
			</div><!-- end of accordion item -->
			<!------End Step 8 ------>

			<!------Start Step 9 ------>
			<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='9') echo "active";?>">
			<div class="accordion-header js-accordion-header">Section 9 - Tax Exempt & Chapter Questions</div>
				<div class="accordion-body js-accordion-body">
				<section>
				<div class="form-row form-group">
					<div class="col-md-12"><p>During the last fiscal year (July 1 <?php echo date('Y')-1 .' - June 30, '.date('Y');?>):</p></div>
					<div class="col-sm-12 mar_bot15">
						<p>1. Did anyone in your chapter receive any compensation or pay for their work with your chapter?</p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
								<label class="form-check-label col-md-4">
									<input id="ReceiveCompensationNo" name="ReceiveCompensation" type="radio" class="form-check-input" value="no" onchange="ToggleReceiveCompensationExplanation()" <?php if (!is_null($financial_report_array['receive_compensation'])) {if ($financial_report_array['receive_compensation'] == false) echo "checked";} ?> disabled>
									<span class="form-check-sign"></span>
										  No
								</label>
								<label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" id="ReceiveCompensationYes" name="ReceiveCompensation" value="yes" onchange="ToggleReceiveCompensationExplanation()" <?php if (!is_null($financial_report_array['receive_compensation'])) {if ($financial_report_array['receive_compensation'] == true) echo "checked";} ?> disabled>
									<span class="form-check-sign"></span>
										  Yes
								</label>
							</div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divReceiveCompensationExplanation">
						<label for="ReceiveCompensationExplanation">If yes, briefly explain:</label>
							<textarea class="form-control" rows="2" name="ReceiveCompensationExplanation" id="ReceiveCompensationExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['receive_compensation_explanation'];}?></textarea>

						<div class="help-block with-errors"></div>
					</div>

					<div class="col-sm-12 mar_bot15">
						<p>2. Did any officer, member or family of a member benefit financially in any way from the member’s position with your chapter?</p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
							   <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" id="FinancialBenefitNo" name="FinancialBenefit" value="no" onchange="ToggleFinancialBenefitExplanation()" disabled <?php if (!is_null($financial_report_array['financial_benefit'])) {if ($financial_report_array['financial_benefit'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							   <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" id="FinancialBenefitYes" name="FinancialBenefit" value="yes" disabled onchange="ToggleFinancialBenefitExplanation()" <?php if (!is_null($financial_report_array['financial_benefit'])) {if ($financial_report_array['financial_benefit'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							</div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divFinancialBenefitExplanation">
						<label for="FinancialBenefitExplanation">If yes, briefly explain:</label>
						<textarea class="form-control" rows="2" name="FinancialBenefitExplanation" id="FinancialBenefitExplanation" readonly> <?php if (!is_null($financial_report_array)) {echo $financial_report_array['financial_benefit_explanation'];} ?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
						<p>3. Did your chapter attempt to influence any national, state/provincial, or local legislation, or did your chapter support any other organization that did?</p>
						<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
						   <label class="form-check-label col-md-4">
								<input type="radio" disabled class="form-check-input" id="InfluencePoliticalNo" name="InfluencePolitical" value="no" onchange="ToggleInfluencePoliticalExplanation()" <?php if (!is_null($financial_report_array['influence_political'])) {if ($financial_report_array['influence_political'] == false) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  No
						   </label>

						   <label class="form-check-label col-md-4">
								<input type="radio" class="form-check-input" id="InfluencePoliticalYes" name="InfluencePolitical" value="yes" disabled onchange="ToggleInfluencePoliticalExplanation()" <?php if (!is_null($financial_report_array['influence_political'])) {if ($financial_report_array['influence_political'] == true) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  Yes
						   </label>
						  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divInfluencePoliticalExplanation">
						<label for="InfluencePoliticalExplanation">If yes, briefly explain:</label>
						<textarea class="form-control" rows="2" name="InfluencePoliticalExplanation" id="InfluencePoliticalExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['influence_political_explanation'];}?></textarea>
					</div>

					<div class="col-sm-12 mar_bot15">
						<p>4. Did your chapter vote on all activities and expenditures during the fiscal year?</p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
							   <label class="form-check-label col-md-4">
									<input type="radio" disabled class="form-check-input" id="VoteAllActivitiesNo" name="VoteAllActivities" value="no" onchange="ToggleVoteAllActivitiesExplanation()" <?php if (!is_null($financial_report_array['vote_all_activities'])) {if ($financial_report_array['vote_all_activities'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							  <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" id="VoteAllActivitiesYes" name="VoteAllActivities" value="yes" disabled onchange="ToggleVoteAllActivitiesExplanation()" <?php if (!is_null($financial_report_array['vote_all_activities'])) {if ($financial_report_array['vote_all_activities'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							    </label>
							</div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divVoteAllActivitiesExplanation">
						<label for="VoteAllActivitiesExplanation">If no, briefly explain:</label>
							<textarea class="form-control" rows="2" name="VoteAllActivitiesExplanation" id="VoteAllActivitiesExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['vote_all_activities_explanation'];}?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
						<p>5. Did you purchase pins from International? If No, why not?</p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
							   <label class="form-check-label col-md-4">
									<input type="radio" disabled class="form-check-input" id="BoughtPinsNo" name="BoughtPins" value="no" onchange="ToggleBoughtPinsExplanation()" <?php if (!is_null($financial_report_array['purchase_pins'])) {if ($financial_report_array['purchase_pins'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							   <label class="form-check-label col-md-4">
									<input type="radio" disabled class="form-check-input" id="BoughtPinsYes" name="BoughtPins" value="yes" <?php if (!is_null($financial_report_array['purchase_pins'])) {if ($financial_report_array['purchase_pins'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 mar_bot15">
                        <p>6. Did you purchase any merchandise from International other than pins? If No, why not?</p>
                        <div class="col-md-4 float-left">
                            <div class="form-check form-check-radio">
                                <label class="form-check-label col-md-4">
                                    <input type="radio" class="form-check-input" disabled id="BoughtMerchNo" name="BoughtMerch" value="" onchange="ToggleBoughtMerchExplanation()" {{ old('bought_merch', $financial_report_array['bought_merch']) == false ? 'checked' : '' }}>
                                    <span class="form-check-sign"></span>
                                    No
                                </label>
                                <label class="form-check-label col-md-4">
                                    <input type="radio" class="form-check-input" disabled id="BoughtMerchYes" name="BoughtMerch" value="yes" onchange="ToggleBoughtMerchExplanation()" {{ old('bought_merch', $financial_report_array['bought_merch']) == true ? 'checked' : '' }}>
                                    <span class="form-check-sign"></span>
                                    Yes
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 brief_exp" id="divBoughtMerchExplanation">
                        <label for="BoughtMerchExplanation">If no, briefly explain:</label>
                        <textarea class="form-control" rows="2" name="BoughtMerchExplanation" id="BoughtMerchExplanation" readonly>{{ old('bought_merch_explanation', $financial_report_array['bought_merch_explanation']) }}</textarea>
                    </div>

					<div class="col-sm-12 mar_bot15">
						<p>7. Did you offer or information your members about MOMS Club merchandise?</p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="OfferedMerchNo" name="OfferedMerch" value="no" onchange="ToggleOfferedMerchExplanation()" <?php if (!is_null($financial_report_array['offered_merch'])) {if ($financial_report_array['offered_merch'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>

									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="OfferedMerchYes" name="OfferedMerch" value="yes" onchange="ToggleOfferedMerchExplanation()" <?php if (!is_null($financial_report_array['offered_merch'])) {if ($financial_report_array['offered_merch'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Yes
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divOfferedMerchExplanation">
						<label for="OfferedMerchExplanation">If no, briefly explain:</label>
						<textarea class="form-control" rows="2" name="OfferedMerchExplanation" id="OfferedMerchExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['offered_merch_explanation'];}?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
						<p>8. Did you make the Bylaws and/or manual available for any chapter members that requested them?</p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="ByLawsAvailableNo" name="ByLawsAvailable" value="no" onchange="ToggleByLawsAvailableExplanation()" <?php if (!is_null($financial_report_array['bylaws_available'])) {if ($financial_report_array['bylaws_available'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>

									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="ByLawsAvailableYes" name="ByLawsAvailable" value="yes" onchange="ToggleByLawsAvailableExplanation()" <?php if (!is_null($financial_report_array['bylaws_available'])) {if ($financial_report_array['bylaws_available'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Yes
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divByLawsAvailableExplanation">
						<label for="ByLawsAvailableExplanation">If no, briefly explain:</label>
						<textarea class="form-control" rows="2" name="ByLawsAvailableExplanation" id="ByLawsAvailableExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['bylaws_available_explanation'];}?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
						<p>9. Did you have a children’s room with babysitters?</p>
						<div class="col-md-12 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="ChildrensRoomNo" name="ChildrensRoom" value="no" <?php if (!is_null($financial_report_array['childrens_room_sitters'])) {if ($financial_report_array['childrens_room_sitters'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>

									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="ChildrensRoomYesVol" name="ChildrensRoom" value="yes_vol" <?php if (!is_null($financial_report_array['childrens_room_sitters'])) {if ($financial_report_array['childrens_room_sitters'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Yes, with volunteer members
									   </label>
									   <label class="form-check-label col-md-4">
											<input type="radio" disabled class="form-check-input" id="ChildrensRoomYesPaid" name="ChildrensRoom" value="yes_paid" <?php if (!is_null($financial_report_array['childrens_room_sitters'])) {if ($financial_report_array['childrens_room_sitters'] == 2) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Yes, with paid sitters
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp">
						<label for="ChildrensRoomExplanation">Briefly explain, if necessary:</label>
						<textarea class="form-control" rows="2" name="ChildrensRoomExplanation" readonly id="ChildrensRoomExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['childrens_room_sitters_explanation'];}?></textarea>
					</div>

					<div class="col-sm-12 mar_bot15">
                    <p>10. Did you have playgroups? If so, how were they arranged.<span class="field-required">*</span></p>
                    <div class="col-md-12 float-left">
                         <div class="form-check form-check-radio">
                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input rd-cls" id="Playgroups1" name="Playgroups" value="no" <?php if (!is_null($financial_report_array['playgroups'])) {if ($financial_report_array['playgroups'] == false) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              No
                                   </label>

                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input" id="Playgroups2" name="Playgroups" value="yes_byage" <?php if (!is_null($financial_report_array['playgroups'])) {if ($financial_report_array['playgroups'] == true) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes, arranged by age
                                   </label>

                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input" id="Playgroups3" name="Playgroups" value="yes_multiage" <?php if (!is_null($financial_report_array['playgroups'])) {if ($financial_report_array['playgroups'] == 2) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes, multi-aged groups
                                   </label>
                          </div>
                          </div>
                    </div>
					<div class="col-sm-12 brief_exp">
                                <label for="PlaygroupsExplanation">Briefly explain, if necessary:</label>
                                <textarea class="form-control" rows="2" name="PlaygroupsExplanation" readonly id="PlaygroupsExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['had_playgroups_explanation'];}?></textarea>
                        </div>

					<div class="col-sm-12 mar_bot15">
						<p>11. Did you have any child focused outings or activities? (Ex: zoo, library, pumpkin patch, etc.)</p>
						<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
							<label class="form-check-label col-md-4">
								<input type="radio" class="form-check-input" disabled id="ChildOutingsNo" name="ChildOutings" value="no" onchange="ToggleChildOutingsExplanation()" <?php if (!is_null($financial_report_array['child_outings'])) {if ($financial_report_array['child_outings'] == false) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  No
							</label>
							<label class="form-check-label col-md-4">
								<input type="radio" class="form-check-input" disabled id="ChildOutingsYes" name="ChildOutings" value="yes" onchange="ToggleChildOutingsExplanation()" <?php if (!is_null($financial_report_array['child_outings'])) {if ($financial_report_array['child_outings'] == true) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  Yes
						   </label>
						  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divChildOutingsExplanation">
						<label for="ChildOutingsExplanation">If no, briefly explain:</label>
						<textarea class="form-control" rows="2" name="ChildOutingsExplanation" id="ChildOutingsExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['child_outings_explanation'];}?></textarea>
					</div>

					<div class="col-sm-12 mar_bot15">
						<p>12. Did you have any mother focused outings or activities? (Ex: mall walks, art museum, etc.)</p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
							   <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" disabled id="MotherOutingsNo" name="MotherOutings" value="no" onchange="ToggleMotherOutingsExplanation()" <?php if (!is_null($financial_report_array['mother_outings'])) {if ($financial_report_array['mother_outings'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							    <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" disabled id="MotherOutingsYes" name="MotherOutings" value="yes" onchange="ToggleMotherOutingsExplanation()" <?php if (!is_null($financial_report_array['mother_outings'])) {if ($financial_report_array['mother_outings'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divMotherOutingsExplanation">
						<label for="MotherOutingsExplanation">If no, briefly explain:</label>
						<textarea class="form-control" rows="2" name="MotherOutingsExplanation" id="MotherOutingsExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['mother_outings_explanation'];}?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
					<p>13. Did you have speakers at any meetings?</p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
							   <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" id="MeetingSpeakersNo" name="MeetingSpeakers" value="no" disabled onchange="ToggleMeetingSpeakersExplanation()" <?php if (!is_null($financial_report_array['meeting_speakers'])) {if ($financial_report_array['meeting_speakers'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							    <label class="form-check-label col-md-4">
									<input type="radio" class="form-check-input" id="MeetingSpeakersNo" name="MeetingSpeakers" value="yes" disabled onchange="ToggleMeetingSpeakersExplanation()" <?php if (!is_null($financial_report_array['meeting_speakers'])) {if ($financial_report_array['meeting_speakers'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divMeetingSpeakersExplanation">
						<label for="MeetingSpeakersExplanation">If no, briefly explain:</label>
						<textarea class="form-control" rows="2" name="MeetingSpeakersExplanation" id="MeetingSpeakersExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['meeting_speakers_explanation'];}?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
						<p>14. If you had speakers, check any of the topics that were covered:</p>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input type="checkbox" disabled class="" id="SpeakersChildRearing" name="SpeakersChildRearing" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_child_rearing']) && $financial_report_array['speaker_child_rearing'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Child Rearing
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input type="checkbox" disabled class="" id="SpeakersEducation" name="SpeakersEducation" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_education']) && $financial_report_array['speaker_education'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Schools/Education
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input type="checkbox" disabled class="" id="SpeakersHomemaking" name="SpeakersHomemaking" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_homemaking']) && $financial_report_array['speaker_homemaking'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Home Management
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input type="checkbox" disabled class="" id="SpeakersPolitics" name="SpeakersPolitics" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_politics']) && $financial_report_array['speaker_politics'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Politics
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input type="checkbox" disabled class="" id="SpeakersOtherNP" name="SpeakersOtherNP" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_other_np']) && $financial_report_array['speaker_other_np'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Other Non-Profit
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input type="checkbox" disabled class="" id="SpeakersOther" name="SpeakersOther" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_other']) && $financial_report_array['speaker_other'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Other
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 mar_bot15">
						<p>15. Did you have any discussion topics at your meetings? If yes, how often?</p>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input type="radio" disabled class="form-check-input" id="SpeakerFrequency1" name="SpeakerFrequency" value="no" <?php if (!is_null($financial_report_array['discussion_topic_frequency'])) {if ($financial_report_array['discussion_topic_frequency'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input type="radio" disabled class="form-check-input" id="SpeakerFrequency2" name="SpeakerFrequency" value="1_3_times" <?php if (!is_null($financial_report_array['discussion_topic_frequency'])) {if ($financial_report_array['discussion_topic_frequency'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  1-3 Times
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input type="radio" disabled class="form-check-input" id="SpeakerFrequency3" name="SpeakerFrequency" value="4_6_times" <?php if (!is_null($financial_report_array['discussion_topic_frequency'])) {if ($financial_report_array['discussion_topic_frequency'] == 2) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  4-6 Times
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input type="radio" disabled class="form-check-input" id="SpeakerFrequency4" name="SpeakerFrequency" value="7_9_times" <?php if (!is_null($financial_report_array['discussion_topic_frequency'])) {if ($financial_report_array['discussion_topic_frequency'] ==3) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  7-9 Times
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input type="radio" disabled class="form-check-input" id="SpeakerFrequency5" name="SpeakerFrequency" value="10_times" <?php if (!is_null($financial_report_array['discussion_topic_frequency'])) {if ($financial_report_array['discussion_topic_frequency'] == 4) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  10+ Times
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12 mar_bot15">
					<p>16. Did your chapter have scheduled park days? If yes, how often?</p>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input type="radio" disabled class="form-check-input" id="ParkDays1" name="ParkDays" value="no" <?php if (!is_null($financial_report_array['park_day_frequency'])) {if ($financial_report_array['park_day_frequency'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input type="radio" disabled class="form-check-input" id="ParkDays2" name="ParkDays" value="1_3_times" <?php if (!is_null($financial_report_array['park_day_frequency'])) {if ($financial_report_array['park_day_frequency'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  1-3 Times
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input type="radio" disabled class="form-check-input" id="ParkDays3" name="ParkDays" value="4_6_times" <?php if (!is_null($financial_report_array['park_day_frequency'])) {if ($financial_report_array['park_day_frequency'] == 2) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  4-6 Times
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input type="radio" disabled class="form-check-input" id="ParkDays4" name="ParkDays" value="7_9_times" <?php if (!is_null($financial_report_array['park_day_frequency'])) {if ($financial_report_array['park_day_frequency'] == 3) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  7-9 Times
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input type="radio" disabled class="form-check-input" id="ParkDays5" name="ParkDays" value="10_times" <?php if (!is_null($financial_report_array['park_day_frequency'])) {if ($financial_report_array['park_day_frequency'] == 4) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  10+ Times
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12 mar_bot15">
					<p>17. Did your chapter have any of the following activity groups?</p>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input type="checkbox" disabled class="" id="ActivityCooking" name="ActivityCooking" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_cooking']) && $financial_report_array['activity_cooking'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Cooking
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input type="checkbox" disabled class="" id="ActivityCouponing" name="ActivityCouponing" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_couponing']) && $financial_report_array['activity_couponing'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Cost Cutting Tips
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input type="checkbox" disabled class="" id="ActivityMommyPlaygroup" name="ActivityMommyPlaygroup" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_mommy_playgroup']) && $financial_report_array['activity_mommy_playgroup'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Mommy Playgroup (moms with all children in school)
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input type="checkbox" disabled class="" id="ActivityBabysitting" name="ActivityBabysitting" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_babysitting']) && $financial_report_array['activity_babysitting'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Babysitting Co-op
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input type="checkbox" disabled class="" id="ActivityMNO" name="ActivityMNO" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_mno']) && $financial_report_array['activity_mno'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  MOMS Night Out
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input type="checkbox" disabled class="" id="ActivityOther" name="ActivityOther" onChange="ToggleActivityOtherExplanation()"  <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_other']) && $financial_report_array['activity_other'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Other
								   </label>
						  </div>
					</div>
					</div>
					 <div class="col-sm-12 brief_exp" id="divActivityOtherExplanation">
						<label for="ActivityOtherExplanation">If other, briefly explain:</label>
						<textarea class="form-control" rows="2" name="ActivityOtherExplanation" id="ActivityOtherExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['activity_other_explanation'];}?></textarea>
					 </div>
					<div class="col-sm-12 mar_bot15">
					<p>18. Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity? If yes, please explain who received the contributions and why you chose them:</p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="ContributionsNotRegNPNo" name="ContributionsNotRegNP" value="no" onChange="ToggleContributionsNotRegNPExplanation()" <?php if (!is_null($financial_report_array['contributions_not_registered_charity'])) {if ($financial_report_array['contributions_not_registered_charity'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>

								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="ContributionsNotRegNPYes" name="ContributionsNotRegNP" value="yes" onChange="ToggleContributionsNotRegNPExplanation()" <?php if (!is_null($financial_report_array['contributions_not_registered_charity'])) {if ($financial_report_array['contributions_not_registered_charity'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divContributionsNotRegNPExplanation">
						<label for="ContributionsNotRegNPExplanation">If yes, briefly explain:</label>
						<textarea class="form-control" rows="2" name="ContributionsNotRegNPExplanation" id="ContributionsNotRegNPExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['contributions_not_registered_charity_explanation'];}?></textarea>
					</div>
					<div class="col-sm-12 mar_bot15">
					<p>19. Did your chapter perform at least one service project to benefit mothers or children?</p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="PerformServiceProjectNo" name="PerformServiceProject" value="no" onChange="TogglePerformServiceProjectExplanation()" <?php if (!is_null($financial_report_array['at_least_one_service_project'])) {if ($financial_report_array['at_least_one_service_project'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>
						 		   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="PerformServiceProjectYes" name="PerformServiceProject" value="yes" onChange="TogglePerformServiceProjectExplanation()" <?php if (!is_null($financial_report_array['at_least_one_service_project'])) {if ($financial_report_array['at_least_one_service_project'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12 brief_exp" id="divPerformServiceProjectExplanation">
						<label for="PerformServiceProjectExplanation">If no, briefly explain:</label>
						<textarea class="form-control" rows="2" name="PerformServiceProjectExplanation" id="PerformServiceProjectExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['at_least_one_service_project_explanation'];}?></textarea>
					</div>
					 <div class="col-sm-12 mar_bot15">
                    <p>20. Did your chapter sister another chapter?</p>
                    <div class="col-md-4 float-left">
                         <div class="form-check form-check-radio">
                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input" id="SisterChapterNo" name="SisterChapter" value="no" <?php if (!is_null($financial_report_array['sister_chapter'])) {if ($financial_report_array['sister_chapter'] == false) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              No
                                   </label>
                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input" id="SisterChapterYes" name="SisterChapter" value="yes" <?php if (!is_null($financial_report_array['sister_chapter'])) {if ($financial_report_array['sister_chapter'] == true) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes
                                   </label>
                          </div>
                    </div>
                    </div>


         <div class="col-sm-12 mar_bot15">
                    <p>21. Did your chapter attend an International Event (in person or virtual)?</p>
                    <div class="col-md-4 float-left">
                         <div class="form-check form-check-radio">
                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input" id="InternationalEventNo" name="InternationalEvent" value="no" <?php if (!is_null($financial_report_array['international_event'])) {if ($financial_report_array['international_event'] == false) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              No
                                   </label>
                                   <label class="form-check-label col-md-4">
                                        <input type="radio" disabled class="form-check-input" id="InternationalEventYes" name="InternationalEvent" value="yes" <?php if (!is_null($financial_report_array['international_event'])) {if ($financial_report_array['international_event'] == true) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes
                                   </label>
                          </div>
                    </div>
                    </div>
					<div class="col-sm-12 mar_bot15">
					<p>22. Did your chapter file their IRS 990N for <?php echo date('Y')-1 .'-'.date('Y');?> (CANNOT BE DONE BEFORE JULY 1, <?php echo date('Y');?>)?</p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="FileIRSNo" name="FileIRS" value="no" onChange="ToggleFileIRSExplanation()" <?php if (!is_null($financial_report_array['file_irs'])) {if ($financial_report_array['file_irs'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>

								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="FileIRSYes" name="FileIRS" value="yes" onChange="ToggleFileIRSExplanation()" <?php if (!is_null($financial_report_array['file_irs'])) {if ($financial_report_array['file_irs'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
						<!--<div class="col-sm-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
						  <div class="form-group">
									<div>
										<p class="form-control-static"><a href="<?php echo $financial_report_array['file_irs_path']; ?>" target="_blank">990N Confirmation File</a></p>
									</div>
						  </div>
						</div>-->
						<input type="hidden" name="990NPath" id="990NPath" value="<?php echo $financial_report_array['file_irs_path']; ?>">
					</div>
					<div class="col-sm-12 brief_exp" id="divFileIRSExplanation">
						<label for="FileIRSExplanation">If no, briefly explain:</label>
							<textarea class="form-control" rows="2" name="FileIRSExplanation" id="FileIRSExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['file_irs_explanation'];}?></textarea>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-sm-12 mar_bot15">
					<p>23. Is a copy of your chapter’s most recent bank statement included with the copy of this report that you are submitting to International? </p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="BankStatementIncludedNo" name="BankStatementIncluded" value="no" onChange="ToggleBankStatementIncludedExplanation()" <?php if (!is_null($financial_report_array['bank_statement_included'])) {if ($financial_report_array['bank_statement_included'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>

								   <label class="form-check-label col-md-4">
										<input type="radio" disabled class="form-check-input" id="BankStatementIncludedYes" name="BankStatementIncluded" value="yes" onChange="ToggleBankStatementIncludedExplanation()" <?php if (!is_null($financial_report_array['bank_statement_included'])) {if ($financial_report_array['bank_statement_included'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
					<!--<div class="col-sm-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} ?>>
					  <div class="form-group">
				            <div>
                            	<p class="form-control-static"><a href="<?php echo $financial_report_array['bank_statement_included_path']; ?>" target="_blank">Primary Bank Statement</a></p>
                           	</div>
                           	</div>
                           	</div>

					<div class="col-sm-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['bank_statement_2_included_path']) echo "style=\"display: none;\"";} ?>>
	                                                						  <div class="form-group">
  <div>
                            	<p class="form-control-static"><a href="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>" target="_blank">Additional Bank Statement</a></p>
                            </div>

                       </div>-->
					<input type="hidden" name="Statement2Path" id="Statement2Path" value="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>">
					<input type="hidden" name="StatementPath" id="StatementPath" value="<?php echo $financial_report_array['bank_statement_included_path']; ?>">
					</div>
					<div class="col-sm-12 brief_exp" id="divBankStatementIncludedExplanation">
					<label for="BankStatementIncludedExplanation">If no, briefly explain:</label>
					<textarea class="form-control" rows="2" name="BankStatementIncludedExplanation" id="BankStatementIncludedExplanation" readonly><?php if (!is_null($financial_report_array)) {echo $financial_report_array['bank_statement_included_explanation'];}?></textarea>
					</div>

					<div class="col-sm-12 mar_bot15">
					<p>24. If your group does not have any bank accounts, where is the chapter money kept?</p>
					   <div>
					  <textarea name="WheresTheMoney" readonly id="WheresTheMoney" class="form-control formctrl-h" rows="3" ><?php if (!is_null($financial_report_array)) {echo $financial_report_array['wheres_the_money'];}?></textarea>
						</div>
					</div>

				 </div>

				 <hr>

				 <!-- start:report_review -->
				<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
					<div class="col-md-12">
						<h2>Annual Report Review</h2>
					</div>



						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									5. Purchased membership pins or had leftovers:
								</label>
							</div>

							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkPurchasedPinsNo" name="checkPurchasedPins" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckPurchasedPinsNo')"  <?php if (!is_null($financial_report_array['check_purchased_pins'])) {if ($financial_report_array['check_purchased_pins'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>
								<label class="form-check-label">
										<input id="checkPurchasedPinsYes" name="checkPurchasedPins" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckPurchasedPinsYes')"  <?php if (!is_null($financial_report_array['check_purchased_pins'])) {if ($financial_report_array['check_purchased_pins'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									6. Purchased MOMS Club merchandise:
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkPurchasedMCMerchNo" name="checkPurchasedMCMerch" type="radio" value="no"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckPurchasedMCMerchNo')"  <?php if (!is_null($financial_report_array['check_purchased_mc_merch'])) {if ($financial_report_array['check_purchased_mc_merch'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>
								<label class="form-check-label">
										<input id="checkPurchasedMCMerchYes" name="checkPurchasedMCMerch" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckPurchasedMCMerchYes')"  <?php if (!is_null($financial_report_array['check_purchased_mc_merch'])) {if ($financial_report_array['check_purchased_mc_merch'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>


						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									7. Offered MOMS Club merchandise or info on how to buy to members:
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkOfferedMerchNo" name="checkOfferedMerch" type="radio" class="form-check-input" value="no" onchange="UpdateRelatedControl('sumcheckOfferedMerch')"  <?php if (!is_null($financial_report_array['check_offered_merch'])) {if ($financial_report_array['check_offered_merch'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>
								<label class="form-check-label">
										<input id="checkOfferedMerchYes" name="checkOfferedMerch" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckOfferedMerchYes')"  <?php if (!is_null($financial_report_array['check_offered_merch'])) {if ($financial_report_array['check_offered_merch'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>


						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									8. Manual/by-laws made available to members:
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkBylawsMadeAvailableNo" name="checkBylawsMadeAvailable" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckBylawsMadeAvailableNo')"  <?php if (!is_null($financial_report_array['check_bylaws_available'])) {if ($financial_report_array['check_bylaws_available'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
								<label class="form-check-label">
										<input id="checkBylawsMadeAvailableYes" name="checkBylawsMadeAvailable" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckBylawsMadeAvailableYes')"  <?php if (!is_null($financial_report_array['check_bylaws_available'])) {if ($financial_report_array['check_bylaws_available'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>
                <div class="col-md-6 mar_bot_20">
					<div class="col-md-12">
						<label for="">
							20. Sistered another Chapter:
						</label>
					</div>
					<div class="col-md-6">
						<div class="form-check form-check-radio">
							<label class="form-check-label">
							<input id="checkSisteredAnotherChapterNo" name="checkSisteredAnotherChapter" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckSisteredAnotherChapterNo')" <?php if (!is_null($financial_report_array['check_sistered_another_chapter'])) {if ($financial_report_array['check_sistered_another_chapter'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
							<span class="form-check-sign"></span>
								No
							</label>
						<label class="form-check-label">
								<input id="checkSisteredAnotherChapterYes" name="checkSisteredAnotherChapter" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckSisteredAnotherChapterYes')" <?php if (!is_null($financial_report_array['check_sistered_another_chapter'])) {if ($financial_report_array['check_sistered_another_chapter'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
								<span class="form-check-sign"></span>
								Yes
							</label>
						</div>
					</div>
				</div>


                        <div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									21. Attended International Event:
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkAttendedTrainingNo" name="checkAttendedTraining" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAttendedTrainingNo')"  <?php if (!is_null($financial_report_array['check_attended_training'])) {if ($financial_report_array['check_attended_training'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>
								<label class="form-check-label">
										<input id="checkAttendedTrainingYes" name="checkAttendedTraining" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAttendedTrainingYes')"  <?php if (!is_null($financial_report_array['check_attended_training'])) {if ($financial_report_array['check_attended_training'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>

                    <!--<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									Attended Luncheon:
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkAttendedLuncheonNo" name="checkAttendedLuncheon" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAttendedLuncheonNo')" <?php if (!is_null($financial_report_array['check_attended_luncheon'])) {if ($financial_report_array['check_attended_luncheon'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>

								</div>
							</div>

							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
										<input id="checkAttendedLuncheonYes" name="checkAttendedLuncheon" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAttendedLuncheonYes')" <?php if (!is_null($financial_report_array['check_attended_luncheon'])) {if ($financial_report_array['check_attended_luncheon'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>-->

						<div class="col-md-6 mar_bot_20">
							<div class="col-md-12">
								<label for="">
									22. Proof of 990N Filing: (July 1, <?php echo date('Y')-1 .' - June 30, '.date('Y');?>)
								</label>
							</div>
							<div class="col-md-6">
								<div class="form-check form-check-radio">
									<label class="form-check-label">
									<input id="checkCurrent990NAttachedNo" name="checkCurrent990NAttached" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckCurrent990NAttachedNo')"  <?php if (!is_null($financial_report_array['check_current_990N_included'])) {if ($financial_report_array['check_current_990N_included'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
									<span class="form-check-sign"></span>
										No
									</label>
							<label class="form-check-label">
										<input id="checkCurrent990NAttachedYes" name="checkCurrent990NAttached" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckCurrent990NAttachedYes')"  <?php if (!is_null($financial_report_array['check_current_990N_included'])) {if ($financial_report_array['check_current_990N_included'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
										<span class="form-check-sign"></span>
										Yes
									</label>
								</div>
							</div>
						</div>

							<div class="form-row">

						<div class="col-md-12 mar_bot_20" id="990NBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
							<div class="col-md-12">
								<label class="control-label" for="990NFiling">Attach the chapter's 990N filing confirmation (5 MB max):</label>
								<input name="990NFiling" id="990NFiling" type="file" accept=".pdf, .jpg, .jpeg" class="demo1 form-control" />
							</div>
						</div>


						<div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
							<div class="col-md-12" >
								<label class="control-label" for="990NLink">990N Confirmation:</label>
								<div>
									<p class="form-control-static"><a href="<?php echo $financial_report_array['file_irs_path']; ?>" target="_blank">990N Confirmation File</a></p>
								</div>
							</div>

							<div class="col-md-6">
								<button type="button" class="btn btn-info btn-primary btn-sm hide-on-print" onclick="Replace990N()">Replace</button>
							</div>

						</div>


						<div class="clearfix"></div>


						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step9_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="10" name="Step9_Log" id="Step9_Log" readonly><?php echo $financial_report_array['step_9_notes_log']; ?></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step9_Note">Note:</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(9)" name="Step9_Note" id="Step9_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<button type="button" id="AddNote9" class="btn btn-large btn-success" onclick="AddNote(9)" disabled>Add Note to Log</button>
							</div>
						</div>

					</div>
				</div>
				<!-- end:report_review -->


				 <div class="form-row form-group">
						<div class="card-body">
							<div class="col-md-12 text-center">
							  <button type="submit" id="btn-step-9" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
							</div>
						</div>
				</div>
			  </section>
		  </div><!-- end of accordion body -->
		  </div><!-- end of accordion item -->
			<!------End Step 9 ------>

			<!------Start Step 10 ------>
		  	<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='10') echo "active";?>">
				<div class="accordion-header js-accordion-header">Section 10 - Financial Summary</div>
				<div class="accordion-body js-accordion-body">
				<section>
				<div class="form-row form-group">
				  <div class="col-sm-12">
                    <h3>July 1, <?php echo date('Y')-1 .' - June 30, '.date('Y');?></h3>
				  </div>
					<div class="col-sm-12 float-left">
						<div class="form-group">
							<div class="col-sm-6 float-left">
							  <label for="">
								  Amount Reserved from Previous Year:
                                  <span class="f-sm">(Treasury Balance 7/1/<?php echo date('Y')-1;?>)</span>
							  </label>
							</div>
							<div class="col-sm-6 float-left">
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" name="SumAmountReservedFromPreviousYear" id="SumAmountReservedFromPreviousYear" class="form-control" value="0.00" aria-describedby="sizing-addon1" disabled>
							</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
					  <div class="box-brd">
						<h4>Income</h4>
						<div class="col-sm-6 float-left">
							<p>Membership Dues Income:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" aria-describedby="sizing-addon1" name="SumMembershipDuesIncome" id="SumMembershipDuesIncome" disabled>
											</div>
										</div>
					  </div>
						<div class="col-sm-6 float-left">
							<p>Service Project Income:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="SumServiceProjectIncome" id="SumServiceProjectIncome" disabled>
											</div>
										</div>
					  </div>
						<div class="col-sm-6 float-left">
							<p>Party Income:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="SumPartyIncome" id="SumPartyIncome" disabled>
											</div>
										</div>
					  </div>
						<div class="col-sm-6 float-left">
							<p>Monetary Donations to Chapter:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="SumMonetaryDonationIncome" id="SumMonetaryDonationIncome" disabled>
											</div>
										</div>
					  </div>
						<div class="col-sm-6 float-left">
							<p>International Event Reservation Income:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="SumInternationalEventIncome" id="SumInternationalEventIncome" disabled>
											</div>
										</div>
					  </div>
						<div class="col-sm-6 float-left">
							<p>Other Income:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="SumOtherIncome" id="SumOtherIncome" disabled>
											</div>
										</div>
					  </div>
						<div class="bg-color">
						<div class="col-sm-6 float-left">
							<p>Total Income:</p>
						</div>
						<div class="col-sm-6 float-left">
										<div class="form-group">
											<div class="input-group">
											<span class="input-group-addon">$</span>
											<input type="number" class="form-control" name="SumTotalIncome" id="SumTotalIncome" disabled>
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
                    <p>Meeting Room Expense:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumMeetingRoomExpense" id="SumMeetingRoomExpense" disabled>
                                    </div>
                                </div>

                </div>
               </div>
                <div class="col-sm-12 nopadding">
                <div class="col-sm-12">
                  <label>
                      Children's Room Expenses:
                  </label>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Supplies:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumChildrensSuppliesExpense" id="SumChildrensSuppliesExpense" disabled>
                                    </div>
                                </div>
              </div>
                <div class="col-sm-6 float-left">
                    <p>Paid Sitters:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumPaidSittersExpense" id="SumPaidSittersExpense" disabled>
                                    </div>
                                </div>
              </div>
                <div class="col-sm-6 float-left">
                    <p>Other:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumChildrensOtherExpense" id="SumChildrensOtherExpense" disabled>
                                    </div>
                                </div>
                </div>
                  <div class="col-sm-6 float-left">
                    <p>Children's Room Expense Total:</p>
                  </div>
                  <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumTotalChildrensRoomExpense" id="SumTotalChildrensRoomExpense" disabled>
                                    </div>
                                </div>
                  </div>
                </div>
                <div class="col-sm-12 nopadding">
                <div class="col-sm-12">
                  <label>
                      Service Project Expenses:
                  </label>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Supplies:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumServiceProjectExpense" id="SumServiceProjectExpense" disabled>
                                    </div>
                                </div>
              </div>
                <div class="col-sm-6 float-left">
                    <p>Amount Donated to Charity/Recipients:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumDonationExpense" id="SumDonationExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Mother-to-Mother Fund Donation:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumM2MExpense" id="SumM2MExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Service Project Expense Total:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumTotalServiceProjectExpense" id="SumTotalServiceProjectExpense" disabled>
                                    </div>
                                </div>
                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Party/Members Only Expense:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumPartyExpense" id="SumPartyExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-12 nopadding">
                <div class="col-sm-12">
                  <label>
                      Office and Operating Expenses:
                  </label>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Printing:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumPrintingExpense" id="SumPrintingExpense" disabled>
                                    </div>
                                </div>
              </div>
                <div class="col-sm-6 float-left">
                    <p>Postage:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumPostageExpense" id="SumPostageExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Membership Pins:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumPinsExpense" id="SumPinsExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Other:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumOtherOperatingExpense" id="SumOtherOperatingExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Office/Operating Expense Total:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumOperatingExpense" id="SumOperatingExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Annual Chapter Registration Fee:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumChapterReRegistrationExpense" id="SumChapterReRegistrationExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>International Event Registration:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumInternationalEventExpense" id="SumInternationalEventExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="col-sm-6 float-left">
                    <p>Other Expense:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumOtherExpense" id="SumOtherExpense" disabled>
                                    </div>
                                </div>
                </div>
                <div class="bg-color">
                <div class="col-sm-6 float-left">
                    <p>Total Expenses:</p>
                </div>
                <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumTotalExpense" id="SumTotalExpense" disabled>
                                    </div>
                                </div>
				</div>
                </div>
                </div>
                </div>

				<div class="box-brd">
				  <div class="col-sm-12 float-left  mar_bot_20">
						<div class="form-group">
							<div class="col-sm-6 float-left">
							  <label for="">
								  Treasury Balance Now

							  </label>
							</div>
							<div class="col-sm-6 float-left">
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" name="SumTreasuryBalanceNow" id="SumTreasuryBalanceNow" disabled>
							</div>
							</div>
						</div>
				  </div>
				  </div>
            </div>

         </div>

         <hr>

         <!-- start:report_review -->
		<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
			<div class="col-md-12">
				<h2>Annual Report Review</h2>
			</div>

			<div class="form-row">
				<div class="col-md-6 float-left">
					<div class="col-md-12">
						<div class="form-group">
							<label for="PartyExpenseTotal">
								Total Income/Revenue:
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0"  step="0.01" name="checkTotalIncomeCalc" id="checkTotalIncomeCalc" readonly>
							</div>
						</div>
					</div>
				</div>

				<div class="clearfix"></div>

				<div class="col-md-6 mar_bot_20">
					<div class="col-md-12">
						<label for="">
							Total income/revenue less than $50,000:
						</label>
					</div>
					<div class="col-md-6">
						<div class="form-check form-check-radio">
							<label class="form-check-label">
							<input id="checkTotalIncomeNo" name="checkTotalIncome" type="radio" value="no"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckTotalIncomeNo')" <?php if (!is_null($financial_report_array['check_total_income_less'])) {if ($financial_report_array['check_total_income_less'] == false) echo "checked";} ?>>
							<span class="form-check-sign"></span>
								No
							</label>
						<label class="form-check-label">
								<input id="checkTotalIncomeYes" name="checkTotalIncome" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckTotalIncomeYes')" <?php if (!is_null($financial_report_array['check_total_income_less'])) {if ($financial_report_array['check_total_income_less'] == true) echo "checked";} ?>>
								<span class="form-check-sign"></span>
								Yes
							</label>
						</div>
					</div>
				</div>


				<div class="col-md-12 mar_bot_20">
					<div class="col-md-12">
						<label for="Step10_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
					</div>
					<div class="col-md-12">
						<textarea class="form-control" style="width:100%" rows="10" name="Step10_Log" id="Step10_Log" readonly><?php echo $financial_report_array['step_10_notes_log']; ?></textarea>
					</div>
				</div>

				<div class="col-md-12 mar_bot_20">
					<div class="col-md-12">
						<label for="Step10_Note">Note:</label>
					</div>
					<div class="col-md-12">
						<textarea class="form-control" style="width:100%" rows="5"  oninput="EnableNoteLogButton(10)" name="Step1_Note" id="Step10_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
					</div>
				</div>

				<div class="col-md-12 mar_bot_20">
					<div class="col-md-12">
						<button type="button" id="AddNote10" class="btn btn-large btn-success" onclick="AddNote(10)" disabled>Add Note to Log</button>
					</div>
				</div>

			</div>
		</div>
		<!-- end:report_review -->

         <div class="form-row form-group">
                                <div class="card-body">
                                    <div class="col-md-12 text-center">
                                      <button type="submit" id="btn-step-10" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
                                    </div>
                                </div>
                        </div>
				</section>
			</div><!-- end of accordion body -->
			</div><!-- end of accordion item -->
			<!------End Step 10 ------>

			<!------Start Step 11 ------>
			<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='11') echo "active";?>">
                <div class="accordion-header js-accordion-header">Section 11 - Award Nominations</div>
				<div class="accordion-body js-accordion-body">
					<section>
					<div class="form-row form-group">
					    <div class="box_brd_contentpad">
						    <div class="box_brd_title_box">
								<h4>Instructions for Recognition Entry</h4>
						    </div>
							<input type="hidden" id="TotalAwardNominations" name="TotalAwardNominations" value=<?php
							if (!empty($financial_report_array)) {
								if ($financial_report_array['award_nominations']>0){
									echo $financial_report_array['award_nominations'];
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

                            </div>
                            <hr>

                        </div>
						<!-- Award 1 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType1">Award 1 Type:</label>
									<select class="form-control" id="NominationType1" name="NominationType1" onClick="ShowOutstandingCriteria(1)" disabled>
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<div class="award_acc_con">
								<div id="OutstandingCriteria1" style="display: none;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws1">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingFollowByLaws1Yes" name="OutstandingFollowByLaws1" type="radio" class="form-check-input" disabled value="yes"<?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_follow_bylaws'] == true) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingFollowByLaws1No" name="OutstandingFollowByLaws1" disabled <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_follow_bylaws'] == false) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded1">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingWellRounded1Yes" name="OutstandingWellRounded1" disabled type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_well_rounded'] == true) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingWellRounded1No" name="OutstandingWellRounded1" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_well_rounded'] == false) echo "checked";} ?> disabled value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated1">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingCommunicated1Yes" name="OutstandingCommunicated1" type="radio" class="form-check-input" disabled value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_communicated'] == true) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input"id="OutstandingCommunicated1No" name="OutstandingCommunicated1" disabled <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_communicated'] == false) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingSupportMomsClub1">Did you support the International MOMS Club? Indications of supporting International MAY include but are not limited to:</label></p>
									<ul>
										<li>Providing MOMS Club pins to your members</li>
										<li>Purchasing MOMS Club merchandise from International</li>
										<li>Forming sister chapters (when possible)</li>
										<li>Donating to the Mother-to-Mother Fund</li>
										<li>Participating in Area, State and Regional events.</li>
									</ul>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingSupportMomsClub1Yes" name="OutstandingSupportMomsClub1" type="radio" disabled class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_support_international'] == true) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingSupportMomsClub1No" name="OutstandingSupportMomsClub1" disabled <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_outstanding_support_international'] == false) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">
									<textarea class="form-control" rows="10" id="AwardDesc1" name="AwardDesc1" readonly><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_1_outstanding_project_desc'];}?></textarea>
								 </div>

								 <input type="hidden" name="Award1Path" id="Award1Path" value="<?php echo $financial_report_array['award_1_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_1_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_1_files = null;
											$Award1FileCount =0;
											if(isset($financial_report_array['award_1_files'])){
												$award_1_files=unserialize(base64_decode($financial_report_array['award_1_files']));
												$Award1FileCount = is_array($award_1_files) ? count($award_1_files) : 0;
												for ($row = 1; $row <= $Award1FileCount; $row++){
													$row_id = $row;
													echo "<p class=\"form-control-static\"><a href=\"" . $award_1_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
												}
											}
										?>
										</div>
									</div>
								</div>

							</div>
						</div>
						<!-- Award 1 Stop -->
						<!-- Award 2 Start -->
						<div class="box_brd_contentpad" id="Award2Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType2">Award 2 Type:</label>
									<select class="form-control" id="NominationType2" name="NominationType2" onClick="ShowOutstandingCriteria(2)" disabled>
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2107)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<div class="award_acc_con">
								<div id="OutstandingCriteria2" style="display: none;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws2">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingFollowByLaws2Yes" name="OutstandingFollowByLaws2" type="radio" class="form-check-input" value="yes" disabled <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_follow_bylaws'] == true) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingFollowByLaws2No" name="OutstandingFollowByLaws2" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_follow_bylaws'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded2">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingWellRounded2Yes" name="OutstandingWellRounded2" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_well_rounded'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingWellRounded2No" name="OutstandingWellRounded2" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_well_rounded'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated2">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingCommunicated2Yes" name="OutstandingCommunicated2" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_communicated'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input"id="OutstandingCommunicated2No" name="OutstandingCommunicated2" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_communicated'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingSupportMomsClub2">Did you support the International MOMS Club? Indications of supporting International MAY include but are not limited to:</label></p>
									<ul>
										<li>Providing MOMS Club pins to your members</li>
										<li>Purchasing MOMS Club merchandise from International</li>
										<li>Forming sister chapters (when possible)</li>
										<li>Donating to the Mother-to-Mother Fund</li>
										<li>Participating in Area, State and Regional events.</li>
									</ul>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingSupportMomsClub2Yes" name="OutstandingSupportMomsClub2" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_support_international'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingSupportMomsClub2No" name="OutstandingSupportMomsClub2" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_support_international'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">
									<textarea class="form-control" rows="10" id="AwardDesc2" name="AwardDesc2" readonly><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_2_outstanding_project_desc'];}?></textarea>
								 </div>


								 <input type="hidden" name="Award2Path" id="Award2Path" value="<?php echo $financial_report_array['award_2_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_2_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_2_files = null;
											$Award2FileCount =0;
											if(isset($financial_report_array['award_2_files'])){
												$award_2_files=unserialize(base64_decode($financial_report_array['award_2_files']));

                                                $row = 2;
												$row_id ='';
                                                if (!empty($award_2_files)) {
                                                    foreach ($award_2_files as $row => $fileInfo) {
                                                        echo "<p class=\"form-control-static\"><a href=\"" . $award_2_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";                                                    }
                                                } else {
                                                    // Handle the case where $award_2_files is empty or null
                                                }
                                            }
    										?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Award 2 Stop -->
						<!-- Award 3 Start -->
						<div class="box_brd_contentpad" id="Award3Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType3">Award 3 Type:</label>
									<select class="form-control" id="NominationType3" name="NominationType3" onClick="ShowOutstandingCriteria(3)" disabled>
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<div class="award_acc_con">
								<div id="OutstandingCriteria3" style="display: none;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws3">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingFollowByLaws3Yes" name="OutstandingFollowByLaws3" type="radio" class="form-check-input" value="yes"<?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_follow_bylaws'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingFollowByLaws3No" name="OutstandingFollowByLaws3" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_follow_bylaws'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded3">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingWellRounded3Yes" name="OutstandingWellRounded3" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_outstanding_well_rounded'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingWellRounded3No" name="OutstandingWellRounded3" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_well_rounded'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated3">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingCommunicated3Yes" name="OutstandingCommunicated3" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_communicated'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input"id="OutstandingCommunicated3No" name="OutstandingCommunicated3" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_communicated'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingSupportMomsClub3">Did you support the International MOMS Club? Indications of supporting International MAY include but are not limited to:</label></p>
									<ul>
										<li>Providing MOMS Club pins to your members</li>
										<li>Purchasing MOMS Club merchandise from International</li>
										<li>Forming sister chapters (when possible)</li>
										<li>Donating to the Mother-to-Mother Fund</li>
										<li>Participating in Area, State and Regional events.</li>
									</ul>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingSupportMomsClub3Yes" name="OutstandingSupportMomsClub3" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_support_international'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingSupportMomsClub3No" name="OutstandingSupportMomsClub3" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_outstanding_support_international'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">
									<textarea class="form-control" rows="10" id="AwardDesc3" name="AwardDesc3" readonly><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_3_outstanding_project_desc'];}?></textarea>
								 </div>

								<input type="hidden" name="Award3Path" id="Award3Path" value="<?php echo $financial_report_array['award_3_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_3_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_3_files = null;
											$Award3FileCount =0;
											if(isset($financial_report_array['award_3_files'])){
												$award_3_files=unserialize(base64_decode($financial_report_array['award_3_files']));

												$row = 3;
												$row_id ='';
												if (!empty($award_3_files)) {
                                                    foreach ($award_3_files as $row => $fileInfo) {
                                                        echo "<p class=\"form-control-static\"><a href=\"" . $award_3_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
                                                    }
                                                } else {
                                                    // Handle the case where $award_2_files is empty or null
                                                }
                                            }

										?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Award 3 Stop -->
						<!-- Award 4 Start -->
						<div class="box_brd_contentpad" id="Award4Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType4">Award 4 Type:</label>
									<select class="form-control" id="NominationType4" name="NominationType4" onClick="ShowOutstandingCriteria(4)" disabled>
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<div class="award_acc_con">
								<div id="OutstandingCriteria4" style="display: none;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws4">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingFollowByLaws4Yes" name="OutstandingFollowByLaws4" type="radio" class="form-check-input" value="yes"<?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_follow_bylaws'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingFollowByLaws4No" name="OutstandingFollowByLaws4" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_follow_bylaws'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded4">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingWellRounded4Yes" name="OutstandingWellRounded4" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_well_rounded'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingWellRounded4No" name="OutstandingWellRounded4" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_well_rounded'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated4">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingCommunicated4Yes" name="OutstandingCommunicated4" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_communicated'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input"id="OutstandingCommunicated4No" name="OutstandingCommunicated4" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_communicated'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingSupportMomsClub4">Did you support the International MOMS Club? Indications of supporting International MAY include but are not limited to:</label></p>
									<ul>
										<li>Providing MOMS Club pins to your members</li>
										<li>Purchasing MOMS Club merchandise from International</li>
										<li>Forming sister chapters (when possible)</li>
										<li>Donating to the Mother-to-Mother Fund</li>
										<li>Participating in Area, State and Regional events.</li>
									</ul>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingSupportMomsClub4Yes" name="OutstandingSupportMomsClub4" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_support_international'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingSupportMomsClub4No" name="OutstandingSupportMomsClub4" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_outstanding_support_international'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">
									<textarea class="form-control" rows="10" id="AwardDesc4" name="AwardDesc4" readonly><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_4_outstanding_project_desc'];}?></textarea>

								<input type="hidden" name="Award4Path" id="Award4Path" value="<?php echo $financial_report_array['award_4_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_4_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_4_files = null;
											$Award4FileCount =0;
											if(isset($financial_report_array['award_4_files'])){
												$award_4_files=unserialize(base64_decode($financial_report_array['award_4_files']));

												$row = 4;
												$row_id ='';
												if (!empty($award_4_files)) {
                                                    foreach ($award_4_files as $row => $fileInfo) {
                                                        echo "<p class=\"form-control-static\"><a href=\"" . $award_4_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
                                                    }
                                                } else {
                                                    // Handle the case where $award_2_files is empty or null
                                                }
                                            }
										?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Award 4 Stop -->
						<!-- Award 5 Start -->
						<div class="box_brd_contentpad" id="Award5Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType5">Award 5 Type:</label>
									<select class="form-control" id="NominationType5" name="NominationType5" onClick="ShowOutstandingCriteria(5)" disabled>
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<div class="award_acc_con">
								<div id="OutstandingCriteria5" style="display: none;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws5">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingFollowByLaws5Yes" name="OutstandingFollowByLaws5" type="radio" class="form-check-input" value="yes"<?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_follow_bylaws'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingFollowByLaws5No" name="OutstandingFollowByLaws5" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_follow_bylaws'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded5">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingWellRounded5Yes" name="OutstandingWellRounded5" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_well_rounded'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingWellRounded5No" name="OutstandingWellRounded5" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_well_rounded'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated5">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingCommunicated5Yes" name="OutstandingCommunicated5" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_communicated'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input"id="OutstandingCommunicated5No" name="OutstandingCommunicated5" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_communicated'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingSupportMomsClub5">Did you support the International MOMS Club? Indications of supporting International MAY include but are not limited to:</label></p>
									<ul>
										<li>Providing MOMS Club pins to your members</li>
										<li>Purchasing MOMS Club merchandise from International</li>
										<li>Forming sister chapters (when possible)</li>
										<li>Donating to the Mother-to-Mother Fund</li>
										<li>Participating in Area, State and Regional events.</li>
									</ul>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input id="OutstandingSupportMomsClub5Yes" name="OutstandingSupportMomsClub5" type="radio" class="form-check-input" value="yes" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_support_international'] == true) echo "checked";} ?> disabled>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input type="radio" class="form-check-input" id="OutstandingSupportMomsClub5No" name="OutstandingSupportMomsClub5" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_outstanding_support_international'] == false) echo "checked";} ?> value="no" disabled>
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">
									<textarea class="form-control" rows="10" id="AwardDesc5" name="AwardDesc5" readonly><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_5_outstanding_project_desc'];}?></textarea>
								 </div>

								<input type="hidden" name="Award5Path" id="Award5Path" value="<?php echo $financial_report_array['award_5_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_5_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_5_files = null;
											$Award5FileCount =0;
											if(isset($financial_report_array['award_5_files'])){
												$award_5_files=unserialize(base64_decode($financial_report_array['award_5_files']));


												$row = 5;
												$row_id ='';
												if (!empty($award_5_files)) {
                                                    foreach ($award_5_files as $row => $fileInfo) {
                                                        echo "<p class=\"form-control-static\"><a href=\"" . $award_5_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
                                                    }
                                                } else {
                                                    // Handle the case where $award_2_files is empty or null
                                                }

											}
										?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- Award 5 Stop -->

						<div class="box_brd_contentpad" id="AwardSignatureBlock" style="display: none;">
							  <div class="box_brd_title_box">
								 <h4>ALL ENTRIES MUST INCLUDE THIS SIGNED AGREEMENT</h4>
							  </div>
								<div class="award_acc_con">
									<p>I, THE UNDERSIGNED, AFFIRM THAT I HAVE THE RIGHT TO SUBMIT THE ENCLOSED ENTRY TO THE INTERNATIONAL MOMS CLUB FOR CONSIDERATION IN THEIR OUTSTANDING CHAPTER RECOGNITIONS, THAT THE ENCLOSED INFORMATION IS ACCURATE AND COMPLETE TO THE BEST OF MY ABILITY AND THAT I HAVE RECEIVED PERMISSION TO ENTER THIS INFORMATION FROM ANY OTHER MEMBERS WHO MAY HAVE CONTRIBUTED TO THIS ENTRY OR THE ORIGINAL ACTIVITY/PROJECT THAT IS BEING CONSIDERED. I UNDERSTAND THAT, WHETHER OR NOT MY CHAPTER RECEIVES A RECOGNITION, THE ENCLOSED ENTRY WILL BECOME THE PROPERTY OF THE INTERNATIONAL MOMS CLUB AND THAT THE INFORMATION, PICTURES, CLIPPINGS AND/OR OTHER MATERIALS ENCLOSED MAY BE SHARED WITH OTHER MOMS CLUB CHAPTERS OR USED IN ANY WAY THE INTERNATIONAL MOMS CLUB SEES FIT, WITH NO COMPENSATION TO ME, OTHERS INVOLVED IN THIS PROJECT AND/OR THE CHAPTER(S). NO ENTRIES OR SUBMITTED MATERIALS WILL BE RETURNED AND THE INTERNATIONAL MOMS CLUB MAY REASSIGN ANY ENTRY TO ANOTHER CATEGORY IF IT DEEMS NECESSARY. RECOGNITIONS WILL BE GIVEN IN THE VARIOUS CATEGORIES ACCORDING TO THE DECISION OF THE INTERNATIONAL MOMS CLUB. THE AWARDING OF RECOGNITIONS WILL BE ACCORDING TO MERIT, AND THE INTERNATIONAL MOMS CLUB MAY DECIDE NOT TO GIVE AN AWARD IN ANY OR ALL CATEGORIES IF IT SO CHOOSES. ALL DECISIONS OF THE INTERNATIONAL MOMS CLUB ARE FINAL. ANY RECOGNITIONS ARE OFFICIALLY PRESENTED TO THE LOCAL CHAPTERS, NOT THE INDIVIDUAL, AND RECOGNITIONS WILL NOT BE PERSONALIZED WITH ANY INDIVIDUAL’S NAME. REPLACEMENT RECOGNITIONS MAY OR MAY NOT BE MADE AVAILABLE AT INTERNATIONAL’S DISCRETION, AND IF A REPLACEMENT IS MADE BECAUSE OF AN ERROR IN THE ENTRY INFORMATION, THE COST WILL BE PAID IN ADVANCE BY THE LOCAL CHAPTER.</p>
									<div class="checkbox">
										<label><input type="checkbox" id="AwardsAgree" name="AwardsAgree" disabled>I understand and agree to the above</label>
									</div>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
										<input id="NominationSubmitor" name="NominationSubmitor" type="text" class="form-control" placeholder="Kathleen MacPhee">
									</div>
								</div>
                            </div>

                    </div>
					<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="award_acc_con">
                            <!-- start:report_review -->
							<div class="form-row report_review">
								<div class="col-md-12">
									<h2>Annual Report Review</h2>
								</div>

								<div class="form-row">


									<div class="col-md-12 mar_bot_20" <?php if ($financial_report_array['award_1_nomination_type']==NULL) echo "style=\"display: none;\""; ?> ?>

										<div class="form-group col-md-6">
											<label for="NominationType1">Award #1:</label>
											<select class="form-control" id="sumcheckNominationType1" name="sumcheckNominationType1" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>

										<div class="form-group col-md-6">
											<label class="radio control-label">Award Status:</label>
											<label class="custom-control custom-radio">
												<input id="sumcheckAward1ApprovedNo" name="sumcheckAward1Approved" type="radio" value="no" class="custom-control-input" onClick="UpdateRelatedControl('checkAward1ApprovedNo')"  <?php if ($financial_report_array['check_award_1_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">NOT Approved</span>
											</label>

											<label class="custom-control custom-radio">
												<input id="sumcheckAward1ApprovedYes" name="sumcheckAward1Approved" type="radio" value="yes" class="custom-control-input" onClick="UpdateRelatedControl('checkAward1ApprovedYes')" <?php if ($financial_report_array['check_award_1_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">Approved</span>
											</label>
										</div>

									</div>
									<div class="col-md-12 mar_bot_20" <?php if ($financial_report_array['award_2_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>

										<div class="form-group col-md-6">
											<label for="NominationType2">Award #2:</label>
											<select class="form-control" id="sumcheckNominationType2" name="sumcheckNominationType2" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>

										<div class="form-group col-md-6">
											<label class="radio control-label">Award Status:</label>
											<label class="custom-control custom-radio">
												<input id="sumcheckAward2ApprovedNo" name="sumcheckAward2Approved" type="radio" value="no" class="custom-control-input" onClick="UpdateRelatedControl('checkAward2ApprovedNo')"  <?php if ($financial_report_array['check_award_2_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">NOT Approved</span>
											</label>

											<label class="custom-control custom-radio">
												<input id="sumcheckAward2ApprovedYes" name="sumcheckAward2Approved" type="radio" value="yes" class="custom-control-input" onClick="UpdateRelatedControl('checkAward2ApprovedYes')" <?php if ($financial_report_array['check_award_2_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">Approved</span>
											</label>
										</div>

									</div>
									<div class="col-md-12 mar_bot_20" <?php if ($financial_report_array['award_3_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>

										<div class="form-group col-md-6">
											<label for="NominationType3">Award #3:</label>
											<select class="form-control" id="sumcheckNominationType3" name="sumcheckNominationType3" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>

										<div class="form-group col-md-6">
											<label class="radio control-label">Award Status:</label>
											<label class="custom-control custom-radio">
												<input id="sumcheckAward3ApprovedNo" name="sumcheckAward3Approved" type="radio" value="no" class="custom-control-input" onClick="UpdateRelatedControl('checkAward3ApprovedNo')"  <?php if ($financial_report_array['check_award_3_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">NOT Approved</span>
											</label>

											<label class="custom-control custom-radio">
												<input id="sumcheckAward3ApprovedYes" name="sumcheckAward3Approved" type="radio" value="yes" class="custom-control-input" onClick="UpdateRelatedControl('checkAward3ApprovedYes')" <?php if ($financial_report_array['check_award_3_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">Approved</span>
											</label>
										</div>

									</div>
									<div class="col-md-12 mar_bot_20" <?php if ($financial_report_array['award_4_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>

										<div class="form-group col-md-6">
											<label for="NominationType4">Award #4:</label>
											<select class="form-control" id="sumcheckNominationType4" name="sumcheckNominationType4" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>

										<div class="form-group col-md-6">
											<label class="radio control-label">Award Status:</label>
											<label class="custom-control custom-radio">
												<input id="sumcheckAward4ApprovedNo" name="sumcheckAward4Approved" type="radio" value="no" class="custom-control-input" onClick="UpdateRelatedControl('checkAward4ApprovedNo')"  <?php if ($financial_report_array['check_award_4_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">NOT Approved</span>
											</label>

											<label class="custom-control custom-radio">
												<input id="sumcheckAward4ApprovedYes" name="sumcheckAward4Approved" type="radio" value="yes" class="custom-control-input" onClick="UpdateRelatedControl('checkAward4ApprovedYes')" <?php if ($financial_report_array['check_award_4_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">Approved</span>
											</label>
										</div>

									</div>
									<div class="col-md-12 mar_bot_20" <?php if ($financial_report_array['award_5_nomination_type']==NULL) echo "style=\"display: none;\""; ?> >

										<div class="form-group col-md-6">
											<label for="NominationType5">Award #5:</label>
											<select class="form-control" id="sumcheckNominationType5" name="sumcheckNominationType5" disabled >
												<option value="" style="display:none" disabled selected>Select an award type</option>
												<option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
												<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
												<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
												<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
												<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
												<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
												<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
											</select>
										</div>

										<div class="form-group col-md-6">
											<label class="radio control-label">Award Status:</label>
											<label class="custom-control custom-radio">
												<input id="sumcheckAward5ApprovedNo" name="sumcheckAward5Approved" type="radio" value="no" class="custom-control-input" onClick="UpdateRelatedControl('checkAward5ApprovedNo')"  <?php if ($financial_report_array['check_award_5_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">NOT Approved</span>
											</label>

											<label class="custom-control custom-radio">
												<input id="sumcheckAward5ApprovedYes" name="sumcheckAward5Approved" type="radio" value="yes" class="custom-control-input" onClick="UpdateRelatedControl('checkAward5ApprovedYes')" <?php if ($financial_report_array['check_award_5_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="custom-control-indicator"></span>
												<span class="custom-control-description">Approved</span>
											</label>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-12 mar_bot_20">
										<div class="col-md-12">
											<label for="Step11_Log">Notes Logged for this Step (these are not visible to the chapter):</label>
										</div>
										<div class="col-md-12">
											<textarea class="form-control" style="width:100%" rows="10" name="Step11_Log" id="Step11_Log" readonly><?php echo $financial_report_array['step_11_notes_log']; ?></textarea>
										</div>
									</div>

									<div class="col-md-12 mar_bot_20">
										<div class="col-md-12">
											<label for="Step11_Note">Note:</label>
										</div>
										<div class="col-md-12">
											<textarea class="form-control" style="width:100%" oninput="EnableNoteLogButton(11)" rows="5" name="Step11_Note" id="Step11_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
										</div>
									</div>

									<div class="col-md-12 mar_bot_20">
										<div class="col-md-12">
											<button type="button" id="AddNote11" class="btn btn-large btn-success" onclick="AddNote(11)" disabled>Add Note to Log</button>
										</div>
									</div>

								</div>
							</div>
							<!-- end:report_review -->
						</div>
					<div class="form-row form-group">
					   <div class="card-body">
						  <div class="col-md-12 text-center">
							 <button type="submit" id="btn-step-11" class="btn btn-info btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>
						  </div>
					   </div>
				    </div>
					</section>
				</div>
            </div>

			<!------Start Step 12 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='12') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 12 - Summary Log</div>
					<div class="accordion-body js-accordion-body">
						<section>
							<!-- start:report_review -->
								<div class="form-row report_review" >
									<div class="col-md-12">
										<h2>Annual Report Review</h2>
									</div>
									<div class="col-md-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['roster_path']) echo "style=\"display: none;\"";} echo "style=\"display: block;\""; ?>>
											<div class="form-group col-xs-12">
												<label class="control-label" for="RosterLink">Chapter Roster File:</label>
													<a href="<?php echo $financial_report_array['roster_path']; ?>" target="_blank">Chapter Roster</a>
											</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Excel roster attached and complete (should be above):
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckRosterAttachedNo" name="sumcheckRosterAttached" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkRosterAttachedNo')"  <?php if (!is_null($financial_report_array['check_roster_attached'])) {if ($financial_report_array['check_roster_attached'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckRosterAttachedYes" name="sumcheckRosterAttached" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkRosterAttachedYes')"  <?php if (!is_null($financial_report_array['check_roster_attached'])) {if ($financial_report_array['check_roster_attached'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Minimum of one service project completed:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckServiceProjectNo" name="sumcheckServiceProject" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkServiceProjectNo')"  <?php if (!is_null($financial_report_array['check_minimum_service_project'])) {if ($financial_report_array['check_minimum_service_project'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
										<label class="form-check-label">
													<input id="sumcheckServiceProjectYes" name="sumcheckServiceProject" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkServiceProjectYes')"  <?php if (!is_null($financial_report_array['check_minimum_service_project'])) {if ($financial_report_array['check_minimum_service_project'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Donation to M2M or General Fund (sustaining chapter):
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckM2MDonationNo" name="sumcheckM2MDonation" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkM2MDonationNo')"  <?php if (!is_null($financial_report_array['check_m2m_donation'])) {if ($financial_report_array['check_m2m_donation'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckM2MDonationYes" name="sumcheckM2MDonation" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkM2MDonationYes')"  <?php if (!is_null($financial_report_array['check_m2m_donation'])) {if ($financial_report_array['check_m2m_donation'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<!--<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Donation to MOMS Club General Fund (sustaining chapter):
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckMCGeneralFundNo" name="sumcheckMCGeneralFund" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkMCGeneralFundNo')"  <?php if (!is_null($financial_report_array['check_mc_general_fund'])) {if ($financial_report_array['check_mc_general_fund'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>

											</div>
										</div>

										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
													<input id="sumcheckMCGeneralFundYes" name="sumcheckMCGeneralFund" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkMCGeneralFundYes')"  <?php if (!is_null($financial_report_array['check_mc_general_fund'])) {if ($financial_report_array['check_mc_general_fund'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>-->
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Attended International Event:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckAttendedTrainingNo" name="sumcheckAttendedTraining" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkAttendedTrainingNo')"  <?php if (!is_null($financial_report_array['check_attended_training'])) {if ($financial_report_array['check_attended_training'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckAttendedTrainingYes" name="sumcheckAttendedTraining" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAttendedTrainingYes')"  onchange="UpdateRelatedControl('checkAttendedTrainingYes')"  <?php if (!is_null($financial_report_array['check_attended_training'])) {if ($financial_report_array['check_attended_training'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>

									<!--<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Attended luncheon:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckAttendedLuncheonNo" name="sumcheckAttendedLuncheon" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkAttendedLuncheonNo')"  <?php if ($financial_report_array['check_attended_luncheon'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
													<input id="sumcheckAttendedLuncheonYes" name="sumcheckAttendedLuncheon" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkAttendedLuncheonYes')"  <?php if ($financial_report_array['check_attended_luncheon'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>-->



									<div class="col-md-6 float-left">
										<div class="col-md-12">
											<div class="form-group">
												<label for="PartyDuesIncomeReview">
													Dues Income:
												</label>
												<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="number" class="form-control" min="0"  step="0.01" name="sumPartyDuesIncomeReview" id="sumPartyDuesIncomeReview" readonly>
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-6 float-left">
										<div class="col-md-12">
											<div class="form-group">
												<label for="PartyIncomeReview">
													Party Income:
												</label>
												<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="number" class="form-control" min="0"  step="0.01" name="sumPartyIncomeReview" id="sumPartyIncomeReview" readonly>
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-6 float-left">
										<div class="col-md-12">
											<div class="form-group">
												<label for="PartyExpenseReview">
													Party Expenses:
												</label>
												<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="number" class="form-control" min="0"  step="0.01" name="sumPartyExpenseReview" id="sumPartyExpenseReview" readonly>
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-6 float-left">
										<div class="col-md-12">
											<div class="form-group">
												<label for="PartyExpensePercent">
													Expense Percentage:
												</label>
												<div class="input-group">
												<span class="input-group-addon">%</span>
												<input type="number" class="form-control" min="0"  step="0.01" name="sumPartyExpensePercent" id="sumPartyExpensePercent" readonly>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Number of members listed, dues received, and renewal paid "seem right":
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckRenewalSeemsRightNo" name="sumcheckRenewalSeemsRight" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkRenewalSeemsRightNo')"  <?php if (!is_null($financial_report_array['check_renewal_seems_right'])) {if ($financial_report_array['check_renewal_seems_right'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckRenewalSeemsRightYes" name="sumcheckRenewalSeemsRight" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkRenewalSeemsRightYes')"  <?php if (!is_null($financial_report_array['check_renewal_seems_right'])) {if ($financial_report_array['check_renewal_seems_right'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Sistered another chapter:</label>
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckSisteredAnotherChapterNo" name="sumcheckSisteredAnotherChapter" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkSisteredAnotherChapterNo')" <?php if (!is_null($financial_report_array['check_sistered_another_chapter'])) {if ($financial_report_array['check_sistered_another_chapter'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckSisteredAnotherChapterYes" name="sumcheckSisteredAnotherChapter" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('checkSisteredAnotherChapterYes')" <?php if (!is_null($financial_report_array['check_sistered_another_chapter'])) {if ($financial_report_array['check_sistered_another_chapter'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} echo "style=\"display: block;\""; ?>>
											<div class="form-group col-xs-12">
												<label class="control-label" for="RosterLink">Primary Bank Statement:</label>
													<a href="<?php echo $financial_report_array['bank_statement_included_path']; ?>" target="_blank">Statement 1</a>
											</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['bank_statement_2_included_path']) echo "style=\"display: none;\"";} echo "style=\"display: block;\""; ?>>
											<div class="form-group col-xs-12">
												<label class="control-label" for="RosterLink">Additional Bank Statement:</label>
													<a href="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>" target="_blank">Statement 2</a>
											</div>
									</div>
									<div class="clearfix"></div>

										<div class="col-md-6 mar_bot_20">
											<div class="col-md-12">
												<label for="">
													Current bank statement included (should be above):
												</label>
											</div>
											<div class="col-md-6">
												<div class="form-check form-check-radio">
													<label class="form-check-label">
													<input id="sumcheckBankStatementIncludedNo" name="sumcheckBankStatementIncluded" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkBankStatementIncludedNo')" <?php if (!is_null($financial_report_array['check_bank_statement_included'])) {if ($financial_report_array['check_bank_statement_included'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
														No
													</label>
												<label class="form-check-label">
														<input id="sumcheckBankStatementIncludedYes" name="sumcheckBankStatementIncluded" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkBankStatementIncludedYes')" <?php if (!is_null($financial_report_array['check_bank_statement_included'])) {if ($financial_report_array['check_bank_statement_included'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
														<span class="form-check-sign"></span>
														Yes
													</label>
												</div>
											</div>
										</div>
										<div class="col-md-6 mar_bot_20">
											<div class="col-md-12">
												<label for="">
													Treasury Balance Now matches Reconciled Bank Balance:
												</label>
											</div>
											<div class="col-md-6">
												<div class="form-check form-check-radio">
													<label class="form-check-label">
													<input id="sumcheckBankStatementMatchesNo" name="sumcheckBankStatementMatches" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkBankStatementMatchesNo')" <?php if (!is_null($financial_report_array['check_bank_statement_matches'])) {if ($financial_report_array['check_bank_statement_matches'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
														No
													</label>
													<label class="form-check-label">
														<input id="sumcheckBankStatementMatchesYes" name="sumcheckBankStatementMatches" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkBankStatementMatchesYes')" <?php if (!is_null($financial_report_array['check_bank_statement_matches'])) {if ($financial_report_array['check_bank_statement_matches'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
														<span class="form-check-sign"></span>
														Yes
													</label>
												</div>
											</div>

									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Purchased membership pins or had leftovers:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckPurchasedPinsNo" name="sumcheckPurchasedPins" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkPurchasedPinsNo')"  <?php if (!is_null($financial_report_array['check_purchased_pins'])) {if ($financial_report_array['check_purchased_pins'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
												<label class="form-check-label">
													<input id="sumcheckPurchasedPinsYes" name="sumcheckPurchasedPins" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkPurchasedPinsYes')"  <?php if (!is_null($financial_report_array['check_purchased_pins'])) {if ($financial_report_array['check_purchased_pins'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Purchased MOMS Club merchandise:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckPurchasedMCMerchNo" name="sumcheckPurchasedMCMerch" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkPurchasedMCMerchNo')"  <?php if (!is_null($financial_report_array['check_purchased_mc_merch'])) {if ($financial_report_array['check_purchased_mc_merch'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
												<label class="form-check-label">
													<input id="sumcheckPurchasedMCMerchYes" name="sumcheckPurchasedMCMerch" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkPurchasedMCMerchYes')"  <?php if (!is_null($financial_report_array['check_purchased_mc_merch'])) {if ($financial_report_array['check_purchased_mc_merch'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Offered MOMS Club merchandise or info on how to buy to members:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckOfferedMerchNo" name="sumcheckOfferedMerch" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkOfferedMerch')"  <?php if (!is_null($financial_report_array['check_offered_merch'])) {if ($financial_report_array['check_offered_merch'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckOfferedMerchYes" name="sumcheckOfferedMerch" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('checkOfferedMerchYes')"  <?php if (!is_null($financial_report_array['check_offered_merch'])) {if ($financial_report_array['check_offered_merch'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Manual/by-laws made available to members:
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckBylawsMadeAvailableNo" name="sumcheckBylawsMadeAvailable" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkBylawsMadeAvailableNo')"  <?php if (!is_null($financial_report_array['check_bylaws_available'])) {if ($financial_report_array['check_bylaws_available'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckBylawsMadeAvailableYes" name="sumcheckBylawsMadeAvailable" type="radio" value="yes" class="form-check-input" onchange="UpdateRelatedControl('checkBylawsMadeAvailableYes')"  <?php if (!is_null($financial_report_array['check_bylaws_available'])) {if ($financial_report_array['check_bylaws_available'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>

									<div class="clearfix"></div>

									<div class="col-md-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} echo "style=\"display: block;\""; ?>>
											<div class="form-group col-xs-12">
												<label class="control-label" for="990NLink">990N Filing:</label>
													<a href="<?php echo $financial_report_array['file_irs_path']; ?>" target="_blank">990N Confirmation</a>
											</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Proof of 990N Filing for July 1 <?php echo date('Y')-1 .' - June 30, '.date('Y');?> (should be above):</label>
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckCurrent990NAttachedNo" name="sumcheckCurrent990NAttached" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkCurrent990NAttachedNo')"  <?php if (!is_null($financial_report_array['check_current_990N_included'])) {if ($financial_report_array['check_current_990N_included'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckCurrent990NAttachedYes" name="sumcheckCurrent990NAttached" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('checkOfferedMerchYes')"  <onchange="UpdateRelatedControl('checkCurrent990NAttachedYes')"  <?php if (!is_null($financial_report_array['check_current_990N_included'])) {if ($financial_report_array['check_current_990N_included'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-6 float-left">
										<div class="col-md-12">
											<div class="form-group">
												<label for="PartyDuesIncomeReview">
													Total Income/Revenue:
												</label>
												<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="number" class="form-control" min="0"  step="0.01"name="sumcheckTotalIncomeCalc" id="sumcheckTotalIncomeCalc" readonly>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
											<label for="">
												Total income/revenue less than $50,000:</label>
											</label>
										</div>
										<div class="col-md-6">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="sumcheckTotalIncomeNo" name="sumcheckTotalIncome" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('checkTotalIncomeNo')" <?php if (!is_null($financial_report_array['check_total_income_less'])) {if ($financial_report_array['check_total_income_less'] == false) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													No
												</label>
											<label class="form-check-label">
													<input id="sumcheckTotalIncomeYes" name="sumcheckTotalIncome" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('checkTotalIncomeYes')" <?php if (!is_null($financial_report_array['check_total_income_less'])) {if ($financial_report_array['check_total_income_less'] == true) echo "checked";} ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Yes
												</label>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="col-md-6 float-left">
										<div class="col-md-12">
											<div class="form-group">
												<label for="checkTreasuryBalanceNow">
													Treasury Balance Now:
												</label>
												<div class="input-group">
												<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="checkTreasuryBalanceNow" id="checkTreasuryBalanceNow" disabled>
                                                </div>
											</div>
										</div>
									</div>
							<div class="col-md-6 mar_bot_20">
										<div class="col-md-12">
								<div class="form-group">
							<label for="post_balance">
								Enter Ending Balance (to be used as beginning balance on next year's report):
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" min="0" step="0.01" name="post_balance" id="post_balance" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['post_balance'] ?>" >
							</div>
						</div>
							</div>
							</div>
																<div class="clearfix"></div>

								<div class="col-md-6 mar_bot_20">

										<div class="col-md-12">
											<label for="">
												Award #1 Status:</label>
											</label>
										</div>



										<div class="col-md-12">

											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="checkAward1ApprovedNo" name="checkAward1Approved" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward1ApprovedNo')" <?php if ($financial_report_array['check_award_1_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													Not Approved
												</label>
											<label class="form-check-label">
													<input id="checkAward1ApprovedYes" name="checkAward1Approved" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward1ApprovedYes')" <?php if ($financial_report_array['check_award_1_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Approved
												</label>
											</div>
												<div class="form-group">
									<select class="form-control" id="NominationType1" name="NominationType1" onClick="ShowOutstandingCriteria(1)" disabled>
									   <option style="display:none" disabled selected>No Award Selected</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
										</div>
									</div>
									<div class="clearfix"></div>

								<div class="col-md-6 mar_bot_20">

										<div class="col-md-12">
											<label for="">
												Award #2 Status:</label>
											</label>
										</div>
										<div class="col-md-12">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="checkAward2ApprovedNo" name="checkAward2Approved" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward2ApprovedNo')" <?php if ($financial_report_array['check_award_2_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													Not Approved
												</label>
											<label class="form-check-label">
													<input id="checkAward2ApprovedYes" name="checkAward2Approved" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward2ApprovedYes')" <?php if ($financial_report_array['check_award_2_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Approved
												</label>
											</div>

								<div class="form-group">
									<select class="form-control" id="NominationType2" name="NominationType2" onClick="ShowOutstandingCriteria(2)" disabled>
									   <option style="display:none" disabled selected>No Award Selected</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2107)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
										</div>
									<div class="clearfix"></div>

								<div class="col-md-6 mar_bot_20">

										<div class="col-md-12">
											<label for="">
												Award #3 Status:</label>
											</label>
										</div>
										<div class="col-md-12">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="checkAward3ApprovedNo" name="checkAward3Approved" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward3ApprovedNo')" <?php if ($financial_report_array['check_award_3_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													Not Approved
												</label>
											<label class="form-check-label">
													<input id="checkAward3ApprovedYes" name="checkAward3Approved" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward3ApprovedYes')" <?php if ($financial_report_array['check_award_3_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Approved
												</label>
											</div>
											<div class="form-group">
									<select class="form-control" id="NominationType3" name="NominationType3" onClick="ShowOutstandingCriteria(3)" disabled>
									   <option style="display:none" disabled selected>No Award Selected</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
									</div>
									</div>
									<div class="clearfix"></div>

								<div class="col-md-6 mar_bot_20">

										<div class="col-md-12">
											<label for="">
												Award #4 Status:</label>
											</label>
										</div>
										<div class="col-md-12">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="checkAward4ApprovedNo" name="checkAward4Approved" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward4ApprovedNo')" <?php if ($financial_report_array['check_award_4_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													Not Approved
												</label>
											<label class="form-check-label">
													<input id="checkAward4ApprovedYes" name="checkAward4Approved" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward4ApprovedYes')" <?php if ($financial_report_array['check_award_4_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Approved
												</label>
											</div>
											<div class="form-group">
									<select class="form-control" id="NominationType4" name="NominationType4" onClick="ShowOutstandingCriteria(4)" disabled>
									   <option style="display:none" disabled selected>No Award Selected</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
										</div>
									</div>
									<div class="clearfix"></div>



								<div class="col-md-6 mar_bot_20">

										<div class="col-md-12">
											<label for="">
												Award #5 Status:</label>
											</label>
										</div>
										<div class="col-md-12">
											<div class="form-check form-check-radio">
												<label class="form-check-label">
												<input id="checkAward5ApprovedNo" name="checkAward5Approved" type="radio" value="no" class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward5ApprovedNo')" <?php if ($financial_report_array['check_award_5_approved'] == false) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
												<span class="form-check-sign"></span>
													Not Approved
												</label>
											<label class="form-check-label">
													<input id="checkAward5ApprovedYes" name="checkAward5Approved" type="radio" value="yes"  class="form-check-input" onchange="UpdateRelatedControl('sumcheckAward5ApprovedYes')" <?php if ($financial_report_array['check_award_5_approved'] == true) echo "checked"; ?> <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
													<span class="form-check-sign"></span>
													Approved
												</label>
										</div>
										<div class="form-group">
									<select class="form-control" id="NominationType5" name="NominationType5" onClick="ShowOutstandingCriteria(5)" disabled>
									   <option style="display:none" disabled selected>No Award Selected</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
											<option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, 2018))</option>
											<option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, 2018)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
									</div>
									</div>
																		<div class="clearfix"></div>


									<div class="col-md-12 mar_bot_20">
										<label for="Summary_Log">Notes Logged for this Report (these are not visible to the chapter):</label>
										<textarea class="form-control" rows="20" name="Summary_Log" id="Summary_Log" readonly><?php echo $financial_report_array['step_1_notes_log'] . PHP_EOL . $financial_report_array['step_2_notes_log'] . PHP_EOL . $financial_report_array['step_3_notes_log'] . PHP_EOL . $financial_report_array['step_4_notes_log'] . PHP_EOL . $financial_report_array['step_5_notes_log'] . PHP_EOL . $financial_report_array['step_6_notes_log'] . PHP_EOL . $financial_report_array['step_7_notes_log'] . PHP_EOL . $financial_report_array['step_8_notes_log'] . PHP_EOL . $financial_report_array['step_9_notes_log'] . PHP_EOL . $financial_report_array['step_10_notes_log'] . PHP_EOL . $financial_report_array['step_11_notes_log']; ?></textarea>
									</div>


								</div>
								<!-- end:report_review -->

		<div class="form-row form-group">
								<div class="card-body">
                                    <div class="col-md-12 text-center">
										<button type="submit" id="btn-step-12" class="btn btn-info margin btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>

                                    </div>
                                </div>

                            </div>
						</section>
					</div>
				</div>
				<!------End Step 12 ------>
				<!------Start Step 13 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='13') echo "active";?>">
					<div class="accordion-header js-accordion-header">Section 13 - Complete Review</div>
					<div class="accordion-body js-accordion-body">
						<section>
						    <div class="col-sm-12">

                    <p>Contact information for the person who completed the report:</p>

                        <div class="col-sm-12">
                       <p>Name:  <?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_name'];}?></p>
                        </div>
                         <div class="col-sm-12">
                           <p>Email:  <a href="mailto:<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?>"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?></a></p>
                        </div>
                        </div>

									<div class="col-md-4 mar_bot_20">
										<div class="form-group">
										<br>
										<label for="AssignedReviewer">Select a Reviewer:</label>
											<select class="form-control" name="AssignedReviewer" id="AssignedReviewer" required>
											   <option value="" style="display:none" disabled selected>Select a reviewer</option>
											    @foreach($reviewerList as $pcl)
												  <option value="{{$pcl['cid']}}" {{$financial_report_array['reviewer_id'] == $pcl['cid']  ? 'selected' : ''}} >{{$pcl['cname']}}</option>
												@endforeach
											</select>
										</div>
									</div>

						    	<div class="form-row form-group">
								<div class="card-body">
                                    <div class="col-md-12 text-center">
                                        <br>
                                        <button type="submit" id="btn-step-13" class="btn btn-info margin btn-blue" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Save</button>

										<?php
										if ($financial_report_array['review_complete']!="" && $submitted && (Session::get('positionid') ==5 ||Session::get('positionid') ==6 || Session::get('positionid') ==7)){ ?>
											<button type="button" class="btn btn-info margin btn-green" id="review-clear" <?php if(!$submitted) echo "disabled"; ?>>Clear Review Complete</button>

										<?php }
										else{
											?>
											<button type="button" class="btn btn-info margin btn-green" id="review-complete" <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>Mark as Review Complete</button>
										<?php } ?>
										<button type="button" class="btn btn-info margin btn-red" id="unsubmit" <?php if ($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"?>>UnSubmit Report</button>
										 <p style="color:red;"><b>"Mark as Review Complete" is for FINAL REVIEWER USE ONLY!</b></p>
                                    </div>
                                </div>

                            </div>
						</section>
					</div>
				</div>
		<!------End Step 13 ------>

			</div><!-- end of accordion -->
			</form>

		</div>
   	</div>
</div>
<div class="box-body text-center">

              <!--<a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Financial Report Review - MOMS Club of {{ $chapterDetails[0]->chapter_name }}, {{$chapterDetails[0]->state}}" class="btn btn-themeBlue margin">E-mail Board</a>-->

              <a href="{{ route('home') }}" class="btn btn-themeBlue margin">Back</a>
              </div>


@endsection
@section('customscript')
<script>
    //$('.demo1').fileselect();
    $(window).on("load", function() {
	    LoadSteps();
    });
</script>
<!-- JQUERY STEP -->
<script>
    var accordion = (function(){

  var $accordion = $('.js-accordion');
  var $accordion_header = $accordion.find('.js-accordion-header');
  var $accordion_item = $('.js-accordion-item');

  // default settings
  var settings = {
    // animation speed
    speed: 400,

    // close all other accordion items if true
    oneOpen: false
  };

  return {
    // pass configurable object literal
    init: function($settings) {
      $accordion_header.on('click', function() {
        accordion.toggle($(this));
      });

      $.extend(settings, $settings);

      // ensure only one accordion is active if oneOpen is true
      if(settings.oneOpen && $('.js-accordion-item.active').length > 1) {
        $('.js-accordion-item.active:not(:first)').removeClass('active');
      }

      // reveal the active accordion bodies
      $('.js-accordion-item.active').find('> .js-accordion-body').show();
    },
    toggle: function($this) {

      if(settings.oneOpen && $this[0] != $this.closest('.js-accordion').find('> .js-accordion-item.active > .js-accordion-header')[0]) {
        $this.closest('.js-accordion')
               .find('> .js-accordion-item')
               .removeClass('active')
               .find('.js-accordion-body')
               .slideUp()
      }

      // show/hide the clicked accordion item
      $this.closest('.js-accordion-item').toggleClass('active');
      $this.next().stop().slideToggle(settings.speed);
    }
  }
})();

$(document).ready(function(){
	accordion.init({ speed: 300, oneOpen: true });

	$("#unsubmit").click(function() {
		var result=confirm("Unsubmitting this report will make it editable by the chapter again and will disable coordinator editing until the chapter has resubmitted - any unsaved changes will be lost.  Do you wish to continue?");
		if(result){
			//RemoveRequired();

			$("#submitted").val('');
			$("#submit_type").val('UnSubmit');
			$("#FurthestStep").val('13');
			//return false;
			$("#financial_report").submit();

		}
	});

	$("#review-complete").click(function() {
		var result=confirm("This will finalize this report and flag it as 'review complete'.  Do you wish to continue?");
		if(result){

		var post_balance = $('#post_balance').val();
		if(post_balance == null || post_balance == ''){
			alert('Please enter Ending Balance in Section 12');
			$('#post_balance').focus();
			return false;
		}
		//	var review_result = CheckReviewAnswers();
		//	if(review_result){
				$("#submit_type").val('review_complete');
				$("#FurthestStep").val('13');
				$("#financial_report").submit();
		//	}else{
		//		return false;
		//	}
		}
	});

	$("#review-clear").click(function() {
		var result=confirm("This will clear the 'review complete' flag and coordinators will be able to edit the report again.  Do you wish to continue?");
		if(result){
			$("#submit_type").val('review_clear');
			$("#FurthestStep").val('13');
			//return false;
			$("#financial_report").submit();

		}
	});

	$("#btn-step-1").click(function() {
		$("#FurthestStep").val('1');
		$("#financial_report").submit();
	});
	$("#btn-step-2").click(function() {
		$("#FurthestStep").val('2');
		$("#financial_report").submit();
	});
	$("#btn-step-3").click(function() {
		$("#FurthestStep").val('3');
		$("#financial_report").submit();
	});
	$("#btn-step-4").click(function() {
		$("#FurthestStep").val('4');
		$("#financial_report").submit();
	});
	$("#btn-step-5").click(function() {
		$("#FurthestStep").val('5');
		$("#financial_report").submit();
	});
	$("#btn-step-6").click(function() {
		$("#FurthestStep").val('6');
		$("#financial_report").submit();
	});
	$("#btn-step-7").click(function() {
		$("#FurthestStep").val('7');
		$("#financial_report").submit();
	});
	$("#btn-step-8").click(function() {
		$("#FurthestStep").val('8');
		$("#financial_report").submit();
	});
	$("#btn-step-9").click(function() {
		$("#FurthestStep").val('9');
		$("#financial_report").submit();
	});
	$("#btn-step-10").click(function() {
		$("#FurthestStep").val('10');
		$("#financial_report").submit();
	});
	$("#btn-step-11").click(function() {
		$("#FurthestStep").val('11');
		$("#financial_report").submit();
	});
	$("#btn-step-12").click(function() {
	    var post_balance = $('#post_balance').val();
		if(post_balance == null || post_balance == ''){
			alert('Please enter Ending Balance');
			$('#post_balance').focus();
			return false;
		}
		$("#FurthestStep").val('12');
		$("#financial_report").submit();
	});
	$("#btn-step-13").click(function() {
    var assignedReviewer = $('#AssignedReviewer').val();
    if (assignedReviewer == null || assignedReviewer == '') {
        alert('Please select a Reviewer');
        $('#AssignedReviewer').focus();
        return false;
    } else {
        $("#FurthestStep").val('13');
        $("#financial_report").submit();
    }
});


});
</script>
<script>

	function ReplaceStatement1(){

		document.getElementById("StatementBlock").style.display = 'block';
		document.getElementById("StatementBlock").style.visibility = 'visible';

	}

		function ReplaceStatement2(){

		document.getElementById("Statement2Block").style.display = 'block';
		document.getElementById("Statement2Block").style.visibility = 'visible';

	}

	function ReplaceRoster(){

		document.getElementById("RosterBlock").style.display = 'block';
		document.getElementById("RosterBlock").style.visibility = 'visible';

	}

	function Replace990N(){

		document.getElementById("990NBlock").style.display = 'block';
		document.getElementById("990NBlock").style.visibility = 'visible';

	}

	function UpdateRelatedControl(RelatedElementName){

		document.getElementById(RelatedElementName).checked = true;

	}

	function EnableNoteLogButton(NoteNumber){

		if(document.getElementById("Step" + NoteNumber + "_Note").value!="")
			document.getElementById("AddNote" + NoteNumber).disabled = false;
		else
			document.getElementById("AddNote" + NoteNumber).disabled = true;

	}

	function AddNote(NoteNumber){
		var Note = "";
		var Log = "";
		var d = new Date();
		var now = "";
		var SummaryNote="";

		now = d.toString();

		<?php $date = date('m/d/Y'); ?>

		Note=document.getElementById("Step" + NoteNumber + "_Note").value;
		console.log(Note);
		Log += "\n" + "Step " + NoteNumber + " Note [<?php echo $date; ?>, <?php echo $loggedInName; ?>] - " + Note;

		document.getElementById("Step" + NoteNumber + "_Log").value += Log;
		document.getElementById("Step" + NoteNumber + "_Note").value = "";
		document.getElementById("AddNote" + NoteNumber).disabled = true;


		for(i=1;i<12;i++){
			Note=document.getElementById("Step" + i + "_Log").value;
			SummaryNote += Note;
		}
		document.getElementById("Summary_Log").value = SummaryNote;
	}

	function ChargeDifferentMembers(ButtonID){

		document.getElementById("chapterid").value=ButtonID;
		return true;

	}

	function ChapterDuesQuestionsChange(){

		var ChangedMeetingFees=false;
		var ChargedMembersDifferently=false;
		var MembersReducedDues=false;

		ChangedMeetingFees = document.getElementById("optChangeDuesYes").checked;
		ChargedMembersDifferently = document.getElementById("optNewOldDifferentYes").checked;
		MembersReducedDues = document.getElementById("optNoFullDuesYes").checked;

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

		var ChangedMeetingFees=false;
		var ChargedMembersDifferently=false;
		var MembersReducedDues=false;
		var NewMembers=0;
		var RenewedMembers=0;
		var NewMembers2=0;
		var RenewedMembers2=0;
		var MemberDues=0;
		var NewMemberDues=0;
		var TotalMembers=0;
		var TotalFees=0;
		var MembersNoDues=0;
		var PartialDuesMembers=0;
		var PartalDuesCollected=0;
		var AssociateMembers=0;
		var AssociateMemberDuesCollected=0;

		ChangedMeetingFees = document.getElementById("optChangeDuesYes").checked;
		ChargedMembersDifferently = document.getElementById("optNewOldDifferentYes").checked;
		MembersReducedDues = document.getElementById("optNoFullDuesYes").checked;

		NewMembers = Number(document.getElementById("TotalNewMembers").value);
		RenewedMembers = Number(document.getElementById("TotalRenewedMembers").value);
		NewMembers2 = Number(document.getElementById("TotalNewMembersNewFee").value);
		RenewedMembers2 = Number(document.getElementById("TotalRenewedMembersNewFee").value);

		MemberDues = Number(document.getElementById("MemberDues").value);
		NewMemberDues = Number(document.getElementById("NewMemberDues").value);
		MemberDuesRenewal = Number(document.getElementById("MemberDuesRenewal").value);
		NewMemberDuesRenewal = Number(document.getElementById("NewMemberDuesRenewal").value);

		MembersNoDues = Number(document.getElementById("MembersNoDues").value);
		PartialDuesMembers = Number(document.getElementById("TotalPartialDuesMembers").value);
		AssociateMembers = Number(document.getElementById("TotalAssociateMembers").value);

		AssociateMemberDuesCollected = Number(document.getElementById("TotalAssociateMembers").value) * Number(document.getElementById("AssociateMemberDues").value);
		PartalDuesCollected = Number(document.getElementById("PartialDuesMemberDues").value);

		if(ChargedMembersDifferently){
			TotalFees = NewMembers * MemberDues // Normal dues, not changes
				+ RenewedMembers * MemberDuesRenewal
				+ NewMembers2 * NewMemberDues  // Changed dues
				+ RenewedMembers2 * NewMemberDuesRenewal
				+ AssociateMemberDuesCollected + PartalDuesCollected ;  // Associate members or partial dues

		}
		else{
			TotalFees = (NewMembers + RenewedMembers) * MemberDues // Normal dues, not changes
				+ (NewMembers2 + RenewedMembers2) * NewMemberDues  // Changed dues
				+ AssociateMemberDuesCollected + PartalDuesCollected ;  // Associate members or partial dues

		}

//		else if(ChangedMeetingFees && !ChargedMembersDifferently){
//			TotalFees = (NewMembers + RenewedMembers) * MemberDues + (NewMembers2 + RenewedMembers2) * NewMemberDues  + AssociateMemberDuesCollected + PartalDuesCollected;
//		}
//		else if(!ChangedMeetingFees && ChargedMembersDifferently){
//			//KAS TO DO
//			TotalFees = (NewMembers + RenewedMembers) * MemberDues + (NewMembers2 + RenewedMembers2) * NewMemberDues  + AssociateMemberDuesCollected + PartalDuesCollected;
//		}
//		else if(ChangedMeetingFees && ChargedMembersDifferently){
//			//KAS TO DO
//			TotalFees = (NewMembers + RenewedMembers) * MemberDues + (NewMembers2 + RenewedMembers2) * NewMemberDues  + AssociateMemberDuesCollected + PartalDuesCollected;
//		}

		TotalFees = TotalFees.toFixed(2);

		TotalMembers = NewMembers + RenewedMembers + MembersNoDues + AssociateMembers + PartialDuesMembers + NewMembers2 + RenewedMembers2;
		document.getElementById("TotalMembers").value = TotalMembers;


		document.getElementById("TotalDues").value = TotalFees;
		document.getElementById("SumMembershipDuesIncome").value = TotalFees;
		document.getElementById("PartyDuesIncomeReview").value = TotalFees;
		document.getElementById("sumPartyDuesIncomeReview").value = TotalFees;

		ReCalculateSummaryTotal();

	}

	function ChangeChildrensRoomExpenses(){
		var SupplyTotal=0;
		var OtherTotal=0;
		var TotalOtherFees=0;

		var SumChildrensSuppliesExpense=0;
		var SumPaidSittersExpense=0;
		var SumChildrensOtherExpense=0;

		var table=document.getElementById("childrens-room");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			SupplyTotal += value;

			value = Number(row.cells[2].children[0].children[0].children[1].value);
			OtherTotal += value;
		}

		TotalOtherFees = (SupplyTotal + OtherTotal).toFixed(2);
		SupplyTotal	= SupplyTotal.toFixed(2);
		OtherTotal	= OtherTotal.toFixed(2);

		document.getElementById("ChildrensRoomTotal").value = TotalOtherFees;
		document.getElementById("SumChildrensOtherExpense").value = OtherTotal;

		document.getElementById("SumChildrensSuppliesExpense").value = SupplyTotal;

		SumPaidSittersExpense = (Number(document.getElementById("PaidBabySitters").value)).toFixed(2);
		document.getElementById("SumPaidSittersExpense").value = SumPaidSittersExpense;

		var TotalFees = (Number(TotalOtherFees) + Number(SumPaidSittersExpense)).toFixed(2);

		document.getElementById("SumTotalChildrensRoomExpense").value = TotalFees;

		ReCalculateSummaryTotal();
	}

	function AddChildrenExpenseRow(){

		var ExpenseCount = document.getElementById("ChildrensExpenseRowCount").value;

		var table=document.getElementById("childrens-room");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);

		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=ChildrensRoomDesc" + ExpenseCount + " id=ChildrensRoomDesc" + ExpenseCount + "></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"ChildrensRoomSupplies" + ExpenseCount + "\" id=\"ChildrensRoomSupplies" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"ChildrensRoomOther" + ExpenseCount + "\" id=\"ChildrensRoomOther" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\"></div></div>";

		ExpenseCount++;
		document.getElementById('ChildrensExpenseRowCount').value = ExpenseCount;
	}

	function DeleteChildrenExpenseRow(){

		var ExpenseCount = document.getElementById("ChildrensExpenseRowCount").value;
		document.getElementById("childrens-room").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeChildrensRoomExpenses();
		ReCalculateSummaryTotal();

		document.getElementById('ChildrensExpenseRowCount').value = ExpenseCount;
	}

	function ChangeServiceProjectExpenses(){
		var SupplyTotal=0;
		var IncomeTotal=0;
		var CharityTotal=0;
		var M2MTotal=0;

		var TotalFees=0;

		var table=document.getElementById("service-projects");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			IncomeTotal += value;

			value = Number(row.cells[2].children[0].children[0].children[1].value);
			SupplyTotal += value;

			value = Number(row.cells[3].children[0].children[0].children[1].value);
			CharityTotal += value;

			value = Number(row.cells[4].children[0].children[0].children[1].value);
			M2MTotal += value;
		}



		document.getElementById("ServiceProjectIncomeTotal").value = IncomeTotal.toFixed(2);
		document.getElementById("SumServiceProjectIncome").value = IncomeTotal.toFixed(2);

		document.getElementById("ServiceProjectSuppliesTotal").value = SupplyTotal.toFixed(2);
		document.getElementById("ServiceProjectDonationTotal").value = CharityTotal.toFixed(2);
		document.getElementById("ServiceProjectM2MDonationTotal").value = M2MTotal.toFixed(2);
		document.getElementById("SumServiceProjectExpense").value = SupplyTotal.toFixed(2);
		document.getElementById("SumDonationExpense").value = CharityTotal.toFixed(2);
		document.getElementById("SumM2MExpense").value = M2MTotal.toFixed(2);

		TotalFees = (SupplyTotal + CharityTotal + M2MTotal).toFixed(2);
		document.getElementById("SumTotalServiceProjectExpense").value = TotalFees;

		ReCalculateSummaryTotal();
	}

	function AddServiceProjectRow(){

		var ExpenseCount = document.getElementById("ServiceProjectRowCount").value;

		var table=document.getElementById("service-projects");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=ServiceProjectDesc" + ExpenseCount + " id=ServiceProjectDesc" + ExpenseCount + "></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"ServiceProjectIncome" + ExpenseCount + "\" id=\"ServiceProjectIncome" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"ServiceProjectSupplies" + ExpenseCount + "\" id=\"ServiceProjectSupplies" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\"></div></div>";
		cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"ServiceProjectDonatedCharity" + ExpenseCount + "\" id=\"ServiceProjectDonatedCharity" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\"></div></div>";
		cell5.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"ServiceProjectDonatedM2M" + ExpenseCount + "\" id=\"ServiceProjectDonatedM2M" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\"></div></div>";

		ExpenseCount++;
		document.getElementById('ServiceProjectRowCount').value = ExpenseCount;
	}

	function DeleteServiceProjectRow(){

		var ExpenseCount = document.getElementById("ServiceProjectRowCount").value;
		document.getElementById("service-projects").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeServiceProjectExpenses();

		document.getElementById('ServiceProjectExpenseCount').value = ExpenseCount;
	}

	function ChangePartyExpenses(){
		var IncomeTotal=0;
		var ExpenseTotal=0;
		var PartyPercent=0;
		var table=document.getElementById("party-expenses");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			IncomeTotal += value;

			value = Number(row.cells[2].children[0].children[0].children[1].value);
			ExpenseTotal += value;
		}

		document.getElementById("PartyIncomeTotal").value = IncomeTotal.toFixed(2);
		document.getElementById("PartyExpenseTotal").value = ExpenseTotal.toFixed(2);
		document.getElementById("SumPartyIncome").value = IncomeTotal.toFixed(2);
		document.getElementById("SumPartyExpense").value = ExpenseTotal.toFixed(2);

		document.getElementById("PartyIncomeReview").value = IncomeTotal.toFixed(2);
		document.getElementById("PartyExpenseReview").value = ExpenseTotal.toFixed(2);

		document.getElementById("sumPartyIncomeReview").value = IncomeTotal.toFixed(2);
		document.getElementById("sumPartyExpenseReview").value = ExpenseTotal.toFixed(2);

		//Calculate Party Percent
		var DuesIncome = Number(document.getElementById("PartyDuesIncomeReview").value);

		if(ExpenseTotal>0)
			PartyPercent = (((ExpenseTotal-IncomeTotal)/DuesIncome)*100).toFixed(2);
		else
			PartyPercent=0;

		document.getElementById("PartyExpensePercent").value = PartyPercent;
		document.getElementById("sumPartyExpensePercent").value = PartyPercent;

		if(PartyPercent>15 && PartyPercent<20){
			document.getElementById("PartyExpensePercent").style.backgroundColor="yellow"
			document.getElementById("sumPartyExpensePercent").style.backgroundColor="yellow"
		}
		else if(PartyPercent>=20){
			document.getElementById("PartyExpensePercent").style.backgroundColor="red"
			document.getElementById("sumPartyExpensePercent").style.backgroundColor="red"
		}

		ReCalculateSummaryTotal();

	}


	function AddPartyExpenseRow(){

		var ExpenseCount = document.getElementById("PartyExpenseRowCount").value;

		var table=document.getElementById("party-expenses");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=PartyDesc" + ExpenseCount + " id=PartyDesc" + ExpenseCount + "></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"PartyIncome" + ExpenseCount + "\" id=\"PartyIncome" + ExpenseCount + "\"  oninput=\"ChangePartyExpenses()\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"PartyExpenses" + ExpenseCount + "\" id=\"PartyExpenses" + ExpenseCount + "\"  oninput=\"ChangePartyExpenses()\"></div></div>";

		ExpenseCount++;
		document.getElementById('PartyExpenseRowCount').value = ExpenseCount;
	}

	function DeletePartyExpenseRow(){

		var ExpenseCount = document.getElementById("PartyExpenseRowCount").value;
		document.getElementById("party-expenses").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangePartyExpenses();

		document.getElementById('PartyExpenseRowCount').value = ExpenseCount;
	}

	function ChangeOfficeExpenses(){
		var ExpenseTotal=0;
		var SumPrintingExpense=0;
		var SumPostageExpense=0;
		var SumPinsExpense=0;

		var table=document.getElementById("office-expenses");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			ExpenseTotal += value;

		}
		document.getElementById("OfficeExpenseTotal").value = ExpenseTotal.toFixed(2);

		SumPrintingExpense=Number(document.getElementById("PrintingCosts").value);
		SumPostageExpense=Number(document.getElementById("PostageCosts").value);
		SumPinsExpense=Number(document.getElementById("MembershipPins").value);


		document.getElementById("SumPrintingExpense").value = SumPrintingExpense.toFixed(2);
		document.getElementById("SumPostageExpense").value = SumPostageExpense.toFixed(2);
		document.getElementById("SumPinsExpense").value = SumPinsExpense.toFixed(2);
		document.getElementById("SumOtherOperatingExpense").value = ExpenseTotal.toFixed(2);

		ExpenseTotal = ExpenseTotal + SumPrintingExpense + SumPostageExpense + SumPinsExpense
		document.getElementById("SumOperatingExpense").value = ExpenseTotal.toFixed(2);

		ReCalculateSummaryTotal();

	}

	function AddOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OfficeExpenseRowCount").value;

		var table=document.getElementById("office-expenses");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);

		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=OfficeDesc" + ExpenseCount + " id=OfficeDesc" + ExpenseCount + "></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"OfficeExpenses" + ExpenseCount + "\" id=\"OfficeExpenses" + ExpenseCount + "\"  oninput=\"ChangeOfficeExpenses()\"></div></div>";

		ExpenseCount++;
		document.getElementById('OfficeExpenseRowCount').value = ExpenseCount;
	}

	function DeleteOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OfficeExpenseRowCount").value;
		document.getElementById("office-expenses").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeOfficeExpenses();

		document.getElementById('OfficeExpenseRowCount').value = ExpenseCount;
	}


	function ChangeDonationAmount(){
		var IncomeTotal=0;

		var table=document.getElementById("donation-income");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[3].children[0].children[0].children[1].value);
			IncomeTotal += value;
		}

		document.getElementById("DonationTotal").value = IncomeTotal.toFixed(2);
		document.getElementById("SumMonetaryDonationIncome").value = IncomeTotal.toFixed(2);
	}

	function AddMonDonationRow(){

		var ExpenseCount = document.getElementById("MonDonationRowCount").value;

		var table=document.getElementById("donation-income");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"DonationDesc" + ExpenseCount + "\" id=\"DonationDesc" + ExpenseCount + "\"></div>";
		cell2.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"DonorInfo" + ExpenseCount + "\" id=\"DonorInfo" + ExpenseCount + "\"></div>";
		cell3.innerHTML = "<div class=\"form-group\"><input type=\"date\" class=\"form-control\" name=\"MonDonationDate" + ExpenseCount + "\" id=\"MonDonationDate" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\"></div>";
		cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"DonationAmount" + ExpenseCount + "\" id=\"DonationAmount" + ExpenseCount + "\" oninput=\"ChangeDonationAmount()\"></div></div>";

		ExpenseCount++;
		document.getElementById('MonDonationRowCount').value = ExpenseCount;
	}

	function DeleteMonDonationRow(){

		var ExpenseCount = document.getElementById("MonDonationRowCount").value;
		document.getElementById("donation-income").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeDonationAmount();

		document.getElementById('MonDonationRowCount').value = ExpenseCount;
	}

	function AddNonMonDonationRow(){

		var ExpenseCount = document.getElementById("NonMonDonationRowCount").value;

		var table=document.getElementById("donation-goods");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=NonMonDonationDesc" + ExpenseCount + " id=NonMonDonationDesc" + ExpenseCount + "></div>";
		cell2.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"NonMonDonorInfo" + ExpenseCount + "\" id=\"NonMonDonorInfo" + ExpenseCount + "\"></div>";
		cell3.innerHTML = "<div class=\"form-group\"><input type=\"date\" class=\"form-control\" name=\"NonMonDonationDate" + ExpenseCount + "\" id=\"NonMonDonationDate" + ExpenseCount + "\"></div>";

		ExpenseCount++;
		document.getElementById('NonMonDonationRowCount').value = ExpenseCount;
	}

	function DeleteNonMonDonationRow(){

		var ExpenseCount = document.getElementById("NonMonDonationRowCount").value;
		document.getElementById("donation-goods").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this

		document.getElementById('NonMonDonationRowCount').value = ExpenseCount;
	}

	function ChangeOtherOfficeExpenses(){
		var ExpenseTotal=0;
		var IncomeTotal=0;

		var table=document.getElementById("other-office-expenses");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			IncomeTotal += value;

			value = Number(row.cells[2].children[0].children[0].children[1].value);
			ExpenseTotal += value;

		}

		document.getElementById("OtherOfficeExpenseTotal").value = ExpenseTotal.toFixed(2);
		document.getElementById("OtherOfficeIncomeTotal").value = IncomeTotal.toFixed(2);

		document.getElementById("SumOtherIncome").value = IncomeTotal.toFixed(2);
		document.getElementById("SumOtherExpense").value = ExpenseTotal.toFixed(2);

		ReCalculateSummaryTotal();
	}

	function ChangeInternationalEventExpense(){

		var ExpenseTotal=0;
		var IncomeTotal=0;

		var table=document.getElementById("international_events");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			IncomeTotal += value;

			value = Number(row.cells[2].children[0].children[0].children[1].value);
			ExpenseTotal += value;

		}

		document.getElementById("InternationalEventIncomeTotal").value = IncomeTotal.toFixed(2);
		document.getElementById("InternationalEventExpenseTotal").value = ExpenseTotal.toFixed(2);

		document.getElementById("SumInternationalEventIncome").value = IncomeTotal.toFixed(2);
		document.getElementById("SumInternationalEventExpense").value = ExpenseTotal.toFixed(2);

		ReCalculateSummaryTotal();

	}

	function DeleteInternationalEventRow(){

		var ExpenseCount = document.getElementById("InternationalEventRowCount").value;
		document.getElementById("international_events").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeDonationAmount();

		document.getElementById('InternationalEventExpenseCount').value = ExpenseCount;
	}

	function AddInternationalEventRow(){

		var ExpenseCount = document.getElementById("InternationalEventRowCount").value;

		var table=document.getElementById("international_events");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"InternationalEventDesc" + ExpenseCount + "\" id=\"InternationalEventDesc" + ExpenseCount + "\"></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"InternationalEventIncome" + ExpenseCount + "\" id=\"InternationalEventIncome" + ExpenseCount + "\" oninput=\"ChangeInternationalEventExpense()\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"InternationalEventExpense" + ExpenseCount + "\" id=\"InternationalEventExpense" + ExpenseCount + "\" oninput=\"ChangeInternationalEventExpense()\"></div></div>";

		ExpenseCount++;
		document.getElementById('InternationalEventRowCount').value = ExpenseCount;
	}

	function ChangeReRegistrationExpense(){

		var ReRegistrationFee=0;

		ReRegistrationFee = Number(document.getElementById("AnnualRegistrationFee").value);

		document.getElementById("SumChapterReRegistrationExpense").value = ReRegistrationFee.toFixed(2);

		ReCalculateSummaryTotal();

	}

	function ReCalculateSummaryTotal(){

		var SumOtherIncome=0;

		var SumMeetingRoomExpense=0;
		var SumTotalChildrensRoomExpense=0;
		var ServiceIncomeTotal=0;
		var ServiceExpenseTotal=0;

		var SumOtherExpense=0;
		var SumOperatingExpense=0;

		var SumTotalExpense=0
		var SumTotalIncome=0

		var SumPartyExpense=0;
		var SumPartyIncome=0;

		var SumInternationalEventExpense=0;
		var SumChapterReRegistrationExpense=0;

		var TreasuryBalance=0;
		var TreasuryBalanceNow=0;

		SumMeetingRoomExpense = Number(document.getElementById("SumMeetingRoomExpense").value);
		SumMembershipDuesIncome = Number(document.getElementById("SumMembershipDuesIncome").value);

		SumTotalChildrensRoomExpense=Number(document.getElementById("SumTotalChildrensRoomExpense").value);

		ServiceIncomeTotal = Number(document.getElementById("SumServiceProjectIncome").value);
		ServiceExpenseTotal = Number(document.getElementById("SumTotalServiceProjectExpense").value);

		SumPartyIncome = Number(document.getElementById("SumPartyIncome").value);
		SumPartyExpense = Number(document.getElementById("SumPartyExpense").value);

		SumOtherIncome = Number(document.getElementById("SumOtherIncome").value);
		SumOtherExpense = Number(document.getElementById("SumOtherExpense").value);

		SumOperatingExpense = Number(document.getElementById("SumOperatingExpense").value);

		SumInternationalEventExpense = Number(document.getElementById("SumInternationalEventExpense").value);
		SumInternationalEventIncome = Number(document.getElementById("SumInternationalEventIncome").value);

		SumMonetaryDonationIncome = Number(document.getElementById("SumMonetaryDonationIncome").value);
		SumChapterReRegistrationExpense = Number(document.getElementById("SumChapterReRegistrationExpense").value);


		SumTotalExpense = SumTotalChildrensRoomExpense + SumMeetingRoomExpense + ServiceExpenseTotal + SumOtherExpense + SumPartyExpense + SumOperatingExpense + SumInternationalEventExpense + SumChapterReRegistrationExpense;
		SumTotalIncome = ServiceIncomeTotal + SumOtherIncome + SumPartyIncome + SumMembershipDuesIncome + SumInternationalEventIncome + SumMonetaryDonationIncome ;

		TreasuryBalance = Number(document.getElementById("SumAmountReservedFromPreviousYear").value);

		document.getElementById("SumTotalExpense").value = SumTotalExpense.toFixed(2);
		document.getElementById("SumTotalIncome").value = SumTotalIncome.toFixed(2);

		document.getElementById("checkTotalIncomeCalc").value = SumTotalIncome.toFixed(2);
		document.getElementById("sumcheckTotalIncomeCalc").value = SumTotalIncome.toFixed(2);

		if(SumTotalIncome>=50000){
			document.getElementById("checkTotalIncomeCalc").style.backgroundColor="red";
			document.getElementById("sumcheckTotalIncomeCalc").style.backgroundColor="red";
		}

		TreasuryBalanceNow = TreasuryBalance - SumTotalExpense + SumTotalIncome;

		document.getElementById("TreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);
		document.getElementById("SumTreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);
		document.getElementById("checkTreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);

		ChangeBankRec();
	}

	function AddOtherOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OtherOfficeExpenseRowCount").value;

		var table=document.getElementById("other-office-expenses");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(1);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"OtherOfficeDesc" + ExpenseCount + "\" id=\"OtherOfficeDesc" + ExpenseCount + "\"></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"OtherOfficeIncome" + ExpenseCount + "\"  id=\"OtherOfficeIncome" + ExpenseCount + "\"  oninput=\"ChangeOtherOfficeExpenses()\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"OtherOfficeExpenses" + ExpenseCount + "\" id=\"OtherOfficeExpenses" + ExpenseCount + "\"  oninput=\"ChangeOtherOfficeExpenses()\"></div></div>";

		ExpenseCount++;
		document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;
	}

	function DeleteOtherOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OtherOfficeExpenseRowCount").value;

		if(ExpenseCount>1){
			document.getElementById("other-office-expenses").deleteRow(ExpenseCount);

			ExpenseCount--; //We removed a row so lower this
			ChangeOtherOfficeExpenses();

			document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;

		}

	}

	function TreasuryBalanceChange(){
		var TreasuryBalance = Number(document.getElementById("AmountReservedFromLastYear").value);
		document.getElementById("SumAmountReservedFromPreviousYear").value = TreasuryBalance.toFixed(2);

		ReCalculateSummaryTotal();
	}

	function ChangeBankRec(){
		var PaymentTotal=0;
		var DepositTotal=0;
		var PettyCash=0;

		var table=document.getElementById("bank-rec");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[3].children[0].children[0].children[1].value);
			PaymentTotal += value;

			value = Number(row.cells[4].children[0].children[0].children[1].value);
			DepositTotal += value;
		}

		//PettyCash = Number(document.getElementById("PettyCash").value);
		var BankBalanceNow = Number(document.getElementById("BankBalanceNow").value);

		TotalFees = BankBalanceNow - PaymentTotal + DepositTotal;
		document.getElementById("ReconciledBankBalance").value = TotalFees.toFixed(2);
	}

	function AddBankRecRow(){

		var ExpenseCount = document.getElementById("BankRecRowCount").value;

		var table=document.getElementById("bank-rec");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"date\" class=\"form-control\" name=\"BankRecDate" + ExpenseCount + "\" id=\"BankRecDate" + ExpenseCount + "\"></div>";
		cell2.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"BankRecCheckNo" + ExpenseCount + "\" id=\"BankRecCheckNo" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\"></div>";
		cell3.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"BankRecDesc" + ExpenseCount + "\" id=\"BankRecDesc" + ExpenseCount + "\"></div>";
		cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"BankRecPaymentAmount" + ExpenseCount + "\" id=\"BankRecPaymentAmount" + ExpenseCount + "\" oninput=\"ChangeBankRec()\"></div></div>";
		cell5.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control\" name=\"BankRecDepositAmount" + ExpenseCount + "\" id=\"BankRecDepositAmount" + ExpenseCount + "\" oninput=\"ChangeBankRec()\"></div></div>";

		ExpenseCount++;
		document.getElementById('BankRecRowCount').value = ExpenseCount;
	}

	function DeleteBankRecRow(){

		var ExpenseCount = document.getElementById("BankRecExpenseCount").value;
		document.getElementById("bank-rec").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeBankRec();

		document.getElementById('BankRecRowCount').value = ExpenseCount;
	}

	function ToggleReceiveCompensationExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("ReceiveCompensationYes").checked){
			document.getElementById("ReceiveCompensationExplanation").required=true;
			document.getElementById("divReceiveCompensationExplanation").style.display = 'block';
		}
		else{
			document.getElementById("ReceiveCompensationExplanation").required=false;
			document.getElementById("divReceiveCompensationExplanation").style.display = 'none';
		}
	}

	function ToggleFinancialBenefitExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("FinancialBenefitYes").checked){
			document.getElementById("FinancialBenefitExplanation").required=true;
			document.getElementById("divFinancialBenefitExplanation").style.display = 'block';
		}
		else{
			document.getElementById("FinancialBenefitExplanation").required=false;
			document.getElementById("divFinancialBenefitExplanation").style.display = 'none';
		}

	}

	function ToggleInfluencePoliticalExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("InfluencePoliticalYes").checked){
			document.getElementById("InfluencePoliticalExplanation").required=true;
			document.getElementById("divInfluencePoliticalExplanation").style.display = 'block';
		}
		else{
			document.getElementById("InfluencePoliticalExplanation").required=false;
			document.getElementById("divInfluencePoliticalExplanation").style.display = 'none';
		}
	}

	function ToggleVoteAllActivitiesExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("VoteAllActivitiesNo").checked){
			document.getElementById("VoteAllActivitiesExplanation").required=true;
			document.getElementById("divVoteAllActivitiesExplanation").style.display = 'block';
		}
		else{
			document.getElementById("VoteAllActivitiesExplanation").required=false;
			document.getElementById("divVoteAllActivitiesExplanation").style.display = 'none';
		}
	}

	function ToggleBoughtPinsExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("BoughtPinsNo").checked){
			document.getElementById("BoughtPinsExplanation").required=true;
			document.getElementById("divBoughtPinsExplanation").style.display = 'block';
		}
		else{
			document.getElementById("BoughtPinsExplanation").required=false;
			document.getElementById("divBoughtPinsExplanation").style.display = 'none';
		}
	}

	function ToggleBoughtMerchExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("BoughtMerchNo").checked){
			document.getElementById("BoughtMerchExplanation").required=true;
			document.getElementById("divBoughtMerchExplanation").style.display = 'block';
		}
		else{
			document.getElementById("BoughtMerchExplanation").required=false;
			document.getElementById("divBoughtMerchExplanation").style.display = 'none';
		}
	}

	function ToggleOfferedMerchExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("OfferedMerchNo").checked){
			document.getElementById("OfferedMerchExplanation").required=true;
			document.getElementById("divOfferedMerchExplanation").style.display = 'block';
		}
		else{
			document.getElementById("OfferedMerchExplanation").required=false;
			document.getElementById("divOfferedMerchExplanation").style.display = 'none';
		}
	}

	function ToggleByLawsAvailableExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("ByLawsAvailableNo").checked){
			document.getElementById("ByLawsAvailableExplanation").required=true;
			document.getElementById("divByLawsAvailableExplanation").style.display = 'block';
		}
		else{
			document.getElementById("ByLawsAvailableExplanation").required=false;
			document.getElementById("divByLawsAvailableExplanation").style.display = 'none';
		}
	}

	function ToggleChildOutingsExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("ChildOutingsNo").checked){
			document.getElementById("ChildOutingsExplanation").required=true;
			document.getElementById("divChildOutingsExplanation").style.display = 'block';
		}
		else{
			document.getElementById("ChildOutingsExplanation").required=false;
			document.getElementById("divChildOutingsExplanation").style.display = 'none';
		}
	}

	function ToggleMotherOutingsExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("MotherOutingsNo").checked){
			document.getElementById("MotherOutingsExplanation").required=true;
			document.getElementById("divMotherOutingsExplanation").style.display = 'block';
		}
		else{
			document.getElementById("MotherOutingsExplanation").required=false;
			document.getElementById("divMotherOutingsExplanation").style.display = 'none';
		}
	}

	function ToggleMeetingSpeakersExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("MeetingSpeakersNo").checked){
			document.getElementById("MeetingSpeakersExplanation").required=true;
			document.getElementById("divMeetingSpeakersExplanation").style.display = 'block';
		}
		else{
			document.getElementById("MeetingSpeakersExplanation").required=false;
			document.getElementById("divMeetingSpeakersExplanation").style.display = 'none';
		}
	}

	function ToggleActivityOtherExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("ActivityOther").checked){
			document.getElementById("ActivityOtherExplanation").required=true;
			document.getElementById("divActivityOtherExplanation").style.display = 'block';
		}
		else{
			document.getElementById("ActivityOtherExplanation").required=false;
			document.getElementById("divActivityOtherExplanation").style.display = 'none';
		}
	}

	function ToggleContributionsNotRegNPExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("ContributionsNotRegNPYes").checked){
			document.getElementById("ContributionsNotRegNPExplanation").required=true;
			document.getElementById("divContributionsNotRegNPExplanation").style.display = 'block';
		}
		else{
			document.getElementById("ContributionsNotRegNPExplanation").required=false;
			document.getElementById("divContributionsNotRegNPExplanation").style.display = 'none';
		}
	}

	function TogglePerformServiceProjectExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("PerformServiceProjectNo").checked){
			document.getElementById("PerformServiceProjectExplanation").required=true;
			document.getElementById("divPerformServiceProjectExplanation").style.display = 'block';
		}
		else{
			document.getElementById("PerformServiceProjectExplanation").required=false;
			document.getElementById("divPerformServiceProjectExplanation").style.display = 'none';
		}
	}

	function ToggleFileIRSExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("FileIRSNo").checked){
			document.getElementById("FileIRSExplanation").required=true;
			document.getElementById("divFileIRSExplanation").style.display = 'block';
		}
		else{
			document.getElementById("FileIRSExplanation").required=false;
			document.getElementById("divFileIRSExplanation").style.display = 'none';
		}
	}

	function ToggleBankStatementIncludedExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("BankStatementIncludedNo").checked){
			document.getElementById("BankStatementIncludedExplanation").required=true;
			document.getElementById("WheresTheMoney").required=true;
			document.getElementById("divBankStatementIncludedExplanation").style.display = 'block';
		}
		else{
			document.getElementById("BankStatementIncludedExplanation").required=false;
			document.getElementById("WheresTheMoney").required=false;
			document.getElementById("divBankStatementIncludedExplanation").style.display = 'none';
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

	function AddCoordAwardNomination(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("CoordAward1Panel").style.display === 'none'){
			document.getElementById("CoordAward1Panel").style.display = 'block';
			document.getElementById("btnDeleteCoordAwardNomination").disabled = false;
			document.getElementById("checkNominationTypeCoord1").required = true;
		}
		else if (document.getElementById("CoordAward2Panel").style.display === 'none'){
			document.getElementById("CoordAward1Panel").style.display = 'block';
			document.getElementById("btnAddCoordAwardNomination").disabled = true;
			document.getElementById("checkNominationTypeCoord2").required = true;
		}

		document.getElementById("TotalCoordAwardNominations").value = Number(document.getElementById("TotalCoordAwardNominations").value) + 1;
	}

	function DeleteCoordAwardNomination(){
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

	function ShowOutstandingCriteria(AwardNumber){

		var NominationElementName="";
		var CriteriaElementName="";

		NominationElementName = "NominationType" + AwardNumber;
		CriteriaElementName = "OutstandingCriteria" + AwardNumber;

		if (document.getElementById(NominationElementName).value == 5 || document.getElementById(NominationElementName).value == 6){
			document.getElementById(CriteriaElementName).style.display = 'block';
		}
		else{
			document.getElementById(CriteriaElementName).style.display = 'none';
		}
	}

	function ShowOutstandingCriteriaCoord(AwardNumber){

		var NominationElementName="";
		var CriteriaElementName="";

		NominationElementName = "checkNominationTypeCoord" + AwardNumber;
		CriteriaElementName = "checkOutstandingCriteriaCoord" + AwardNumber;

		if (document.getElementById(NominationElementName).value == 5 || document.getElementById(NominationElementName).value == 6){
			document.getElementById(CriteriaElementName).style.display = 'block';
		}
		else{
			document.getElementById(CriteriaElementName).style.display = 'none';
		}
	}

	// Initialize tooltip component
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	})

	function UpdateCalculatedValues(){
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
	}


	function RemoveRequired(){

		var x = document.forms[0];
		var i;
		for (i = 0; i < x.length; i++) {
			x.elements[i].required = false;
		}
	}

	function SetReadOnly(){

		var x = document.forms[0];
		var i;
		for (i = 0; i < x.length; i++) {
			if(x.elements[i].type!="button"){
				x.elements[i].readOnly = true;
				x.elements[i].disabled = true;
			}
		}
	}

	function LoadSteps(){
		UpdateCalculatedValues();
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

		if(document.getElementById("NominationType1").value==5 || document.getElementById("NominationType1").value==6){
			ShowOutstandingCriteria(1);
		}
		if(document.getElementById("NominationType2").value==5 || document.getElementById("NominationType2").value==6){
			ShowOutstandingCriteria(2);
		}
		if(document.getElementById("NominationType3").value==5 || document.getElementById("NominationType3").value==6){
			ShowOutstandingCriteria(3);
		}
		if(document.getElementById("NominationType4").value==5 || document.getElementById("NominationType4").value==6){
			ShowOutstandingCriteria(4);
		}
		if(document.getElementById("NominationType5").value==5 || document.getElementById("NominationType5").value==6){
			ShowOutstandingCriteria(5);
		}

	}

	function CheckReviewAnswers(){

		if(!document.getElementById("sumcheckRosterAttachedNo").checked && !document.getElementById("sumcheckRosterAttachedYes").checked){
			alert("You have not verified a roster was attached.");
			document.getElementById("sumcheckRosterAttachedNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckServiceProjectNo").checked && !document.getElementById("sumcheckServiceProjectYes").checked){
			alert("You have not verified a service project was completed.");
			document.getElementById("sumcheckServiceProjectNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckM2MDonationNo").checked && !document.getElementById("sumcheckM2MDonationYes").checked){
			alert("You have not indicated if the chapter donated to the M2M Fund.");
			document.getElementById("sumcheckM2MDonationNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckMCGeneralFundNo").checked && !document.getElementById("sumcheckMCGeneralFundYes").checked){
			alert("You have not indicated if the chapter made a sustaining chapter donation.");
			document.getElementById("sumcheckMCGeneralFundNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckRenewalSeemsRightNo").checked && !document.getElementById("sumcheckRenewalSeemsRightYes").checked){
			alert("You have not indicated if the renewal numbers 'seem right'.");
			document.getElementById("sumcheckRenewalSeemsRightNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckAttendedTrainingNo").checked && !document.getElementById("sumcheckAttendedTrainingYes").checked){
			alert("You have not indicated if the chapter attended training.");
			document.getElementById("sumcheckAttendedTrainingNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckAttendedLuncheonNo").checked && !document.getElementById("sumcheckAttendedLuncheonYes").checked){
			alert("You have not indicated if the chapter attended the luncheon.");
			document.getElementById("sumcheckAttendedLuncheonNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckBankStatementIncludedNo").checked && !document.getElementById("sumcheckBankStatementIncludedYes").checked && !document.getElementById("sumBankInfo").style.display=="none"){
			alert("You have not indicated if the chapter included a bank statement for the end of the year.");
			document.getElementById("sumcheckBankStatementIncludedNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckBankStatementMatchesNo").checked && !document.getElementById("sumcheckBankStatementMatchesYes").checked && !document.getElementById("sumBankInfo").style.display=="none"){
			alert("You have not indicated if the chapter's bank balance matches stated bank balance.");
			document.getElementById("sumcheckBankStatementMatchesNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckPurchasedPinsNo").checked && !document.getElementById("sumcheckPurchasedPinsYes").checked){
			alert("You have not indicated if the chapter purchased pins.");
			document.getElementById("sumcheckPurchasedPinsNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckPurchasedMCMerchNo").checked && !document.getElementById("sumcheckPurchasedMCMerchYes").checked){
			alert("You have not indicated if the chapter purchased other MOMS Club merchandise.");
			document.getElementById("sumcheckPurchasedMCMerchNo").focus();
			return false;
		}
		if(!document.getElementById("sumcheckOfferedMerchNo").checked && !document.getElementById("sumcheckOfferedMerchYes").checked){
			alert("You have not indicated if the chapter Offered MOMS Club merchandise.");
			document.getElementById("sumcheckOfferedMerchNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckBylawsMadeAvailableNo").checked && !document.getElementById("sumcheckBylawsMadeAvailableYes").checked){
			alert("You have not indicated if the chapter made the by-laws available to members.");
			document.getElementById("sumcheckBylawsMadeAvailableNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckCurrent990NAttachedNo").checked && !document.getElementById("sumcheckCurrent990NAttachedYes").checked){
			alert("You have not indicated if the chapter attached their 990N filing confirmation.");
			document.getElementById("sumcheckCurrent990NAttachedNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckTotalIncomeNo").checked && !document.getElementById("sumcheckTotalIncomeYes").checked){
			alert("You have not indicated if the chapter's total income is less than $50,000.");
			document.getElementById("sumcheckTotalIncomeNo").focus();
			return false;
		}

		if(!document.getElementById("sumcheckSisteredAnotherChapterNo").checked && !document.getElementById("sumcheckSisteredAnotherChapterYes").checked){
			alert("You have not indicated if the chapter sistered another chapter.");
			document.getElementById("sumcheckSisteredAnotherChapterNo").focus();
			return false;
		}

		if(document.getElementById("post_balance").val === null || document.getElementById("post_balance").val === ''){
			alert("You have not entered the Ending Balance");
			document.getElementById("post_balance").focus();
			return false;
		}

		if(document.getElementById("AssignedReviewer").val === null || document.getElementById("AssignedReviewer").val === ''){
			alert("You have not select a Reviewer");
			document.getElementById("AssignedReviewer").focus();
			return false;
		}

		return true;

	}

</script>
@endsection

