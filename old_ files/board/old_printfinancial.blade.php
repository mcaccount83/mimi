@extends('layouts.chapter_theme')

@section('content')
<center><h4 style="color: red;" class="moms-c">MOMS Club of {{ $chapterDetails[0]->chapter_name }}, {{$chapterDetails[0]->state}} Financial Report</h4>
<h4 style="color: red;">Print Preview</h4>
<h4 style="color: red;">To Save Report, Print to PDF</h4></center>
<br>
<div class="box-body text-center">
    	<button type="button" class="btn btn-info btn-fill" onClick="fullPrintDiv('{{ $chapterDetails[0]->id }}')">Print</button>
	<a href="{{ route('board.showfinancial',$chapterDetails[0]->id) }}" class="btn btn-info btn-fill">Cancel</a>

</div>
<br><hr><br><br>



<!-- Print Functionality -->
<div class="container" id="full-print-div">
	<center><h2 class="moms-c">MOMS Club of {{ $chapterDetails[0]->chapter_name }}, {{$chapterDetails[0]->state}} Financial Report</h2>
	                           <h2 class="moms-c"> <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report </h2></center>

    <div class="row">
		<div class="col-md-12">
			<div class="">
				<!------Start Step 1 ------>
					 <h4>Section 1 - Chapter Dues</h4>
						<section>

						<div class="form-row form-group">
							<div class="form-holder col-sm-12 float-left">
								<div class="col-md-12">
									<label for="">
										Did your chapter change your dues this year?
									</label>
								</div>
								<div class="col-md-6 float-left">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
										<input type="radio" class="form-check-input" name="optChangeDues" id="optChangeDuesNo" value="no" onchange="ChapterDuesQuestionsChange()" <?php if (!empty($financial_report_array)) {if ($financial_report_array['changed_dues'] == false || $financial_report_array['changed_dues'] != true) echo "checked";} ?> disabled>
										<span class="form-check-sign"></span>
											No
										</label>
										<label class="form-check-label">
											<input type="radio" class="form-check-input" name="optChangeDues" id="optChangeDuesYes" value="yes" onchange="ChapterDuesQuestionsChange()" <?php if (!empty($financial_report_array)) {if ($financial_report_array['changed_dues'] == true) echo "checked";} ?> disabled>
											<span class="form-check-sign"></span>
											Yes
										</label>
									</div>
								</div>
							</div>
							<div class="form-holder col-sm-12 float-left">
								<div class="col-md-12">
									<label for="">
										Did your chapter charge different amounts for new and returning members?
									</label>
								</div>
								<div class="col-md-6 float-left">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input disabled type="radio" class="form-check-input" name="optNewOldDifferent" id="optNewOldDifferentNo" value="no" onchange="ChapterDuesQuestionsChange()" <?php if (!empty($financial_report_array)) {if ($financial_report_array['different_dues'] == false || $financial_report_array['different_dues'] != true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
											No
										</label>

										<label class="form-check-label">
											<input disabled type="radio" class="form-check-input" name="optNewOldDifferent" id="optNewOldDifferentYes" value="yes" onchange="ChapterDuesQuestionsChange()" <?php if (!empty($financial_report_array)) {if ($financial_report_array['different_dues'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
											Yes
										</label>
									</div>
								</div>
							</div>

							<div class="form-holder">
								<div class="col-md-12">
									<label for="">
										Did your chapter have any members who didn't pay full dues? <br>
										<span>(Associate members or members whose dues were reduced or waived)</span>
									</label>
								</div>
								<div class="col-md-6 float-left">
									<div class="form-check form-check-radio">
										<label class="form-check-label">
											<input disabled type="radio" class="form-check-input" name="optNoFullDues" id="optNoFullDuesNo" value="no" onchange="ChapterDuesQuestionsChange()" <?php if (!empty($financial_report_array)) {if ($financial_report_array['not_all_full_dues'] == false || $financial_report_array['not_all_full_dues'] != true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
											No
										</label>
										<label class="form-check-label">
											<input disabled type="radio" class="form-check-input" name="optNoFullDues" id="optNoFullDuesYes" value="yes" onchange="ChapterDuesQuestionsChange()" <?php if (!empty($financial_report_array)) {if ($financial_report_array['not_all_full_dues'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
											Yes
										</label>
									</div>
								</div>
							</div>
							<hr>
						</div>
						<div class="form-row">
							<div class="col-md-12">
								<p>What dues did your chapter charge its members this year?  Count all members who paid full dues, even if they are not still members.</p>
							</div>
							<div class="col-md-6 float-left">
								<div class="form-group">
									<label for="TotalNewMembers" id="lblTotalNewMembers">Total Paid New Members</label>
									<div class="input-group">
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalNewMembers" id="TotalNewMembers" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_new_members'] ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6 float-left">
								<div class="form-group">
								<label for="TotalRenewedMembers" id="lblTotalRenewedMembers">
									Total Paid Renewed Members
								</label>
								<div class="input-group">
								<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalRenewedMembers" id="TotalRenewedMembers" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_renewed_members'] ?>">
								</div>
								</div>
							</div>
							<div class="col-md-12" id="ifChangeDues" style="display:none">
								<div class="col-md-6 float-left nopadding-l">
									<div class="form-group">
										<label for="TotalNewMembersNewFee">Total Paid New Members New Fee</label>
										<div class="input-group">
										<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalNewMembersNewFee" id="TotalNewMembersNewFee" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_new_members_changed_dues'] ?>">
										</div>
									</div>
								</div>
								<div class="col-md-6 float-left nopadding">
									<div class="form-group">
									<label for="TotalRenewedMembersNewFee">Total Paid Renewed Members New Fee</label>
									<div class="input-group">
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalRenewedMembersNewFee" id="TotalRenewedMembersNewFee" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_renewed_members_changed_dues'] ?>">
									</div>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="MemberDues" id="lblMemberDues">
										Dues per Member
									</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDues" oninput="ChangeMemberCount()" id="MemberDues" step="0.01" min=0 aria-describedby="sizing-addon1" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member'] ?>">
									</div>
								</div>
							</div>
							<div class="col-md-6" id="ifChangedDues1" style="visibility:hidden">
								<div class="form-group">
									<label for="NewMemberDues" id="lblNewMemberDues">
										New Dues per Member
									</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="NewMemberDues" oninput="ChangeMemberCount()" id="NewMemberDues" step="0.01" min=0 aria-describedby="sizing-addon1" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_new_changed'] ?>">
									</div>
								</div>
							</div>


							<div class="col-md-12" id="ifChangedDuesDifferentPerMemberType" style="display:none">
								<div class="col-md-6 float-left nopadding-l">
									<div class="form-group">
										<label for="MemberDuesRenewal">Dues per Renewed Member</label>
										<div class="input-group">
									<span class="input-group-addon">$</span>
										<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDuesRenewal"  oninput="ChangeMemberCount()" id="MemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal'] ?>">
										</div>
									</div>
								</div>
								<div class="col-md-6 float-left nopadding" id="ifChangedDuesDifferentPerMemberType1" style="visibility:hidden">
									<div class="form-group">
									<label for="NewMemberDuesRenewal">New Dues per Renewed Member</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="NewMemberDuesRenewal" oninput="ChangeMemberCount()" id="NewMemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal_changed'] ?>">
									</div>
									</div>
								</div>
							</div>

							<div class="col-md-12" id="ifMembersNoDues" style="display:none">
								<div class="col-md-6 float-left nopadding-l">
									<div class="form-group">
										<label for="MembersNoDues">Members Who Paid No Dues</label>
										<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MembersNoDues" id="MembersNoDues"  min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['members_who_paid_no_dues'] ?>">
									</div>
								</div>
								<div class="col-md-6 float-left " style="visibility:hidden"><div class="form-group">
										<label for="MembersNoDues">Hidden</label>
										<input type="number" class="form-control"  value="0">
									</div></div>

								<div class="col-md-6 float-left nopadding-l">
									<div class="form-group">
									<label for="TotalPartialDuesMembers">Total Members Who Paid Partial Dues</label>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalPartialDuesMembers" id="TotalPartialDuesMembers" min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['members_who_paid_partial_dues'] ?>">
									</div>
								</div>
								<div class="col-md-6 float-left nopadding">
									<div class="form-group">
									<label for="PartialDuesMemberDues">Total Partial Dues Collected</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="PartialDuesMemberDues" id="PartialDuesMemberDues" min="0" step="0.01" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_partial_fees_collected'] ?>">
									</div>
									</div>
								</div>
								<p><i>Note: Associate Members are not dues-waived or reduced members. They are a separate category of members. Many chapters do not have any Associate Members, but if your
chapter did have Associate Members this year, how many Associate Members did your chapter have?</i></p>
								<div class="col-md-6 float-left nopadding-l">
									<div class="form-group">
									<label for="TotalAssociateMembers">Total Associate Members</label>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalAssociateMembers" id="TotalAssociateMembers" min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_associate_members'] ?>">
									</div>
								</div>
								<div class="col-md-6 float-left nopadding">
									<div class="form-group">
									<label for="AssociateMemberDues">Associate Member Dues</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="AssociateMemberDues" id="AssociateMemberDues" min="0" step="0.01" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['associate_member_fee'] ?>">
									</div>
									</div>
								</div>

							</div>


							<div class="col-md-6 float-left">
								<div class="form-group">
									<label for="TotalMembers">Total Members</label>
									<!--<input disabled type="text" class="form-control" name="TotalMembers" id="TotalMembers">-->
									<p class="form-control" id="TotalMembers" disabled></p>

								</div>
							</div>
							<div class="col-md-6 float-left">
								<div class="form-group">
									<label for="TotalDues">Total Dues Collected</label>
									<div class="input-group">
									<span class="input-group-addon">$</span>
									<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalDues" id="TotalDues" step="0.01" aria-describedby="sizing-addon1">-->
									<p class="form-control" id="TotalDues" disabled></p>
									</div>
								</div>
							</div>
							<hr>
						</div>

						</section>
						<h4>Section 2 - Monthly Meeting Expenses</h4>
						<section>
							<div class="form-row form-group">
								<div class="col-md-6 float-left">
									<div class="form-group">
										<label for="ManditoryMeetingFeesPaid">
											Mandatory Meeting Room Fees Paid
										</label>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="ManditoryMeetingFeesPaid" id="ManditoryMeetingFeesPaid" oninput="ChangeMeetingFees()" min="0"  step="0.01" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['manditory_meeting_fees_paid']; else echo "0"; ?>">
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
										<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="VoluntaryDonationsPaid" id="VoluntaryDonationsPaid" oninput="ChangeMeetingFees()" min="0"  step="0.01" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['voluntary_donations_paid']; else echo "0"; ?>">
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
										<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalMeetingRoomExpenses" id="TotalMeetingRoomExpenses" disabled>-->
										<p class="form-control" id="TotalMeetingRoomExpenses" disabled></p>
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
										<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="PaidBabySitters" id="PaidBabySitters"  min="0"  step="0.01" oninput="ChangeChildrensRoomExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['paid_baby_sitters'] ?>">
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
													<p class=\"form-group\" name=\"ChildrensRoomDesc" . $row . "\" id=\"ChildrensRoomDesc" . $row . "\">" . $childrens_room[$row]['childrens_room_desc'] . "</p>
												</div>
												</td>";

												echo "<td>
												<div class=\"form-group\">
												<div class=\"input-group\">";
												echo "<span class = \"input-group-addon\">$</span>";
												echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\"  name=\"ChildrensRoomSupplies" . $row . "\" id=\"ChildrensRoomSupplies" . $row . "\" oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $childrens_room[$row]['childrens_room_supplies'] . "\">";
												echo "</div>
												</div>
												</td>";

												echo "<td>
												<div class=\"form-group\">
												<div class=\"input-group\">";
												echo "<span class = \"input-group-addon\">$</span>";
												echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ChildrensRoomOther" . $row . "\" id=\"ChildrensRoomOther" . $row . "\" oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $childrens_room[$row]['childrens_room_other'] . "\">";
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
										<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" value="0.00"name="ChildrensRoomTotal"  id="ChildrensRoomTotal"  step="0.01" disabled>-->
										<p class="form-control" id="ChildrensRoomTotal" disabled></p>
										</div>
									</div>
								</div>
								<hr>
							</div>
							<input type="hidden" name="ChildrensExpenseRowCount" id="ChildrensExpenseRowCount" value="<?php echo $ChildrensExpenseRowCount; ?>" />

						</section>
					<h4>Section 3 - Service Projects</h4>
					<section>
						<div class="form-row form-group">
						<div class="col-md-12">
						  <p>
							A Service Project is one that benefits others OUTSIDE your chapter. However, a Service Project may also be a project to benefit a member-in-distress or one who has special emergency needs, if the needs are the reason for the project. For example, a fundraiser may benefit the International MOMS Club’s Mother-to-Mother Fund or may be used to help pay extreme medical expenses for a life-threatening illness suffered by a member’s child. (Any fundraisers or projects that benefited your chapter or members who are not suffering emergency or devastating situations should not be listed here. Those should be listed in Step 7.)
						  </p>
						  <p>
							Not all Service Projects are fundraisers! If you did a Service Project that was not a fundraiser, you will have expenses listed here, but no income for that project. If your chapter made a donation from the treasury to another charity and used treasury money collected as dues (instead of money raised by your chapter for the donation), you will have expenses listed (the donation), but no income for that project.
						  </p>
						  <p>
							<strong>List all Service Projects below, even if there was no income or expense.</strong> Briefly describe the project and who was benefited by it. List any income and expenses for each project.
						  </p>
						  </div>
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
												<p class=\"form-group\" name=\"ServiceProjectDesc" . $row . "\" id=\"ServiceProjectDesc" . $row . "\">" . $service_projects[$row]['service_project_desc'] . "</p>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectIncome" . $row . "\" id=\"ServiceProjectIncome" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_income'] . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectSupplies" . $row . "\" id=\"ServiceProjectSupplies" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_supplies'] . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectDonatedCharity" . $row . "\" id=\"ServiceProjectDonatedCharity" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_charity'] . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectDonatedM2M" . $row . "\" id=\"ServiceProjectDonatedM2M" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_m2m'] . "\">";
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
								<label for="ServiceProjectIncomeTotal">
									Service Project Income Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<!--<input disabled type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectIncomeTotal"  id="ServiceProjectIncomeTotal" disabled>-->
								<p class="form-control" id="ServiceProjectIncomeTotal" disabled></p>
								</div>
							</div>
						</div>
						<div class="col-md-4 float-left">
							<div class="form-group">
								<label for="ServiceProjectSuppliesTotal">
									Service Project Supply & Expense Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<!--<input disabled type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectSuppliesTotal" id="ServiceProjectSuppliesTotal" disabled>-->
								<p class="form-control" id="ServiceProjectSuppliesTotal" disabled></p>
								</div>
							</div>
						</div>
						<div class="col-md-4 float-left">
							<div class="form-group">
								<label for="ServiceProjectDonationTotal">
									Charity Donation Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<!--<input disabled type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectDonationTotal" id="ServiceProjectDonationTotal" disabled>-->
								<p class="form-control" id="ServiceProjectDonationTotal" disabled></p>
								</div>
							</div>
						</div>
						<div class="col-md-4 float-left">
							<div class="form-group">
								<label for="ServiceProjectM2MDonationTotal">
									M2M/Sustaining Chapter Donation Total
								</label>
								<div class="input-group">
								<span class="input-group-addon">$</span>
								<!--<input disabled type="number" class="form-control" min="0"  step="0.01" name="ServiceProjectM2MDonationTotal" id="ServiceProjectM2MDonationTotal" disabled>-->
								<p class="form-control" id="ServiceProjectM2MDonationTotal" disabled></p>
								</div>
							</div>
						</div>
						<hr>
					</div>
					<input type="hidden" name="ServiceProjectRowCount" id="ServiceProjectRowCount" value="<?php echo $ServiceProjectRowCount; ?>" />
				</section>
				<h4>Section 4 - Parties and Member Benefits</h4>
				<section>
					<div class="form-row form-group">
					<div class="col-md-12">
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
					  </div>
					  <div class="col-md-12">
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
										<p class=\"form-group\" name=\"PartyDesc" . $row . "\" id=\"PartyDesc" . $row . "\">" . $party_expenses[$row]['party_expense_desc'] . "</p>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"PartyIncome" . $row . "\" id=\"PartyIncome" . $row . "\" oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $party_expenses[$row]['party_expense_income'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"PartyExpenses" . $row . "\" id=\"PartyExpenses" . $row . "\" oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $party_expenses[$row]['party_expense_expenses'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "</tr>";
								}
							?>
							</tbody>
                        </table>
						</div>
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
								<!--<input type="number" class="form-control" min="0"  step="0.01" name="PartyIncomeTotal" id="PartyIncomeTotal" disabled>-->
								<p class="form-control" id="PartyIncomeTotal" disabled></p>
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
								<!--<input type="number" class="form-control" min="0"  step="0.01" name="PartyExpenseTotal" id="PartyExpenseTotal" disabled>-->
								<p class="form-control" id="PartyExpenseTotal" disabled></p>
								</div>
							</div>
						</div>
						<hr>
					</div>
					<input type="hidden" name="PartyExpenseRowCount" id="PartyExpenseRowCount" value="<?php echo $PartyExpenseRowCount; ?>" />
					</section>
					<h4>Section 5 - Office and Operating Expenses</h4>
					<section>
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
								<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="PrintingCosts" id="PrintingCosts" oninput="ChangeOfficeExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['office_printing_costs']?>">
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
								<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="PostageCosts" id="PostageCosts" oninput="ChangeOfficeExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['office_postage_costs'] ?>">
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
								<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="MembershipPins" id="MembershipPins" oninput="ChangeOfficeExpenses()" value="<?php echo $financial_report_array['office_membership_pins_cost']; ?>">
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
									<p class=\"form-group\" name=\"OfficeDesc" . $row . "\" id=\"OfficeDesc" . $row . "\">" . $other_office_expenses[$row]['office_other_desc'] . "</p>
								</div>
								</td>";

								echo "<td>
								<div class=\"form-group\">
								<div class=\"input-group\">";
								echo "<span class = \"input-group-addon\">$</span>";
								echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"OfficeExpenses" . $row . "\" id=\"OfficeExpenses" . $row . "\" oninput=\"ChangeOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $other_office_expenses[$row]['office_other_expense'] . "\">";
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
							<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num"  min="0"  step="0.01" name="OfficeExpenseTotal" id="OfficeExpenseTotal" disabled>-->
							<p class="form-control" id="OfficeExpenseTotal" disabled></p>
							</div>
						</div>
						<input type="hidden" name="OfficeExpenseRowCount" id="OfficeExpenseRowCount" value="<?php echo $OfficeExpenseRowCount; ?>" />
					</div>
					</div>
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
						</div>
						<div class="form-group">
						   <label for="">
								International Event Registrations
						   </label>
						</div>
						<div class="col-md-12">
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
										<p class=\"form-group\" name=\"InternationalEventDesc" . $row . "\" id=\"InternationalEventDesc" . $row . "\">" . $international_event_array[$row]['intl_event_desc'] . "</p>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"InternationalEventIncome" . $row . "\" id=\"InternationalEventIncome" . $row . "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $international_event_array[$row]['intl_event_income'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"InternationalEventExpense" . $row . "\" id=\"InternationalEventExpense" . $row . "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $international_event_array[$row]['intl_event_expenses'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "</tr>";
								}
							?>
							</tbody>
						</table>
						</div>
					</div>

					<div class="form-row form-group">
					<div class="col-md-4 float-left">
						<div class="form-group">
							<label for="InternationalEventIncomeTotal">
								International Event Registration Income
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="InternationalEventIncomeTotal" id="InternationalEventIncomeTotal" disabled>-->
							<p class="form-control" id="InternationalEventIncomeTotal" disabled></p>
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
							<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="InternationalEventExpenseTotal" id="InternationalEventExpenseTotal" disabled>-->
							<p class="form-control" id="InternationalEventExpenseTotal" disabled></p>
							</div>
						</div>
					</div>
					<input type="hidden" name="InternationalEventRowCount" name="InternationalEventRowCount" id="InternationalEventRowCount" value="<?php echo $InternationalEventRowCount; ?>" />
					</div>

					<div class="form-row form-group">
					<div class="col-md-12">
					  <h4>
						Chapter Re-registration
					  </h4>
					</div>
					<div class="col-md-6 float-left">
						<div class="form-group">
							<label for="AnnualRegistrationFee">
								Annual Chapter Registration Fee paid to International MOMS Club<span class="field-required">*</span>
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input disabled type="number" class="form-control txt-num" min="0"  step="0.01" name="AnnualRegistrationFee" id="AnnualRegistrationFee" onkeydown="return event.keyCode !== 69" oninput="ChangeReRegistrationExpense()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['annual_registration_fee'] ?>">
							</div>
						</div>
					</div>
					<hr>
					</div>
				</section>
				<h4>Section 6 - Donations to Your Chapter</h4>
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
											<p class=\"form-group\" name=\"DonationDesc" . $row . "\" id=\"DonationDesc" . $row . "\">" . $monetary_dontations_to_chapter[$row]['mon_donation_desc'] . "</p>
										</div>
										</td>";

										echo "<td>
										<div class=\"form-group\">
											<p class=\"form-group\" name=\"DonorInfo" . $row . "\" id=\"DonorInfo" . $row . "\">" . $monetary_dontations_to_chapter[$row]['mon_donation_info'] . "</p>
										</div>
										</td>";

										echo "<td>
										<div class=\"form-group\">
										<input disabled type=\"date\" class=\"form-control\" min='2018-07-01' max='2019-06-30' name=\"MonDonationDate" . $row . "\" id=\"MonDonationDate" . $row . "\" value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_date'] . "\" onchange=\"IsValidDate(this)\">
										</div>
										</td>";

										echo "<td>
										<div class=\"form-group\">
										<div class=\"input-group\">";
										echo "<span class = \"input-group-addon\">$</span>";
										echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"DonationAmount" . $row . "\" id=\"DonationAmount" . $row . "\" oninput=\"ChangeDonationAmount()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_amount'] . "\">";
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
						<!--<input disabled type="number" class="form-control"  min="0"  step="0.01" name="DonationTotal" id="DonationTotal" disabled>-->
						<p class="form-control" id="DonationTotal" disabled></p>
						</div>
					</div>
					<input type="hidden" name="MonDonationRowCount" id="MonDonationRowCount" value="<?php echo $MonDonationRowCount; ?>" />
				</div>
				<hr>
				</div>
				<div class="form-row form-group">
				<div class="col-md-12">
					<label for="donation-goods">
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
									<p class=\"form-group\" name=\"NonMonDonationDesc" . $row . "\" id=\"NonMonDonationDesc" . $row . "\">" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_desc'] . "</p>
								</div>
								</td>";

								echo "<td>
								<div class=\"form-group\">
									<p class=\"form-group\" name=\"NonMonDonorInfo" . $row . "\" id=\"NonMonDonorInfo" . $row . "\">" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_info'] . "</p>
								</div>
								</td>";

								echo "<td>
								<div class=\"form-group\">
								<input disabled type=\"date\" class=\"form-control\" min='2018-07-01' max='2019-06-30' name=\"NonMonDonationDate" . $row . "\" id=\"NonMonDonationDate" . $row . "\" value=\"" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_date'] . "\" onchange=\"IsValidDate(this)\">
								</div>
								</td>";

								echo "</tr>";
							}
						?>
						</tbody>
					</table>
					</div>
					<input type="hidden" name="NonMonDonationRowCount" id="NonMonDonationRowCount" value="<?php echo $NonMonDonationRowCount; ?>" />
					<hr>
					</div>
				</section>
				<h4>Section 7 - Other Income & Expenses</h4>
					<section>
					<div class="form-row form-group">
						<div class="col-md-12">
						<p>If your chapter had any other income not listed elsewhere, enter those amounts and descriptions here. (If there are multiple entries of one type of income in your books, please group them together as one total for that type of entry below. For example, if local businesses paid for advertising in your newsletter, enter one amount for all the advertising sold by your chapter during the year.)</p>
						<p>Use this section to list any fundraisers your chapter may have had to benefit the chapter or the members. If your chapter participated in any programs offering rebates, matching contributions or bonus cards, include that information here.</p>
						<table id="other-office-expenses" width="100%" class="table table-bordered">
							<thead>
							<tr>
							  <td>Description of Expense/Incomed</td>
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
								<input disabled type="text" class="form-control" name="OtherOfficeDesc0" id="OtherOfficeDesc0" value="Outgoing Board Gifts" readonly>
								</div>
								</td>

								<td>
								<div class="form-group">
								<div class="input-group">
								<span class = "input-group-addon">$</span>
								<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeIncome0" id="OtherOfficeIncome0" oninput="ChangeOtherOfficeExpenses()" value="<?php echo $other_income_and_expenses_array[0]['other_income'];?>">
								</div>
								</div>
								</td>

								<td>
								<div class="form-group">
								<div class="input-group">
								<span class = "input-group-addon">$</span>
								<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeExpenses0" id="OtherOfficeExpenses0" oninput="ChangeOtherOfficeExpenses()" value="<?php echo $other_income_and_expenses_array[0]['other_expenses'];?>">
								</div>
								</div>
								</td>

								</tr>

								<?php

										for ($row = 1; $row < $OtherOfficeExpenseRowCount; $row++){
											echo "<tr>";
											echo "<td>
											<div class=\"form-group\">
												<p class=\"form-group\" name=\"OtherOfficeDesc" . $row . "\" id=\"OtherOfficeDesc" . $row . "\">" . $other_income_and_expenses_array[$row]['other_desc'] . "</p>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"OtherOfficeIncome" . $row . "\" id=\"OtherOfficeIncome" . $row . "\" oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $other_income_and_expenses_array[$row]['other_income'] . "\">";
											echo "</div>
											</div>
											</td>";

											echo "<td>
											<div class=\"form-group\">
											<div class=\"input-group\">";
											echo "<span class = \"input-group-addon\">$</span>";
											echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"OtherOfficeExpenses" . $row . "\" id=\"OtherOfficeExpenses" . $row . "\" oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $other_income_and_expenses_array[$row]['other_expenses'] . "\">";
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
								<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeIncomeTotal" id="OtherOfficeIncomeTotal" disabled>-->
								<p class="form-control" id="OtherOfficeIncomeTotal" disabled></p>
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
								<!--<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeExpenseTotal" id="OtherOfficeExpenseTotal" disabled>-->
								<p class="form-control" id="OtherOfficeExpenseTotal" disabled></p>
								</div>
							</div>
					   </div>
					   <input type="hidden" name="OtherOfficeExpenseRowCount" id="OtherOfficeExpenseRowCount" value="<?php echo $OtherOfficeExpenseRowCount; ?>"/>
						<hr>
					</div>

				</section>
				<h4>Section 8 - Bank Reconciliation</h4>
					<section>
					<div class="form-row form-group">
                    <div class="col-md-6 float-left">
						<div class="form-group">
							<label for="AmountReservedFromLastYear">
								Amount Reserved from Previous Year:
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="TreasuryBalanceChange()" min="0" step="0.01" name="AmountReservedFromLastYear" id="AmountReservedFromLastYear" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['amount_reserved_from_previous_year'] ?>">
							</div>
						</div>
					</div>
					<div class="col-md-6 float-left">
						<div class="form-group">
							<label for="BankBalanceNow">
								Last Bank Statement Balance:
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input disabled type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="BankBalanceNow" id="BankBalanceNow" oninput="ChangeBankRec()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['bank_balance_now'] ?>">
							</div>
						</div>
					</div>
					<div class="col-md-6 float-left" style="display:none" >
						<div class="form-group">
							<label for="PettyCash">
								Petty Cash on Hand (if any):
							</label>
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="PettyCash" id="PettyCash" oninput="ChangeBankRec()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['petty_cash'] ?>">
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
							<input type="hidden" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="TreasuryBalanceNowP" id="TreasuryBalanceNowP" disabled>
							<p class="form-control" id="TreasuryBalanceNow" disabled></p>
							</div>
						</div>
					</div>
					<hr>
				</div>
				<div class="form-row form-group">
				<div class="col-md-12">
				  <p>If your most recent bank statement’s ending balance does not match your “Treasury Balance Now”, you must reconcile your checking account using the worksheet below so that the balances match.</p>
				  <p>To balance your account, start with your bank statement’s ending balance, then list any deposits and any outstanding payments. When done, the new balance will match your current checking account balance.</p>
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
									<input disabled type=\"date\" class=\"form-control\" min='2018-07-01' max='2019-06-30' name=\"BankRecDate" . $row . "\" id=\"BankRecDate" . $row . "\"value=\"" . $bank_rec_array[$row]['bank_rec_date'] . "\"  onchange=\"IsValidDate(this)\">
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<input disabled type=\"text\" class=\"form-control\" name=\"BankRecCheckNo" . $row . "\" id=\"BankRecCheckNo" . $row . "\"value=\"" . $bank_rec_array[$row]['bank_rec_check_no'] . "\">
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<input disabled type=\"text\" class=\"form-control\" name=\"BankRecDesc" . $row . "\" id=\"BankRecDesc" . $row . "\"value=\"" . $bank_rec_array[$row]['bank_rec_desc'] . "\">
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"BankRecPaymentAmount" . $row . "\" id=\"BankRecPaymentAmount" . $row . "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $bank_rec_array[$row]['bank_rec_payment_amount'] . "\">";
									echo "</div>
									</div>
									</td>";

									echo "<td>
									<div class=\"form-group\">
									<div class=\"input-group\">";
									echo "<span class = \"input-group-addon\">$</span>";
									echo "<input disabled type=\"number\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"BankRecDepositAmount" . $row . "\" id=\"BankRecDepositAmount" . $row . "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $bank_rec_array[$row]['bank_rec_desposit_amount'] . "\">";
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
							<!--<input disabled type="number" class="form-control" min="0" step="0.01" name="ReconciledBankBalance" id="ReconciledBankBalance" disabled>-->
							<p class="form-control" name="ReconciledBankBalance" id="ReconciledBankBalance" disabled></p>
							</div>
						</div>
					</div>
					<div class="col-md-6 float-right">
						<div class="form-group">
							<label for="ReconciledBankBalance" style="visibility:hidden">
								Message
							</label>
							<div class="input-group">
							<!--<textarea class="form-control" rows="6" cols="30" name="ReconciledBankBalanceWarning" id="ReconciledBankBalanceWarning" disabled></textarea>-->
							<p class="form-control" name="ReconciledBankBalanceWarning" id="ReconciledBankBalanceWarning" disabled></p>
							</div>
						</div>
					</div>
					<input type="hidden" name="BankRecRowCount" id="BankRecRowCount" value="<?php echo $BankRecRowCount; ?>" />
					<hr>
				</div>
			</section>
			<h4>Section 9 - Tax Exempt & Chapter Questions</h4>
				<section>
				<div id="form-step-8" role="form" data-toggle="validator" class="form-row form-group">
				<div class="col-md-12">
                    <p>During the last fiscal year (July 1, <?php echo date('Y')-1 .' - June 30, '.date('Y');?>)</p>
					</div>

					<div class="col-sm-12">
						<p>1. Did anyone in your chapter receive any compensation or pay for their work with your chapter?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
								<label class="form-check-label">
									<input disabled id="ReceiveCompensationNo" name="ReceiveCompensation" type="radio" class="form-check-input rd-cls" value="no" onchange="ToggleReceiveCompensationExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['receive_compensation'] == false) echo "checked";} ?> >
									<span class="form-check-sign"></span>
										  No
								</label>
								<label class="form-check-label">
									<input disabled type="radio" class="form-check-input" id="ReceiveCompensationYes" name="ReceiveCompensation" value="yes" onchange="ToggleReceiveCompensationExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['receive_compensation'] == true) echo "checked";} ?> required="required">
									<span class="form-check-sign"></span>
										  Yes
								</label>
							</div>
						</div>
					</div>
					<div class="col-sm-12" id="divReceiveCompensationExplanation">
						<label for="ReceiveCompensationExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['receive_compensation_explanation'];?></p>
						<div class="help-block with-errors"></div>
					</div>

					<div class="col-sm-12">
						<p>2. Did any officer, member or family of a member benefit financially in any way from the member’s position with your chapter?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input rd-cls" id="FinancialBenefitNo" name="FinancialBenefit" value="no" onchange="ToggleFinancialBenefitExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['financial_benefit'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input" id="FinancialBenefitYes" name="FinancialBenefit" value="yes" onchange="ToggleFinancialBenefitExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['financial_benefit'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							</div>
						</div>
					</div>
					<div class="col-sm-12" id="divFinancialBenefitExplanation">
						<label for="FinancialBenefitExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['financial_benefit_explanation'];?></p>
					</div>
					<div class="col-sm-12">
						<p>3. Did your chapter attempt to influence any national, state/provincial, or local legislation, or did your chapter support any other organization that did?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
						   <label class="form-check-label">
								<input disabled type="radio" class="form-check-input rd-cls" id="InfluencePoliticalNo" name="InfluencePolitical" value="no" onchange="ToggleInfluencePoliticalExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['influence_political'] == false) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  No
						   </label>

						   <label class="form-check-label">
								<input disabled type="radio" class="form-check-input" id="InfluencePoliticalYes" name="InfluencePolitical" value="yes" onchange="ToggleInfluencePoliticalExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['influence_political'] == true) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  Yes
						   </label>
						  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divInfluencePoliticalExplanation">
						<label for="InfluencePoliticalExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['influence_political_explanation'];?></p>
					</div>

					<div class="col-sm-12">
						<p>4. Did your chapter vote on all activities and expenditures during the fiscal year?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input rd-cls" id="VoteAllActivitiesNo" name="VoteAllActivities" value="no" onchange="ToggleVoteAllActivitiesExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['vote_all_activities'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							  <label class="form-check-label">
									<input disabled type="radio" class="form-check-input" id="VoteAllActivitiesYes" name="VoteAllActivities" value="yes" onchange="ToggleVoteAllActivitiesExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['vote_all_activities'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							    </label>
							</div>
						</div>
					</div>
					<div class="col-sm-12" id="divVoteAllActivitiesExplanation">
						<label for="VoteAllActivitiesExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['vote_all_activities_explanation'];?></p>
					</div>
					<div class="col-sm-12">
						<p>5. Did you purchase pins from International? If No, why not?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input rd-cls" id="BoughtPinsNo" name="BoughtPins" value="no" onchange="ToggleBoughtPinsExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['purchase_pins'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input" id="BoughtPinsYes" name="BoughtPins" value="yes" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['purchase_pins'] == true) echo "checked";} ?> onchange="ToggleBoughtPinsExplanation()">
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divBoughtPinsExplanation">
						<label for="BoughtPinsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['purchase_pins_explanation'];?></p>
					</div>
					<div class="col-sm-12">
						<p>6. Did you purchase any merchandise from International other than pins? If No, why not?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							<div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input rd-cls" id="BoughtMerchNo" name="BoughtMerch" value="" onchange="ToggleBoughtMerchExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['bought_merch'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="BoughtMerchYes" name="BoughtMerch" value="yes" onchange="ToggleBoughtMerchExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['bought_merch'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
							</div>
						</div>
					</div>
					<div class="col-sm-12" id="divBoughtMerchExplanation">
						<label for="BoughtMerchExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['bought_merch_explanation'];?></p>

					</div>
					<div class="col-sm-12">
						<p>7. Did you offer or information your members about MOMS Club merchandise?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input rd-cls" id="OfferedMerchNo" name="OfferedMerch" value="no" onchange="ToggleOfferedMerchExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['offered_merch'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>

									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input" id="OfferedMerchYes" name="OfferedMerch" value="yes" onchange="ToggleOfferedMerchExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['offered_merch'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Yes
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divOfferedMerchExplanation">
						<label for="OfferedMerchExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['offered_merch_explanation'];?></p>
					</div>

					<div class="col-sm-12">
						<p>8. Did you make the Bylaws and/or manual available for any chapter members that requested them?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input rd-cls" id="ByLawsAvailableNo" name="ByLawsAvailable" value="no" onchange="ToggleByLawsAvailableExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['bylaws_available'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>

									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input" id="ByLawsAvailableYes" name="ByLawsAvailable" value="yes" onchange="ToggleByLawsAvailableExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['bylaws_available'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Yes
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divByLawsAvailableExplanation">
						<label for="ByLawsAvailableExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['bylaws_available_explanation'];?></p>
					</div>
				<div class="col-sm-12">
                        <p>9. Did you have a children’s room with babysitters?<span class="field-required">*</span></p>
                        <div class="col-md-12 float-left">
                             <div class="form-check form-check-radio">
                                       <label class="form-check-label">
                                            <input disabled type="radio" class="form-check-input rd-cls" id="ChildrensRoomYesVol" name="ChildrensRoom" value="no" <?php if (!is_null($financial_report_array['childrens_room_sitters'])) {if ($financial_report_array['childrens_room_sitters'] == false) echo "checked";} ?>>
                                            <span class="form-check-sign"></span>
                                                  No
                                       </label>

                                       <label class="form-check-label">
                                            <input disabled type="radio" class="form-check-input" id="ChildrensRoomYesPaid" name="ChildrensRoom" value="yes_vol" <?php if (!is_null($financial_report_array['childrens_room_sitters'])) {if ($financial_report_array['childrens_room_sitters'] == true) echo "checked";} ?>>
                                            <span class="form-check-sign"></span>
                                                  Yes, with volunteer members
                                       </label>
                                       <label class="form-check-label">
                                            <input disabled type="radio" class="form-check-input" id="ChildrensRoomNo" name="ChildrensRoom" value="yes_paid" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['childrens_room_sitters'] == 2) echo "checked";} ?>>
                                            <span class="form-check-sign"></span>
                                                  Yes, with paid sitters
                                       </label>
                              </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <label for="ChildrensRoomExplanation">Briefly explain, if necessary:</label>
                        <textarea disabled class="form-control" rows="2" name="ChildrensRoomExplanation" id="ChildrensRoomExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['childrens_room_sitters_explanation'];}?></textarea>
                    </div>

					<div class="col-sm-12">
                    <p>10. Did you have playgroups? If so, how were they arranged.<span class="field-required">*</span></p>
                    <div class="col-md-12 float-left">
                         <div class="form-check form-check-radio">
                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input rd-cls" id="Playgroups1" name="Playgroups" value="no" <?php if (!is_null($financial_report_array['playgroups'])) {if ($financial_report_array['playgroups'] == false) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              No
                                   </label>

                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input" id="Playgroups2" name="Playgroups" value="yes_byage" <?php if (!is_null($financial_report_array['playgroups'])) {if ($financial_report_array['playgroups'] == true) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes, arranged by age
                                   </label>

                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input" id="Playgroups3" name="Playgroups" value="yes_multiage" <?php if (!is_null($financial_report_array['playgroups'])) {if ($financial_report_array['playgroups'] == 2) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes, multi-aged groups
                                   </label>
                          </div>
                    </div>

                    </div>


                        <div class="col-sm-12" id="divPlaygroupsExplanation">
                          <div class="form-group">
                                <label for="PlaygroupsExplanation">Briefly explain, if necessary:</label>
                                <textarea disabled class="form-control" rows="2" name="PlaygroupsExplanation" id="PlaygroupsExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['had_playgroups_explanation'];}?></textarea>

                            </div>
                        </div>


					<div class="col-sm-12">
						<p>11. Did you have any child focused outings or activities?<span class="field-required">*</span> (Ex: zoo, library, pumpkin patch, etc.)</p>
						<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
							<label class="form-check-label">
								<input disabled type="radio" class="form-check-input rd-cls" id="ChildOutingsNo" name="ChildOutings" value="no" onchange="ToggleChildOutingsExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['child_outings'] == false) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  No
							</label>
							<label class="form-check-label">
								<input disabled type="radio" class="form-check-input" id="ChildOutingsYes" name="ChildOutings" value="yes" onchange="ToggleChildOutingsExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['child_outings'] == true) echo "checked";} ?>>
								<span class="form-check-sign"></span>
									  Yes
						   </label>
						  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divChildOutingsExplanation">
						<label for="ChildOutingsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['child_outings_explanation'];?></p>

					</div>
					<div class="col-sm-12">
						<p>12. Did you have any mother focused outings or activities?<span class="field-required">*</span> (Ex: mall walks, art museum, etc.)</p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input rd-cls" id="MotherOutingsNo" name="MotherOutings" value="no" onchange="ToggleMotherOutingsExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['mother_outings'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							    <label class="form-check-label">
									<input disabled type="radio" class="form-check-input" id="MotherOutingsYes" name="MotherOutings" value="yes" onchange="ToggleMotherOutingsExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['mother_outings'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divMotherOutingsExplanation">
						<label for="MotherOutingsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['mother_outings_explanation'];?></p>

					</div>
					<div class="col-sm-12">
					<p>13. Did you have speakers at any meetings?<span class="field-required">*</span></p>
						<div class="col-md-4 float-left">
							 <div class="form-check form-check-radio">
							   <label class="form-check-label">
									<input disabled type="radio" class="form-check-input rd-cls" id="MeetingSpeakersNo" name="MeetingSpeakers" value="no" onchange="ToggleMeetingSpeakersExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['meeting_speakers'] == false) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  No
							   </label>
							    <label class="form-check-label">
									<input disabled type="radio" class="form-check-input" id="MeetingSpeakersYes" name="MeetingSpeakers" value="yes" onchange="ToggleMeetingSpeakersExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['meeting_speakers'] == true) echo "checked";} ?>>
									<span class="form-check-sign"></span>
										  Yes
							   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12" id="divMeetingSpeakersExplanation">
						<label for="MeetingSpeakersExplanation">If no, briefly explain:<span class="field-required">*</span></label>

						<p class="form-group"><?php echo $financial_report_array['meeting_speakers_explanation'];?></p>

					</div>
					<div class="col-sm-12">
						<p>14. If you had speakers, check any of the topics that were covered:</p>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input disabled type="checkbox" class="chk-cls" id="SpeakersChildRearing" name="SpeakersChildRearing" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_child_rearing']) && $financial_report_array['speaker_child_rearing'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Child Rearing
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input disabled type="checkbox" class="chk-cls" id="SpeakersEducation" name="SpeakersEducation" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_education']) && $financial_report_array['speaker_education'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Schools/Education
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input disabled type="checkbox" class="chk-cls" id="SpeakersHomemaking" name="SpeakersHomemaking" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_homemaking']) && $financial_report_array['speaker_homemaking'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Homemaking Topics
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input disabled type="checkbox" class="chk-cls" id="SpeakersPolitics" name="SpeakersPolitics" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_politics']) && $financial_report_array['speaker_politics'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Politics
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input disabled type="checkbox" class="chk-cls" id="SpeakersOtherNP" name="SpeakersOtherNP" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_other_np']) && $financial_report_array['speaker_other_np'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Other Non-Profit
									   </label>
							  </div>
						</div>
						<div class="col-md-4 float-left">
							 <div class="">
									   <label class="form-check-label">
											<input disabled type="checkbox" class="chk-cls" id="SpeakersOther" name="SpeakersOther" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['speaker_other']) && $financial_report_array['speaker_other'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  Other
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12">
						<p>15. Did you have any discussion topics at your meetings? If yes, how often?<span class="field-required">*</span></p>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input rd-cls" id="SpeakerFrequency1" name="SpeakerFrequency" value="no" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['discussion_topic_frequency'] == false) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  No
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input" id="SpeakerFrequency2" name="SpeakerFrequency" value="1_3_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['discussion_topic_frequency'] == true) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  1-3 Times
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input" id="SpeakerFrequency3" name="SpeakerFrequency" value="4_6_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['discussion_topic_frequency'] == 2) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  4-6 Times
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input rd-cls" id="SpeakerFrequency4" name="SpeakerFrequency" value="7_9_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['discussion_topic_frequency'] == 3) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  7-9 Times
									   </label>
							  </div>
						</div>
						<div class="col-md-2 float-left">
							 <div class="form-check form-check-radio">
									   <label class="form-check-label">
											<input disabled type="radio" class="form-check-input" id="SpeakerFrequency5" name="SpeakerFrequency" value="10_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['discussion_topic_frequency'] == 4) echo "checked";} ?>>
											<span class="form-check-sign"></span>
												  10+ Times
									   </label>
							  </div>
						</div>
					</div>
					<div class="col-sm-12">
					<p>16. Did your chapter have scheduled park days? If yes, how often?<span class="field-required">*</span></p>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input rd-cls" id="ParkDays1" name="ParkDays" value="no" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['park_day_frequency'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="ParkDays2" name="ParkDays" value="1_3_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['park_day_frequency'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  1-3 Times
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="ParkDays3" name="ParkDays" value="4_6_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['park_day_frequency'] == 2) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  4-6 Times
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="ParkDays4" name="ParkDays" value="7_9_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['park_day_frequency'] == 3) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  7-9 Times
								   </label>
						  </div>
					</div>
					<div class="col-md-2 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="ParkDays5" name="ParkDays" value="10_times" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['park_day_frequency'] == 4) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  10+ Times
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12">
					<p>17. Did your chapter have any of the following activity groups?</p>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input disabled type="checkbox" class="" id="ActivityCooking" name="ActivityCooking" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_cooking']) && $financial_report_array['activity_cooking'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Cooking
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input disabled type="checkbox" class="" id="ActivityCouponing" name="ActivityCouponing" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_couponing']) && $financial_report_array['activity_couponing'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Couponing
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input disabled type="checkbox" class="" id="ActivityMommyPlaygroup" name="ActivityMommyPlaygroup" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_mommy_playgroup']) && $financial_report_array['activity_mommy_playgroup'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Mommy Playgroup (moms with all children in school)
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input disabled type="checkbox" class="" id="ActivityBabysitting" name="ActivityBabysitting" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_babysitting']) && $financial_report_array['activity_babysitting'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Babysitting Co-op
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input disabled type="checkbox" class="" id="ActivityMNO" name="ActivityMNO" <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_mno']) && $financial_report_array['activity_mno'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  MOMS Night Out
								   </label>
						  </div>
					</div>
					<div class="col-md-4 float-left">
						 <div class="">
								   <label class="form-check-label">
										<input disabled type="checkbox" class="" id="ActivityOther" name="ActivityOther" onChange="ToggleActivityOtherExplanation()"  <?php if (!is_null($financial_report_array)) {if (isset($financial_report_array['activity_other']) && $financial_report_array['activity_other'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Other
								   </label>
						  </div>
					</div>
					</div>
					 <div class="col-sm-12" id="divActivityOtherExplanation">
						<label for="ActivityOtherExplanation">If other, briefly explain:</label>
						<textarea disabled class="form-control" rows="2" name="ActivityOtherExplanation" id="ActivityOtherExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['activity_other_explanation'];}?></textarea>
					 </div>
					<div class="col-sm-12">
					<p>18. Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity? If yes, please explain who received the contributions and why you chose them:<span class="field-required">*</span></p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input rd-cls" id="ContributionsNotRegNPNo" name="ContributionsNotRegNP" value="no" onChange="ToggleContributionsNotRegNPExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['contributions_not_registered_charity'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>

								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="ContributionsNotRegNPYes" name="ContributionsNotRegNP" value="yes" onChange="ToggleContributionsNotRegNPExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['contributions_not_registered_charity'] == 1) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12" id="divContributionsNotRegNPExplanation">
						<label for="ContributionsNotRegNPExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
							<p class="form-group"><?php echo $financial_report_array['contributions_not_registered_charity_explanation'];?></p>

					</div>
					<div class="col-sm-12">
					<p>19. Did your chapter perform at least one service project to benefit mothers or children?<span class="field-required">*</span></p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input rd-cls" id="PerformServiceProjectNo" name="PerformServiceProject" value="no" onChange="TogglePerformServiceProjectExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['at_least_one_service_project'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>
						 		   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="PerformServiceProjectYes" name="PerformServiceProject" value="yes" onChange="TogglePerformServiceProjectExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['at_least_one_service_project'] == 1) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12" id="divPerformServiceProjectExplanation">
						<label for="PerformServiceProjectExplanation">If no, briefly explain:<span class="field-required">*</span></label>

						<p class="form-group"><?php echo $financial_report_array['at_least_one_service_project_explanation'];?></p>
					</div>


					  <div class="col-sm-12">
                    <p>20. Did your chapter sister another chapter?<span class="field-required">*</span></p>
                    <div class="col-md-4 float-left">
                         <div class="form-check form-check-radio">
                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input rd-cls" id="SisterChapterNo" name="SisterChapter" value="no" <?php if (!is_null($financial_report_array['sister_chapter'])) {if ($financial_report_array['sister_chapter'] == false) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              No
                                   </label>
                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input" id="SisterChapterYes" name="SisterChapter" value="yes" <?php if (!is_null($financial_report_array['sister_chapter'])) {if ($financial_report_array['sister_chapter'] == true) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes
                                   </label>
                          </div>
                    </div>
                    </div>


                <div class="col-sm-12">
                    <p>21. Did your chapter attend an International Event (in person or virtual)?<span class="field-required">*</span></p>
                    <div class="col-md-4 float-left">
                         <div class="form-check form-check-radio">
                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input rd-cls" id="InternationalEventNo" name="InternationalEvent" value="no" <?php if (!is_null($financial_report_array['international_event'])) {if ($financial_report_array['international_event'] == false) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              No
                                   </label>
                                   <label class="form-check-label">
                                        <input disabled type="radio" class="form-check-input" id="InternationalEventYes" name="InternationalEvent" value="yes" <?php if (!is_null($financial_report_array['international_event'])) {if ($financial_report_array['international_event'] == true) echo "checked";} ?>>
                                        <span class="form-check-sign"></span>
                                              Yes
                                   </label>
                          </div>
                    </div>
                    </div>

                    <div class="col-sm-12">
					                   <p>22. Did your chapter file their IRS 990N for <?php echo date('Y')-1 .'-'.date('Y');?> (CANNOT BE DONE BEFORE JULY 1, <?php echo date('Y');?>)? Please include a confirmation copy below).<span class="field-required">*</span></p>
					<div class="col-md-4 float-left">
						<div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input rd-cls" id="FileIRSNo" name="FileIRS" value="no" onChange="ToggleFileIRSExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['file_irs'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>
						 		   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="FileIRSYes" name="FileIRS" value="yes" onChange="ToggleFileIRSExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['file_irs'] == 1) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
					</div>
					<div class="col-sm-12" id="divFileIRSExplanation">
						<label for="FileIRSExplanation">If no, briefly explain:</label>
							<p class="form-group" name="FileIRSExplanation" id="FileIRSExplanation"><?php echo $financial_report_array['file_irs_explanation'];?></p>
					</div>

                    <div class="col-sm-12">
					<p>23. Is a copy of your chapter’s most recent bank statement included with the copy of this report that you are submitting to International? (Include copies below for all accounts, if your chapter has more than one - each file 5 MB max.)<span class="field-required">*</span></p>
					<div class="col-md-4 float-left">
						 <div class="form-check form-check-radio">
								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input rd-cls" id="BankStatementIncludedNo" name="BankStatementIncluded" value="no" onChange="ToggleBankStatementIncludedExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['bank_statement_included'] == false) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  No
								   </label>

								   <label class="form-check-label">
										<input disabled type="radio" class="form-check-input" id="BankStatementIncludedYes" name="BankStatementIncluded" value="yes" onChange="ToggleBankStatementIncludedExplanation()" <?php if (!is_null($financial_report_array)) {if ($financial_report_array['bank_statement_included'] == true) echo "checked";} ?>>
										<span class="form-check-sign"></span>
											  Yes
								   </label>
						  </div>
					</div>
				</div>


					<div class="col-sm-12" id="divBankStatementIncludedExplanation">
					<label for="BankStatementIncludedExplanation">If no, briefly explain:</label>
					<p class="form-group" name="BankStatementIncludedExplanation" id="BankStatementIncludedExplanation"><?php echo $financial_report_array['file_irs_explanation'];?></p>
					</div>

					<div class="col-sm-12">
					<p>24. If your group does not have any bank accounts, where is the chapter money kept?</p>
					   <div class="col-sm-12">

					  <p  class="form-group"><?php echo $financial_report_array['wheres_the_money'];?></p>
						</div>
					</div>
					<hr>
				 </div>
			  </section>
			  <h4>Section 10 - Financial Summary</h4>
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
							<input type="hidden" name="SumAmountReservedFromPreviousYearP" id="SumAmountReservedFromPreviousYearP" class="form-control" value="0.00" aria-describedby="sizing-addon1" disabled>
							<p class="form-control" id="SumAmountReservedFromPreviousYear" disabled></p>
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
											<input type="hidden" class="form-control" aria-describedby="sizing-addon1" name="SumMembershipDuesIncomeP" id="SumMembershipDuesIncomeP" disabled>
											<p class="form-control" id="SumMembershipDuesIncome" disabled></p>
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
											<input type="hidden" class="form-control" name="SumServiceProjectIncomeP" id="SumServiceProjectIncomeP" disabled>
											<p class="form-control" id="SumServiceProjectIncome" disabled></p>
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
											<input type="hidden" class="form-control" name="SumPartyIncomeP" id="SumPartyIncomeP" disabled>
											<p class="form-control" id="SumPartyIncome" disabled></p>
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
											<input type="hidden" class="form-control" name="SumMonetaryDonationIncomeP" id="SumMonetaryDonationIncomeP" disabled>
											<p class="form-control" id="SumMonetaryDonationIncome" disabled></p>
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
											<input type="hidden" class="form-control" name="SumInternationalEventIncomeP" id="SumInternationalEventIncomeP" disabled>
											<p class="form-control" id="SumInternationalEventIncome" disabled></p>
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
											<input type="hidden" class="form-control" name="SumOtherIncomeP" id="SumOtherIncomeP" disabled>
											<p class="form-control" id="SumOtherIncome" disabled></p>
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
											<input type="hidden" class="form-control" name="SumTotalIncomeP" id="SumTotalIncomeP" disabled>
											<p class="form-control" id="SumTotalIncome" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumMeetingRoomExpenseP" id="SumMeetingRoomExpenseP" disabled>
									<p class="form-control" id="SumMeetingRoomExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumChildrensSuppliesExpenseP" id="SumChildrensSuppliesExpenseP" disabled>
									<p class="form-control" id="SumChildrensSuppliesExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumPaidSittersExpenseP" id="SumPaidSittersExpenseP" disabled>
									<p class="form-control" id="SumPaidSittersExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumChildrensOtherExpenseP" id="SumChildrensOtherExpenseP" disabled>
									<p class="form-control" id="SumChildrensOtherExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumTotalChildrensRoomExpenseP" id="SumTotalChildrensRoomExpenseP" disabled>
									<p class="form-control" id="SumTotalChildrensRoomExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumServiceProjectExpenseP" id="SumServiceProjectExpenseP" disabled>
									<p class="form-control" id="SumServiceProjectExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumDonationExpenseP" id="SumDonationExpenseP" disabled>
									<p class="form-control" id="SumDonationExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumM2MExpenseP" id="SumM2MExpenseP" disabled>
									<p class="form-control" id="SumM2MExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumTotalServiceProjectExpenseP" id="SumTotalServiceProjectExpenseP" disabled>
									<p class="form-control" id="SumTotalServiceProjectExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumPartyExpenseP" id="SumPartyExpenseP" disabled>
									<p class="form-control" id="SumPartyExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumPrintingExpenseP" id="SumPrintingExpenseP" disabled>
									<p class="form-control" id="SumPrintingExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumPostageExpenseP" id="SumPostageExpenseP" disabled>
									<p class="form-control" id="SumPostageExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumPinsExpenseP" id="SumPinsExpenseP" disabled>
									<p class="form-control" id="SumPinsExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumOtherOperatingExpenseP" id="SumOtherOperatingExpenseP" disabled>
									<p class="form-control" id="SumOtherOperatingExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumOperatingExpenseP" id="SumOperatingExpenseP" disabled>
									<p class="form-control" id="SumOperatingExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumChapterReRegistrationExpenseP" id="SumChapterReRegistrationExpenseP" disabled>
									<p class="form-control" id="SumChapterReRegistrationExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumInternationalEventExpenseP" id="SumInternationalEventExpenseP" disabled>
									<p class="form-control" id="SumInternationalEventExpense" disabled></p>
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
                                   <input type="hidden" class="form-control" name="SumOtherExpenseP" id="SumOtherExpenseP" disabled>
									<p class="form-control" id="SumOtherExpense" disabled></p>
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
                                    <input type="hidden" class="form-control" name="SumTotalExpenseP" id="SumTotalExpenseP" disabled>
									<p class="form-control" id="SumTotalExpense" disabled></p>
                                    </div>
                                </div>
				</div>
                </div>
                </div>
                </div>
				<div class="box-brd">
				  <div class="col-sm-12 float-left">
						<div class="form-group">
							<div class="col-sm-6 float-left">
							  <label for="">
								  Treasury Balance Now

							  </label>
							</div>
							<div class="col-sm-6 float-left">
							<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="hidden" class="form-control" name="SumTreasuryBalanceNowP" id="SumTreasuryBalanceNowP" disabled>
							<p class="form-control" id="SumTreasuryBalanceNow" disabled></p>
							</div>
							</div>
						</div>
				  </div>
				  </div>
            </div>

         </div>
			</section>
			<h4>Section 11 - Award Nominations</h4>
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
                        </div>
						<!-- Award 1 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<1) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType1">Select list:</label>
									<select disabled class="form-control" id="NominationType1" name="NominationType1" onClick="ShowOutstandingCriteria(1)">
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<?php
							if($financial_report_array['award_1_nomination_type'] == 5 || $financial_report_array['award_1_nomination_type'] == 6){?>
							<div class="award_acc_con">
								<div id="OutstandingCriteria1" style="display: block;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws1">Did you follow the Bylaws and all instructions from International?</label></p>
										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingFollowByLaws1Yes" name="OutstandingFollowByLaws1" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_1_outstanding_follow_bylaws'])) {if ($financial_report_array['award_1_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingFollowByLaws1No" name="OutstandingFollowByLaws1" <?php if (!is_null($financial_report_array['award_1_outstanding_follow_bylaws'])) {if ($financial_report_array['award_1_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded1">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingWellRounded1Yes" name="OutstandingWellRounded1" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_1_outstanding_well_rounded'])) {if ($financial_report_array['award_1_outstanding_well_rounded'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingWellRounded1No" name="OutstandingWellRounded1" <?php if (!is_null($financial_report_array['award_1_outstanding_well_rounded'])) {if ($financial_report_array['award_1_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated1">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingCommunicated1Yes" name="OutstandingCommunicated1" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_1_outstanding_communicated'])) {if ($financial_report_array['award_1_outstanding_communicated'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input"id="OutstandingCommunicated1No" name="OutstandingCommunicated1" <?php if (!is_null($financial_report_array['award_1_outstanding_communicated'])) {if ($financial_report_array['award_1_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
												<input disabled id="OutstandingSupportMomsClub1Yes" name="OutstandingSupportMomsClub1" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_1_outstanding_support_international'])) {if ($financial_report_array['award_1_outstanding_support_international'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingSupportMomsClub1No" name="OutstandingSupportMomsClub1" <?php if (!is_null($financial_report_array['award_1_outstanding_support_international'])) {if ($financial_report_array['award_1_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">

									<p class="form-group" id="AwardDesc1" name="AwardDesc1"><?php echo $financial_report_array['award_1_outstanding_project_desc'];?></p>
								 </div>

								<!-- <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_files']) echo "style=\"display: none;\"";} ?>>
									<label>Award Files for this Nomination (each file 5MB max):</label>
									<input type="file" class="demo1" name="Award1[]" id="Award1" accept=".pdf, .jpg, .jpeg">
								 </div>-->
								 <input type="hidden" name="Award1Path" id="Award1Path" value="<?php echo $financial_report_array['award_1_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_1_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
									<div class="col-md-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_1_files = null;
											$Award1FileCount =0;
											if(isset($financial_report_array['award_1_files'])){
												$award_1_files=unserialize(base64_decode($financial_report_array['award_1_files']));
												//print_r($award_1_files); die;
												//$Award1FileCount = count($award_1_files);
												$Award1FileCount = is_array($award_1_files) ? count($award_1_files) : 0;

												for ($row = 1; $row <= $Award1FileCount; $row++){
													$row_id = $row;
													echo "<p class=\"form-control-static\">" . $award_1_files[$row]['url'] . "</p>";
												}
											}
										?>
										</div>
										</div>

									</div>
								</div>

							</div>
							<?php }
							else{ ?>
								 <h4>Description</h4>
								 <div class="col-md-12">
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">


									<p class="form-group" id="AwardDesc1" name="AwardDesc1"><?php echo $financial_report_array['award_1_outstanding_project_desc'];?></p>
								 </div>
								 </div>

							<?php }	?>

						</div>
						<!-- Award 1 Stop -->
						<!-- Award 2 Start -->
						<div class="box_brd_contentpad" id="Award2Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<2) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType2">Select list:</label>
									<select disabled class="form-control" id="NominationType2" name="NominationType2" onClick="ShowOutstandingCriteria(2)">
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<?php
							if($financial_report_array['award_2_nomination_type'] == 5 || $financial_report_array['award_2_nomination_type'] == 6){?>
							<div class="award_acc_con">
								<div id="OutstandingCriteria2" style="display: block;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws2">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingFollowByLaws2Yes" name="OutstandingFollowByLaws2" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_2_outstanding_follow_bylaws'])) {if ($financial_report_array['award_2_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingFollowByLaws2No" name="OutstandingFollowByLaws2" <?php if (!is_null($financial_report_array['award_2_outstanding_follow_bylaws'])) {if ($financial_report_array['award_2_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded2">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingWellRounded2Yes" name="OutstandingWellRounded2" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_2_outstanding_well_rounded'])) {if ($financial_report_array['award_2_outstanding_well_rounded'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingWellRounded2No" name="OutstandingWellRounded2" <?php if (!is_null($financial_report_array['award_2_outstanding_well_rounded'])) {if ($financial_report_array['award_2_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated2">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingCommunicated2Yes" name="OutstandingCommunicated2" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_2_outstanding_communicated'])) {if ($financial_report_array['award_2_outstanding_communicated'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input"id="OutstandingCommunicated2No" name="OutstandingCommunicated2" <?php if (!is_null($financial_report_array['award_2_outstanding_communicated'])) {if ($financial_report_array['award_2_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
												<input disabled id="OutstandingSupportMomsClub2Yes" name="OutstandingSupportMomsClub2" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_2_outstanding_support_international'])) {if ($financial_report_array['award_2_outstanding_support_international'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingSupportMomsClub2No" name="OutstandingSupportMomsClub2" <?php if (!is_null($financial_report_array['award_2_outstanding_support_international'])) {if ($financial_report_array['award_2_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>

								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">


									<p class="form-group" id="AwardDesc2" name="AwardDesc2"><?php echo $financial_report_array['award_2_outstanding_project_desc'];?></p>
								 </div>

								<!-- <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_files']) echo "style=\"display: none;\"";} ?>>
									<label>Award Files for this Nomination (each file 5MB max):</label>
									<input type="file" class="demo1" name="Award2[]" id="Award2" accept=".pdf, .jpg, .jpeg">
								 </div>-->
								 <input type="hidden" name="Award2Path" id="Award2Path" value="<?php echo $financial_report_array['award_2_files']; ?>">

								 <div class="row" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['award_2_files']) echo "style=\"display: none;\"";} ?>>
									<div class="form-group col-xs-12">
									<div class="col-md-12">
										<label class="control-label" for="Award1Link">Supporting Award Files:</label>
										<div>
										<?php
											$award_2_files = null;
											$Award2FileCount =0;
											if(isset($financial_report_array['award_2_files'])){
												$award_2_files=unserialize(base64_decode($financial_report_array['award_2_files']));

												//$Award1FileCount = count($award_1_files);
												//$Award2FileCount = is_array($award_2_files) ? count($award_2_files) : 0;
												$row = 2;
												$row_id ='';
												echo "<p class=\"form-control-static\">" . $award_2_files[$row]['url'] . "</p>";
												/*for ($row = 2; $row <= $Award2FileCount; $row++){
													$row_id = $row;
													echo "<p class=\"form-control-static\"><a href=\"" . $award_2_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
												}*/
											}
										?>
										</div>
										</div>
									</div>
								</div>

							</div>
							<?php }
							else{ ?>
							<div class="col-md-12">
								 <h4>Description</h4>

								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">


									<p class="form-group" id="AwardDesc2" name="AwardDesc2"><?php echo $financial_report_array['award_2_outstanding_project_desc'];?></p>
								 </div>
								 </div>
							<?php }	?>
						</div>
						<!-- Award 2 Stop -->
						<!-- Award 3 Start -->
						<div class="box_brd_contentpad" id="Award3Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<3) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType3">Select list:</label>
									<select disabled class="form-control" id="NominationType3" name="NominationType3" onClick="ShowOutstandingCriteria(3)">
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<?php
							if($financial_report_array['award_3_nomination_type'] == 5 || $financial_report_array['award_3_nomination_type'] == 6){?>
							<div class="award_acc_con">
								<div id="OutstandingCriteria3" style="display: block;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws3">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingFollowByLaws3Yes" name="OutstandingFollowByLaws3" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_3_outstanding_follow_bylaws'])) {if ($financial_report_array['award_3_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingFollowByLaws3No" name="OutstandingFollowByLaws3" <?php if (!is_null($financial_report_array['award_3_outstanding_follow_bylaws'])) {if ($financial_report_array['award_3_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded3">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingWellRounded3Yes" name="OutstandingWellRounded3" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_3_outstanding_well_rounded'])) {if ($financial_report_array['award_3_outstanding_well_rounded'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingWellRounded3No" name="OutstandingWellRounded3" <?php if (!is_null($financial_report_array['award_3_outstanding_well_rounded'])) {if ($financial_report_array['award_3_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated3">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingCommunicated3Yes" name="OutstandingCommunicated3" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_3_outstanding_communicated'])) {if ($financial_report_array['award_3_outstanding_communicated'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input"id="OutstandingCommunicated3No" name="OutstandingCommunicated3" <?php if (!is_null($financial_report_array['award_3_outstanding_communicated'])) {if ($financial_report_array['award_3_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
												<input disabled id="OutstandingSupportMomsClub3Yes" name="OutstandingSupportMomsClub3" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_3_outstanding_support_international'])) {if ($financial_report_array['award_3_outstanding_support_international'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingSupportMomsClub3No" name="OutstandingSupportMomsClub3" <?php if (!is_null($financial_report_array['award_3_outstanding_support_international'])) {if ($financial_report_array['award_3_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">

									<p class="form-group" id="AwardDesc3" name="AwardDesc3"><?php echo $financial_report_array['award_3_outstanding_project_desc'];?></p>
								 </div>
								 <!--<div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_files']) echo "style=\"display: none;\"";} ?>>
									<label>Award Files for this Nomination (each file 5MB max):</label>
									<input type="file" class="demo1" name="Award3[]" id="Award3" accept=".pdf, .jpg, .jpeg">
								 </div>-->
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

												//$Award1FileCount = count($award_1_files);
												//$Award2FileCount = is_array($award_2_files) ? count($award_2_files) : 0;
												$row = 3;
												$row_id ='';
												echo "<p class=\"form-control-static\">" . $award_3_files[$row]['url'] . "</p>";
												/*for ($row = 2; $row <= $Award2FileCount; $row++){
													$row_id = $row;
													echo "<p class=\"form-control-static\"><a href=\"" . $award_2_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
												}*/
											}
										?>
										</div>
									</div>
								</div>
							</div>
							<?php }
							else{ ?>
							<div class="col-md-12">
								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">


									<p class="form-group" id="AwardDesc3" name="AwardDesc3"><?php echo $financial_report_array['award_3_outstanding_project_desc'];?></p>
								 </div>
								 </div>
							<?php }	?>
						</div>
						<!-- Award 3 Stop -->
						<!-- Award 4 Start -->
						<div class="box_brd_contentpad" id="Award4Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<4) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType4">Select list:</label>
									<select disabled class="form-control" id="NominationType4" name="NominationType4" onClick="ShowOutstandingCriteria(4)">
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<?php
							if($financial_report_array['award_4_nomination_type'] == 5 || $financial_report_array['award_4_nomination_type'] == 6){?>
							<div class="award_acc_con">
								<div id="OutstandingCriteria4" style="display: block;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws4">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingFollowByLaws4Yes" name="OutstandingFollowByLaws4" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_4_outstanding_follow_bylaws'])) {if ($financial_report_array['award_4_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingFollowByLaws4No" name="OutstandingFollowByLaws4" <?php if (!is_null($financial_report_array['award_4_outstanding_follow_bylaws'])) {if ($financial_report_array['award_4_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded4">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingWellRounded4Yes" name="OutstandingWellRounded4" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_4_outstanding_well_rounded'])) {if ($financial_report_array['award_4_outstanding_well_rounded'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingWellRounded4No" name="OutstandingWellRounded4" <?php if (!is_null($financial_report_array['award_4_outstanding_well_rounded'])) {if ($financial_report_array['award_4_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated4">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingCommunicated4Yes" name="OutstandingCommunicated4" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_4_outstanding_communicated'])) {if ($financial_report_array['award_4_outstanding_communicated'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input"id="OutstandingCommunicated4No" name="OutstandingCommunicated4" <?php if (!is_null($financial_report_array['award_4_outstanding_communicated'])) {if ($financial_report_array['award_4_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
												<input disabled id="OutstandingSupportMomsClub4Yes" name="OutstandingSupportMomsClub4" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_4_outstanding_support_international'])) {if ($financial_report_array['award_4_outstanding_support_international'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingSupportMomsClub4No" name="OutstandingSupportMomsClub4" <?php if (!is_null($financial_report_array['award_4_outstanding_support_international'])) {if ($financial_report_array['award_4_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">

									<p class="form-group" id="AwardDesc4" name="AwardDesc4"><?php echo $financial_report_array['award_4_outstanding_project_desc'];?></p>
								 </div>

								<!--<div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_files']) echo "style=\"display: none;\"";} ?>>
									<label>Award Files for this Nomination (each file 5MB max):</label>
									<input type="file" class="demo1" name="Award4[]" id="Award4" accept=".pdf, .jpg, .jpeg">
								 </div>-->
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

												//$Award1FileCount = count($award_1_files);
												//$Award2FileCount = is_array($award_2_files) ? count($award_2_files) : 0;
												$row = 4;
												$row_id ='';
												echo "<p class=\"form-control-static\">" . $award_4_files[$row]['url'] . "</p>";
												/*for ($row = 2; $row <= $Award2FileCount; $row++){
													$row_id = $row;
													echo "<p class=\"form-control-static\"><a href=\"" . $award_2_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
												}*/
											}
										?>
										</div>
									</div>
								</div>
							</div>
							<?php }
							else{ ?>
							<div class="col-md-12">
								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">


									<p class="form-group" id="AwardDesc4" name="AwardDesc4"><?php echo $financial_report_array['award_4_outstanding_project_desc'];?></p>
								 </div>
								 </div>
							<?php }	?>
						</div>
						<!-- Award 4 Stop -->
						<!-- Award 5 Start -->
						<div class="box_brd_contentpad" id="Award5Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<5) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
								<label for="NominationType5">Select list:</label>
									<select disabled class="form-control" id="NominationType5" name="NominationType5" onClick="ShowOutstandingCriteria(5)">
									   <option style="display:none" disabled selected>Select an award type</option>
									   <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
											<option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
											<option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
											<option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
											<option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
									</select>
								</div>
							</div>
							<?php
							if($financial_report_array['award_5_nomination_type'] == 5 || $financial_report_array['award_5_nomination_type'] == 6){?>
							<div class="award_acc_con">
								<div id="OutstandingCriteria5" style="display: block;">
								<div class="col-sm-12">
									<h4>Outstanding Chapter Criteria</h4>
									<p><label for="OutstandingFollowByLaws5">Did you follow the Bylaws and all instructions from International?</label></p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingFollowByLaws5Yes" name="OutstandingFollowByLaws5" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_5_outstanding_follow_bylaws'])) {if ($financial_report_array['award_5_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingFollowByLaws5No" name="OutstandingFollowByLaws5" <?php if (!is_null($financial_report_array['award_5_outstanding_follow_bylaws'])) {if ($financial_report_array['award_5_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingWellRounded5">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingWellRounded5Yes" name="OutstandingWellRounded5" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_5_outstanding_well_rounded'])) {if ($financial_report_array['award_5_outstanding_well_rounded'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingWellRounded5No" name="OutstandingWellRounded5" <?php if (!is_null($financial_report_array['award_5_outstanding_well_rounded'])) {if ($financial_report_array['award_5_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								<div class="col-sm-12">
									<p><label for="OutstandingCommunicated5">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

										<div class="form-check form-check-radio">
											<label class="form-check-label">
												<input disabled id="OutstandingCommunicated5Yes" name="OutstandingCommunicated5" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_5_outstanding_communicated'])) {if ($financial_report_array['award_5_outstanding_communicated'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input"id="OutstandingCommunicated5No" name="OutstandingCommunicated5" <?php if (!is_null($financial_report_array['award_5_outstanding_communicated'])) {if ($financial_report_array['award_5_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
												<input disabled id="OutstandingSupportMomsClub5Yes" name="OutstandingSupportMomsClub5" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_5_outstanding_support_international'])) {if ($financial_report_array['award_5_outstanding_support_international'] == 1) echo "checked";} ?>>
												<span class="form-check-sign"></span>
													  Yes
											</label>
											<label class="form-check-label">
												<input disabled type="radio" class="form-check-input" id="OutstandingSupportMomsClub5No" name="OutstandingSupportMomsClub5" <?php if (!empty($financial_report_array['award_5_outstanding_support_international'])) {if ($financial_report_array['award_5_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
												<span class="form-check-sign"></span>
													  No
											</label>
										</div>

								</div>
								</div>

								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">
									<p class="form-group" id="AwardDesc5" name="AwardDesc5"><?php echo $financial_report_array['award_5_outstanding_project_desc'];?></p>
								 </div>

								<!--<div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_files']) echo "style=\"display: none;\"";} ?>>
									<label>Award Files for this Nomination (each file 5MB max):</label>
									<input type="file" class="demo1" name="Award5[]" id="Award5" accept=".pdf, .jpg, .jpeg">
								 </div>-->
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

												//$Award1FileCount = count($award_1_files);
												//$Award2FileCount = is_array($award_2_files) ? count($award_2_files) : 0;
												$row = 5;
												$row_id ='';
												echo "<p class=\"form-control-static\">" . $award_5_files[$row]['url'] . "</p>";
												/*for ($row = 2; $row <= $Award2FileCount; $row++){
													$row_id = $row;
													echo "<p class=\"form-control-static\"><a href=\"" . $award_2_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a></p>";
												}*/
											}
										?>
										</div>
									</div>
								</div>
							</div>
							<?php }
							else{ ?>
							<div class="col-md-12">
								 <h4>Description</h4>
								 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
								 <div class="form-group">


									<p class="form-group" id="AwardDesc5" name="AwardDesc5"><?php echo $financial_report_array['award_5_outstanding_project_desc'];?></p>
								 </div>
								 </div>
							<?php }	?>
						</div>
						<!-- Award 5 Stop -->

						<div class="box_brd_contentpad" id="AwardSignatureBlock" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<1) echo "none;"; else echo "block;";} else echo "none;";?>">
							  <div class="box_brd_title_box">
								 <h4>ALL ENTRIES MUST INCLUDE THIS SIGNED AGREEMENT</h4>
							  </div>
								<div class="award_acc_con">
								<div class="col-md-12">
									<p>I, THE UNDERSIGNED, AFFIRM THAT I HAVE THE RIGHT TO SUBMIT THE ENCLOSED ENTRY TO THE INTERNATIONAL MOMS CLUB FOR CONSIDERATION IN THEIR OUTSTANDING CHAPTER RECOGNITIONS, THAT THE ENCLOSED INFORMATION IS ACCURATE AND COMPLETE TO THE BEST OF MY ABILITY AND THAT I HAVE RECEIVED PERMISSION TO ENTER THIS INFORMATION FROM ANY OTHER MEMBERS WHO MAY HAVE CONTRIBUTED TO THIS ENTRY OR THE ORIGINAL ACTIVITY/PROJECT THAT IS BEING CONSIDERED. I UNDERSTAND THAT, WHETHER OR NOT MY CHAPTER RECEIVES A RECOGNITION, THE ENCLOSED ENTRY WILL BECOME THE PROPERTY OF THE INTERNATIONAL MOMS CLUB AND THAT THE INFORMATION, PICTURES, CLIPPINGS AND/OR OTHER MATERIALS ENCLOSED MAY BE SHARED WITH OTHER MOMS CLUB CHAPTERS OR USED IN ANY WAY THE INTERNATIONAL MOMS CLUB SEES FIT, WITH NO COMPENSATION TO ME, OTHERS INVOLVED IN THIS PROJECT AND/OR THE CHAPTER(S). NO ENTRIES OR SUBMITTED MATERIALS WILL BE RETURNED AND THE INTERNATIONAL MOMS CLUB MAY REASSIGN ANY ENTRY TO ANOTHER CATEGORY IF IT DEEMS NECESSARY. RECOGNITIONS WILL BE GIVEN IN THE VARIOUS CATEGORIES ACCORDING TO THE DECISION OF THE INTERNATIONAL MOMS CLUB. THE AWARDING OF RECOGNITIONS WILL BE ACCORDING TO MERIT, AND THE INTERNATIONAL MOMS CLUB MAY DECIDE NOT TO GIVE AN AWARD IN ANY OR ALL CATEGORIES IF IT SO CHOOSES. ALL DECISIONS OF THE INTERNATIONAL MOMS CLUB ARE FINAL. ANY RECOGNITIONS ARE OFFICIALLY PRESENTED TO THE LOCAL CHAPTERS, NOT THE INDIVIDUAL, AND RECOGNITIONS WILL NOT BE PERSONALIZED WITH ANY INDIVIDUAL’S NAME. REPLACEMENT RECOGNITIONS MAY OR MAY NOT BE MADE AVAILABLE AT INTERNATIONAL’S DISCRETION, AND IF A REPLACEMENT IS MADE BECAUSE OF AN ERROR IN THE ENTRY INFORMATION, THE COST WILL BE PAID IN ADVANCE BY THE LOCAL CHAPTER.</p>
									</div>
									<div class="checkbox">
										<label><input type="checkbox" id="AwardsAgree" name="AwardsAgree" <?php if (isset($financial_report_array['award_agree']) && $financial_report_array['award_agree'] == 1) echo "checked"; ?> checked disabled>I understand and agree to the above</label>
									</div>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
										<input disabled id="NominationSubmitor" name="NominationSubmitor" type="text" class="form-control" value="<?php echo $loggedInName; ?>">
									</div>
								</div>
                            </div>

                    </div>
					</section>

			</div>
		</div><!-- end of accordion -->
	</div>
</div>


@endsection
@section('customscript')
<script>
    $('.demo1').fileselect();
	$(window).on("load", function() {
        LoadSteps();
    });
</script>

<!-- JQUERY STEP -->
<script>
function fullPrintDiv(id) {
	//$("#full-print-div").show();
	//$("#test").hide();
	var printContents = $("#full-print-div").html();
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
	//window.reload();
    document.body.innerHTML = originalContents;
	//	window.reload();
	//window.location.reload();
	window.location.href = "/board/financial/"+id;
}
$(document).ready(function(){
	//$("#full-print-div").hide();
	(".txt-num").keypress(function (e) {
        var key = e.charCode || e.keyCode || 0;
		// only numbers
		if (key < 48 || key > 58) {
			return false;
		}
	});
});
</script>
<script>

function ChargeDifferentMembers(ButtonID){

		document.getElementById("chapterid").value=ButtonID;
		return true;

	}

	function IsValidDate(element){

		var strDate="";

		strDate = element.value;

		if(!Date.parse(strDate)>0){
			element.validity.valid=false;
			element.className += " has-error";
		}
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

			document.getElementById("lblTotalNewMembers").innerHTML = "Total Paid New Members Old Fee:"
			document.getElementById("lblTotalRenewedMembers").innerHTML = "Total Paid Renewed Members Old Fee:"
		}
		else{
			document.getElementById("ifChangeDues").style.display = 'none';
			document.getElementById("ifChangedDues1").style.visibility = 'hidden';

			document.getElementById("TotalNewMembersNewFee").value = 0;
			document.getElementById("TotalRenewedMembersNewFee").value = 0;

			document.getElementById("lblTotalNewMembers").innerHTML = "Total Paid New Members:"
			document.getElementById("lblTotalRenewedMembers").innerHTML = "Total Paid Renewed Members:"
		}

		////////////////////////////////////////////////////////////////////////////////////////////////////
		if(ChargedMembersDifferently){
			document.getElementById("ifChangedDuesDifferentPerMemberType").style.display = 'block';
			document.getElementById("lblMemberDues").innerHTML  = "Dues per New Member:"
			document.getElementById("lblNewMemberDues").innerHTML = "New Dues per New Member:"

			if(ChangedMeetingFees){
				document.getElementById("ifChangedDuesDifferentPerMemberType1").style.visibility = 'visible';
			}
			else{
				document.getElementById("ifChangedDuesDifferentPerMemberType1").style.visibility = 'hidden';
			}

		}
		else{
			document.getElementById("ifChangedDuesDifferentPerMemberType").style.display = 'none';
			document.getElementById("lblMemberDues").innerHTML = "Dues per Member:"
			document.getElementById("lblNewMemberDues").innerHTML = "New Dues per Member:"

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

		document.getElementById("TotalMeetingRoomExpenses").innerHTML = TotalFees;
		document.getElementById("SumMeetingRoomExpense").innerHTML = TotalFees;
		document.getElementById("SumMeetingRoomExpenseP").value = TotalFees;

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

		TotalFees = TotalFees.toFixed(2);

		TotalMembers = NewMembers + RenewedMembers + MembersNoDues + AssociateMembers + PartialDuesMembers + NewMembers2 + RenewedMembers2;
		//document.getElementById("TotalMembers").value = TotalMembers;
		document.getElementById("TotalMembers").innerHTML = TotalMembers;
		//document.getElementById("TotalDues").value = TotalFees;
		document.getElementById("TotalDues").innerHTML = TotalFees;
		document.getElementById("SumMembershipDuesIncome").innerHTML = TotalFees;
		document.getElementById("SumMembershipDuesIncomeP").value = TotalFees;

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

		document.getElementById("ChildrensRoomTotal").innerHTML = TotalOtherFees;
		document.getElementById("SumChildrensOtherExpense").innerHTML = OtherTotal;
		document.getElementById("SumChildrensOtherExpenseP").value = OtherTotal;

		document.getElementById("SumChildrensSuppliesExpense").innerHTML = SupplyTotal;
		document.getElementById("SumChildrensSuppliesExpenseP").value = SupplyTotal;

		SumPaidSittersExpense = (Number(document.getElementById("PaidBabySitters").value)).toFixed(2);
		document.getElementById("SumPaidSittersExpense").innerHTML = SumPaidSittersExpense;
		document.getElementById("SumPaidSittersExpenseP").value = SumPaidSittersExpense;

		var TotalFees = (Number(TotalOtherFees) + Number(SumPaidSittersExpense)).toFixed(2);

		document.getElementById("SumTotalChildrensRoomExpense").innerHTML = TotalFees;
		document.getElementById("SumTotalChildrensRoomExpenseP").value = TotalFees;

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
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ChildrensRoomSupplies" + ExpenseCount + "\" id=\"ChildrensRoomSupplies" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ChildrensRoomOther" + ExpenseCount + "\" id=\"ChildrensRoomOther" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('ChildrensExpenseRowCount').value = ExpenseCount;

		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeleteChildrenExpenseRow(){

		var ExpenseCount = document.getElementById("ChildrensExpenseRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("childrens-room");

		var row = table.rows[ExpenseCount];
		row.cells[1].children[0].children[0].children[1].value=0;
		row.cells[2].children[0].children[0].children[1].value=0;

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



		document.getElementById("ServiceProjectIncomeTotal").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumServiceProjectIncome").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumServiceProjectIncomeP").value = IncomeTotal.toFixed(2);

		document.getElementById("ServiceProjectSuppliesTotal").innerHTML = SupplyTotal.toFixed(2);
		document.getElementById("ServiceProjectDonationTotal").innerHTML = CharityTotal.toFixed(2);
		document.getElementById("ServiceProjectM2MDonationTotal").innerHTML = M2MTotal.toFixed(2);
		document.getElementById("SumServiceProjectExpense").innerHTML = SupplyTotal.toFixed(2);
		document.getElementById("SumServiceProjectExpenseP").value = SupplyTotal.toFixed(2);
		document.getElementById("SumDonationExpense").innerHTML = CharityTotal.toFixed(2);
		document.getElementById("SumDonationExpenseP").value = CharityTotal.toFixed(2);
		document.getElementById("SumM2MExpense").innerHTML = M2MTotal.toFixed(2);
		document.getElementById("SumM2MExpenseP").value = M2MTotal.toFixed(2);

		TotalFees = (SupplyTotal + CharityTotal + M2MTotal).toFixed(2);
		document.getElementById("SumTotalServiceProjectExpense").innerHTML = TotalFees;
		document.getElementById("SumTotalServiceProjectExpenseP").value = TotalFees;

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

		cell1.innerHTML = "<div class=\"form-group\"><textarea class=\"form-control\" rows=\"4\" name=\"ServiceProjectDesc" + ExpenseCount + "\" id=\"ServiceProjectDesc" + ExpenseCount + "\"></textarea></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" class=\"form-control txt-num\"  min=\"0\"  step=\"0.01\" name=\"ServiceProjectIncome" + ExpenseCount + "\" id=\"ServiceProjectIncome" + ExpenseCount + "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ServiceProjectSupplies" + ExpenseCount + "\" id=\"ServiceProjectSupplies" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ServiceProjectDonatedCharity" + ExpenseCount + "\" id=\"ServiceProjectDonatedCharity" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell5.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ServiceProjectDonatedM2M" + ExpenseCount + "\" id=\"ServiceProjectDonatedM2M" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('ServiceProjectRowCount').value = ExpenseCount;
		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeleteServiceProjectRow(){

		var ExpenseCount = document.getElementById("ServiceProjectRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("service-projects");

		var row = table.rows[ExpenseCount];
		row.cells[1].children[0].children[0].children[1].value=0;
		row.cells[2].children[0].children[0].children[1].value=0;
		row.cells[3].children[0].children[0].children[1].value=0;
		row.cells[4].children[0].children[0].children[1].value=0;

		document.getElementById("service-projects").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeServiceProjectExpenses();

		document.getElementById('ServiceProjectRowCount').value = ExpenseCount;
	}

	function ChangePartyExpenses(){
		var IncomeTotal=0;
		var ExpenseTotal=0;

		var table=document.getElementById("party-expenses");

		for (var i = 1, row; row = table.rows[i]; i++) {
		   //iterate through rows
		   //rows would be accessed using the "row" variable assigned in the for loop
			value = Number(row.cells[1].children[0].children[0].children[1].value);
			IncomeTotal += value;

			value = Number(row.cells[2].children[0].children[0].children[1].value);
			ExpenseTotal += value;
		}

		document.getElementById("PartyIncomeTotal").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("PartyExpenseTotal").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("SumPartyIncome").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumPartyIncomeP").value = IncomeTotal.toFixed(2);
		document.getElementById("SumPartyExpense").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("SumPartyExpenseP").value = ExpenseTotal.toFixed(2);

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
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"PartyIncome" + ExpenseCount + "\" id=\"PartyIncome" + ExpenseCount + "\"  oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"PartyExpenses" + ExpenseCount + "\" id=\"PartyExpenses" + ExpenseCount + "\"  oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('PartyExpenseRowCount').value = ExpenseCount;
		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeletePartyExpenseRow(){

		var ExpenseCount = document.getElementById("PartyExpenseRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("party-expenses");

		var row = table.rows[ExpenseCount];
		row.cells[1].children[0].children[0].children[1].value=0;
		row.cells[2].children[0].children[0].children[1].value=0;

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
		document.getElementById("OfficeExpenseTotal").innerHTML = ExpenseTotal.toFixed(2);

		SumPrintingExpense=Number(document.getElementById("PrintingCosts").value);
		SumPostageExpense=Number(document.getElementById("PostageCosts").value);
		SumPinsExpense=Number(document.getElementById("MembershipPins").value);


		document.getElementById("SumPrintingExpense").innerHTML = SumPrintingExpense.toFixed(2);
		document.getElementById("SumPrintingExpenseP").value = SumPrintingExpense.toFixed(2);

		document.getElementById("SumPostageExpense").innerHTML = SumPostageExpense.toFixed(2);
		document.getElementById("SumPostageExpenseP").value = SumPostageExpense.toFixed(2);
		document.getElementById("SumPinsExpense").innerHTML = SumPinsExpense.toFixed(2);
		document.getElementById("SumPinsExpenseP").value = SumPinsExpense.toFixed(2);
		document.getElementById("SumOtherOperatingExpense").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("SumOtherOperatingExpenseP").value = ExpenseTotal.toFixed(2);

		ExpenseTotal = ExpenseTotal + SumPrintingExpense + SumPostageExpense + SumPinsExpense

		document.getElementById("SumOperatingExpense").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("SumOperatingExpenseP").value = ExpenseTotal.toFixed(2);

		ReCalculateSummaryTotal();

	}

	function AddOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OfficeExpenseRowCount").value;

		var table=document.getElementById("office-expenses");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);

		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=OfficeDesc" + ExpenseCount + " id=OfficeDesc" + ExpenseCount + "></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"OfficeExpenses" + ExpenseCount + "\" id=\"OfficeExpenses" + ExpenseCount + "\"  oninput=\"ChangeOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('OfficeExpenseRowCount').value = ExpenseCount;
		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeleteOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OfficeExpenseRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("office-expenses");

		var row = table.rows[ExpenseCount];
		row.cells[1].children[0].children[0].children[1].value=0;

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

		document.getElementById("DonationTotal").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumMonetaryDonationIncome").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumMonetaryDonationIncomeP").value = IncomeTotal.toFixed(2);
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
		cell3.innerHTML = "<div class=\"form-group\"><input type=\"date\" class=\"form-control\" min='2017-07-01' max='2018-06-30' name=\"MonDonationDate" + ExpenseCount + "\" id=\"MonDonationDate" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\" onchange=\"IsValidDate(this)\"></div>";
		cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"DonationAmount" + ExpenseCount + "\" id=\"DonationAmount" + ExpenseCount + "\" oninput=\"ChangeDonationAmount()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('MonDonationRowCount').value = ExpenseCount;
		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeleteMonDonationRow(){

		var ExpenseCount = document.getElementById("MonDonationRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("donation-income");

		var row = table.rows[ExpenseCount];
		row.cells[3].children[0].children[0].children[1].value=0;

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
		cell3.innerHTML = "<div class=\"form-group\"><input type=\"date\" min='2017-07-01' max='2018-06-30' class=\"form-control\" name=\"NonMonDonationDate" + ExpenseCount + "\" id=\"NonMonDonationDate" + ExpenseCount + "\" onchange=\"IsValidDate(this)\"></div>";

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

		document.getElementById("OtherOfficeExpenseTotal").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("OtherOfficeIncomeTotal").innerHTML = IncomeTotal.toFixed(2);

		document.getElementById("SumOtherIncome").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumOtherIncomeP").value = IncomeTotal.toFixed(2);
		document.getElementById("SumOtherExpense").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("SumOtherExpenseP").value = ExpenseTotal.toFixed(2);

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

		document.getElementById("InternationalEventIncomeTotal").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("InternationalEventExpenseTotal").innerHTML = ExpenseTotal.toFixed(2);

		document.getElementById("SumInternationalEventIncome").innerHTML = IncomeTotal.toFixed(2);
		document.getElementById("SumInternationalEventIncomeP").value = IncomeTotal.toFixed(2);
		document.getElementById("SumInternationalEventExpense").innerHTML = ExpenseTotal.toFixed(2);
		document.getElementById("SumInternationalEventExpenseP").value = ExpenseTotal.toFixed(2);

		ReCalculateSummaryTotal();

	}

	function DeleteInternationalEventRow(){

		var ExpenseCount = document.getElementById("InternationalEventRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("international_events");

		var row = table.rows[ExpenseCount];
		row.cells[1].children[0].children[0].children[1].value=0;
		row.cells[2].children[0].children[0].children[1].value=0;

		document.getElementById("international_events").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeDonationAmount();

		document.getElementById('InternationalEventRowCount').value = ExpenseCount;
	}

	function AddInternationalEventRow(){

		var ExpenseCount = document.getElementById("InternationalEventRowCount").value;

		var table=document.getElementById("international_events");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"InternationalEventDesc" + ExpenseCount + "\" id=\"InternationalEventDesc" + ExpenseCount + "\"></div>";
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"InternationalEventIncome" + ExpenseCount + "\" id=\"InternationalEventIncome" + ExpenseCount + "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"InternationalEventExpense" + ExpenseCount + "\" id=\"InternationalEventExpense" + ExpenseCount + "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('InternationalEventRowCount').value = ExpenseCount;
		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function ChangeReRegistrationExpense(){

		var ReRegistrationFee=0;

		ReRegistrationFee = Number(document.getElementById("AnnualRegistrationFee").value);

		document.getElementById("SumChapterReRegistrationExpense").innerHTML = ReRegistrationFee.toFixed(2);
		document.getElementById("SumChapterReRegistrationExpenseP").value = ReRegistrationFee.toFixed(2);

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
		var SumInternationalEventIncome=0;

		var SumMonetaryDonationIncome=0;
		var SumChapterReRegistrationExpense=0;

		var TreasuryBalance=0;
		var TreasuryBalanceNow=0;

		SumMeetingRoomExpense = Number(document.getElementById("SumMeetingRoomExpense").value);
		SumMeetingRoomExpenseP = Number(document.getElementById("SumMeetingRoomExpenseP").value);

		SumMembershipDuesIncome = Number(document.getElementById("SumMembershipDuesIncome").value);
		SumMembershipDuesIncomeP = Number(document.getElementById("SumMembershipDuesIncomeP").value);

		SumTotalChildrensRoomExpense=Number(document.getElementById("SumTotalChildrensRoomExpense").value);
		SumTotalChildrensRoomExpenseP=Number(document.getElementById("SumTotalChildrensRoomExpenseP").value);

		ServiceIncomeTotal = Number(document.getElementById("SumServiceProjectIncome").value);
		ServiceIncomeTotalP = Number(document.getElementById("SumServiceProjectIncomeP").value);

		ServiceExpenseTotal = Number(document.getElementById("SumTotalServiceProjectExpense").value);
		ServiceExpenseTotalP = Number(document.getElementById("SumTotalServiceProjectExpenseP").value);

		SumPartyIncome = Number(document.getElementById("SumPartyIncome").value);
		SumPartyIncomeP = Number(document.getElementById("SumPartyIncomeP").value);

		SumPartyExpense = Number(document.getElementById("SumPartyExpense").value);
		SumPartyExpenseP = Number(document.getElementById("SumPartyExpenseP").value);

		SumOtherIncome = Number(document.getElementById("SumOtherIncome").value);
		SumOtherIncomeP = Number(document.getElementById("SumOtherIncomeP").value);

		SumOtherExpense = Number(document.getElementById("SumOtherExpense").value);
		SumOtherExpenseP = Number(document.getElementById("SumOtherExpenseP").value);

		SumOperatingExpense = Number(document.getElementById("SumOperatingExpense").value);
		SumOperatingExpenseP = Number(document.getElementById("SumOperatingExpenseP").value);

		SumInternationalEventExpense = Number(document.getElementById("SumInternationalEventExpense").value);
		SumInternationalEventExpenseP = Number(document.getElementById("SumInternationalEventExpenseP").value);

		SumInternationalEventIncome = Number(document.getElementById("SumInternationalEventIncome").value);
		SumInternationalEventIncomeP = Number(document.getElementById("SumInternationalEventIncomeP").value);

		SumMonetaryDonationIncome = Number(document.getElementById("SumMonetaryDonationIncome").value);
		SumMonetaryDonationIncomeP = Number(document.getElementById("SumMonetaryDonationIncomeP").value);

		SumChapterReRegistrationExpense = Number(document.getElementById("SumChapterReRegistrationExpense").value);
		SumChapterReRegistrationExpenseP = Number(document.getElementById("SumChapterReRegistrationExpenseP").value);


		SumTotalExpense = SumTotalChildrensRoomExpense + SumMeetingRoomExpense + ServiceExpenseTotal + SumOtherExpense + SumPartyExpense + SumOperatingExpense + SumInternationalEventExpense + SumChapterReRegistrationExpense;


		SumTotalExpenseP = SumTotalChildrensRoomExpenseP + SumMeetingRoomExpenseP + ServiceExpenseTotalP + SumOtherExpenseP + SumPartyExpenseP + SumOperatingExpenseP + SumInternationalEventExpenseP + SumChapterReRegistrationExpenseP;

		SumTotalIncome = ServiceIncomeTotal + SumOtherIncome + SumPartyIncome + SumMembershipDuesIncome + SumInternationalEventIncome + SumMonetaryDonationIncome ;

		SumTotalIncomeP = ServiceIncomeTotalP + SumOtherIncomeP + SumPartyIncomeP + SumMembershipDuesIncomeP + SumInternationalEventIncomeP + SumMonetaryDonationIncomeP ;

		TreasuryBalance = Number(document.getElementById("SumAmountReservedFromPreviousYear").value);
		TreasuryBalanceP = Number(document.getElementById("SumAmountReservedFromPreviousYearP").value);

		document.getElementById("SumTotalExpense").innerHTML = SumTotalExpense.toFixed(2);
		document.getElementById("SumTotalExpense").innerHTML = SumTotalExpenseP.toFixed(2);

		document.getElementById("SumTotalIncome").innerHTML = SumTotalIncome.toFixed(2);
		document.getElementById("SumTotalIncome").innerHTML = SumTotalIncomeP.toFixed(2);

		TreasuryBalanceNow = TreasuryBalance - SumTotalExpense + SumTotalIncome;
		TreasuryBalanceNowP = TreasuryBalanceP - SumTotalExpenseP + SumTotalIncomeP;

		document.getElementById("TreasuryBalanceNow").innerHTML = TreasuryBalanceNowP.toFixed(2);
		document.getElementById("TreasuryBalanceNowP").value = TreasuryBalanceNowP.toFixed(2);

		document.getElementById("SumTreasuryBalanceNow").innerHTML = TreasuryBalanceNowP.toFixed(2);

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
		cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"OtherOfficeExpenses" + ExpenseCount + "\"  id=\"OtherOfficeExpenses" + ExpenseCount + "\"  oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"OtherOfficeIncome" + ExpenseCount + "\" id=\"OtherOfficeIncome" + ExpenseCount + "\"  oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";


		ExpenseCount++;
		document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;

		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeleteOtherOfficeExpenseRow(){

		var ExpenseCount = document.getElementById("OtherOfficeExpenseRowCount").value;
		if(ExpenseCount>1){

			//clear the values to make sure they aren't error conditions
			var table=document.getElementById("other-office-expenses");

			var row = table.rows[ExpenseCount];
			row.cells[1].children[0].children[0].children[1].value=0;
			row.cells[2].children[0].children[0].children[1].value=0;

			document.getElementById("other-office-expenses").deleteRow(ExpenseCount);

			ExpenseCount--; //We removed a row so lower this
			ChangeOtherOfficeExpenses();

			document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;

		}

	}

	function TreasuryBalanceChange(){
		var TreasuryBalance = Number(document.getElementById("AmountReservedFromLastYear").value);

		document.getElementById("SumAmountReservedFromPreviousYear").innerHTML = TreasuryBalance.toFixed(2);
		document.getElementById("SumAmountReservedFromPreviousYearP").value = TreasuryBalance.toFixed(2);

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
		TotalFees = Number(BankBalanceNow - PaymentTotal + DepositTotal).toFixed(2);
		document.getElementById("ReconciledBankBalance").innerHTML = TotalFees;
		TreasuryBalanceNow = Number(document.getElementById("TreasuryBalanceNow").value).toFixed(2);


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

		var table=document.getElementById("bank-rec");
		var row = table.insertRow(-1);

		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);


		cell1.innerHTML = "<div class=\"form-group\"><input type=\"date\" class=\"form-control\" name=\"BankRecDate" + ExpenseCount + "\" id=\"BankRecDate" + ExpenseCount + "\" onchange=\"IsValidDate(this)\"></div>";
		cell2.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"BankRecCheckNo" + ExpenseCount + "\" id=\"BankRecCheckNo" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\"></div>";
		cell3.innerHTML = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"BankRecDesc" + ExpenseCount + "\" id=\"BankRecDesc" + ExpenseCount + "\"></div>";
		cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"BankRecPaymentAmount" + ExpenseCount + "\" id=\"BankRecPaymentAmount" + ExpenseCount + "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
		cell5.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"BankRecDepositAmount" + ExpenseCount + "\" id=\"BankRecDepositAmount" + ExpenseCount + "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

		ExpenseCount++;
		document.getElementById('BankRecRowCount').value = ExpenseCount;
		$(".txt-num").keypress(function (e) {
			var key = e.charCode || e.keyCode || 0;
			// only numbers
			if (key < 48 || key > 58) {
				return false;
			}
		});
	}

	function DeleteBankRecRow(){

		var ExpenseCount = document.getElementById("BankRecRowCount").value;

		//clear the values to make sure they aren't error conditions
		var table=document.getElementById("bank-rec");

		var row = table.rows[ExpenseCount];
		row.cells[3].children[0].children[0].children[1].value=0;
		row.cells[4].children[0].children[0].children[1].value=0;

		document.getElementById("bank-rec").deleteRow(ExpenseCount);

		ExpenseCount--; //We removed a row so lower this
		ChangeBankRec();

		document.getElementById('BankRecRowCount').value = ExpenseCount;
	}

	function ToggleReceiveCompensationExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("ReceiveCompensationYes").checked){
			//document.getElementById("ReceiveCompensationExplanation").required=true;
			$('#ReceiveCompensationExplanation').addClass('tx-cls');
			document.getElementById("divReceiveCompensationExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("ReceiveCompensationExplanation").required=false;
			$('#ReceiveCompensationExplanation').removeClass('tx-cls');
			document.getElementById("divReceiveCompensationExplanation").style.display = 'none';
		}
	}

	function ToggleFinancialBenefitExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("FinancialBenefitYes").checked){
			//document.getElementById("FinancialBenefitExplanation").required=true;
			$('#FinancialBenefitExplanation').addClass('tx-cls');
			document.getElementById("divFinancialBenefitExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("FinancialBenefitExplanation").required=false;
			$('#FinancialBenefitExplanation').removeClass('tx-cls');
			document.getElementById("divFinancialBenefitExplanation").style.display = 'none';
		}

	}

	function ToggleInfluencePoliticalExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("InfluencePoliticalYes").checked){
			//document.getElementById("InfluencePoliticalExplanation").required=true;
			$('#InfluencePoliticalExplanation').addClass('tx-cls');
			document.getElementById("divInfluencePoliticalExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("InfluencePoliticalExplanation").required=false;
			$('#InfluencePoliticalExplanation').removeClass('tx-cls');
			document.getElementById("divInfluencePoliticalExplanation").style.display = 'none';
		}
	}

	function ToggleVoteAllActivitiesExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("VoteAllActivitiesNo").checked){
			//document.getElementById("VoteAllActivitiesExplanation").required=true;
			$('#VoteAllActivitiesExplanation').addClass('tx-cls');
			document.getElementById("divVoteAllActivitiesExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("VoteAllActivitiesExplanation").required=false;
			$('#VoteAllActivitiesExplanation').removeClass('tx-cls');
			document.getElementById("divVoteAllActivitiesExplanation").style.display = 'none';
		}
	}

	function ToggleBoughtPinsExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("BoughtPinsNo").checked){
			//document.getElementById("BoughtPinsExplanation").required=true;
			$('#BoughtPinsExplanation').addClass('tx-cls');
			document.getElementById("divBoughtPinsExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("BoughtPinsExplanation").required=false;
			$('#BoughtPinsExplanation').removeClass('tx-cls');
			document.getElementById("divBoughtPinsExplanation").style.display = 'none';
		}
	}

	function ToggleBoughtMerchExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("BoughtMerchNo").checked){
			//document.getElementById("BoughtMerchExplanation").required=true;
			$('#BoughtMerchExplanation').addClass('tx-cls');
			document.getElementById("divBoughtMerchExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("BoughtMerchExplanation").required=false;
			$('#BoughtMerchExplanation').removeClass('tx-cls');
			document.getElementById("divBoughtMerchExplanation").style.display = 'none';
		}
	}

	function ToggleOfferedMerchExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("OfferedMerchNo").checked){
			//document.getElementById("OfferedMerchExplanation").required=true;
			$('#OfferedMerchExplanation').addClass('tx-cls');
			document.getElementById("divOfferedMerchExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("OfferedMerchExplanation").required=false;
			$('#OfferedMerchExplanation').removeClass('tx-cls');
			document.getElementById("divOfferedMerchExplanation").style.display = 'none';
		}
	}

	function ToggleByLawsAvailableExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("ByLawsAvailableNo").checked){
			//document.getElementById("ByLawsAvailableExplanation").required=true;
			$('#ByLawsAvailableExplanation').addClass('tx-cls');
			document.getElementById("divByLawsAvailableExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("ByLawsAvailableExplanation").required=false;
			$('#ByLawsAvailableExplanation').removeClass('tx-cls');
			document.getElementById("divByLawsAvailableExplanation").style.display = 'none';
		}
	}

	function TogglePlaygroupsExplanation(NoGroupsRun){
		// Did they say no, if so, we need to mark the explanation field as required
		if (NoGroupsRun){
			//document.getElementById("PlaygroupsExplanation").required=true;
			$('#PlaygroupsExplanation').addClass('tx-cls');
			document.getElementById("PlaygroupsYesByAge").checked=false;
			document.getElementById("PlaygroupsYesMultiAge").checked=false;
			document.getElementById("divPlaygroupsExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("PlaygroupsExplanation").required=false;
			$('#PlaygroupsExplanation').removeClass('tx-cls');
			document.getElementById("PlaygroupsNo").checked = false;
			document.getElementById("divPlaygroupsExplanation").style.display = 'none';
		}
	}

	function ToggleChildOutingsExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("ChildOutingsNo").checked){
			//document.getElementById("ChildOutingsExplanation").required=true;
			$('#ChildOutingsExplanation').addClass('tx-cls');
			document.getElementById("divChildOutingsExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("ChildOutingsExplanation").required=false;
			$('#ChildOutingsExplanation').removeClass('tx-cls');
			document.getElementById("divChildOutingsExplanation").style.display = 'none';
		}
	}

	function ToggleMotherOutingsExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("MotherOutingsNo").checked){
			//document.getElementById("MotherOutingsExplanation").required=true;
			$('#MotherOutingsExplanation').addClass('tx-cls');
			document.getElementById("divMotherOutingsExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("MotherOutingsExplanation").required=false;
			$('#MotherOutingsExplanation').removeClass('tx-cls');
			document.getElementById("divMotherOutingsExplanation").style.display = 'none';
		}
	}

	function ToggleMeetingSpeakersExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("MeetingSpeakersNo").checked){
			//document.getElementById("MeetingSpeakersExplanation").required=true;
			$('#MeetingSpeakersExplanation').addClass('tx-cls');
			document.getElementById("divMeetingSpeakersExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("MeetingSpeakersExplanation").required=false;
			$('#MeetingSpeakersExplanation').removeClass('tx-cls');
			document.getElementById("divMeetingSpeakersExplanation").style.display = 'none';
		}
	}

	function ToggleActivityOtherExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("ActivityOther").checked){
			//document.getElementById("ActivityOtherExplanation").required=true;
			$('#ActivityOtherExplanation').addClass('tx-cls');
			document.getElementById("divActivityOtherExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("ActivityOtherExplanation").required=false;
			$('#ActivityOtherExplanation').removeClass('tx-cls');
			document.getElementById("divActivityOtherExplanation").style.display = 'none';
		}
	}

	function ToggleContributionsNotRegNPExplanation(){
		// Did they say yes, if so, we need to mark the explanation field as required
		if (document.getElementById("ContributionsNotRegNPYes").checked){
			//document.getElementById("ContributionsNotRegNPExplanation").required=true;
			$('#ContributionsNotRegNPExplanation').addClass('tx-cls');
			document.getElementById("divContributionsNotRegNPExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("ContributionsNotRegNPExplanation").required=false;
			$('#ContributionsNotRegNPExplanation').removeClass('tx-cls');
			document.getElementById("divContributionsNotRegNPExplanation").style.display = 'none';
		}
	}

	function TogglePerformServiceProjectExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("PerformServiceProjectNo").checked){
			//document.getElementById("PerformServiceProjectExplanation").required=true;
			$('#PerformServiceProjectExplanation').addClass('tx-cls');
			document.getElementById("divPerformServiceProjectExplanation").style.display = 'block';
		}
		else{
			//document.getElementById("PerformServiceProjectExplanation").required=false;
			$('#PerformServiceProjectExplanation').removeClass('tx-cls');
			document.getElementById("divPerformServiceProjectExplanation").style.display = 'none';
		}
	}

	function ToggleFileIRSExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("FileIRSNo").checked){
			$('#990NFile').hide();
			//document.getElementById("FileIRSExplanation").required=true;
			$('#FileIRSExplanation').addClass('tx-cls');
			document.getElementById("divFileIRSExplanation").style.display = 'block';

		}
		else if (document.getElementById("FileIRSYes").checked){
			$('#990NFile').show();
			//document.getElementById("FileIRSExplanation").required=true;
			$('#FileIRSExplanation').removeClass('tx-cls');
			document.getElementById("divFileIRSExplanation").style.display = 'none';

		}
		else{
			$('#990NFile').hide();
			//document.getElementById("FileIRSExplanation").required=false;
			$('#FileIRSExplanation').removeClass('tx-cls');
			document.getElementById("divFileIRSExplanation").style.display = 'none';
		}
	}

	function ToggleBankStatementIncludedExplanation(){
		// Did they say no, if so, we need to mark the explanation field as required
		if (document.getElementById("BankStatementIncludedNo").checked){
			$('#BankFile').hide();
			//document.getElementById("BankStatementIncludedExplanation").required=true;
			//document.getElementById("WheresTheMoney").required=true;
			$('#BankStatementIncludedExplanation').addClass('tx-cls');
			$('#WheresTheMoney').addClass('tx-cls');
			document.getElementById("divBankStatementIncludedExplanation").style.display = 'block';

		}
		else if (document.getElementById("BankStatementIncludedYes").checked){
			$('#BankFile').show();
			//document.getElementById("BankStatementIncludedExplanation").required=false;
			//document.getElementById("WheresTheMoney").required=false;
			$('#BankStatementIncludedExplanation').removeClass('tx-cls');
			$('#WheresTheMoney').removeClass('tx-cls');
			document.getElementById("divBankStatementIncludedExplanation").style.display = 'none';
		}
		else{
			$('#BankFile').hide();
			//document.getElementById("BankStatementIncludedExplanation").required=false;
			//document.getElementById("WheresTheMoney").required=false;
			$('#BankStatementIncludedExplanation').removeClass('tx-cls');
			$('#WheresTheMoney').removeClass('tx-cls');
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

	function ShowSelectedBankFileInfo(){

		var files = document.getElementById("BankStatementFiles").files;

		for (var i = 0; i < files.length; i++)
		{
		 alert(files[i].name);
		}

	}

	function RemoveRequired(){

		var x = document.forms[0];
		var i;
		for (i = 0; i < x.length; i++) {
			x.elements[i].required = false;
		}
	}

	function SetReadOnly(){

		var x = document.forms[1];
		console.log(x);
		var i;
		for (i = 0; i < x.length; i++) {
			if(x.elements[i].type!="button" && !hasClass(x.elements[i], "btn") && x.elements[i].type!="hidden"){
				console.log(x.elements[i]);
				x.elements[i].readOnly = true;
				x.elements[i].disabled = true;
			}
		}
	}

	function hasClass(element, cls) {
		return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
	}

	function EnsureFilesWereSubmitted(){

		//Check for roster, it must ALWAYS be submitted
		if(document.getElementById('RosterPath').value=="" && document.getElementById('RosterFile').files.length <= 0){
			//Roster file was not submitted
			alert("Your chapter's roster was not attached in CHAPTER DUES.  This is a required file for all reports.  Please attach the file and submit again.");
			return false;
		}

		//Check for 990N, it must be submitted unless they said it was
		if(document.getElementById('FileIRSYes').checked && document.getElementById('990NPath').value=="" && document.getElementById('990NFiling').files.length <= 0){
			//Roster file was not submitted
			alert("Your chapter's 990N filing confirmation was not attached in TAX EXEMPT & CHAPTER QUESTIONS but you indicated the file was attached.  Please attach the file and submit again.");
			return false;
		}

		//Check for bank statement, it must be submitted unless they said it was
		if(document.getElementById('BankStatementIncludedYes').checked && document.getElementById('BankStatementPath').value=="" && document.getElementById('BankStatements').files.length <= 0){
			//Roster file was not submitted
			alert("Your chapter's bank statement was not attached in TAX EXEMPT & CHAPTER QUESTIONS but you indicated the file was attached.  Please attach the file and submit again.");
			return false;
		}

		return true;
	}

	function LoadSteps(){
		UpdateCalculatedValues();
		if(<?php if($submitted) echo "1"; else echo "0"; ?>){
			SetReadOnly();
		}

		ToggleReceiveCompensationExplanation();
		ToggleFinancialBenefitExplanation();
		ToggleInfluencePoliticalExplanation();
		ToggleVoteAllActivitiesExplanation();
		ToggleBoughtPinsExplanation();
		ToggleBoughtMerchExplanation();
		ToggleOfferedMerchExplanation();
		ToggleByLawsAvailableExplanation();
		TogglePlaygroupsExplanation();
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
</script>
@endsection
