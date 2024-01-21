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
                <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer in Final Review section before continuing to prevent errors.</span>
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
					<div class="accordion-header js-accordion-header">CHAPTER DUES</div>
					<div class="accordion-body js-accordion-body">
						<section>
                            Did your chapter change dues this year?&nbsp;&nbsp;&nbsp;
                            <strong>{{ $financial_report_array ['changed_dues'] == 1 ? 'YES' : 'NO' }}</strong><br>
                            Did your chapter charge different amounts for new and returning members?&nbsp;&nbsp;&nbsp;
                            <strong>{{ $financial_report_array ['different_dues'] == 1 ? 'YES' : 'NO' }}</strong><br>
                            Did your chapter have any members who didn't pay full dues?&nbsp;&nbsp;&nbsp;
                            <strong>{{ $financial_report_array ['not_all_full_dues'] == 1 ? 'YES' : 'NO' }}</strong><br>
                            <br>
                            <style>
                                .flex-container {
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 0px;
                                    width: 75%;
                                    overflow-x: auto;
                                }

                                .flex-item {
                                    flex: 0 0 calc(48% - 10px);
                                    box-sizing: border-box;
                                }
                            </style>

                            <div class="flex-container">
                                @if ($financial_report_array['changed_dues'] != 1)
                                    <div class="flex-item">
                                        New Members:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_new_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member']) }}</strong>
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member']) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_renewed_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member_renewal']) }}</strong>
                                        </div>
                                    @endif
                                @endif

                                @if ($financial_report_array['changed_dues'] == 1)
                                    <div class="flex-item">
                                        New Members (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_new_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member']) }}</strong>
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member']) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members (OLD dues amount): <strong>{{ $financial_report_array['total_renewed_members'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues (OLD dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member_renewal']) }}</strong>
                                        </div>
                                    @endif

                                    <div class="flex-item">
                                        New Members (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_new_members_changed_dues'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] != 1)
                                        <div class="flex-item">
                                            Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member_new_changed']) }}</strong>
                                        </div>
                                    @endif
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            New Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member_new_changed']) }}</strong>
                                        </div>
                                    @endif
                                    <div class="flex-item">
                                        Renewed Members (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_renewed_members_changed_dues'] }}</strong>
                                    </div>
                                    @if ($financial_report_array['different_dues'] == 1)
                                        <div class="flex-item">
                                            Renewal Dues (NEW dues amount):&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['dues_per_member_renewal_changed']) }}</strong>
                                        </div>
                                    @endif
                                @endif

                                @if ($financial_report_array['not_all_full_dues'] == 1)
                                    <div class="flex-item">
                                        Members Who Paid No Dues:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['members_who_paid_no_dues'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        &nbsp;&nbsp;&nbsp;
                                    </div>
                                    <div class="flex-item">
                                        Members Who Paid Partial Dues:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['members_who_paid_partial_dues'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Partial Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['total_partial_fees_collected']) }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Assiciate Members:&nbsp;&nbsp;&nbsp;<strong>{{ $financial_report_array['total_associate_members'] }}</strong>
                                    </div>
                                    <div class="flex-item">
                                        Associate Dues:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['associate_member_fee']) }}</strong>
                                    </div>
                                @endif
                            </div>

                            <?php
                                $newMembers = $financial_report_array['total_new_members'] * $financial_report_array['dues_per_member'];
                                $renewalMembers = $financial_report_array['total_renewed_members'] * $financial_report_array['dues_per_member'];
                                $renewMembersOld = $financial_report_array['total_renewed_members'] * $financial_report_array['dues_per_member_renewal'];
                                $newMembersNew = $financial_report_array['total_new_members_changed_dues'] * $financial_report_array['dues_per_member_new_changed'];
                                $renewMembersNew = $financial_report_array['total_renewed_members_changed_dues'] * $financial_report_array['dues_per_member_renewal_changed'];
                                $partialMembers = $financial_report_array['members_who_paid_partial_dues'] * $financial_report_array['total_partial_fees_collected'];
                                $associateMembers = $financial_report_array['total_associate_members'] * $financial_report_array['associate_member_fee'];

                                $totalMembers = $financial_report_array['total_new_members'] +$financial_report_array['total_renewed_members'] + $financial_report_array['total_new_members_changed_dues'] + $financial_report_array['total_renewed_members_changed_dues']
                                        + $financial_report_array['members_who_paid_partial_dues'] + $financial_report_array['members_who_paid_partial_dues'] + $financial_report_array['total_associate_members'];
                                $totalDues = $newMembers + $renewalMembers + $renewMembersOld + $newMembersNew + $renewMembersNew + $partialMembers + $associateMembers;
                            ?>

                        <br><strong>Total Members:&nbsp;&nbsp;&nbsp;{{ $totalMembers }}</strong></td><br>
                            <strong>Total Dues Collected:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalDues) }}</strong></td><br>
						<hr>
						<!-- start:report_review -->
						<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
							<div class="col-md-12">
								<h2>Annual Report Review</h2>
                            </div>
							<div class="form-row">
								{{-- <div class="col-md-12 mar_bot_20" id="RosterBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['roster_path']) echo "style=\"display: none;\"";} ?>>
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
								</div> --}}
								<div class="clearfix"></div>

                                <div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
                                    <div class="form-inline">
                                        <label style="display: block;">Excel roster attached and complete:<span class="field-required">*</span></label>
                                        <select id="checkRosterAttached" name="checkRosterAttached" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                            <option value="" {{ is_null($financial_report_array->check_roster_attached) ? 'selected' : '' }} disabled>Please Select</option>
                                            <option value="0" {{$financial_report_array->check_roster_attached === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$financial_report_array->check_roster_attached == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
                                    <div class="form-inline">
                                        <label style="display: block;">Number of members listed, dues received, and renewal paid "seem right":<span class="field-required">*</span></label>
                                        <select id="checkRenewalSeemsRight" name="checkRenewalSeemsRight" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                            <option value="" {{ is_null($financial_report_array->check_renewal_seems_right) ? 'selected' : '' }} disabled>Please Select</option>
                                            <option value="0" {{$financial_report_array->check_renewal_seems_right === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$financial_report_array->check_renewal_seems_right == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step1_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" rows="8" name="Step1_Log" id="Step1_Log" readonly style="width:100%"><?php echo $financial_report_array['step_1_notes_log']; ?></textarea>
									</div>
								</div>
								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step1_Note">Note:</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="3" name="Step1_Note" id="Step1_Note" oninput="EnableNoteLogButton(1)" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
					<div class="accordion-header js-accordion-header">MONTHLY MEETING EXPENSES</div>
					<div class="accordion-body js-accordion-body">
						<section>
                            Meeting Room Fees:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['manditory_meeting_fees_paid']) }}</strong><br>
                            Voluntary Donations Paid:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['voluntary_donations_paid']) }}</strong><br>
                            <strong>Total Meeting Room Expenses:&nbsp;&nbsp;&nbsp;
                                {{ '$'.sprintf('%0.2f',$financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid']) }}</strong><br>
                            <br>
                            Paid Babysitter Expense:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['paid_baby_sitters']) }}</strong><br>
                            <br>
                            Children's Room Miscellaneous:
                            <table width="75%" style="border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #333;">
                                        <td>Description</td>
                                        <td>Supplies</td>
                                        <td>Other Expenses</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $childrens_room = null;
                                        $totalChildrenSupplies = 0;
                                        $totalChildrenOther = 0;

                                        if (isset($financial_report_array['childrens_room_expenses']) && $financial_report_array['childrens_room_expenses'] !== null) {
                                            $blobData = base64_decode($financial_report_array['childrens_room_expenses']);
                                            $childrens_room = unserialize($blobData);

                                            if ($childrens_room === false) {
                                                echo "Error: Failed to unserialize data.";
                                            } else {
                                                if (is_array($childrens_room) && count($childrens_room) > 0) {
                                                    foreach ($childrens_room as $row) {
                                                        echo "<tr>";
                                                        echo "<td>" . $row['childrens_room_desc'] . "</td>";
                                                        echo "<td>" . ($row['childrens_room_supplies'] ? "$" . number_format($row['childrens_room_supplies'], 2) : "$0.00") . "</td>";
                                                        echo "<td>" . ($row['childrens_room_other'] ? "$" . number_format($row['childrens_room_other'], 2) : "$0.00") . "</td>";
                                                        echo "</tr>";

                                                        $totalChildrenSupplies += floatval($row['childrens_room_supplies']);
                                                        $totalChildrenOther += floatval($row['childrens_room_other']);
                                                    }

                                                    // Total row
                                                    echo "<tr style='border-top: 1px solid #333;'>";
                                                    echo "<td><strong>Total</strong></td>";
                                                    echo "<td><strong>$" . number_format($totalChildrenSupplies, 2) . "</strong></td>";
                                                    echo "<td><strong>$" . number_format($totalChildrenOther, 2) . "</strong></td>";
                                                    echo "</tr>";
                                                } else {
                                                    echo "<tr style='border-top: 1px solid #333;'>";
                                                    echo "<td colspan='3'>No Children's Room Expenses Entered.</td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='3'>No Children's Room Expenses Entered.</td>";
                                            echo "</tr>";
                                        }

                                        $totalChildrensRoomExpenses = $totalChildrenSupplies + $totalChildrenOther;
                                        ?>

                                </tbody>
                                </table>
                            <br>
                            <strong>Total Children's Room Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses) }}</strong><br>
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
												<label for="Step2_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
											</div>
											<div class="col-md-12">
                                                <textarea class="form-control" rows="8" name="Step2_Log" id="Step2_Log" readonly style="width:100%"><?php echo $financial_report_array['step_2_notes_log']; ?></textarea>
                                            </div>
										</div>

										<div class="col-md-12 mar_bot_20">
											<div class="col-md-12">
												<label for="Step2_Note">Note:</label>
											</div>
											<div class="col-md-12">
												<textarea class="form-control" style="width:100%" rows="3" name="Step2_Note" id="Step2_Note" oninput="EnableNoteLogButton(2)" ></textarea>
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
					<div class="accordion-header js-accordion-header">SERVICE PROJECTS</div>
					<div class="accordion-body js-accordion-body">
					<section>
                        <table width="100%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Project Description</td>
                                    <td>Project Income</td>
                                    <td>Supplies/Expenses</td>
                                    <td>Charity Donation</td>
                                    <td>M2M Donation</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $service_projects = null;
                                    $totalServiceIncome = 0;
                                    $totalServiceSupplies = 0;
                                    $totalServiceCharity = 0;
                                    $totalServiceM2M = 0;

                                    if (isset($financial_report_array['service_project_array'])) {
                                        $blobData = base64_decode($financial_report_array['service_project_array']);
                                        $service_projects = unserialize($blobData);

                                        if ($service_projects === false) {
                                            echo "Error: Failed to unserialize data.";
                                        } else {
                                            foreach ($service_projects as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $row['service_project_desc'] . "</td>";
                                                echo "<td>" . ($row['service_project_income'] ? "$" . number_format($row['service_project_income'], 2) : "$0.00") . "</td>";
                                                echo "<td>" . ($row['service_project_supplies'] ? "$" . number_format($row['service_project_supplies'], 2) : "$0.00") . "</td>";
                                                echo "<td>" . ($row['service_project_charity'] ? "$" . number_format($row['service_project_charity'], 2) : "$0.00") . "</td>";
                                                echo "<td>" . ($row['service_project_m2m'] ? "$" . number_format($row['service_project_m2m'], 2) : "$0.00") . "</td>";
                                                echo "</tr>";

                                                $totalServiceIncome += floatval($row['service_project_income']);
                                                $totalServiceSupplies += floatval($row['service_project_supplies']);
                                                $totalServiceCharity += floatval($row['service_project_charity']);
                                                $totalServiceM2M += floatval($row['service_project_m2m']);
                                            }
                                             // Total row
                                             echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td><strong>Total</strong></td>";
                                    echo "<td><strong>$" . number_format($totalServiceIncome, 2) . "</strong></td>";
                                    echo "<td><strong>$" . number_format($totalServiceSupplies, 2) . "</strong></td>";
                                    echo "<td><strong>$" . number_format($totalServiceCharity, 2) . "</strong></td>";
                                    echo "<td><strong>$" . number_format($totalServiceM2M, 2) . "</strong></td>";
                                    echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='5'>No Service Projects Entered.</td>";
                                        echo "</tr>";
                                    }
                                    $totalServiceProjectExpenses = $totalServiceSupplies + $totalServiceCharity + $totalServiceM2M;
                                    ?>
                            </tbody>
                        </table>
                        <br>
                        <strong>Total Service Project Income:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalServiceIncome) }}</strong><br>
                        <strong>Total Service Project Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalServiceProjectExpenses) }}</strong><br>
					<hr>
					<!-- start:report_review -->
						<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
							<div class="col-md-12">
								<h2>Annual Report Review</h2>
							</div>
							<div class="form-row">
                                <div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
                                    <div class="form-inline">
                                        <label style="display: block;">Minimum of one service project completed:<span class="field-required">*</span></label>
                                        <select id="checkServiceProject" name="checkServiceProject" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                            <option value="" {{ is_null($financial_report_array->check_minimum_service_project) ? 'selected' : '' }} disabled>Please Select</option>
                                            <option value="0" {{$financial_report_array->check_minimum_service_project === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$financial_report_array->check_minimum_service_project == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                    <div class="col-md-6 mar_bot_20">
                                        <div class="col-md-12">
                                        <div class="form-inline">
                                            <label style="display: block;">Made a donation to the M2M Fund:<span class="field-required">*</span></label>
                                            <select id="checkM2MDonation" name="checkM2MDonation" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                                <option value="" {{ is_null($financial_report_array->check_m2m_donation) ? 'selected' : '' }} disabled>Please Select</option>
                                                <option value="0" {{$financial_report_array->check_m2m_donation === 0 ? 'selected' : ''}}>No</option>
                                                <option value="1" {{$financial_report_array->check_m2m_donation == 1 ? 'selected' : ''}}>Yes</option>
                                            </select>
                                        </div>
                                        </div>
                                    </div>
								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step3_Log"><strong>Reviewer Notes Logged for this Section (not visible to chapter):</strong></label>
									</div>
									<div class="col-md-12">
                                        <textarea class="form-control" rows="8" name="Step3_Log" id="Step3_Log" readonly style="width:100%"><?php echo $financial_report_array['step_3_notes_log']; ?></textarea>
									</div>
								</div>

								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step3_Note">Enter New Note:</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(3)" name="Step3_Note" id="Step3_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
				<div class="accordion-header js-accordion-header">PARTIES & MEMBER BENEFITS</div>
				<div class="accordion-body js-accordion-body">
				<section>
                    <table width="75%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>Paty/Member Benefit Description</td>
                                <td>Benefit Income</td>
                                <td>Benefit Expenses</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $party_expenses = null;
                                $totalPartyIncome = 0;
                                $totalPartyExpense = 0;

                                if (isset($financial_report_array['party_expense_array'])) {
                                    $blobData = base64_decode($financial_report_array['party_expense_array']);
                                    $party_expenses = unserialize($blobData);

                                    if ($party_expenses === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        foreach ($party_expenses as $row) {
                                            echo "<tr>";
                                            echo "<td>" . $row['party_expense_desc'] . "</td>";
                                            echo "<td>" . ($row['party_expense_income'] ? "$" . number_format($row['party_expense_income'], 2) : "$0.00") . "</td>";
                                            echo "<td>" . ($row['party_expense_expenses'] ? "$" . number_format($row['party_expense_expenses'], 2) : "$0.00") . "</td>";
                                            echo "</tr>";

                                            $totalPartyIncome += floatval($row['party_expense_income']);
                                            $totalPartyExpense += floatval($row['party_expense_expenses']);;
                                        }
                                        // Total row
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td><strong>Total</strong></td>";
                                        echo "<td><strong>$" . number_format($totalPartyIncome, 2) . "</strong></td>";
                                        echo "<td><strong>$" . number_format($totalPartyExpense, 2) . "</strong></td>";
                                        echo "</tr>";

                                        $partyPercentage = ($totalPartyExpense - $totalPartyIncome) / $totalDues;
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='3'>No Parties or Member Benefits Entered.</td>";
                                    echo "</tr>";

                                    $partyPercentage = 0;
                                }
                            ?>
                        </tbody>
                    </table>
                    <br>
                    <strong>Total Member Benefit Income:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalPartyIncome) }}</strong><br>
                    <strong>Total Member Benefit Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalPartyExpense) }}</strong><br>
                    <strong>Member Benefit/Dues Income Percentage:&nbsp;&nbsp;&nbsp;{{ number_format($partyPercentage * 100, 2) }}%</strong><br>
					<hr>
					<!-- start:report_review -->
					<div class="form-row report_review" <?php if(!$submitted) echo "style=\"display:none\""; ?>>
						<div class="col-md-12">
							<h2>Annual Report Review</h2>
						</div>
						<div class="form-row">
							<div class="col-md-6 mar_bot_20">
                                <div class="col-md-12">
                                <div class="form-inline">
                                    <label style="display: block;">Is the Chapter's Party Expense under 15%?<span class="field-required">*</span></label>
                                    <select id="check_party_percentage" name="check_party_percentage" class="form-control select2" style="width: 300px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                        <option value="" {{ is_null($financial_report_array->check_party_percentage) ? 'selected' : '' }} disabled>Please Select</option>
                                        <option value="0" {{$financial_report_array->check_party_percentage === 0 ? 'selected' : ''}}>No, they are over 20%</option>
                                        <option value="1" {{$financial_report_array->check_party_percentage == 1 ? 'selected' : ''}}>No, but they are under 20%</option>
                                        <option value="2" {{$financial_report_array->check_party_percentage == 2 ? 'selected' : ''}}>Yes, they are under 15%</option>
                                    </select>
                                </div>
                                </div>
                            </div>
							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step4_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="8" name="Step4_Log" id="Step4_Log" readonly><?php echo $financial_report_array['step_4_notes_log']; ?></textarea>
								</div>
							</div>

							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step4_Note">Note:</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(4)" name="Step4_Note" id="Step4_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
				<div class="accordion-header js-accordion-header">OFFICE & OPERATING EXPENSES</div>
				<div class="accordion-body js-accordion-body">
                <section>
                    Printing Costs:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['office_printing_costs']) }}</strong><br>
                    Postage Costs:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['office_postage_costs']) }}</strong><br>
                    Membership Pins:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['office_membership_pins_cost']) }}</strong><br>
                <br>
                Other Office/Operating Expenses:
                    <table width="50%" >
                        <tbody>
                            <?php
                                $other_office_expenses = null;
                                $totalOfficeExpense = 0;

                                if (isset($financial_report_array['office_other_expenses']) && $financial_report_array['office_other_expenses'] !== null) {
                                    $blobData = base64_decode($financial_report_array['office_other_expenses']);
                                    $other_office_expenses = unserialize($blobData);

                                    if ($other_office_expenses === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($other_office_expenses) && count($other_office_expenses) > 0) {
                                            foreach ($other_office_expenses as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $row['office_other_desc'] . "</td>";
                                                echo "<td>" . ($row['office_other_expense'] ? "$" . number_format($row['office_other_expense'], 2) : "$0.00") . "</td>";
                                                echo "</tr>";

                                                $totalOfficeExpense += floatval($row['office_other_expense']);
                                            }
                                            // Total row
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td><strong>Total</strong></td>";
                                            echo "<td><strong>$" . number_format($totalOfficeExpense, 2) . "</strong></td>";
                                            echo "</tr>";
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='2'>No Other Office/Operating Expenses Entered.</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='2'>No Other Office/Operating Expenses Entered.</td>";
                                    echo "</tr>";
                                }
                                ?>

                        </tbody>
                    </table>
                    <br>
                    <strong>Total Office/Operating Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                            $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense) }}</strong><br>
			<hr>
			<!-- start:report_review -->
				<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
					<div class="col-md-12">
						<h2>Annual Report Review</h2>
					</div>

					<div class="form-row">

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step5_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="8" name="Step5_Log" id="Step5_Log" readonly><?php echo $financial_report_array['step_5_notes_log']; ?></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step5_Note">Note:</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(5)" name="Step5_Note" id="Step5_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
                </section>
			</div><!-- end of accordion body -->
			</div><!-- end of accordion item -->
			<!------End Step 5 ------>

            <!------Start Step 5 ------>
			    <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='5') echo "active";?>">
                    <div class="accordion-header js-accordion-header">INTERNATIONAL EVENTS & RE-REGISTRATION</div>
                    <div class="accordion-body js-accordion-body">
                    <section>
                        <table width="75%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Description</td>
                                    <td>Income</td>
                                    <td>Expenses</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $international_event_array = null;
                                    $totalEventIncome = 0;
                                    $totalEventExpense = 0;

                                    if (isset($financial_report_array['international_event_array']) && $financial_report_array['international_event_array'] !== null) {
                                        $blobData = base64_decode($financial_report_array['international_event_array']);
                                        $international_event_array = unserialize($blobData);

                                        if ($international_event_array === false) {
                                            echo "Error: Failed to unserialize data.";
                                        } else {
                                            if (is_array($international_event_array) && count($international_event_array) > 0) {
                                                foreach ($international_event_array as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['intl_event_desc'] . "</td>";
                                                    echo "<td>" . ($row['intl_event_income'] ? "$" . number_format($row['intl_event_income'], 2) : "$0.00") . "</td>";
                                                    echo "<td>" . ($row['intl_event_expenses'] ? "$" . number_format($row['intl_event_expenses'], 2) : "$0.00") . "</td>";
                                                    echo "</tr>";

                                                    $totalEventIncome += floatval($row['intl_event_income']);
                                                    $totalEventExpense += floatval($row['intl_event_expenses']);
                                                }
                                                // Total row
                                                echo "<tr style='border-top: 1px solid #333;'>";
                                                echo "<td><strong>Total</strong></td>";
                                                echo "<td><strong>$" . number_format($totalEventIncome, 2) . "</strong></td>";
                                                echo "<td><strong>$" . number_format($totalEventExpense, 2) . "</strong></td>";
                                                echo "</tr>";
                                            } else {
                                                echo "<tr style='border-top: 1px solid #333;'>";
                                                echo "<td colspan='3'>No International Events Entered.</td>";
                                                echo "</tr>";
                                            }
                                        }
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='3'>No International Events Entered.</td>";
                                        echo "</tr>";
                                    }
                                 ?>

                            </tbody>
                        </table>
                        <br>
                        <strong>Total Events Income:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalEventIncome) }}</strong><br>
                        <strong>Total Events Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalEventExpense) }}</strong><br>
                        <br>
                        <strong>Chapter Re-Registration:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$financial_report_array['annual_registration_fee']) }}</strong><br>
                <hr>
                <!-- start:report_review -->
                    <div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
                        <div class="col-md-12">
                            <h2>Annual Report Review</h2>
                        </div>

                        <div class="form-row">

                            <div class="col-md-12 mar_bot_20">
                                <div class="col-md-12">
                                    <label for="Step5_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
                                </div>
                                <div class="col-md-12">
                                    <textarea class="form-control" style="width:100%" rows="8" name="Step5_Log" id="Step5_Log" readonly><?php echo $financial_report_array['step_5_notes_log']; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-12 mar_bot_20">
                                <div class="col-md-12">
                                    <label for="Step5_Note">Note:</label>
                                </div>
                                <div class="col-md-12">
                                    <textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(5)" name="Step5_Note" id="Step5_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
                    </section>
                </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
                <!------End Step 5 ------>

			<!------Start Step 6 ------>
		    <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='6') echo "active";?>">
				<div class="accordion-header js-accordion-header">DONATIONS TO YOUR CHAPTER</div>
				<div class="accordion-body js-accordion-body">
				<section>
                    Monetary Donations:
                    <table width="100%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>Purpose of Donation</td>
                                <td>Donor Name/Address</td>
                                <td>Date</td>
                                <td>Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $monetary_donations_to_chapter = null;
                            $totalDonationAmount = 0;

                            if (isset($financial_report_array['monetary_donations_to_chapter']) && $financial_report_array['monetary_donations_to_chapter'] !== null) {
                                $blobData = base64_decode($financial_report_array['monetary_donations_to_chapter']);
                                $monetary_donations_to_chapter = unserialize($blobData);

                                if ($monetary_donations_to_chapter === false) {
                                    echo "Error: Failed to unserialize data.";
                                } else {
                                    if (is_array($monetary_donations_to_chapter) && count($monetary_donations_to_chapter) > 0) {
                                        foreach ($monetary_donations_to_chapter as $row) {
                                            echo "<tr>";
                                            echo "<td>" . $row['mon_donation_desc'] . "</td>";
                                            echo "<td>" . $row['mon_donation_info'] . "</td>";
                                            echo "<td>" . $row['mon_donation_date'] . "</td>";
                                            echo "<td>" . ($row['mon_donation_amount'] ? "$" . number_format($row['mon_donation_amount'], 2) : "$0.00") . "</td>";
                                            echo "</tr>";

                                            $totalDonationAmount += floatval($row['mon_donation_amount']);
                                        }
                                        // Total row
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td><strong>Total</strong></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td><strong>$" . number_format($totalDonationAmount, 2) . "</strong></td>";
                                        echo "</tr>";
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='4'>No Monetary Donations Entered.</td>";
                                        echo "</tr>";
                                    }
                                }
                            } else {
                                echo "<tr style='border-top: 1px solid #333;'>";
                                echo "<td colspan='4'>No Monetary Donations Entered.</td>";
                                echo "</tr>";
                            }
                        ?>

                        </tbody>
                    </table>
                    <br>
                    <strong>Total Monetary Donations:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f', $totalDonationAmount) }}</strong><br>
                    <br>
					Non-Monetary Donations:
                    <table width="75%" style="border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #333;">
                                <td>Purpose of Donation</td>
                                <td>Donor Name/Address</td>
                                <td>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $non_monetary_donations_to_chapter = null;

                                if (isset($financial_report_array['non_monetary_donations_to_chapter']) && $financial_report_array['non_monetary_donations_to_chapter'] !== null) {
                                    $blobData = base64_decode($financial_report_array['non_monetary_donations_to_chapter']);
                                    $non_monetary_donations_to_chapter = unserialize($blobData);

                                    if ($non_monetary_donations_to_chapter === false) {
                                        echo "Error: Failed to unserialize data.";
                                    } else {
                                        if (is_array($non_monetary_donations_to_chapter) && count($non_monetary_donations_to_chapter) > 0) {
                                            foreach ($non_monetary_donations_to_chapter as $row) {
                                                echo "<tr style='border-top: 1px solid #333;'>";
                                                echo "<td>" . $row['nonmon_donation_desc'] . "</td>";
                                                echo "<td>" . $row['nonmon_donation_info'] . "</td>";
                                                echo "<td>" . $row['nonmon_donation_date'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr style='border-top: 1px solid #333;'>";
                                            echo "<td colspan='3'>No Non-Monetary Donations Entered.</td>";
                                            echo "</tr>";
                                        }
                                    }
                                } else {
                                    echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td colspan='3'>No Non-Monetary Donations Entered.</td>";
                                    echo "</tr>";
                                }
                            ?>

                        </tbody>
                    </table>
                    <br>
					<hr>
					<!-- start:report_review -->
						<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
							<div class="col-md-12">
								<h2>Annual Report Review</h2>
							</div>
							<div class="form-row">
								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step6_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="8" name="Step6_Log" id="Step6_Log" readonly><?php echo $financial_report_array['step_6_notes_log']; ?></textarea>
									</div>
								</div>
								<div class="col-md-12 mar_bot_20">
									<div class="col-md-12">
										<label for="Step6_Note">Note:</label>
									</div>
									<div class="col-md-12">
										<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(6)" name="Step6_Note" id="Step6_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
					<div class="accordion-header js-accordion-header">OTHER INCOME & EXPENSES</div>
					<div class="accordion-body js-accordion-body">
					<section>
                        <table width="75%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Description</td>
                                    <td>Income</td>
                                    <td>Expenses</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $other_income_and_expenses_array = null;
                                    $totalOtherIncome = 0;
                                    $totalOtherExpenses = 0;

                                    if (isset($financial_report_array['other_income_and_expenses_array'])) {
                                        $blobData = base64_decode($financial_report_array['other_income_and_expenses_array']);
                                        $other_income_and_expenses_array = unserialize($blobData);

                                        if ($other_income_and_expenses_array === false) {
                                            echo "Error: Failed to unserialize data.";
                                        } else {
                                            foreach ($other_income_and_expenses_array as $row) {
                                                echo "<tr>";
                                                echo "<td>" . $row['other_desc'] . "</td>";
                                                echo "<td>" . ($row['other_income'] ? "$" . number_format($row['other_income'], 2) : "$0.00") . "</td>";
                                                echo "<td>" . ($row['other_expenses'] ? "$" . number_format($row['other_expenses'], 2) : "$0.00") . "</td>";
                                                echo "</tr>";

                                                $totalOtherIncome += floatval($row['other_income']);
                                                $totalOtherExpenses += floatval($row['other_expenses']);
                                            }
                                             // Total row
                                             echo "<tr style='border-top: 1px solid #333;'>";
                                    echo "<td><strong>Total</strong></td>";
                                    echo "<td><strong>$" . number_format($totalOtherIncome, 2) . "</strong></td>";
                                    echo "<td><strong>$" . number_format($totalOtherExpenses, 2) . "</strong></td>";
                                    echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='3'>No Other Income or Expenses Entered.</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                            </tbody>
                        </table>
                        <br>
                        <strong>Total Other Income:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalOtherIncome) }}</strong><br>
                        <strong>Total Other Expenses:&nbsp;&nbsp;&nbsp;{{ '$'.sprintf('%0.2f',$totalOtherExpenses) }}</strong><br>
					<hr>

					<!-- start:report_review -->
					<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
						<div class="col-md-12">
							<h2>Annual Report Review</h2>
						</div>
						<div class="form-row">
							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step7_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="8" name="Step7_Log" id="Step7_Log" readonly><?php echo $financial_report_array['step_7_notes_log']; ?></textarea>
								</div>
							</div>
							<div class="col-md-12 mar_bot_20">
								<div class="col-md-12">
									<label for="Step7_Note">Note:</label>
								</div>
								<div class="col-md-12">
									<textarea class="form-control" style="width:100%" rows="3" oninput="EnableNoteLogButton(7)"  name="Step7_Note" id="Step7_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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

                <!------Start Step 10 ------>
		  	<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='10') echo "active";?>">
				<div class="accordion-header js-accordion-header">FINANCIAL SUMMARY</div>
				<div class="accordion-body js-accordion-body">
				<section>
                    <?php
                        $totalIncome = $totalDues + $totalServiceIncome + $totalPartyIncome + $totalDonationAmount + $totalEventIncome + $totalOtherIncome;
                        $totalExpenses = $financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid'] + $financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses + $totalServiceProjectExpenses
                                + $totalPartyExpense + $financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                                $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense + $financial_report_array['annual_registration_fee'] + $totalEventExpense + $totalOtherExpenses;
                        $treasuryBalance = $financial_report_array ['amount_reserved_from_previous_year'] + $totalIncome - $totalExpenses
                    ?>
                    <table width="50%" style="border-collapse: collapse;">
                        <tbody>
                            <tr><td><strong>INCOME</strong></td></tr>
                            <tr><td style="border-top: 1px solid #333;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Membership Dues Income</td>
                            <td style="border-top: 1px solid #333;">{{ '$'.sprintf('%0.2f',$totalDues) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Project Income</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalServiceIncome) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Party/Member Benefit Income</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalPartyIncome) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Monetary Donations to Chapter</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalDonationAmount) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;International Event Registration</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalEventIncome) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Income</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalOtherIncome) }}</td></tr>
                            <tr><td style="border-top: 1px solid #333;"><strong>TOTAL INCOME:</strong></td>
                            <td style="border-top: 1px solid #333;"><strong>{{ '$'.sprintf('%0.2f',$totalIncome) }}</strong></td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td><strong>EXPENSES<strong></td></tr>
                            <tr><td style="border-top: 1px solid #333;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Meeting Room Expenses</td>
                            <td style="border-top: 1px solid #333;">{{ '$'.sprintf('%0.2f',$financial_report_array['manditory_meeting_fees_paid'] + $financial_report_array['voluntary_donations_paid']) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expenses:</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supplies</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalChildrenSupplies) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paid Sitters</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['paid_baby_sitters'])  }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalChildrenOther) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expense Total</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['paid_baby_sitters'] + $totalChildrensRoomExpenses) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Project Expenses</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supplies:</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalServiceSupplies) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Charitable Donations</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalServiceCharity) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;M2M fund Donation</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalServiceM2M) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Service Project Expense Total</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalServiceProjectExpenses) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Party/Member Benefit Expenses</td>
                            <td> {{ '$'.sprintf('%0.2f',$totalPartyExpense) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Office/Operating Expenses</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Printing</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['office_printing_costs']) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Postage</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['office_postage_costs']) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Membership Pins</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['office_membership_pins_cost']) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalOfficeExpense) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Office/Operating Expense Total</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['office_printing_costs'] + $financial_report_array['office_postage_costs'] +
                                $financial_report_array['office_membership_pins_cost'] + $totalOfficeExpense) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Annual Chapter Re-registration Fee</td>
                            <td>{{ '$'.sprintf('%0.2f',$financial_report_array['annual_registration_fee'])  }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;International Event Registration</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalEventExpense) }}</td></tr>
                            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Expenses</td>
                            <td>{{ '$'.sprintf('%0.2f',$totalOtherExpenses) }}</td></tr>
                            <tr><td style="border-top: 1px solid #333;"><strong>TOTAL EXPENSES</strong></td>
                            <td style="border-top: 1px solid #333;"><strong>{{ '$'.sprintf('%0.2f',$totalExpenses) }}</strong></td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td style="border-top: 1px solid #333; border-bottom: 1px solid #333;"><strong>PROFIT (LOSS)</strong></td>
                            <td style="border-top: 1px solid #333; border-bottom: 1px solid #333;"><strong>
                            @php
                                $netAmount = $totalIncome - $totalExpenses;
                                $formattedAmount = ($netAmount < 0) ? '($' . number_format(abs($netAmount), 2) . ')' : '$' . number_format($netAmount, 2);
                            @endphp
                            {{ $formattedAmount }}</strong></td></tr>
                        </tbody>
                    </table>
                    <br>
                <hr>

         <!-- start:report_review -->
		<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
			<div class="col-md-12">
				<h2>Annual Report Review</h2>
			</div>
			<div class="form-row">
                <div class="col-md-12 mar_bot_20">
                    <div class="col-md-12">
                        Total Income/Revenue:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$totalIncome) }}</strong>
                    </div>
                </div>
                <div class="col-md-6 mar_bot_20">
                    <div class="col-md-12">
                    <div class="form-inline">
                        <label style="display: block;">Is the Total Income/Revenue less than $50,000?<span class="field-required">*</span></label>
                        <select id="checkTotalIncome" name="checkTotalIncome" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                            <option value="" {{ is_null($financial_report_array->check_total_income_less) ? 'selected' : '' }} disabled>Please Select</option>
                            <option value="0" {{$financial_report_array->check_total_income_less === 0 ? 'selected' : ''}}>No</option>
                            <option value="1" {{$financial_report_array->check_total_income_less == 1 ? 'selected' : ''}}>Yes</option>
                        </select>
                    </div>
                    </div>
                </div>
				<div class="col-md-12 mar_bot_20">
					<div class="col-md-12">
						<label for="Step10_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
					</div>
					<div class="col-md-12">
						<textarea class="form-control" style="width:100%" rows="8" name="Step10_Log" id="Step10_Log" readonly><?php echo $financial_report_array['step_10_notes_log']; ?></textarea>
					</div>
				</div>
				<div class="col-md-12 mar_bot_20">
					<div class="col-md-12">
						<label for="Step10_Note">Note:</label>
					</div>
					<div class="col-md-12">
						<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(10)" name="Step1_Note" id="Step10_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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

				<!------Start Step 8 ------>
				<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='8') echo "active";?>">
					<div class="accordion-header js-accordion-header">BANK RECONCILIATION</div>
					<div class="accordion-body js-accordion-body">
					<section>
                        <div class="flex-container">
                            <div class="flex-item">
                                Beginning Balance&nbsp;&nbsp;&nbsp;<strong>{{ '$'.($financial_report_array ['amount_reserved_from_previous_year'])}}</strong><br>
                            </div>
                            <div class="flex-item">
                                Ending Bank Statement Balance&nbsp;&nbsp;&nbsp;<strong>{{ '$'.($financial_report_array ['bank_balance_now'])}}</strong><br>
                            </div>
                            <div class="flex-item">
                                Profit (Loss)&nbsp;&nbsp;&nbsp;<strong>
                                    @php
                                        $netAmount = $totalIncome - $totalExpenses;
                                        $formattedAmount = ($netAmount < 0) ? '($' . number_format(abs($netAmount), 2) . ')' : '$' . number_format($netAmount, 2);
                                    @endphp
                                    {{ $formattedAmount }}</strong><br>
                            </div>
                            <div class="flex-item">
                                &nbsp;&nbsp;&nbsp;
                            </div>
                            <div class="flex-item">
                                Ending Balance (Treasury Balance Now)&nbsp;&nbsp;&nbsp;<strong>{{ '$'.($treasuryBalance)}}</strong><br>
                            </div>
                            <div class="flex-item">
                                &nbsp;&nbsp;&nbsp;
                            </div>
                        </div>
                        <br>
                        <table width="75%" style="border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid #333;">
                                    <td>Date</td>
                                    <td>Check No.</td>
                                    <td>Transation Desc.</td>
                                    <td>Payment Amt.</td>
                                    <td>Deposit Amt.</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $bank_rec_array = null;
                                    $totalPayments = 0;
                                    $totalDeposits = 0;

                                    if (isset($financial_report_array['bank_reconciliation_array']) && $financial_report_array['bank_reconciliation_array'] !== null) {
                                        $blobData = base64_decode($financial_report_array['bank_reconciliation_array']);
                                        $bank_rec_array = unserialize($blobData);

                                        if ($bank_rec_array === false) {
                                            echo "Error: Failed to unserialize data.";
                                        } else {
                                            if (is_array($bank_rec_array) && count($bank_rec_array) > 0) {
                                                foreach ($bank_rec_array as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row['bank_rec_date'] . "</td>";
                                                    echo "<td>" . $row['bank_rec_check_no'] . "</td>";
                                                    echo "<td>" . $row['bank_rec_desc'] . "</td>";
                                                    echo "<td>" . ($row['bank_rec_payment_amount'] ? "$" . number_format($row['bank_rec_payment_amount'], 2) : "$0.00") . "</td>";
                                                    echo "<td>" . ($row['bank_rec_desposit_amount'] ? "$" . number_format($row['bank_rec_desposit_amount'], 2) : "$0.00") . "</td>";
                                                    echo "</tr>";

                                                    $totalPayments += floatval($row['bank_rec_payment_amount']);
                                                    $totalDeposits += floatval($row['bank_rec_desposit_amount']);
                                                }

                                                // Total row
                                                echo "<tr style='border-top: 1px solid #333;'>";
                                                echo "<td><strong>Total</strong></td>";
                                                echo "<td><strong></strong></td>";
                                                echo "<td><strong></strong></td>";
                                                echo "<td><strong>$" . number_format($totalPayments, 2) . "</strong></td>";
                                                echo "<td><strong>$" . number_format($totalDeposits, 2) . "</strong></td>";
                                                echo "</tr>";
                                            } else {
                                                echo "<tr style='border-top: 1px solid #333;'>";
                                                echo "<td colspan='5'>No Reconciliation Transactions Entered.</td>";
                                                echo "</tr>";
                                            }
                                        }
                                    } else {
                                        echo "<tr style='border-top: 1px solid #333;'>";
                                        echo "<td colspan='5'>No Reconciliation Transactions Entered.</td>";
                                        echo "</tr>";
                                    }

                                    $totalReconciliation = - $totalPayments + $totalDeposits;
                                    ?>

                            </tbody>
                            </table>
                        <br>
                        Reconciled Bank Statement:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array ['bank_balance_now'] + $totalReconciliation) }}</strong><br>
                        Treasury Balance Now:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.($treasuryBalance)}}</strong><br>
				    <hr>

				<!-- start:report_review -->
				<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
					<div class="col-md-12">
						<h2>Annual Report Review</h2>
					</div>
						<div class="form-row">
								{{-- <div class="col-md-12 mar_bot_20" id="StatementBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} ?>>
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
								</div> --}}
								<div class="clearfix"></div>
                                <div class="col-md-6 mar_bot_20">
                                    <div class="col-md-12">
                                Ending Balance on Last Year's Report:&nbsp;&nbsp;&nbsp;<strong>{{ '$'.sprintf('%0.2f',$financial_report_array['pre_balance']) }}</strong>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Does this year's Beginning Balance match last year's Ending Balance?<span class="field-required">*</span></label>
                                <select id="check_beginning_balance" name="check_beginning_balance" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_beginning_balance) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_beginning_balances === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_beginning_balance == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                                <div class="col-md-6 mar_bot_20">
                                    <div class="col-md-12">
                                    <div class="form-inline">
                                        <label style="display: block;">Current bank statement included and balance matches chapter entry:<span class="field-required">*</span></label>
                                        <select id="checkBankStatementIncluded" name="checkBankStatementIncluded" class="form-control select2" style="width: 150px;" required >
                                            <option value="" {{ is_null($financial_report_array->check_bank_statement_included) ? 'selected' : '' }} disabled>Please Select</option>
                                            <option value="0" {{$financial_report_array->check_bank_statement_included === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$financial_report_array->check_bank_statement_included == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mar_bot_20">
                                    <div class="col-md-12">
                                    <div class="form-inline">
                                        <label style="display: block;">Treasury Balance Now matches Reconciled Bank Balance:<span class="field-required">*</span></label>
                                        <select id="checkBankStatementMatches" name="checkBankStatementMatches" class="form-control select2" style="width: 150px;" required >
                                            <option value="" {{ is_null($financial_report_array->check_bank_statement_matches) ? 'selected' : '' }} disabled>Please Select</option>
                                            <option value="0" {{$financial_report_array->check_bank_statement_matches === 0 ? 'selected' : ''}}>No</option>
                                            <option value="1" {{$financial_report_array->check_bank_statement_matches == 1 ? 'selected' : ''}}>Yes</option>
                                        </select>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mar_bot_20">
									<div class="col-md-12">
								    <div class="form-group">
							            <label for="post_balance">
								            Enter Ending Balance (to be used as beginning balance on next year's report):
							            </label>
							        <div class="input-group"><span class="input-group-addon">$</span>
							            <input type="number" class="form-control" min="0" step="0.01" name="post_balance" id="post_balance" style="width: 120px;" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['post_balance'] ?>"
                                            <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
							        </div>
						            </div>
							        </div>
							</div>
							<div class="clearfix"></div>
						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step8_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="8" name="Step8_Log" id="Step8_Log" readonly><?php echo $financial_report_array['step_8_notes_log']; ?></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step8_Note">Note:</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(8)" name="Step8_Note" id="Step8_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
							</div>
						</div>

						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<button type="button" id="AddNote8" class="btn btn-large btn-success" onclick="AddNote(8)" disabled>Add Note to Log</button>
							</div>
						</div>

					{{-- </div> --}}
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
			<div class="accordion-header js-accordion-header">CHAPTER QUESTIONS</div>
				<div class="accordion-body js-accordion-body">
				<section>
                    <table>
                        <tbody>
                           <tr><td>1.</td>
                               <td>Did anyone in your chapter receive any compensation or pay for their work with your chapter?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['receive_compensation'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['receive_compensation_explanation']}}</strong></td></tr>
                           <tr><td>2.</td>
                               <td>Did any officer, member or family of a member benefit financially in any way from the member's position with your chapter?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['financial_benefit'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['financial_benefit_explanation']}}</strong></td></tr>
                           <tr><td>3.</td>
                               <td>Did your chapter attempt to influence any national, state/provincial, or local legislation, or support any other organization that did?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['influence_political'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['influence_political_explanation']}}</strong></td></tr>
                           <tr><td>4.</td>
                               <td>Did your chapter vote on all activities and expenditures during the fiscal year?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['vote_all_activities'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['vote_all_activities_explanation']}}</strong></td></tr>
                           <tr><td>5.</td>
                               <td>Did you purchase pins from International?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['purchase_pins'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['purchase_pins_explanation']}}</strong></td></tr>
                           <tr><td>6.</td>
                               <td>Did you purchase any merchandise from International other than pins?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['bought_merch'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{$financial_report_array ['bought_merch_explanation']}}</strong></td></tr>
                           <tr><td>7.</td>
                               <td>Did you offer or inform your members about MOMS Club merchandise?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['offered_merch'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['offered_merch_explanation']}}</strong></td></tr>
                           <tr><td>8.</td>
                               <td>Did you make the Bylaws and/or manual available for any chapter members that requested them?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['bylaws_available'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['bylaws_available_explanation']}}</strong></td></tr>
                           <tr><td>9.</td>
                               <td>Did you have a children's room with babysitters?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['childrens_room_sitters'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array['childrens_room_sitters_explanation']}}</strong></td></tr>
                           <tr><td>10.</td>
                               <td>Did you have playgroups? If so, how were they arranged.</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['playgroups'] == 1 ? 'YES   Arranged by Age' : (['playgroups'] == 2 ? 'YES   Multi-aged Groups' : 'NO') }}</strong></td></tr>
                           <tr><td>11.</td>
                               <td>Did you have any child focused outings or activities?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['child_outings'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['child_outings_explanation']}}</strong></td></tr>
                           <tr><td>12.</td>
                               <td>Did you have any mother focused outings or activities?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['mother_outings'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['mother_outings_explanation']}}</strong></td></tr>
                           <tr><td>13.</td>
                               <td>Did you have speakers at any meetings?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['meeting_speakers'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['meeting_speakers_explanation']}}</strong></td></tr>
                           <tr><td>14.</td>
                               <td>If you had speakers, check any of the topics that were covered:</td></tr>
                               <tr><td></td>
                                   <td><strong>
                                       @php
                                           $meetingSpeakersArray = json_decode($financial_report_array['meeting_speakers_array']);
                                           $meetingSpeakersMapping = [
                                               '0' => 'N/A',
                                               '1' => 'Child Rearing',
                                               '2' => 'Schools/Education',
                                               '3' => 'Home Management',
                                               '4' => 'Politics',
                                               '5' => 'Other Non-Profit',
                                               '6' => 'Other',
                                           ];
                                       @endphp

                                       @if (!empty($meetingSpeakersArray))
                                           {{ implode(', ', array_map(function($value) use ($meetingSpeakersMapping) {
                                               return $meetingSpeakersMapping[$value];
                                           }, $meetingSpeakersArray)) }}
                                       @else
                                           N/A
                                       @endif
                                   </strong></td></tr>
                           <tr><td>15.</td>
                               <td>Did you have any discussion topics at your meetings? If yes, how often?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['discussion_topic_frequency'] == 1 ? '1-3 Times' : (['discussion_topic_frequency'] == 2 ? '4-6 Times' :
                                   (['discussion_topic_frequency'] == 3 ? '7-9 Times' : (['discussion_topic_frequency'] == 4 ? '10+ Times' : 'NO'))) }}</strong></td></tr>
                           <tr><td>16.</td>
                               <td>Did your chapter have scheduled park days? If yes, how often?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['park_day_frequency'] == 1 ? '1-3 Times' : (['park_day_frequency'] == 2 ? '4-6 Times' :
                                   (['park_day_frequency'] == 3 ? '7-9 Times' : (['park_day_frequency'] == 4 ? '10+ Times' : 'NO'))) }}</strong></td></tr>
                           <tr><td>17.</td>
                               <td>Did your chapter have any of the following activity groups?</td></tr>
                               <tr><td></td>
                               <td><strong>
                                   @php
                                       $activityArray = json_decode($financial_report_array['activity_array']);
                                       $activityMapping = [
                                           '0' => 'N/A',
                                           '1' => 'Cooking',
                                           '2' => 'Cost Cutting Tips',
                                           '3' => 'Mommy Playgroup',
                                           '4' => 'Babysitting Co-op',
                                           '5' => 'MOMS Night Out',
                                           '6' => 'Other',
                                       ];
                                   @endphp

                                   @if (!empty($activityArray))
                                       {{ implode(', ', array_map(function($value) use ($activityMapping) {
                                           return $activityMapping[$value];
                                       }, $activityArray)) }}
                                   @else
                                       N/A
                                   @endif
                               </strong></td></tr>
                           <tr><td>18.</td>
                               <td>Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['contributions_not_registered_charity'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['contributions_not_registered_charity_explanation']}}</strong></td></tr>
                           <tr><td>19.</td>
                               <td>Did your chapter perform at least one service project to benefit mothers or children?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['at_least_one_service_project'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array['at_least_one_service_project_explanation']}}</strong></td></tr>
                           <tr><td>20.</td>
                               <td>Did your chapter sister another chapter?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['sister_chapter'] == 1 ? 'YES' : 'NO' }}</strong></td></tr>
                           <tr><td>21.</td>
                               <td>Did your chapter attend an International Event?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array['international_event'] == 1 ? 'YES' : 'NO' }}</strong></td></tr>
                           <tr><td>22.</td>
                               <td>Did your chapter file their IRS 990N?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['file_irs'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{$financial_report_array ['file_irs_explanation']}}</strong></td></tr>
                           <tr><td>23.</td>
                               <td>Is a copy of your chapter's most recent bank statement included with the copy of this report that you are submitting to International?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['bank_statement_included'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $financial_report_array ['bank_statement_included_explanation']}}</strong></td></tr>
                           <tr><td>24.</td>
                               <td>If your group does not have any bank accounts, where is the chapter money kept?</td></tr>
                           <tr><td></td>
                               <td><strong>{{ $financial_report_array ['wheres_the_money'] ?? 'N/A'}}</strong></td></tr>
                       </tbody>
                   </table>
                <hr>
				 <!-- start:report_review -->
				<div <?php if(!$submitted) echo "style=\"display:none\""; ?> class="form-row report_review">
					<div class="col-md-12">
						<h2>Annual Report Review</h2>
					</div>
                    <div class="form-row">
                        {{-- <div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
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
                        <div class="form-row">

                            <div class="col-md-12 mar_bot_20" id="990NBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
                                <div class="col-md-12">
                                    <label class="control-label" for="990NFiling">Attach the chapter's 990N filing confirmation (5 MB max):</label>
                                    <input name="990NFiling" id="990NFiling" type="file" accept=".pdf, .jpg, .jpeg" class="demo1 form-control" />
                                </div>
                            </div>
                        </div> --}}
                        <div class="clearfix"></div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they purchase or have leftover pins? (Quesion 5)<span class="field-required">*</span></label>
                                <select id="checkPurchasedPins" name="checkPurchasedPins" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_purchased_pins) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_purchased_pins === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_purchased_pins == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they purchase MOMS Club merchandise? (Quesion 6):<span class="field-required">*</span></label>
                                <select id="checkPurchasedMCMerch" name="checkPurchasedMCMerch" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_purchased_mc_merch) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_purchased_mc_merch === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_purchased_mc_merch == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they offer MOMS Club merchandise or info on how to buy to members? (Question 7)<span class="field-required">*</span></label>
                                <select id="checkOfferedMerch" name="checkOfferedMerch" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_offered_merch) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_offered_merch === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_offered_merch == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they make the Manual/By-Laws available to members? (Question 8)<span class="field-required">*</span></label>
                                <select id="checkBylawsMadeAvailable" name="checkBylawsMadeAvailable" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_bylaws_available) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_bylaws_available === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_bylaws_available == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they Sistered another chapter? (Question 20)<span class="field-required">*</span></label>
                                <select id="checkSisteredAnotherChapter" name="checkSisteredAnotherChapter" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_sistered_another_chapter) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_sistered_another_chapter === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_sistered_another_chapter == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they attended an in person or viurtual International Event? (Question 21)<span class="field-required">*</span></label>
                                <select id="checkAttendedTraining" name="checkAttendedTraining" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_attended_training) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_attended_training === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_attended_training == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6 mar_bot_20">
                            <div class="col-md-12">
                            <div class="form-inline">
                                <label style="display: block;">Did they attach proof of 990N Filing with the date range of <strong>7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?></strong>?<span class="field-required">*</span></label>
                                <select id="checkCurrent990NAttached" name="checkCurrent990NAttached" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                    <option value="" {{ is_null($financial_report_array->check_current_990N_included) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->check_current_990N_included === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->check_current_990N_included == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                            </div>
                        </div>
						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step9_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="8" name="Step9_Log" id="Step9_Log" readonly><?php echo $financial_report_array['step_9_notes_log']; ?></textarea>
							</div>
						</div>
						<div class="col-md-12 mar_bot_20">
							<div class="col-md-12">
								<label for="Step9_Note">Note:</label>
							</div>
							<div class="col-md-12">
								<textarea class="form-control" style="width:100%" rows="3"  oninput="EnableNoteLogButton(9)" name="Step9_Note" id="Step9_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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

			<!------Start Step 11 ------>
			<div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited_coord'] =='11') echo "active";?>">
                <div class="accordion-header js-accordion-header">AWARD NOMINATIONS</div>
				<div class="accordion-body js-accordion-body">
					<section>
					<div class="form-row form-group">
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

							<?php
                                if (empty($financial_report_array) || $financial_report_array['award_1_nomination_type'] === null || $financial_report_array['award_1_nomination_type'] == 0) {
                                    echo "No Award Nominations for this Chapter";
                                }
                            ?>

						<!-- Award 1 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #1:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_1_nomination_type']) ? 'N/A' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_1_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_1_nomination_type'] == 5 || $financial_report_array['award_1_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_1_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_1_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_1_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_1_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_1_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_1_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
							    <input type="hidden" name="Award1Path" id="Award1Path" value="<?php echo $financial_report_array['award_1_files']; ?>">
                                <div class="col-sm-12">
                                    Supporting Award Files:
                                    <div class="form-group col-sm-12">
                                        <?php
                                            $award_1_files = null;
                                            $Award1FileCount = 0;

                                            if (isset($financial_report_array['award_1_files'])) {
                                                $award_1_files = unserialize(base64_decode($financial_report_array['award_1_files']));
                                                $Award1FileCount = is_array($award_1_files) ? count($award_1_files) : 0;

                                                if ($Award1FileCount > 0) {
                                                    for ($row = 1; $row <= $Award1FileCount; $row++) {
                                                        $row_id = $row;
                                                        if (isset($award_1_files[$row]['url'])) {
                                                            echo "<a href=\"" . $award_1_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a>";
                                                        } else {
                                                            echo "No files uploaded";
                                                        }
                                                    }
                                                } else {
                                                    echo "No files uploaded";
                                                }
                                            } else {
                                                echo "No files uploaded";
                                            }
                                        ?>
                                    </div>
                                </div>
						</div>
						<!-- Award 1 Stop -->
						<!-- Award 2 Start -->
						<div class="box_brd_contentpad" id="Award2Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #2:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_2_nomination_type']) ? 'N/A' : ($financial_report_array['award_2_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_2_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_2_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_2_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_2_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_2_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_2_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_2_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_2_nomination_type'] == 5 || $financial_report_array['award_2_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_2_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_2_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_2_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_2_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_2_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_2_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
							    <input type="hidden" name="Award2Path" id="Award2Path" value="<?php echo $financial_report_array['award_2_files']; ?>">
                                <div class="col-sm-12">
                                    Supporting Award Files:
                                    <div class="form-group col-sm-12">
                                        <?php
                                            $award_2_files = null;
                                            $Award2FileCount = 0;

                                            if (isset($financial_report_array['award_2_files'])) {
                                                $award_2_files = unserialize(base64_decode($financial_report_array['award_2_files']));
                                                $Award2FileCount = is_array($award_2_files) ? count($award_2_files) : 0;

                                                if ($Award2FileCount > 0) {
                                                    for ($row = 1; $row <= $Award2FileCount; $row++) {
                                                        $row_id = $row;
                                                        if (isset($award_2_files[$row]['url'])) {
                                                            echo "<a href=\"" . $award_2_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a>";
                                                        } else {
                                                            echo "No files uploaded";
                                                        }
                                                    }
                                                } else {
                                                    echo "No files uploaded";
                                                }
                                            } else {
                                                echo "No files uploaded";
                                            }
                                        ?>
                                    </div>
                                </div>
						</div>
						<!-- Award 2 Stop -->
						<!-- Award 3 Start -->
						<div class="box_brd_contentpad" id="Award3Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #3:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_3_nomination_type']) ? 'N/A' : ($financial_report_array['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_3_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_3_nomination_type'] == 5 || $financial_report_array['award_3_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_3_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_3_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_3_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_3_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_3_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_3_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
							    <input type="hidden" name="Award3Path" id="Award3Path" value="<?php echo $financial_report_array['award_3_files']; ?>">
                                <div class="col-sm-12">
                                    Supporting Award Files:
                                    <div class="form-group col-sm-12">
                                        <?php
                                            $award_3_files = null;
                                            $Award3FileCount = 0;

                                            if (isset($financial_report_array['award_3_files'])) {
                                                $award_3_files = unserialize(base64_decode($financial_report_array['award_3_files']));
                                                $Award3FileCount = is_array($award_3_files) ? count($award_3_files) : 0;

                                                if ($Award3FileCount > 0) {
                                                    for ($row = 1; $row <= $Award3FileCount; $row++) {
                                                        $row_id = $row;
                                                        if (isset($award_3_files[$row]['url'])) {
                                                            echo "<a href=\"" . $award_3_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a>";
                                                        } else {
                                                            echo "No files uploaded";
                                                        }
                                                    }
                                                } else {
                                                    echo "No files uploaded";
                                                }
                                            } else {
                                                echo "No files uploaded";
                                            }
                                        ?>
                                    </div>
                                </div>
						</div>
						<!-- Award 3 Stop -->
						<!-- Award 4 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #4:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_4_nomination_type']) ? 'N/A' : ($financial_report_array['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_4_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_4_nomination_type'] == 5 || $financial_report_array['award_4_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_4_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_4_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_4_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_4_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_4_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_4_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
							    <input type="hidden" name="Award4Path" id="Award4Path" value="<?php echo $financial_report_array['award_4_files']; ?>">
                                <div class="col-sm-12">
                                    Supporting Award Files:
                                    <div class="form-group col-sm-12">
                                        <?php
                                            $award_4_files = null;
                                            $Award4FileCount = 0;

                                            if (isset($financial_report_array['award_4_files'])) {
                                                $award_4_files = unserialize(base64_decode($financial_report_array['award_4_files']));
                                                $Award4FileCount = is_array($award_4_files) ? count($award_4_files) : 0;

                                                if ($Award4FileCount > 0) {
                                                    for ($row = 1; $row <= $Award4FileCount; $row++) {
                                                        $row_id = $row;
                                                        if (isset($award_4_files[$row]['url'])) {
                                                            echo "<a href=\"" . $award_4_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a>";
                                                        } else {
                                                            echo "No files uploaded";
                                                        }
                                                    }
                                                } else {
                                                    echo "No files uploaded";
                                                }
                                            } else {
                                                echo "No files uploaded";
                                            }
                                        ?>
                                    </div>
                                </div>
						</div>
						<!-- Award 4 Stop -->
						<!-- Award 5 Start -->
						<div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==NULL) echo "none;"; else echo "block;";} else echo "none;";?>">
							<div class="box_brd_title_box">
								<div class="form-group">
                                    <div class="col-md-12">
                                        Award #5:&nbsp;&nbsp;&nbsp;{{ is_null($financial_report_array['award_5_nomination_type']) ? 'N/A' : ($financial_report_array['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                    </div>
								</div>
							</div>
                            <div class="col-sm-12">
								 Description:
                                 <div class="col-sm-12">
                                    <?php
                                        if (!empty($financial_report_array)) {
                                            $description = $financial_report_array['award_5_outstanding_project_desc'];
                                            echo ($description !== null) ? $description : "No description entered";
                                        }
                                    ?>
                                </div>
                            </div>
                            <?php if (!empty($financial_report_array) && ($financial_report_array['award_5_nomination_type'] == 5 || $financial_report_array['award_5_nomination_type'] == 6)) : ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
                            <div class="col-sm-12">
                                <table>
                                    <tr><td colspan="2">Did you follow the Bylaws and all instructions from International?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_follow_bylaws']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_follow_bylaws'] == 0 ? 'NO'
                                            : ($financial_report_array['award_5_outstanding_follow_bylaws'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you run a well-rounded program for your members?</td></tr>
                                    <tr><td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits
                                        kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older
                                        children (or vice versa) would not be offering a well-rounded program.</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_well_rounded']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_well_rounded'] == 0 ? 'NO'
                                            : ($financial_report_array['award_5_outstanding_well_rounded'] == 1 ? 'YES' : 'Not Answered')) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2">Did you communicate with your Coordinator?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_communicated']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_communicated'] == 0 ? 'NO'
                                        : ($financial_report_array['award_5_outstanding_communicated'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                    <tr><td colspan="2" colspan="2">Did you support the International MOMS Club?</td></tr>
                                    <tr><td colspan="2"><strong>{{ is_null($financial_report_array['award_5_outstanding_support_international']) ? 'Not Answered' : ($financial_report_array['award_5_outstanding_support_international'] == 0 ? 'NO'
                                        : ($financial_report_array['award_5_outstanding_support_international'] == 1 ? 'YES' : 'NO' )) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                            <?php endif; ?>
                            <div class="col-sm-12">&nbsp;&nbsp;&nbsp;</div>
							    <input type="hidden" name="Award5Path" id="Award5Path" value="<?php echo $financial_report_array['award_5_files']; ?>">
                                <div class="col-sm-12">
                                    Supporting Award Files:
                                    <div class="form-group col-sm-12">
                                        <?php
                                            $award_5_files = null;
                                            $Award5FileCount = 0;

                                            if (isset($financial_report_array['award_5_files'])) {
                                                $award_5_files = unserialize(base64_decode($financial_report_array['award_5_files']));
                                                $Award5FileCount = is_array($award_5_files) ? count($award_5_files) : 0;

                                                if ($Award5FileCount > 0) {
                                                    for ($row = 1; $row <= $Award5FileCount; $row++) {
                                                        $row_id = $row;
                                                        if (isset($award_5_files[$row]['url'])) {
                                                            echo "<a href=\"" . $award_5_files[$row]['url'] . "\" target=\"_blank\">Submitted Award Files " . $row_id . "</a>";
                                                        } else {
                                                            echo "No files uploaded";
                                                        }
                                                    }
                                                } else {
                                                    echo "No files uploaded";
                                                }
                                            } else {
                                                echo "No files uploaded";
                                            }
                                        ?>
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
									<div class="col-md-12" <?php if ($financial_report_array['award_1_nomination_type']==NULL) echo "style=\"display: none;\""; ?> ?>
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
										<div class="col-md-6">
                                            <div class="col-md-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward1Approved" name="checkAward1Approved" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                                    <option value="" {{ is_null($financial_report_array->check_award_1_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_1_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_1_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-md-12" <?php if ($financial_report_array['award_2_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>
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
                                        <div class="col-md-6">
                                            <div class="col-md-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward2Approved" name="checkAward2Approved" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                                    <option value="" {{ is_null($financial_report_array->check_award_2_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_2_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_2_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-md-12" <?php if ($financial_report_array['award_3_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>
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
										<div class="col-md-6">
                                            <div class="col-md-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward3Approved" name="checkAward3Approved" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                                    <option value="" {{ is_null($financial_report_array->check_award_3_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_3_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_3_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-md-12" <?php if ($financial_report_array['award_4_nomination_type']==NULL) echo "style=\"display: none;\""; ?>>
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
										<div class="col-md-6">
                                            <div class="col-md-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward4Approved" name="checkAward4Approved" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                                    <option value="" {{ is_null($financial_report_array->check_award_4_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_4_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_4_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-md-12" <?php if ($financial_report_array['award_5_nomination_type']==NULL) echo "style=\"display: none;\""; ?> >
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
										<div class="col-md-6">
                                            <div class="col-md-12">
                                            <div class="form-inline">
                                                <label style="display: block;">Award Status:<span class="field-required">*</span></label>
                                                <select id="checkAward5Approved" name="checkAward5Approved" class="form-control select2" style="width: 150px;" required <?php if($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"; ?>>
                                                    <option value="" {{ is_null($financial_report_array->check_award_5_approved) ? 'selected' : '' }} disabled>Please Select</option>
                                                    <option value="0" {{$financial_report_array->check_award_5_approved === 0 ? 'selected' : ''}}>No</option>
                                                    <option value="1" {{$financial_report_array->check_award_5_approved == 1 ? 'selected' : ''}}>Yes</option>
                                                </select>
                                            </div>
                                            </div>
                                        </div>
									</div>

									<div class="col-md-12" style="margin-top: 20px; margin-bottom: 20px;">
                                        <div class="col-md-12" >
                                            <strong style="color:red">Please Note</strong><br>
                                            This will take you to a new screen - be sure to save all work before clicking button to Add Additional Awards.<br>
                                            <a id="addAwardsLink" href="{{ url("/chapter/awardsview/{$chapterid}") }}" class="btn btn-themeBlue margin" <?php if($financial_report_array['review_complete'] != "" || !$submitted) echo "disabled"; ?>>Add Awards</a>
                                        </div>
                                    </div>
									<div class="clearfix"></div>
									<div class="col-md-12 mar_bot_20">
										<div class="col-md-12">
											<label for="Step11_Log">Reviewer Notes Logged for this Section (not visible to chapter):</label>
										</div>
										<div class="col-md-12">
											<textarea class="form-control" style="width:100%" rows="8" name="Step11_Log" id="Step11_Log" readonly><?php echo $financial_report_array['step_11_notes_log']; ?></textarea>
										</div>
									</div>

									<div class="col-md-12 mar_bot_20">
										<div class="col-md-12">
											<label for="Step11_Note">Note:</label>
										</div>
										<div class="col-md-12">
											<textarea class="form-control" style="width:100%" oninput="EnableNoteLogButton(11)" rows="3" name="Step11_Note" id="Step11_Note" <?php if ($financial_report_array['review_complete']!="") echo "readonly"?>></textarea>
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
					<div class="accordion-header js-accordion-header">FINAL REVIEW</div>
					<div class="accordion-body js-accordion-body">
						<section>
									{{-- <div class="col-md-12">
										<h2>Annual Report Review</h2>
									</div> --}}
									{{-- <div class="col-md-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['roster_path']) echo "style=\"display: none;\"";} echo "style=\"display: block;\""; ?>>
											<div class="form-group col-xs-12">
												<label class="control-label" for="RosterLink">Chapter Roster File:</label>
													<a href="<?php echo $financial_report_array['roster_path']; ?>" target="_blank">Chapter Roster</a>
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
									<div class="col-md-12" <?php if (!empty($financial_report_array) || !is_null($financial_report_array)) {if (!$financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} echo "style=\"display: block;\""; ?>>
											<div class="form-group col-xs-12">
												<label class="control-label" for="990NLink">990N Filing:</label>
													<a href="<?php echo $financial_report_array['file_irs_path']; ?>" target="_blank">990N Confirmation</a>
											</div>
									</div> --}}
									{{-- <div class="clearfix"></div> --}}

                                    <style>
                                        .flex-container2 {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 0px;
                                            width: 100%;
                                            overflow-x: auto;
                                        }

                                        .flex-item2 {
                                            flex: 0 0 calc(48% - 10px);
                                            box-sizing: border-box;
                                        }
                                    </style>


                                    <div class="form-group col-md-12">
                                        <div class="col-md-12">
                                        <div class="flex-container2">
                                        <div class="flex-item2">
                                            Excel roster attached and complete (should be above):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_roster_attached']) ? 'Please Review'
                                                : ($financial_report_array ['check_roster_attached'] == 0 ? 'NO' : ($financial_report_array ['check_roster_attached'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Number of members listed, dues received, and renewal paid "seem right":&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_renewal_seems_right']) ? 'Please Review'
                                                : ($financial_report_array ['check_renewal_seems_right'] == 0 ? 'NO' : ($financial_report_array ['check_renewal_seems_right'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Minimum of one service project completed:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_minimum_service_project']) ? 'Please Review'
                                                : ( $financial_report_array ['check_minimum_service_project'] == 0 ? 'NO' : ($financial_report_array ['check_minimum_service_project'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Donation to M2M Fund:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_m2m_donation']) ? 'Please Review'
                                                : ($financial_report_array ['check_m2m_donation'] == 0 ? 'NO' : ($financial_report_array ['check_m2m_donation'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Party Percentage:&nbsp;&nbsp;&nbsp;<strong>{{ number_format($partyPercentage * 100, 2) }}%</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Party Percentage less than 15%:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_party_percentage']) ? 'Please Review' : ($financial_report_array['check_party_percentage'] == 0 ? 'NO, they are over 20%'
                                                : ($financial_report_array['check_party_percentage'] == 1 ? 'NO, but they are under 20%' : ($financial_report_array['check_party_percentage'] == 2 ? 'YES, they are under 15%' : 'Please Review' ))) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Total income/revenue less than $50,000:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_total_income_less']) ? 'Please Review'
                                                : ( $financial_report_array ['check_total_income_less'] == 0 ? 'NO' : ($financial_report_array ['check_total_income_less'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Current bank statement included (should be above):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_bank_statement_included']) ? 'Please Review'
                                                : ( $financial_report_array ['check_bank_statement_included'] == 0 ? 'NO' : ($financial_report_array ['check_bank_statement_included'] == 1 ? 'YES' : 'Please Review')) }}</strong><br>
                                        </div>
                                        <div class="flex-item">
                                            Treasury Balance Now matches Reconciled Bank Balance:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_bank_statement_matches']) ? 'Please Review'
                                                : ( $financial_report_array ['check_bank_statement_matches'] == 0 ? 'NO' : ($financial_report_array ['check_bank_statement_matches'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>

                                        <div class="flex-item2">
                                            Purchased membership pins or had leftovers:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_purchased_pins']) ? 'Please Review'
                                                : ( $financial_report_array ['check_purchased_pins'] == 0 ? 'NO' : ($financial_report_array ['check_purchased_pins'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Purchased MOMS Club merchandise:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_purchased_mc_merch']) ? 'Please Review'
                                                : ($financial_report_array ['check_purchased_mc_merch'] == 0 ? 'NO' : ($financial_report_array ['check_purchased_mc_merch'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Offered MOMS Club merchandise or info on how to buy to members:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_offered_merch']) ? 'Please Review'
                                                : ( $financial_report_array ['check_offered_merch'] == 0 ? 'NO' : ( $financial_report_array ['check_offered_merch'] == 1 ? 'YES' : 'Pleae Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Manual/by-laws made available to members:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_bylaws_available']) ? 'Please Review'
                                                : ( $financial_report_array ['check_bylaws_available'] == 0 ? 'NO' : ($financial_report_array ['check_bylaws_available'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Attended International Event (in person or virtual):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_attended_training']) ? 'Please Review'
                                                : ($financial_report_array ['check_attended_training'] == 0 ? 'NO' : ($financial_report_array ['check_attended_training'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Sistered another chapter:&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_sistered_another_chapter']) ? 'Please Review'
                                                : ($financial_report_array ['check_sistered_another_chapter'] == 0 ? 'NO' : ($financial_report_array ['check_sistered_another_chapter'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                        <div class="flex-item2">
                                            Proof of 990N Filing for 7/1/<?php echo date('Y')-1 .' - 6/30/'.date('Y');?> (should be above):&nbsp;&nbsp;&nbsp;<strong>{{ is_null($financial_report_array['check_current_990N_included']) ? 'Please Review'
                                                : ($financial_report_array ['check_current_990N_included'] == 0 ? 'NO' : ($financial_report_array ['check_current_990N_included'] == 1 ? 'YES' : 'Please Review' )) }}</strong><br>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-md-12"><br></div>
                                    <div class="col-md-12">
                                        Award #1 Status:&nbsp;&nbsp;&nbsp;
                                        <strong>{{ is_null($financial_report_array['check_award_1_approved']) ? 'N/A' : ($financial_report_array['check_award_1_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_1_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_1_nomination_type']) ? 'N/A' : ($financial_report_array['award_1_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_1_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_1_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_1_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_1_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_1_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_1_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </strong><br>
                                    </div>
									<div class="col-md-12">
                                        Award #2 Status:&nbsp;&nbsp;&nbsp;
                                        <strong>{{ is_null($financial_report_array['check_award_2_approved']) ? 'N/A' : ($financial_report_array['check_award_2_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_2_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_2_nomination_type']) ? 'N/A' : ($financial_report_array['award_2_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_2_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_2_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_2_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_2_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_2_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_2_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </strong><br>
                                    </div>
                                    <div class="col-md-12">
                                        Award #3 Status:&nbsp;&nbsp;&nbsp;
                                        <strong>{{ is_null($financial_report_array['check_award_3_approved']) ? 'N/A' : ($financial_report_array['check_award_3_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_3_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_3_nomination_type']) ? 'N/A' : ($financial_report_array['award_3_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_3_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_3_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_3_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_3_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_3_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_3_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </strong><br>
                                    </div>
                                    <div class="col-md-12">
                                        Award #4 Status:&nbsp;&nbsp;&nbsp;
                                        <strong>{{ is_null($financial_report_array['check_award_4_approved']) ? 'N/A' : ($financial_report_array['check_award_4_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_4_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_4_nomination_type']) ? 'N/A' : ($financial_report_array['award_4_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_4_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_4_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_4_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_4_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_4_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_4_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </strong><br>
                                    </div>
                                    <div class="col-md-12">
                                        Award #5 Status:&nbsp;&nbsp;&nbsp;
                                        <strong>{{ is_null($financial_report_array['check_award_5_approved']) ? 'N/A' : ($financial_report_array['check_award_5_approved'] == 0 ? 'NO' : ($financial_report_array['check_award_5_approved'] == 1 ? 'YES' : 'N/A')) }}
                                            &nbsp;&nbsp;-&nbsp;&nbsp; {{ is_null($financial_report_array['award_5_nomination_type']) ? 'N/A' : ($financial_report_array['award_5_nomination_type'] == 1 ? 'Outstanding Specific Service Project'
                                                : ($financial_report_array['award_5_nomination_type'] == 2 ? 'Outstanding Overall Service Program' : ($financial_report_array['award_5_nomination_type'] == 3 ? 'Outstanding Childrens Activity' : ($financial_report_array['award_5_nomination_type'] == 4 ? 'Outstanding Spirit'
                                                : ($financial_report_array['award_5_nomination_type'] == 5 ? 'Outstanding Chapter' : ($financial_report_array['award_5_nomination_type'] == 6 ? 'Outstanding New Chapter' : ($financial_report_array['award_5_nomination_type'] == 7 ? 'Other Outstanding Award' : 'No Award Selected' ))))))) }}
                                        </strong><br>
                                    </div>
                                </div>
                                <div class="col-md-12">
									<div class="col-md-12 mar_bot_20">
										<strong>Reviewer Notes Logged for this Report (not visible to chapter):</strong><br>
                                        <?php
                                        $financial_report_notes = [];
                                        for ($i = 1; $i <= 11; $i++) {
                                            $key = 'step_' . $i . '_notes_log';
                                            if (isset($financial_report_array[$key])) {
                                                $notes = explode("\n", $financial_report_array[$key]);
                                                $financial_report_notes = array_merge($financial_report_notes, $notes);
                                            }
                                        }

                                        echo empty($financial_report_notes) ? 'No notes logged for this report.' : implode('<br>', $financial_report_notes);
                                        ?>
									</div>
						<div class="col-sm-12">
                        <strong>Contact information for the person who completed the report:</strong><br>
                        Name -&nbsp;<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_name'];}?><br>
                        Email -&nbsp;<a href="mailto:<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?>"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?></a></p>
                        </div>

                        <div class="col-md-12 mar_bot_20">
                            <div class="form-group">
                                <?php if ($chapterDetails[0]->financial_report_received == 1 && $financial_report_array['reviewer_id'] == null): ?>
                                    <span style="display: inline; color: red;">No Reviewer Assigned - Select Reviewer before continuing to prevent errors.<br></span>
                                <?php endif; ?>
                            <label for="AssignedReviewer"><strong>Assigned Reviewer:</strong></label>
                                <select class="form-control" name="AssignedReviewer" id="AssignedReviewer" style="width: 250px;" <?php if ($financial_report_array['review_complete']!="" || !$submitted) echo "disabled"?> required>
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

              <a href="{{ route('home') }}" class="btn btn-themeBlue margin">Back</a>
<a id="viewPdfLink" href="{{ url("/chapter/financial/pdf/{$chapterid}") }}" target="_blank" class="btn btn-themeBlue margin" <?php if(!$submitted =='1') echo "disabled"; ?>>View PDF</a>
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
			$("#submitted").val('');
			$("#submit_type").val('UnSubmit');
			$("#FurthestStep").val('13');
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
            $("#submit_type").val('review_complete');
            $("#FurthestStep").val('13');
            $("#financial_report").submit();
		}
	});

	$("#review-clear").click(function() {
		var result=confirm("This will clear the 'review complete' flag and coordinators will be able to edit the report again.  Do you wish to continue?");
		if(result){
			$("#submit_type").val('review_clear');
			$("#FurthestStep").val('13');
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
	// function ReplaceStatement1(){
	// 	document.getElementById("StatementBlock").style.display = 'block';
	// 	document.getElementById("StatementBlock").style.visibility = 'visible';
	// }

	// 	function ReplaceStatement2(){
	// 	document.getElementById("Statement2Block").style.display = 'block';
	// 	document.getElementById("Statement2Block").style.visibility = 'visible';
	// }

	// function ReplaceRoster(){
	// 	document.getElementById("RosterBlock").style.display = 'block';
	// 	document.getElementById("RosterBlock").style.visibility = 'visible';
	// }

	// function Replace990N(){
	// 	document.getElementById("990NBlock").style.display = 'block';
	// 	document.getElementById("990NBlock").style.visibility = 'visible';
	// }

	// function UpdateRelatedControl(RelatedElementName){
	// 	document.getElementById(RelatedElementName).checked = true;
	// }


	document.getElementById('addAwardsLink').addEventListener('click', function(event) {
        if (<?php echo ($financial_report_array['review_complete'] != "" || !$submitted) ? 'true' : 'false'; ?>) {
            event.preventDefault(); // Prevent the default link behavior
        }
    });

    document.getElementById('viewPdfLink').addEventListener('click', function(event) {
        if (<?php echo (!$submitted == '1') ? 'true' : 'false'; ?>) {
            event.preventDefault(); // Prevent the default link behavior
        }
    });

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

	function CheckReviewAnswers(){
		if(!document.getElementById("checkRosterAttached").val === null || document.getElementById("checkRosterAttached").val === ''){
			alert("You have not verified a roster was attached.");
			document.getElementById("checkRosterAttached").focus();
			return false;
		}

		if(!document.getElementById("checkServiceProject").val === null || document.getElementById("checkServiceProject").val === ''){
			alert("You have not verified a service project was completed.");
			document.getElementById("checkServiceProject").focus();
			return false;
		}

		if(!document.getElementById("chec_party_percentage").val === null || document.getElementById("check_party_percentage").val === ''){
			alert("You have not indicated if the chapter's party/member benefit percentage was under 15%.");
			document.getElementById("check_party_percentage").focus();
			return false;
		}

		if(!document.getElementById("checkM2MDonation").val === null || document.getElementById("checkM2MDonation").val === ''){
			alert("You have not indicated if the chapter donated to the M2M Fund.");
			document.getElementById("checkM2MDonation").focus();
			return false;
		}

		if(!document.getElementById("checkRenewalSeemsRight").val === null || document.getElementById("heckRenewalSeemsRight").val === ''){
			alert("You have not indicated if the renewal numbers 'seem right'.");
			document.getElementById("checkRenewalSeemsRight").focus();
			return false;
		}

		if(!document.getElementById("checkAttendedTraining").val === null || document.getElementById("checkAttendedTraining").val === ''){
			alert("You have not indicated if the chapter attended training.");
			document.getElementById("checkAttendedTraining").focus();
			return false;
		}

		if(!document.getElementById("checkBankStatementIncluded").val === null || document.getElementById("checkBankStatementIncluded").val === ''){
			alert("You have not indicated if the chapter included a bank statement for the end of the year.");
			document.getElementById("checkBankStatementIncluded").focus();
			return false;
		}

		if(!document.getElementById("checkBankStatementMatches").val === null || document.getElementById("checkBankStatementMatches").val === ''){
			alert("You have not indicated if the chapter's bank balance matches stated bank balance.");
			document.getElementById("checkBankStatementMatches").focus();
			return false;
		}

		if(!document.getElementById("checkPurchasedPins").val === null || document.getElementById("checkPurchasedPins").val === ''){
			alert("You have not indicated if the chapter purchased pins.");
			document.getElementById("checkPurchasedPins").focus();
			return false;
		}

		if(!document.getElementById("checkPurchasedMCMerch").val === null || document.getElementById("checkPurchasedMCMerch").val === ''){
			alert("You have not indicated if the chapter purchased other MOMS Club merchandise.");
			document.getElementById("checkPurchasedMCMerch").focus();
			return false;
		}
		if(!document.getElementById("checkOfferedMerch").val === null || document.getElementById("checkOfferedMerch").val === ''){
			alert("You have not indicated if the chapter Offered MOMS Club merchandise.");
			document.getElementById("checkOfferedMerch").focus();
			return false;
		}

		if(!document.getElementById("checkBylawsMadeAvailable").val === null || document.getElementById("checkBylawsMadeAvailable").val === ''){
			alert("You have not indicated if the chapter made the by-laws available to members.");
			document.getElementById("checkBylawsMadeAvailable").focus();
			return false;
		}

		if(!document.getElementById("checkCurrent990NAttached").val === null || document.getElementById("checkCurrent990NAttached").val === ''){
			alert("You have not indicated if the chapter attached their 990N filing confirmation.");
			document.getElementById("checkCurrent990NAttached").focus();
			return false;
		}

		if(!document.getElementById("checkTotalIncome").val === null || document.getElementById("checkTotalIncome").val === ''){
			alert("You have not indicated if the chapter's total income is less than $50,000.");
			document.getElementById("checkTotalIncome").focus();
			return false;
		}

		if(!document.getElementById("checkSisteredAnotherChapter").val === null || document.getElementById("checkSisteredAnotherChapter").val === ''){
			alert("You have not indicated if the chapter sistered another chapter.");
			document.getElementById("checkSisteredAnotherChapter").focus();
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
