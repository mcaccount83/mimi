@extends('layouts.chapter_theme')

@section('content')

<div class="container" id="test">
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
    <div class="row">
        <div class="col-md-12">
            <div class="card card-user">
                <div class="card-image color_header"></div>
                <div class="card-body">
                    <div class="author">
                            <div class="border-gray avatar">
								<img src="{{ asset('chapter_theme/img/logo.png') }}" alt="...">
							</div>
                        <h2 class="moms-c"> MOMS Club of {{ $chapterDetails[0]->chapter_name }}, {{$chapterDetails[0]->state}}</h2>
                        <h2 class="moms-c"> <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</h2>
                        <h4><center><?php if(!$chapterDetails[0]->financial_report_received) echo "<br>Please complete the report below with finanacial information about your chapter."; ?></center></h4>
                        <h4><center><?php if($chapterDetails[0]->financial_report_received) echo "<br><font color=\"red\">Your chapter's Financial Report has been Submitted!<br>
                            Please save a copy of the PDF for your records.</font>"; ?></center></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="financial_report" name="financial_report" role="form" data-toggle="validator" enctype="multipart/form-data" method="POST" action='{{ route("board.storefinancial", ["id" => Session::get("chapterid")]) }}' novalidate>
            @csrf
            <input type="hidden" name="ch_name" value="<?php echo $chapterDetails[0]->chapter_name; ?>" />
            <input type="hidden" name="ch_state" value="<?php echo $chapterDetails[0]->state; ?>" />
            <input type="hidden" name="ch_pcid" value="<?php echo $chapterDetails[0]->pcid; ?>" />
            <input type="hidden" name="ch_conf" value="<?php echo $chapterDetails[0]->conf; ?>" />
            <input type="hidden" name="submitted" id="submitted" value="<?php echo $submitted; ?>" />
            <input type="hidden" name="FurthestStep" id="FurthestStep" value="<?php if($financial_report_array['farthest_step_visited'] > 0) echo $financial_report_array['farthest_step_visited']; else echo "0"; ?>" />

            <div class="accordion js-accordion">
                <!------Start Step 1 ------>
                <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] <='1') echo "active";?>" >
                    <div class="accordion-header js-accordion-header">CHAPTER DUES</div>
                    <div class="accordion-body js-accordion-body">
                        <section>
                            <div class="col-md-12" id="RosterBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['roster_path']) echo "style=\"display: none;\"";} ?>>
                                <div class="col-md-12">
                                    <strong style="color:red">Please Note</strong><br>
                                        This will refresh the screen - be sure to save all work before clicking button to Upload Roster File.<br>
                                    <button type="button" class="btn btn-themeBlue margin" data-toggle="modal" data-target="#modal-roster" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Roster File</button>
                                </div>
                            </div>
                            <input type="hidden" name="RosterPath" id="RosterPath" value="<?php echo $financial_report_array['roster_path']; ?>">
                            <div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['roster_path']) echo "style=\"display: none;\"";} ?>>
                                <div class="col-md-12" >
                                    <div>
                                        <label class="control-label" for="RosterLink">Chapter Roster File:</label>
                                       <a href="<?php echo $financial_report_array['roster_path']; ?>" target="_blank">View Chapter Roster</a><br>
                                        <strong style="color:red">Please Note</strong><br>
                                        This will refresh the screen - be sure to save all work before clicking button to Replace Roster File.<br>
                                       <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-roster" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Roster File</button>
                                </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        <br>
                        <div class="col-md-12 float-left">
                            <div class="form-group">
                                <label>Did your chapter change your dues this year?<span class="field-required">*</span></label>
                                <select id="optChangeDues" name="optChangeDues" class="form-control select2" style="width: 25%;" required onchange="ChapterDuesQuestionsChange()">
                                    <option value="" name="optChangeDues" id="optChangeDuesNo" {{$financial_report_array->changed_dues === null ? 'selected' : ''}}>No</option>
                                    <option value="1" name="optChangeDues" id="optChangeDuesYes" {{$financial_report_array->changed_dues == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 float-left">
                            <div class="form-group">
                                <label>Did your chapter charge different amounts for new and returning members?<span class="field-required">*</span></label>
                                <select id="optNewOldDifferent" name="optNewOldDifferent" class="form-control select2" style="width: 25%;" required onchange="ChapterDuesQuestionsChange()">
                                    <option value="" id="optNewOldDifferentNo" {{$financial_report_array->different_dues == null ? 'selected' : ''}}>No</option>
                                    <option value="1" id="optNewOldDifferentYes" {{$financial_report_array->different_dues == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 float-left">
                            <div class="form-group">
                                <label> Did your chapter have any members who didn't pay full dues?<span class="field-required">*</span></label>
                                <select id="optNoFullDues" name="optNoFullDues" class="form-control select2" style="width: 25%;" required onchange="ChapterDuesQuestionsChange()">
                                    <option value="" id="optNoFullDuesNo" {{$financial_report_array->not_all_full_dues == null ? 'selected' : ''}}>No</option>
                                    <option value="1" id="optNoFullDuesYes" {{$financial_report_array->not_all_full_dues == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-row">
                           <div class="col-md-12">
                            <p><i>Note: Count all members who paid dues, even if they are not still members.</i></p>
                            </div>
                            <div class="col-md-6 float-left nopadding-l">
                                <div class="form-group">
                                    <label for="TotalNewMembers" id="lblTotalNewMembers">Total New Members (who paid dues)</label>
                                    <div class="">
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalNewMembers" id="TotalNewMembers" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_new_members'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                <label for="TotalRenewedMembers" id="lblTotalRenewedMembers">
                                    Total Renewed Members (who paid dues)
                                </label>
                                <div class="">
                                <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalRenewedMembers" id="TotalRenewedMembers" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_renewed_members'] ?>">
                                </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="ifChangeDues" style="display:none">
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                        <label for="TotalNewMembersNewFee">Total New Members (who paid NEW dues)</label>
                                        <div class="input-group">
                                                                                <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalNewMembersNewFee" id="TotalNewMembersNewFee" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_new_members_changed_dues'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding">
                                    <div class="form-group">
                                    <label for="TotalRenewedMembersNewFee">Total Renewed Members (who paid NEW dues)</label>
                                    <div class="input-group">

                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="ChangeMemberCount()" name="TotalRenewedMembersNewFee" id="TotalRenewedMembersNewFee" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_renewed_members_changed_dues'] ?>">
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="MemberDues" id="lblMemberDues">
                                        Member Dues
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDues" oninput="ChangeMemberCount()" id="MemberDues" step="0.01" min=0 aria-describedby="sizing-addon1" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="ifChangedDues1" style="visibility:hidden">
                                <div class="form-group">
                                    <label for="NewMemberDues" id="lblNewMemberDues">
                                        Member Dues (New Amount)
                                    </label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="NewMemberDues" oninput="ChangeMemberCount()" id="NewMemberDues" step="0.01" min=0 aria-describedby="sizing-addon1" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_new_changed'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="ifChangedDuesDifferentPerMemberType" style="display:none">
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                        <label for="MemberDuesRenewal">Renewal Dues</label>
                                        <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MemberDuesRenewal"  oninput="ChangeMemberCount()" id="MemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding" id="ifChangedDuesDifferentPerMemberType1" style="visibility:hidden">
                                    <div class="form-group">
                                    <label for="NewMemberDuesRenewal">Renewal Dues (NEW Amount)</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="NewMemberDuesRenewal" oninput="ChangeMemberCount()" id="NewMemberDuesRenewal" step="0.01" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['dues_per_member_renewal_changed'] ?>">
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="ifMembersNoDues" style="display:none">
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                        <label for="MembersNoDues">Total Members Who Paid No Dues</label>
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="MembersNoDues" id="MembersNoDues"  min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['members_who_paid_no_dues'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 float-left " style="visibility:hidden"><div class="form-group">
                                        <label for="MembersNoDues">Hidden</label>
                                        <input type="number" class="form-control"  value="0">
                                    </div></div>

                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                    <label for="TotalPartialDuesMembers">Total Members Who Paid Partial Dues</label>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalPartialDuesMembers" id="TotalPartialDuesMembers" min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['members_who_paid_partial_dues'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding">
                                    <div class="form-group">
                                    <label for="PartialDuesMemberDues">Total Partial Dues Amount Collected</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="PartialDuesMemberDues" id="PartialDuesMemberDues" min="0" step="0.01" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_partial_fees_collected'] ?>">
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding-l">
                                    <div class="form-group">
                                    <label for="TotalAssociateMembers">Total Associate Members</label>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalAssociateMembers" id="TotalAssociateMembers" min="0" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['total_associate_members'] ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 float-left nopadding">
                                    <div class="form-group">
                                    <label for="AssociateMemberDues">Associate Member Dues</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="AssociateMemberDues" id="AssociateMemberDues" min="0" step="0.01" oninput="ChangeMemberCount()" min=0 value="<?php if(!empty($financial_report_array)) echo $financial_report_array['associate_member_fee'] ?>">
                                    </div>
                                    </div>
                                </div>
                                <p><small><i>Note: Associate Members are not dues-waived or reduced members. They are a separate category of members. Many chapters do not have any Associate Members, but if your
                                        chapter did have Associate Members this year, how many Associate Members did your chapter have?</i></small></p>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label for="TotalMembers">Total Members</label>
                                    <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalMembers" id="TotalMembers" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label for="TotalDues">Total Dues Collected</label>
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalDues" id="TotalDues" step="0.01" aria-describedby="sizing-addon1" disabled=>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="form-row form-group">
                            <div class="card-body">
                                <div class="col-md-12 text-center">
                                  <button type="button" id="btn-step-1" class="btn btn-info btn-fill" onSubmit="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                                </div>
                            </div>
                        </div>
                        </section>
                    </div>
                </div>
                <!------End Step 1 ------>

                <!------Start Step 2 ------>
                <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='2') echo "active";?>">
                    <div class="accordion-header js-accordion-header">MONTHLY MEETING EXPENSES</div>
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
                                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="ManditoryMeetingFeesPaid" id="ManditoryMeetingFeesPaid" oninput="ChangeMeetingFees()" min="0"  step="0.01" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['manditory_meeting_fees_paid']; else echo "0"; ?>">
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
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="VoluntaryDonationsPaid" id="VoluntaryDonationsPaid" oninput="ChangeMeetingFees()" min="0"  step="0.01" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['voluntary_donations_paid']; else echo "0"; ?>">
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
                                        <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="TotalMeetingRoomExpenses" id="TotalMeetingRoomExpenses" disabled>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <div class="form-row form-group">
                                <p>Use this section to list individually any Children’s Room expenses. Examples include craft supplies and snacks.</p>
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="PaidBabySitters">Paid Babysitter Expenses (if any)</label>
                                        <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" name="PaidBabySitters" id="PaidBabySitters"  min="0"  step="0.01" oninput="ChangeChildrensRoomExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['paid_baby_sitters'] ?>">
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
                                                $ChildrensExpenseRowCount = is_array($childrens_room) ? count($childrens_room) : 0;
                                            }
                                            else{
                                                $ChildrensExpenseRowCount = 1;
                                            }

                                            for ($row = 0; $row < $ChildrensExpenseRowCount; $row++){
                                                echo "<tr>";
                                                echo "<td>
                                                <div class=\"form-group\">
                                                <input type=\"text\" class=\"form-control\" name=\"ChildrensRoomDesc" . $row . "\" id=\"ChildrensRoomDesc" . $row . "\" value=\"" . $childrens_room[$row]['childrens_room_desc'] . "\">
                                                </div>
                                                </td>";

                                                echo "<td>
                                                <div class=\"form-group\">
                                                <div class=\"input-group\">";
                                                echo "<span class = \"input-group-addon\">$</span>";
                                                echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\"  name=\"ChildrensRoomSupplies" . $row . "\" id=\"ChildrensRoomSupplies" . $row . "\" oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $childrens_room[$row]['childrens_room_supplies'] . "\">";
                                                echo "</div>
                                                </div>
                                                </td>";

                                                echo "<td>
                                                <div class=\"form-group\">
                                                <div class=\"input-group\">";
                                                echo "<span class = \"input-group-addon\">$</span>";
                                                echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ChildrensRoomOther" . $row . "\" id=\"ChildrensRoomOther" . $row . "\" oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $childrens_room[$row]['childrens_room_other'] . "\">";
                                                echo "</div>
                                                </div>
                                                </td>";

                                                echo "</tr>";
                                            }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 float-left">
                                    <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddChildrenExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                                    <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteChildrenExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
                                </div>
                                <div class="col-md-6 float-left">
                                    <div>
                                        <label for="ChildrensRoomTotal">
                                            Children's Room Miscellaneous Total
                                        </label>
                                        <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" value="0.00"name="ChildrensRoomTotal"  id="ChildrensRoomTotal"  step="0.01" disabled>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <input type="hidden" name="ChildrensExpenseRowCount" id="ChildrensExpenseRowCount" value="<?php echo $ChildrensExpenseRowCount; ?>" />
                            <div class="form-row form-group">
                                <div class="card-body">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" id="btn-step-2" class="btn btn-info btn-fill" onClick="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                                    </div>
                                </div>

                            </div>
                        </section>
                    </div>
                </div>
                <!------End Step 2 ------>

                <!------Start Step 3 ------>
                <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='3') echo "active";?>">
                    <div class="accordion-header js-accordion-header">SERVICE PROJECTS</div>
                    <div class="accordion-body js-accordion-body ">
                    <section>
                        <div class="form-row form-group">
                          <p>
                            A Service Project is one that benefits others OUTSIDE your chapter. However, a Service Project may also be a project to benefit a member-in-distress or one who has special emergency needs, if the needs are the reason for the project. For example, a fundraiser may benefit the International MOMS Club’s Mother-to-Mother Fund or may be used to help pay extreme medical expenses for a life-threatening illness suffered by a member’s child. (Any fundraisers or projects that benefited your chapter or members who are not suffering emergency or devastating situations should not be listed here. Those should be listed in Step 7.)
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
                                  <td width="36%">Project Description<span class="field-required">*</span></td>
                                  <td width="16%">Income<span class="field-required">*</span></td>
                                  <td width="16%">Supplies & Expenses<span class="field-required">*</span></td>
                                  <td width="16%">Charity Donation<span class="field-required">*</span></td>
                                  <td width="16%">M2M Donation<span class="field-required">*</span></td>
                                </tr>
                                </thead>

                                <?php
                                        $service_projects = null;
                                        if(isset($financial_report_array['service_project_array'])){
                                            $service_projects=unserialize(base64_decode($financial_report_array['service_project_array']));
                                            $ServiceProjectRowCount = is_array($service_projects) ? count($service_projects) : 0;
                                        }
                                        else{
                                            $ServiceProjectRowCount = 1;
                                        }
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td>
                                        <div class="form-group">
                                            <textarea class="form-control" rows="4" name="ServiceProjectDesc0" id="ServiceProjectDesc0" ><?php echo $service_projects[0]['service_project_desc'];?>  </textarea>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                            <div class="input-group">
                                            <span class = "input-group-addon">$</span>
                                            <input type="number" onKeyPress="if(this.value.length==9) return false;" class="form-control txt-num" min="0"  step="0.01" name="ServiceProjectIncome0" id="ServiceProjectIncome0" oninput="ChangeServiceProjectExpenses()" onkeydown="return event.keyCode !== 69" value="<?php echo $service_projects[0]['service_project_income'];?>">
                                            </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                            <div class="input-group"><span class = "input-group-addon">$</span>
                                            <input type="number" onKeyPress="if(this.value.length==9) return false;" class="form-control txt-num" min="0"  step="0.01" name="ServiceProjectSupplies0" id="ServiceProjectSupplies0" oninput="ChangeServiceProjectExpenses()" onkeydown="return event.keyCode !== 69" value="<?php echo $service_projects[0]['service_project_supplies'];?>">
                                            </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                            <div class="input-group">
                                            <span class = "input-group-addon">$</span>
                                            <input type="number" onKeyPress="if(this.value.length==9) return false;" class="form-control txt-num" min="0"  step="0.01" name="ServiceProjectDonatedCharity0" id="ServiceProjectDonatedCharity0" oninput="ChangeServiceProjectExpenses()" onkeydown="return event.keyCode !== 69" value="<?php echo $service_projects[0]['service_project_charity'];?>">
                                            </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                            <div class="input-group">
                                            <span class = "input-group-addon">$</span>
                                            <input type="number" onKeyPress="if(this.value.length==9) return false;" class="form-control txt-num" min="0"  step="0.01" name="ServiceProjectDonatedM2M0" id="ServiceProjectDonatedM2M0" oninput="ChangeServiceProjectExpenses()" onkeydown="return event.keyCode !== 69" value="<?php echo $service_projects[0]['service_project_m2m'];?>">
                                            </div>
                                            </div>
                                        </td>
                                    </tr>


                                    <?php
                                        for ($row = 1; $row < $ServiceProjectRowCount; $row++){
                                            echo "<tr>";
                                            echo "<td>
                                            <div class=\"form-group\">
                                            <textarea class=\"form-control\" rows=\"4\" name=\"ServiceProjectDesc" . $row . "\" id=\"ServiceProjectDesc" . $row . "\">" . $service_projects[$row]['service_project_desc'] . "</textarea>
                                            </div>
                                            </td>";

                                            echo "<td>
                                            <div class=\"form-group\">
                                            <div class=\"input-group\">";
                                            echo "<span class = \"input-group-addon\">$</span>";
                                            echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectIncome" . $row . "\" id=\"ServiceProjectIncome" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_income'] . "\">";
                                            echo "</div>
                                            </div>
                                            </td>";

                                            echo "<td>
                                            <div class=\"form-group\">
                                            <div class=\"input-group\">";
                                            echo "<span class = \"input-group-addon\">$</span>";
                                            echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectSupplies" . $row . "\" id=\"ServiceProjectSupplies" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_supplies'] . "\">";
                                            echo "</div>
                                            </div>
                                            </td>";

                                            echo "<td>
                                            <div class=\"form-group\">
                                            <div class=\"input-group\">";
                                            echo "<span class = \"input-group-addon\">$</span>";
                                            echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectDonatedCharity" . $row . "\" id=\"ServiceProjectDonatedCharity" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_charity'] . "\">";
                                            echo "</div>
                                            </div>
                                            </td>";

                                            echo "<td>
                                            <div class=\"form-group\">
                                            <div class=\"input-group\">";
                                            echo "<span class = \"input-group-addon\">$</span>";
                                            echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"ServiceProjectDonatedM2M" . $row . "\" id=\"ServiceProjectDonatedM2M" . $row . "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $service_projects[$row]['service_project_m2m'] . "\">";
                                            echo "</div>
                                            </div>
                                            </td>";

                                            echo "</tr>";
                                        }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 float-left">
                            <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddServiceProjectRow()" >Add</button>
                            <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteServiceProjectRow()" >Remove</button>
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
                        <hr>
                    </div>
                    <input type="hidden" name="ServiceProjectRowCount" id="ServiceProjectRowCount" value="<?php echo $ServiceProjectRowCount; ?>" />
                    <div class="form-row form-group">
                        <div class="card-body">
                        <div class="col-md-12 text-center">
                          <button type="button" class="btn btn-info btn-fill" id="btn-step-3" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                        </div>
                        </div>
                    </div>
                </section>
                </div>
                </div>
                <!------End Step 3 ------>

                <!------Start Step 4 ------>
                <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='4') echo "active";?>">
                <div class="accordion-header js-accordion-header">PARTIES & MEMBER BENEFITS</div>
                <div class="accordion-body js-accordion-body ">
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
                                    $PartyExpenseRowCount = is_array($party_expenses) ? count($party_expenses) : 0;
                                }
                                else{
                                    $PartyExpenseRowCount = 1;
                                }

                                for ($row = 0; $row < $PartyExpenseRowCount; $row++){
                                    echo "<tr>";
                                    echo "<td>
                                    <div class=\"form-group\">
                                    <input type=\"text\" class=\"form-control\" name=\"PartyDesc" . $row . "\" id=\"PartyDesc" . $row . "\" value=\"" . $party_expenses[$row]['party_expense_desc'] . "\">
                                    </div>
                                    </td>";

                                    echo "<td>
                                    <div class=\"form-group\">
                                    <div class=\"input-group\">";
                                    echo "<span class = \"input-group-addon\">$</span>";
                                    echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"PartyIncome" . $row . "\" id=\"PartyIncome" . $row . "\" oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $party_expenses[$row]['party_expense_income'] . "\">";
                                    echo "</div>
                                    </div>
                                    </td>";

                                    echo "<td>
                                    <div class=\"form-group\">
                                    <div class=\"input-group\">";
                                    echo "<span class = \"input-group-addon\">$</span>";
                                    echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"PartyExpenses" . $row . "\" id=\"PartyExpenses" . $row . "\" oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $party_expenses[$row]['party_expense_expenses'] . "\">";
                                    echo "</div>
                                    </div>
                                    </td>";
                                    echo "</tr>";
                                }
                            ?>
                            </tbody>
                        </table>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddPartyExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                            <button type="button" class="btn btn-danger btn-add-remove" onclick="DeletePartyExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
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
                        <hr>
                    </div>
                    <input type="hidden" name="PartyExpenseRowCount" id="PartyExpenseRowCount" value="<?php echo $PartyExpenseRowCount; ?>" />
                    <div class="form-row form-group">
                        <div class="card-body">
                            <div class="col-md-12 text-center">
                              <button type="submit" id="btn-step-4" class="btn btn-info btn-fill" onClick="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                            </div>
                        </div>
                    </div>
                </section>
                </div>
                </div>
                <!------End Step 4 ------>

                <!------Start Step 5 ------>
            <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='5') echo "active";?>">
                <div class="accordion-header js-accordion-header">OFFICE & OPERATING EXPENSES</div>
                <div class="accordion-body js-accordion-body">
                    <section>
                    <div class="form-row form-group">
                      <p>
                        Use this section to list individually any Office Expenses or other Operating Expenses. Please include only one expense type per line (i.e. website hosting, advertising, etc.).
                      </p>
                    <div class="col-md-4 float-left">
                        <div class="form-group">
                            <label for="PrintingCosts">
                                Printing Costs
                            </label>
                            <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="PrintingCosts" id="PrintingCosts" oninput="ChangeOfficeExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['office_printing_costs']?>">
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
                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="PostageCosts" id="PostageCosts" oninput="ChangeOfficeExpenses()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['office_postage_costs'] ?>">
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
                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="MembershipPins" id="MembershipPins" oninput="ChangeOfficeExpenses()" value="<?php echo $financial_report_array['office_membership_pins_cost']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 float-left">
                        <label for="">
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
                                <input maxlength=\"250\" type=\"text\" class=\"form-control\" name=\"OfficeDesc" . $row . "\" id=\"OfficeDesc" . $row . "\" value=\"" . $other_office_expenses[$row]['office_other_desc'] . "\">
                                </div>
                                </td>";

                                echo "<td>
                                <div class=\"form-group\">
                                <div class=\"input-group\">";
                                echo "<span class = \"input-group-addon\">$</span>";
                                echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"OfficeExpenses" . $row . "\" id=\"OfficeExpenses" . $row . "\" oninput=\"ChangeOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $other_office_expenses[$row]['office_other_expense'] . "\">";
                                echo "</div>
                                </div>
                                </td>";
                                echo "</tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                    <div class="col-md-12 float-left">
                        <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddOfficeExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                        <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteOfficeExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
                    </div>
                    <div class="col-md-4 float-left">
                        <div class="form-group">
                            <label for="OfficeExpenseTotal">
                                Other Office & Operating Expenses Total
                            </label>
                            <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num"  min="0"  step="0.01" name="OfficeExpenseTotal" id="OfficeExpenseTotal" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <input type="hidden" name="OfficeExpenseRowCount" id="OfficeExpenseRowCount" value="<?php echo $OfficeExpenseRowCount; ?>" />
                <div class="form-row form-group">
                    <div class="card-body">
                        <div class="col-md-12 text-center">
                          <button type="button" id="btn-step-5" class="btn btn-info btn-fill" onSubmit="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                        </div>
                    </div>
                </div>
                </section>
                </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
            <!------End Step 5 ------>

            <!------Start Step 5 ------>
            <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='55') echo "active";?>">
                <div class="accordion-header js-accordion-header">INTERNATIONAL EVENTS & RE-REGISTRATION</div>
                <div class="accordion-body js-accordion-body ">
                    <section>
                    <div class="form-row form-group">
                      <p>
                        Use this section to list individually any Internaitonal Events (in person or virtual).
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
                                <input type=\"text\" onKeyPress=\"if(this.value.length==30) return false;\" class=\"form-control\" name=\"InternationalEventDesc" . $row . "\" id=\"InternationalEventDesc" . $row . "\" value=\"" . $international_event_array[$row]['intl_event_desc'] . "\">
                                </div>
                                </td>";

                                echo "<td>
                                <div class=\"form-group\">
                                <div class=\"input-group\">";
                                echo "<span class = \"input-group-addon\">$</span>";
                                echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"InternationalEventIncome" . $row . "\" id=\"InternationalEventIncome" . $row . "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $international_event_array[$row]['intl_event_income'] . "\">";
                                echo "</div>
                                </div>
                                </td>";

                                echo "<td>
                                <div class=\"form-group\">
                                <div class=\"input-group\">";
                                echo "<span class = \"input-group-addon\">$</span>";
                                echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"InternationalEventExpense" . $row . "\" id=\"InternationalEventExpense" . $row . "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $international_event_array[$row]['intl_event_expenses'] . "\">";
                                echo "</div>
                                </div>
                                </td>";

                                echo "</tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                    <div class="col-md-12 float-left">
                    <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddInternationalEventRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                    <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteInternationalEventRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
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
                        <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="InternationalEventIncomeTotal" id="InternationalEventIncomeTotal" disabled>
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
                        <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0"  step="0.01" name="InternationalEventExpenseTotal" id="InternationalEventExpenseTotal" disabled>
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
                            Annual Chapter Registration Fee paid to International MOMS Club<span class="field-required">*</span>
                        </label>
                        <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="number" onKeyPress="if(this.value.length==9) return false;" class="form-control txt-num" min="0"  step="0.01" name="AnnualRegistrationFee" id="AnnualRegistrationFee" onkeydown="return event.keyCode !== 69" oninput="ChangeReRegistrationExpense()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['annual_registration_fee'] ?>">
                        </div>
                    </div>
                </div>
                <hr>
                </div>
                <div class="form-row form-group">
                    <div class="card-body">
                        <div class="col-md-12 text-center">
                          <button type="button" id="btn-step-55" class="btn btn-info btn-fill" onSubmit="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                        </div>
                    </div>
                </div>
                </section>
                </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
            <!------End Step 5 ------>

            <!------Start Step 6 ------>
            <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='6') echo "active";?>">
                <div class="accordion-header js-accordion-header">DONATIONS TO YOUR CHAPTER</div>
                <div class="accordion-body js-accordion-body ">
                <section>
                <div class="form-row form-group">
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
                            <?php
                                $monetary_dontations_to_chapter = null;
                                if(isset($financial_report_array['monetary_donations_to_chapter'])){
                                    $monetary_dontations_to_chapter=unserialize(base64_decode($financial_report_array['monetary_donations_to_chapter']));
                                    $MonDonationRowCount = is_array($monetary_dontations_to_chapter) ? count($monetary_dontations_to_chapter) : 0;
                                }
                                else{
                                    $MonDonationRowCount = 1;
                                }

                                    for ($row = 0; $row < $MonDonationRowCount; $row++){
                                        echo "<tr>";
                                        echo "<td>
                                        <div class=\"form-group\">
                                        <input type=\"text\" class=\"form-control\" name=\"DonationDesc" . $row . "\" id=\"DonationDesc" . $row . "\" value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_desc'] . "\">
                                        </div>
                                        </td>";

                                        echo "<td>
                                        <div class=\"form-group\">
                                        <input type=\"text\" class=\"form-control\" name=\"DonorInfo" . $row . "\" id=\"DonorInfo" . $row . "\" value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_info'] . "\">
                                        </div>
                                        </td>";

                                        echo "<td>
                                        <div class=\"form-group\">
                                        <input type=\"date\" class=\"form-control\" min='2021-07-01' max='2022-06-30' name=\"MonDonationDate" . $row . "\" id=\"MonDonationDate" . $row . "\" value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_date'] . "\" onchange=\"IsValidDate(this)\">
                                        </div>
                                        </td>";

                                        echo "<td>
                                        <div class=\"form-group\">
                                        <div class=\"input-group\">";
                                        echo "<span class = \"input-group-addon\">$</span>";
                                        echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"DonationAmount" . $row . "\" id=\"DonationAmount" . $row . "\" oninput=\"ChangeDonationAmount()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $monetary_dontations_to_chapter[$row]['mon_donation_amount'] . "\">";
                                        echo "</div>
                                        </div>
                                        </td>";

                                        echo "</tr>";
                                    }
                                ?>
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddMonDonationRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                        <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteMonDonationRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
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
                            <?php
                            $non_monetary_dontations_to_chapter = null;
                            if(isset($financial_report_array['non_monetary_donations_to_chapter'])){
                                $non_monetary_dontations_to_chapter=unserialize(base64_decode($financial_report_array['non_monetary_donations_to_chapter']));
                                $NonMonDonationRowCount = is_array($non_monetary_dontations_to_chapter) ? count($non_monetary_dontations_to_chapter) : 0;
                            }
                            else{
                                $NonMonDonationRowCount = 1;
                            }

                            for ($row = 0; $row < $NonMonDonationRowCount; $row++){
                                echo "<tr>";
                                echo "<td>
                                <div class=\"form-group\">
                                <input type=\"text\" class=\"form-control\" name=\"NonMonDonationDesc" . $row . "\" id=\"NonMonDonationDesc" . $row . "\" value=\"" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_desc'] . "\">
                                </div>
                                </td>";

                                echo "<td>
                                <div class=\"form-group\">
                                <input type=\"text\" class=\"form-control\" name=\"NonMonDonorInfo" . $row . "\" id=\"NonMonDonorInfo" . $row . "\" value=\"" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_info'] . "\">
                                </div>
                                </td>";

                                echo "<td>
                                <div class=\"form-group\">
                                <input type=\"date\" class=\"form-control\" min='2021-07-01' max='2022-06-30' name=\"NonMonDonationDate" . $row . "\" id=\"NonMonDonationDate" . $row . "\" value=\"" . $non_monetary_dontations_to_chapter[$row]['nonmon_donation_date'] . "\" onchange=\"IsValidDate(this)\">
                                </div>
                                </td>";

                                echo "</tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                    <div class="col-md-12 float-left">
                        <button type="button"  class="btn btn-large btn-success btn-add-remove" onclick="AddNonMonDonationRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                        <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteNonMonDonationRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
                    </div>
                    <input type="hidden" name="NonMonDonationRowCount" id="NonMonDonationRowCount" value="<?php echo $NonMonDonationRowCount; ?>" />
                    <hr>
                    </div>
                    <div class="form-row form-group">
                        <div class="card-body">
                            <div class="col-md-12 text-center">
                              <button type="submit" id="btn-step-6" class="btn btn-info btn-fill" onClick="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                            </div>
                        </div>
                    </div>
                </section>
                </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
                <!------End Step 6 ------>

                <!------Start Step 7 ------>
                <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='7') echo "active";?>">
                    <div class="accordion-header js-accordion-header">OTHER INCOME & EXPENSES</div>
                    <div class="accordion-body js-accordion-body ">
                    <section>
                    <div class="form-row form-group">

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
                                <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeIncome0" id="OtherOfficeIncome0" oninput="ChangeOtherOfficeExpenses()" value="<?php echo $other_income_and_expenses_array[0]['other_income'];?>">
                                </div>
                                </div>
                                </td>

                                <td>
                                <div class="form-group">
                                <div class="input-group">
                                <span class = "input-group-addon">$</span>
                                <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeExpenses0" id="OtherOfficeExpenses0" oninput="ChangeOtherOfficeExpenses()" value="<?php echo $other_income_and_expenses_array[0]['other_expenses'];?>">
                                </div>
                                </div>
                                </td>
                                </tr>
                                <?php

                                        for ($row = 1; $row < $OtherOfficeExpenseRowCount; $row++){
                                            echo "<tr>";
                                            echo "<td>
                                            <div class=\"form-group\">
                                            <input type=\"text\" class=\"form-control\" name=\"OtherOfficeDesc" . $row . "\" id=\"OtherOfficeDesc" . $row . "\" value=\"" . $other_income_and_expenses_array[$row]['other_desc'] . "\">
                                            </div>
                                            </td>";

                                            echo "<td>
                                            <div class=\"form-group\">
                                            <div class=\"input-group\">";
                                            echo "<span class = \"input-group-addon\">$</span>";
                                            echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"OtherOfficeIncome" . $row . "\" id=\"OtherOfficeIncome" . $row . "\" oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $other_income_and_expenses_array[$row]['other_income'] . "\">";
                                            echo "</div>
                                            </div>
                                            </td>";

                                            echo "<td>
                                            <div class=\"form-group\">
                                            <div class=\"input-group\">";
                                            echo "<span class = \"input-group-addon\">$</span>";
                                            echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"OtherOfficeExpenses" . $row . "\" id=\"OtherOfficeExpenses" . $row . "\" oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $other_income_and_expenses_array[$row]['other_expenses'] . "\">";
                                            echo "</div>
                                            </div>
                                            </td>";
                                            echo "</tr>";
                                        }
                                ?>
                            </tbody>
                        </table>
                        <div class="col-md-12 float-left">
                        <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddOtherOfficeExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                        <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteOtherOfficeExpenseRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
                        </div>
                        <div class="col-md-6 float-left">
                            <div class="form-group">
                                <label for="OtherOfficeIncomeTotal">
                                    Other Income Total:
                                </label>
                                <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeIncomeTotal" id="OtherOfficeIncomeTotal" disabled>
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
                                <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="OtherOfficeExpenseTotal" id="OtherOfficeExpenseTotal" disabled>
                                </div>
                            </div>
                       </div>
                       <input type="hidden" name="OtherOfficeExpenseRowCount" id="OtherOfficeExpenseRowCount" value="<?php echo $OtherOfficeExpenseRowCount; ?>"/>
                        <hr>
                    </div>
                    <div class="form-row form-group">
                        <div class="card-body">
                            <div class="col-md-12 text-center">
                              <button type="submit" id="btn-step-7" class="btn btn-info btn-fill" onClick="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                            </div>
                        </div>

                    </div>
                </section>
                </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
                <!------End Step 7 ------>

                <!------Start Step 8 ------>
                  <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='8') echo "active";?>">
                    <div class="accordion-header js-accordion-header">BANK RECONCILIATION</div>
                    <div class="accordion-body js-accordion-body ">
                    <section>

                        <div class="col-md-12 mar_bot_20" id="StatementBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} ?>>
                            <div class="col-md-12">
                                <strong style="color:red">Please Note</strong><br>
                                    This will refresh the screen - be sure to save all work before clicking button to Upload Bank Statment.<br>
                                <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-statement1" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Bank Statement</button>
                            </div>
                        </div>
                        <input type="hidden" name="StatementFile" id="StatementPath" value="<?php echo $financial_report_array['bank_statement_included_path']; ?>">
                        <div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['bank_statement_included_path']) echo "style=\"display: none;\"";} ?>>
                            <div class="col-md-12" >
                                <div>
                                   <a href="<?php echo $financial_report_array['bank_statement_included_path']; ?>" target="_blank">View Bank Statement</a><br>
                                    <strong style="color:red">Please Note</strong><br>
                                    This will refresh the screen - be sure to save all work before clicking button to Replace Bank Statement.<br>
                                   <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-statement1" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Bank Statement</button>
                            </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-12 mar_bot_20" id="Statement2Block" <?php if (!empty($financial_report_array)) {if ($financial_report_array['bank_statement_2_included_path']) echo "style=\"display: none;\"";} ?>>
                            <div class="col-md-12">
                                <strong style="color:red">Please Note</strong><br>
                                    This will refresh the screen - be sure to save all work before clicking button to Upload Additional Bank Statment.<br>
                                <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-statement2" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload Additional Bank Statement</button>
                            </div>
                        </div>
                        <input type="hidden" name="Statement2File" id="Statement2Path" value="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>">
                        <div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['bank_statement_2_included_path']) echo "style=\"display: none;\"";} ?>>
                            <div class="col-md-12" >
                                <div>
                                   <a href="<?php echo $financial_report_array['bank_statement_2_included_path']; ?>" target="_blank">View Additional Bank Statement</a><br>
                                    <strong style="color:red">Please Note</strong><br>
                                    This will refresh the screen - be sure to save all work before clicking button to Replace Additional Bank Statement.<br>
                                   <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-statement2" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace Additional Bank Statement</button>
                            </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="form-row form-group">
                    <div class="col-md-6 float-left">
                        <div class="form-group">
                            <label for="AmountReservedFromLastYear">
                                This Year's Beginning Balance (July 1, <?php echo date('Y')-1;?>):
                            </label>
                            <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" oninput="TreasuryBalanceChange()" min="0" step="0.01" name="AmountReservedFromLastYear" id="AmountReservedFromLastYear" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['amount_reserved_from_previous_year'] ?>">
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
                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="BankBalanceNow" id="BankBalanceNow" oninput="ChangeBankRec()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['bank_balance_now'] ?>">
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
                            <input type="number" onKeyPress="if(this.value.length==9) return false;" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="PettyCash" id="PettyCash" oninput="ChangeBankRec()" value="<?php if(!empty($financial_report_array)) echo $financial_report_array['petty_cash'] ?>">
                            </div>
                        </div>
                   </div>
                   <div class="col-md-6 float-left">
                    <div class="form-group">
                        <label for="TotalNetIncome">
                            Profit/Loss:
                        </label>
                        <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="TotalNetIncome" id="TotalNetIncome" disabled>
                        </div>
                    </div>
                </div>
                    <div class="col-md-6 float-left">
                        <div class="form-group">
                            <label for="TreasuryBalanceNow">
                                Ending Balance (Treasury Balance Now):
                            </label>
                            <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" onkeydown="return event.keyCode !== 69" class="form-control txt-num" min="0" step="0.01" name="TreasuryBalanceNow" id="TreasuryBalanceNow" disabled>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="form-row form-group">
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
                              <td>Desposit Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $bank_rec_array = null;
                            if(isset($financial_report_array['bank_reconciliation_array'])){
                                $bank_rec_array=unserialize(base64_decode($financial_report_array['bank_reconciliation_array']));
                                $BankRecRowCount = is_array($bank_rec_array) ? count($bank_rec_array) : 0;
                            }
                            else{
                                $BankRecRowCount = 1;
                            }

                                for ($row = 0; $row < $BankRecRowCount; $row++){
                                    echo "<tr>";
                                    echo "<td>
                                    <div class=\"form-group\">
                                    <input type=\"date\" class=\"form-control\" min='2021-07-01' max='2022-06-30' name=\"BankRecDate" . $row . "\" id=\"BankRecDate" . $row . "\"value=\"" . $bank_rec_array[$row]['bank_rec_date'] . "\"  onchange=\"IsValidDate(this)\">
                                    </div>
                                    </td>";

                                    echo "<td>
                                    <div class=\"form-group\">
                                    <input type=\"text\" class=\"form-control\" name=\"BankRecCheckNo" . $row . "\" id=\"BankRecCheckNo" . $row . "\"value=\"" . $bank_rec_array[$row]['bank_rec_check_no'] . "\">
                                    </div>
                                    </td>";

                                    echo "<td>
                                    <div class=\"form-group\">
                                    <input type=\"text\" class=\"form-control\" name=\"BankRecDesc" . $row . "\" id=\"BankRecDesc" . $row . "\"value=\"" . $bank_rec_array[$row]['bank_rec_desc'] . "\">
                                    </div>
                                    </td>";

                                    echo "<td>
                                    <div class=\"form-group\">
                                    <div class=\"input-group\">";
                                    echo "<span class = \"input-group-addon\">$</span>";
                                    echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"BankRecPaymentAmount" . $row . "\" id=\"BankRecPaymentAmount" . $row . "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $bank_rec_array[$row]['bank_rec_payment_amount'] . "\">";
                                    echo "</div>
                                    </div>
                                    </td>";

                                    echo "<td>
                                    <div class=\"form-group\">
                                    <div class=\"input-group\">";
                                    echo "<span class = \"input-group-addon\">$</span>";
                                    echo "<input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\" min=\"0\"  step=\"0.01\" name=\"BankRecDepositAmount" . $row . "\" id=\"BankRecDepositAmount" . $row . "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\" value=\"" . $bank_rec_array[$row]['bank_rec_desposit_amount'] . "\">";
                                    echo "</div>
                                    </div>
                                    </td>";
                                    echo "</tr>";
                                }
                                ?>
                        </tbody>
                    </table>
                    <div class="col-md-12 float-left">
                        <button type="button" class="btn btn-large btn-success btn-add-remove" onclick="AddBankRecRow()" <?php if($submitted) echo "disabled"; ?>>Add</button>
                        <button type="button" class="btn btn-danger btn-add-remove" onclick="DeleteBankRecRow()" <?php if($submitted) echo "disabled"; ?>>Remove</button>
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
                    <div class="col-md-6"><br></div>
                    <div class="col-md-6 float-left">
                        <div class="form-group">
                            <label for="TreasuryBalanceNowR">
                                Treasury Balance Now:
                            </label>
                            <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" class="form-control" min="0" step="0.01" name="TreasuryBalanceNowR" id="TreasuryBalanceNowR" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 float-right">
                        <div class="form-group">
                            <textarea class="form-control" style="width:100%" rows="3" name="ReconciledBankBalanceWarning" id="ReconciledBankBalanceWarning" disabled></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="BankRecRowCount" id="BankRecRowCount" value="<?php echo $BankRecRowCount; ?>" />
                    <hr>
                </div>
                 <div class="form-row form-group">
                        <div class="card-body">
                            <div class="col-md-12 text-center">
                              <button type="submit" id="btn-step-8" class="btn btn-info btn-fill" onClick="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                            </div>
                        </div>
                </div>
            </section>
            </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
            <!------End Step 8 ------>

            <!------Start Step 10 ------>
            <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='10') echo "active";?>">
                <div class="accordion-header js-accordion-header">FINANCIAL SUMMARY</div>
                <div class="accordion-body js-accordion-body ">
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
                                  <span class="f-sm">(Beginning Balance 7/1/<?php echo date('Y')-1;?>)</span>
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
                            <p style="margin-left:10%;">Membership Dues Income:</p>
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
                            <p style="margin-left:10%;">Service Project Income:</p>
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
                            <p style="margin-left:10%;">Party Income:</p>
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
                            <p style="margin-left:10%;">Monetary Donations to Chapter:</p>
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
                            <p style="margin-left:10%;">International Event Reservation Income:</p>
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
                            <p style="margin-left:10%;">Other Income:</p>
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
                            <p style="margin-left:10%;">Meeting Room Expense:</p>
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
                        <div class="col-sm-6">
                        <p style="margin-left:10%;">Children's Room Expenses:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Supplies:</p>
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
                            <p style="margin-left:15%;">Paid Sitters:</p>
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
                            <p style="margin-left:15%;">Other:</p>
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
                            <p style="margin-left:10%;">Children's Room Expense Total:</p>
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
                                <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" class="form-control" name="SumServiceProjectExpense" id="SumServiceProjectExpense" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Amount Donated to Charity/Recipients:</p>
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
                            <p style="margin-left:15%;">Mother-to-Mother Fund Donation:</p>
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
                            <p style="margin-left:10%;">Service Project Expense Total:</p>
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
                            <p style="margin-left:10%;">Party/Members Only Expense:</p>
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
                        <div class="col-sm-6">
                            <p style="margin-left:10%;">Office and Operating Expenses:</p>
                        </div>
                        <div class="col-sm-6 float-left">
                            <p style="margin-left:15%;">Printing:</p>
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
                            <p style="margin-left:15%;">Postage:</p>
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
                            <p style="margin-left:15%;">Membership Pins:</p>
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
                            <p style="margin-left:15%;">Other:</p>
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
                            <p style="margin-left:10%;">Office/Operating Expense Total:</p>
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
                            <p style="margin-left:10%;">Annual Chapter Registration Fee:</p>
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
                            <p style="margin-left:10%;">International Event Registration:</p>
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
                            <p style="margin-left:10%;">Other Expense:</p>
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
                    </div>
                    <div class="col-sm-12">
                        <div class="box-brd">
                            <h4>Net Income</h4>
                            <div class="col-sm-6 float-left">
                                <p style="margin-left:10%;">Profit/Loss:</p>
                            </div>
                            <div class="col-sm-6 float-left">
                                <div class="form-group">
                                    <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" name="SumTotalNetIncome" id="SumTotalNetIncome" disabled>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-sm-12 float-left">
                            <div class="form-group">
                                <div class="col-sm-6 float-left">
                                <label for="">
                                    Treasury Balance Now:
                                    <span class="f-sm">(Ending Balance 6/30/<?php echo date('Y');?>)</span>
                                </label>
                                </div>
                                <div class="col-sm-6 float-left">
                                <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" name="SumTreasuryBalanceNow" id="SumTreasuryBalanceNow" class="form-control" aria-describedby="sizing-addon1" disabled>
                                </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="form-row form-group">
                            <div class="card-body">
                                <div class="col-md-12 text-center">
                                <button type="submit" id="btn-step-10" class="btn btn-info btn-fill" onClick="this.form.submit(); this.disabled=true;" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                                </div>
                            </div>
                        </div> --}}
                </section>
            </div><!-- end of accordion body -->
            </div><!-- end of accordion item -->
            <!------End Step 10 ------>


            <!------Start Step 9 ------>
            <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='9') echo "active";?>">
            <div class="accordion-header js-accordion-header">CHAPTER QUESTIONS</div>
                <div class="accordion-body js-accordion-body">
                <section>
                <div id="form-step-8" role="form" data-toggle="validator" class="form-row form-group">
                    <p>During the last fiscal year (July 1, <?php echo date('Y')-1 .' - June 30, '.date('Y');?>)</p>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>1.</strong> Did anyone in your chapter receive any compensation or pay for their work with your chapter?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="ReceiveCompensation" name="ReceiveCompensation" class="form-control select2" style="width: 100%;" required onchange="ToggleReceiveCompensationExplanation()">
                                    <option value="" {{ is_null($financial_report_array->receive_compensation) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{ $financial_report_array->receive_compensation === 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $financial_report_array->receive_compensation == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="divReceiveCompensationExplanation">
                        <div class="col-md-12">
                            <label for="ReceiveCompensationExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="2" name="ReceiveCompensationExplanation" id="ReceiveCompensationExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['receive_compensation_explanation'];}?></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>2.</strong> Did any officer, member or family of a member benefit financially in any way from the member’s position with your chapter?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="FinancialBenefit" name="FinancialBenefit" class="form-control select2" style="width: 100%" required onchange="ToggleFinancialBenefitExplanation()">
                                    <option value="" {{ is_null($financial_report_array->financial_benefit) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->financial_benefit === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->financial_benefit == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="divFinancialBenefitExplanation">
                        <div class="col-md-12">
                            <label for="FinancialBenefitExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="FinancialBenefitExplanation" id="FinancialBenefitExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['financial_benefit_explanation'];} ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>3.</strong> Did your chapter attempt to influence any national, state/provincial, or local legislation, or did your chapter support any other organization that did?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="InfluencePolitical" name="InfluencePolitical" class="form-control select2" style="width: 100%;" required onchange="ToggleInfluencePoliticalExplanation()">
                                    <option value="" {{ is_null($financial_report_array->influence_political) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->influence_political === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->influence_political == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="divInfluencePoliticalExplanation">
                        <div class="col-md-12">
                            <label for="InfluencePoliticalExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="InfluencePoliticalExplanation" id="InfluencePoliticalExplanation" ><?php if (!is_null($financial_report_array)) {echo $financial_report_array['influence_political_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>4.</strong> Did your chapter vote on all activities and expenditures during the fiscal year?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="VoteAllActivities" name="VoteAllActivities" class="form-control select2" style="width: 100%;" required onchange="ToggleVoteAllActivitiesExplanation()">
                                    <option value="" {{ is_null($financial_report_array->vote_all_activities) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->vote_all_activities === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->vote_all_activities == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12" id="divVoteAllActivitiesExplanation">
                        <div class="col-md-12">
                            <label for="VoteAllActivitiesExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="VoteAllActivitiesExplanation" id="VoteAllActivitiesExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['vote_all_activities_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>5.</strong> Did you purchase pins from International? If No, why not?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="BoughtPins" name="BoughtPins" class="form-control select2" style="width: 100%;" required onchange="ToggleBoughtPinsExplanation()">
                                    <option value="" {{ is_null($financial_report_array->purchase_pins) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->purchase_pins === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->purchase_pins == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-12" id="divBoughtPinsExplanation">
                            <label for="BoughtPinsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="BoughtPinsExplanation" id="BoughtPinsExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['purchase_pins_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>6.</strong> Did you purchase any merchandise from International other than pins? If No, why not?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="BoughtMerch" name="BoughtMerch" class="form-control select2" style="width: 100%;" required onchange="ToggleBoughtMerchExplanation()">
                                    <option value="" {{ is_null($financial_report_array->bought_merch) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->bought_merch === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->bought_merch == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-12" id="divBoughtMerchExplanation">
                            <label for="BoughtMerchExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="BoughtMerchExplanation" id="BoughtMerchExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['bought_merch_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>7.</strong> Did you offer or inform your members about MOMS Club merchandise?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="OfferedMerch" name="OfferedMerch" class="form-control select2" style="width: 100%;" required onchange="ToggleOfferedMerchExplanation()">
                                    <option value="" {{ is_null($financial_report_array->offered_merch) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->offered_merch === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->offered_merch == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-12" id="divOfferedMerchExplanation">
                            <label for="OfferedMerchExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="OfferedMerchExplanation" id="OfferedMerchExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['offered_merch_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                                <label style="display: block;"><strong>8.</strong> Did you make the Bylaws and/or manual available for any chapter members that requested them?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                                <select id="ByLawsAvailable" name="ByLawsAvailable" class="form-control select2" style="width: 100%;" required onchange="ToggleByLawsAvailableExplanation()">
                                    <option value="" {{ is_null($financial_report_array->bylaws_available) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->bylaws_available === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->bylaws_available == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-12" id="divByLawsAvailableExplanation">
                            <label for="ByLawsAvailableExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                            <textarea class="form-control" rows="2" name="ByLawsAvailableExplanation" id="ByLawsAvailableExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['bylaws_available_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                            <label style="display: block;"><strong>9.</strong> Did you have a children’s room with babysitters?<span class="field-required">*</span></label>
                        </div>
                        <div class="col-md-3">
                            <select id="ChildrensRoom" name="ChildrensRoom" class="form-control select2" style="width: 100%;" required>
                                <option value="" {{ is_null($financial_report_array->childrens_room_sitters) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->childrens_room_sitters === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->childrens_room_sitters == 1 ? 'selected' : ''}}>Yes, with volunteer members</option>
                                <option value="2" {{$financial_report_array->childrens_room_sitters == 2 ? 'selected' : ''}}>Yes, with paid sitters</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-sm-12">
                        <div class="col-sm-12" id="ChildrensRoomExplanation">
                        <label for="ChildrensRoomExplanation">Briefly explain, if necessary:</label>
                        <textarea class="form-control" rows="2" name="ChildrensRoomExplanation" id="ChildrensRoomExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['childrens_room_sitters_explanation'];}?></textarea>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                            <label style="display: block;"><strong>10.</strong> Did you have playgroups? If so, how were they arranged.<span class="field-required">*</span></label>
                        </div>
                        <div class="col-md-3">
                            <select id="Playgroups" name="Playgroups" class="form-control select2" style="width: 100%;" required>
                                <option value="" {{ is_null($financial_report_array->playgroups) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->playgroups === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->playgroups == 1 ? 'selected' : ''}}>Yes, arranged by age</option>
                                <option value="2" {{$financial_report_array->playgroups == 2 ? 'selected' : ''}}>Yes, multi-aged groups</option>
                            </select>
                        </div>
                    </div>
                </div>
                        <div class="col-sm-12">
                            <div class="col-sm-12" id="PlaygroupsExplanation">
                                <label for="PlaygroupsExplanation">Briefly explain, if necessary:</label>
                                <textarea class="form-control" rows="2" name="PlaygroupsExplanation" id="PlaygroupsExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['had_playgroups_explanation'];}?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12 float-left">
                            <div class="form-inline">
                                <div class="col-md-9">
                                <label style="display: block;"><strong>11.</strong> Did you have any child focused outings or activities?<span class="field-required">*</span></label>
                                </div>
                                <div class="col-md-3">
                                <select id="ChildOutings" name="ChildOutings" class="form-control select2" style="width: 100%;" required onchange="ToggleChildOutingsExplanation()">
                                    <option value="" {{ is_null($financial_report_array->child_outings) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->child_outings === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->child_outings == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                    <div class="col-sm-12" id="divChildOutingsExplanation">
                        <label for="ChildOutingsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                        <textarea class="form-control" rows="2" name="ChildOutingsExplanation" id="ChildOutingsExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['child_outings_explanation'];}?></textarea>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-md-9">
                            <label style="display: block;"><strong>12.</strong> Did you have any mother focused outings or activities?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-md-3">
                            <select id="MotherOutings" name="MotherOutings" class="form-control select2" style="width: 100%;" required onchange="ToggleMotherOutingsExplanation()">
                                <option value="" {{ is_null($financial_report_array->mother_outings) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->mother_outings === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->mother_outings == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-12" id="divMotherOutingsExplanation">
                        <label for="MotherOutingsExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                        <textarea class="form-control" rows="2" name="MotherOutingsExplanation" id="MotherOutingsExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['mother_outings_explanation'];}?></textarea>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;"><strong>13.</strong> Did you have speakers at any meetings?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-3">
                            <select id="MeetingSpeakers" name="MeetingSpeakers" class="form-control select2" style="width: 100%;" required onchange="ToggleMeetingSpeakersExplanation()">
                                <option value="" {{ is_null($financial_report_array->meeting_speakers) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->meeting_speakers === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->meeting_speakers == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-12" id="divMeetingSpeakersExplanation">
                        <label for="MeetingSpeakersExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                        <textarea class="form-control" rows="2" name="MeetingSpeakersExplanation" id="MeetingSpeakersExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['meeting_speakers_explanation'];}?></textarea>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label style="display: block;"><strong>14.</strong> If you had speakers, check any of the topics that were covered:<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-12">
                            <div class="col-sm-12">
                            <select id="Speakers" name="Speakers[]" class="form-control select2" style="width: 200px; height: 200px;" multiple="multiple" required>
                                <option value="" {{ is_null($financial_report_array->meeting_speakers_array) ? 'selected' : '' }}>N/A</option>
                                @php
                                    $selectedValues = is_null($financial_report_array->meeting_speakers_array)
                                        ? []
                                        : json_decode($financial_report_array->meeting_speakers_array);
                                @endphp
                                <option value="0" {{ in_array('0', $selectedValues) ? 'selected' : '' }}>Child Rearing</option>
                                <option value="1" {{ in_array('1', $selectedValues) ? 'selected' : '' }}>Schools/Education</option>
                                <option value="2" {{ in_array('2', $selectedValues) ? 'selected' : '' }}>Home Management</option>
                                <option value="3" {{ in_array('3', $selectedValues) ? 'selected' : '' }}>Politics</option>
                                <option value="4" {{ in_array('4', $selectedValues) ? 'selected' : '' }}>Other Non-Profit</option>
                                <option value="5" {{ in_array('5', $selectedValues) ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;"><strong>15.</strong> Did you have any discussion topics at your meetings? If yes, how often?<span class="field-required">*</span></label>
                        </div>
                        <div class="col-sm-3">
                            <select id="SpeakerFrequency" name="SpeakerFrequency" class="form-control select2" style="width: 100%;" required>
                                <option value="" {{ is_null($financial_report_array->discussion_topic_frequency) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->discussion_topic_frequency === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->discussion_topic_frequency == 1 ? 'selected' : ''}}>1-3 Times</option>
                                <option value="2" {{$financial_report_array->discussion_topic_frequency == 2 ? 'selected' : ''}}>4-6 Times</option>
                                <option value="3" {{$financial_report_array->discussion_topic_frequency == 3 ? 'selected' : ''}}>7-9 Times</option>
                                <option value="4" {{$financial_report_array->discussion_topic_frequency == 4 ? 'selected' : ''}}>10+ Times</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;"><strong>16.</strong> Did your chapter have scheduled park days? If yes, how often?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-3">
                            <select id="ParkDays" name="ParkDays" class="form-control select2" style="width: 100%;" required>
                                <option value="" {{ is_null($financial_report_array->park_day_frequency) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->park_day_frequency === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->park_day_frequency == 1 ? 'selected' : ''}}>1-3 Times</option>
                                <option value="2" {{$financial_report_array->park_day_frequency == 2 ? 'selected' : ''}}>4-6 Times</option>
                                <option value="3" {{$financial_report_array->park_day_frequency == 3 ? 'selected' : ''}}>7-9 Times</option>
                                <option value="4" {{$financial_report_array->park_day_frequency == 4 ? 'selected' : ''}}>10+ Times</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label style="display: block;">17. Did your chapter have any of the following activity groups?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-12">
                            <select id="Activity" name="Activity[]" class="form-control select2" style="width: 200px; height: 200px;" multiple="multiple" required>
                                <option value="" {{ is_null($financial_report_array->activity_array) ? 'selected' : '' }}>N/A</option>
                                @php
                                    $selectedValues = is_null($financial_report_array->activity_array)
                                        ? []
                                        : json_decode($financial_report_array->activity_array);
                                @endphp
                                <option value="0" {{ in_array('0', $selectedValues) ? 'selected' : '' }}>Cooking</option>
                                <option value="1" {{ in_array('1', $selectedValues) ? 'selected' : '' }}>Cost Cutting Tips</option>
                                <option value="2" {{ in_array('2', $selectedValues) ? 'selected' : '' }}>Mommy Playgroup</option>
                                <option value="3" {{ in_array('3', $selectedValues) ? 'selected' : '' }}>Babysitting Co-op</option>
                                <option value="4" {{ in_array('4', $selectedValues) ? 'selected' : '' }}>MOMS Night Out</option>
                                <option value="5" {{ in_array('5', $selectedValues) ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                </div>
                <div class="col-sm-12">
                     <div class="col-sm-12" id="divActivityOtherExplanation">
                        <label for="ActivityOtherExplanation">If other, briefly explain:</label>
                        <textarea class="form-control" rows="2" name="ActivityOtherExplanation" id="ActivityOtherExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['activity_other_explanation'];}?></textarea>
                     </div>
                    </div>
                    <div class="clearfix"></div>
                     <div class="col-md-12"><br></div>
                     <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;">18. Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity? If yes, please explain who received the contributions and why you chose them:<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-3">
                            <select id="ContributionsNotRegNP" name="ContributionsNotRegNP" class="form-control select2" style="width: 100%;" required onchange="ToggleContributionsNotRegNPExplanation()">
                                <option value="" {{ is_null($financial_report_array->contributions_not_registered_charity) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->contributions_not_registered_charity === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->contributions_not_registered_charity == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-12" id="divContributionsNotRegNPExplanation">
                        <label for="ContributionsNotRegNPExplanation">If yes, briefly explain:<span class="field-required">*</span></label>
                        <textarea class="form-control" rows="2" name="ContributionsNotRegNPExplanation" id="ContributionsNotRegNPExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['contributions_not_registered_charity_explanation'];}?></textarea>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;">19. Did your chapter perform at least one service project to benefit mothers or children?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-3">
                            <select id="PerformServiceProject" name="PerformServiceProject" class="form-control select2" style="width: 100%;" required onchange="TogglePerformServiceProjectExplanation()">
                                <option value="" {{ is_null($financial_report_array->at_least_one_service_project) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->at_least_one_service_project === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->at_least_one_service_project == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-12" id="divPerformServiceProjectExplanation">
                        <label for="PerformServiceProjectExplanation">If no, briefly explain:<span class="field-required">*</span></label>
                        <textarea class="form-control" rows="2" name="PerformServiceProjectExplanation" id="PerformServiceProjectExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['at_least_one_service_project_explanation'];}?></textarea>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;">20. Did your chapter sister another chapter?<span class="field-required">*</span></label>
                            </div><div class="col-sm-3">
                            <select id="SisterChapter" name="SisterChapter" class="form-control select2" style="width: 100%;" required>
                                <option value="" {{ is_null($financial_report_array->sister_chapter) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->sister_chapter === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->sister_chapter == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;">21. Did your chapter attend an International Event (in person or virtual)?<span class="field-required">*</span></label>
                            </div><div class="col-sm-3">
                            <select id="InternationalEvent" name="InternationalEvent" class="form-control select2" style="width: 100%;" required>
                                <option value="" {{ is_null($financial_report_array->international_event) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->international_event === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->international_event == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 float-left">
                        <div class="form-inline">
                            <div class="col-sm-9">
                            <label style="display: block;">22. Did your chapter file their IRS 990N for <?php echo date('Y')-1 .'-'.date('Y');?> (CANNOT BE DONE BEFORE JULY 1, <?php echo date('Y');?>)?<span class="field-required">*</span></label>
                            </div>
                            <div class="col-sm-3">
                            <select id="FileIRS" name="FileIRS" class="form-control select2" style="width: 100%;" required onchange="ToggleFileIRSExplanation()">
                                <option value="" {{ is_null($financial_report_array->file_irs) ? 'selected' : '' }} disabled>Please Select</option>
                                <option value="0" {{$financial_report_array->file_irs === 0 ? 'selected' : ''}}>No</option>
                                <option value="1" {{$financial_report_array->file_irs == 1 ? 'selected' : ''}}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-12" id="divFileIRSExplanation">
                        <label for="FileIRSExplanation">If no, briefly explain:</label>
                        <textarea class="form-control" rows="2" name="FileIRSExplanation" id="FileIRSExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['file_irs_explanation'];}?></textarea>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                    <div class="col-md-12"><br></div>
                    <div class="col-md-12 mar_bot_20" id="990NBlock" <?php if (!empty($financial_report_array)) {if ($financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
                        <div class="col-md-12">
                            <strong style="color:red">Please Note</strong><br>
                                This will refresh the screen - be sure to save all work before clicking button to Upload 990N Confirmation.<br>
                            <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-990N" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-upload fa-fw" aria-hidden="true" ></i>&nbsp; Upload 990N Confirmation</button>
                        </div>
                    </div>
                    <input type="hidden" name="990NFiling" id="990NFiling" value="<?php echo $financial_report_array['file_irs_path']; ?>">
                    <div class="col-md-12 mar_bot_20" <?php if (!empty($financial_report_array)) {if (!$financial_report_array['file_irs_path']) echo "style=\"display: none;\"";} ?>>
                        <div class="col-md-12" >
                            <div>
                               <a href="<?php echo $financial_report_array['file_irs_path']; ?>" target="_blank">View 990N Confirmation</a><br>
                               <br>
                                <strong style="color:red">Please Note</strong><br>
                                This will refresh the screen - be sure to save all work before clicking button to Replace 990N Confirmation.<br>
                               <button type="button" class="btn btn-info btn-fill" data-toggle="modal" data-target="#modal-990N" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-refresh fa-fw" aria-hidden="true" ></i>&nbsp; Replace 990N Confirmation</button>
                        </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12 float-left">
                            <div class="form-inline">
                                <div class="col-sm-9">
                                <label style="display: block;">23. Is a copy of your chapter’s most recent bank statement included with the copy of this report that you are submitting to International?<span class="field-required">*</span></label>
                                </div>
                                <div class="col-sm-3">
                                <select id="BankStatementIncluded" name="BankStatementIncluded" class="form-control select2" style="width: 100%;" required onchange="ToggleBankStatementIncludedExplanation()">
                                    <option value="" {{ is_null($financial_report_array->bank_statement_included) ? 'selected' : '' }} disabled>Please Select</option>
                                    <option value="0" {{$financial_report_array->bank_statement_included === 0 ? 'selected' : ''}}>No</option>
                                    <option value="1" {{$financial_report_array->bank_statement_included == 1 ? 'selected' : ''}}>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-12" id="divBankStatementIncludedExplanation">
                            <label for="BankStatementIncludedExplanation">If no, briefly explain:</label>
                            <textarea class="form-control" rows="2" name="BankStatementIncludedExplanation" id="BankStatementIncludedExplanation"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['bank_statement_included_explanation'];}?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12"><br></div>
                        <div class="col-sm-12" >
                            <div class="col-sm-12" id="WheresTheMoney">
                            <label style="display: block;">24. If your group does not have any bank accounts, where is the chapter money kept?:</label>
                            <textarea class="form-control" rows="2" name="WheresTheMoney" id="WheresTheMoney"><?php if (!is_null($financial_report_array)) {echo $financial_report_array['wheres_the_money'];}?></textarea>
                        </div>
                    </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="card-body">
                            <div class="col-md-12 text-center">
                              <button type="button" class="btn btn-info btn-fill" id="btn-step-9" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                            </div>
                        </div>
                    </div>
              </section>
          </div><!-- end of accordion body -->
          </div><!-- end of accordion item -->
            <!------End Step 9 ------>

            <!------Begin Step 11 ------>
            <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='11') echo "active";?>">
                <div class="accordion-header js-accordion-header">AWARD NOMINATIONS</div>
                <div class="accordion-body js-accordion-body ">
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

                                    <div class="award_sec_btn">
                                        <button type="button" id="btnAddAwardNomination" class="btn btn-large btn-success btn-add-remove" onclick="AddAwardNomination()" <?php if($submitted || $financial_report_array['award_nominations']==5) echo "disabled"; ?>>Add Nomination</button>
                                        <button type="button" id="btnDeleteAwardNomination" class="btn btn-danger btn-add-remove" onclick="DeleteAwardNomination()" <?php if($submitted || $financial_report_array['award_nominations']<1) echo "disabled"; ?>>Delete Nomination</button>
                                    </div>
                            </div>
                        </div>
                        <!-- Award 1 Start -->
                        <div class="box_brd_contentpad" id="Award1Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<1) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="form-group">
                                <label for="NominationType1">Select list:</label>
                                    <select class="form-control" id="NominationType1" name="NominationType1" onClick="ShowOutstandingCriteria(1)">
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
                            <div class="award_acc_con">
                                <div id="OutstandingCriteria1" style="display: none;">
                                <div class="col-sm-12">
                                    <h4>Outstanding Chapter Criteria</h4>
                                    <p><label for="OutstandingFollowByLaws1">Did you follow the Bylaws and all instructions from International?</label></p>
                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingFollowByLaws1Yes" name="OutstandingFollowByLaws1" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_1_outstanding_follow_bylaws'])) {if ($financial_report_array['award_1_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingFollowByLaws1No" name="OutstandingFollowByLaws1" <?php if (!is_null($financial_report_array['award_1_outstanding_follow_bylaws'])) {if ($financial_report_array['award_1_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingWellRounded1">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingWellRounded1Yes" name="OutstandingWellRounded1" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_1_outstanding_well_rounded'])) {if ($financial_report_array['award_1_outstanding_well_rounded'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingWellRounded1No" name="OutstandingWellRounded1" <?php if (!is_null($financial_report_array['award_1_outstanding_well_rounded'])) {if ($financial_report_array['award_1_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingCommunicated1">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingCommunicated1Yes" name="OutstandingCommunicated1" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_1_outstanding_communicated'])) {if ($financial_report_array['award_1_outstanding_communicated'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input"id="OutstandingCommunicated1No" name="OutstandingCommunicated1" <?php if (!is_null($financial_report_array['award_1_outstanding_communicated'])) {if ($financial_report_array['award_1_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
                                                <input id="OutstandingSupportMomsClub1Yes" name="OutstandingSupportMomsClub1" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_1_outstanding_support_international'])) {if ($financial_report_array['award_1_outstanding_support_international'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingSupportMomsClub1No" name="OutstandingSupportMomsClub1" <?php if (!is_null($financial_report_array['award_1_outstanding_support_international'])) {if ($financial_report_array['award_1_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                </div>

                                 <h4>Description</h4>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc1" name="AwardDesc1"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_1_outstanding_project_desc'];}?></textarea>
                                 </div>

                                 <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_files']) echo "style=\"display: none;\"";} ?>>
                                    <label>Award Files for this Nomination (each file 5MB max):</label>
                                    <input type="file" class="demo1" name="Award1[]" id="Award1" accept=".pdf, .jpg, .jpeg">
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
                        <div class="box_brd_contentpad" id="Award2Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<2) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="form-group">
                                <label for="NominationType2">Select list:</label>
                                    <select class="form-control" id="NominationType2" name="NominationType2" onClick="ShowOutstandingCriteria(2)">
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
                            <div class="award_acc_con">
                                <div id="OutstandingCriteria2" style="display: none;">
                                <div class="col-sm-12">
                                    <h4>Outstanding Chapter Criteria</h4>
                                    <p><label for="OutstandingFollowByLaws2">Did you follow the Bylaws and all instructions from International?</label></p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingFollowByLaws2Yes" name="OutstandingFollowByLaws2" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_2_outstanding_follow_bylaws'])) {if ($financial_report_array['award_2_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingFollowByLaws2No" name="OutstandingFollowByLaws2" <?php if (!is_null($financial_report_array['award_2_outstanding_follow_bylaws'])) {if ($financial_report_array['award_2_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingWellRounded2">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingWellRounded2Yes" name="OutstandingWellRounded2" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_2_outstanding_well_rounded'])) {if ($financial_report_array['award_2_outstanding_well_rounded'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingWellRounded2No" name="OutstandingWellRounded2" <?php if (!is_null($financial_report_array['award_2_outstanding_well_rounded'])) {if ($financial_report_array['award_2_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingCommunicated2">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingCommunicated2Yes" name="OutstandingCommunicated2" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_2_outstanding_communicated'])) {if ($financial_report_array['award_2_outstanding_communicated'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input"id="OutstandingCommunicated2No" name="OutstandingCommunicated2" <?php if (!is_null($financial_report_array['award_2_outstanding_communicated'])) {if ($financial_report_array['award_2_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
                                                <input id="OutstandingSupportMomsClub2Yes" name="OutstandingSupportMomsClub2" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_2_outstanding_support_international'])) {if ($financial_report_array['award_2_outstanding_support_international'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingSupportMomsClub2No" name="OutstandingSupportMomsClub2" <?php if (!is_null($financial_report_array['award_2_outstanding_support_international'])) {if ($financial_report_array['award_2_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                </div>

                                 <h4>Description</h4>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc2" name="AwardDesc2"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_2_outstanding_project_desc'];}?></textarea>
                                 </div>

                                 <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_files']) echo "style=\"display: none;\"";} ?>>
                                    <label>Award Files for this Nomination (each file 5MB max):</label>
                                    <input type="file" class="demo1" name="Award2[]" id="Award2" accept=".pdf, .jpg, .jpeg">
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
                        <div class="box_brd_contentpad" id="Award3Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<3) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="form-group">
                                <label for="NominationType3">Select list:</label>
                                    <select class="form-control" id="NominationType3" name="NominationType3" onClick="ShowOutstandingCriteria(3)">
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
                            <div class="award_acc_con">
                                <div id="OutstandingCriteria3" style="display: none;">
                                <div class="col-sm-12">
                                    <h4>Outstanding Chapter Criteria</h4>
                                    <p><label for="OutstandingFollowByLaws3">Did you follow the Bylaws and all instructions from International?</label></p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingFollowByLaws3Yes" name="OutstandingFollowByLaws3" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_3_outstanding_follow_bylaws'])) {if ($financial_report_array['award_3_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingFollowByLaws3No" name="OutstandingFollowByLaws3" <?php if (!is_null($financial_report_array['award_3_outstanding_follow_bylaws'])) {if ($financial_report_array['award_3_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingWellRounded3">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingWellRounded3Yes" name="OutstandingWellRounded3" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_3_outstanding_well_rounded'])) {if ($financial_report_array['award_3_outstanding_well_rounded'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingWellRounded3No" name="OutstandingWellRounded3" <?php if (!is_null($financial_report_array['award_3_outstanding_well_rounded'])) {if ($financial_report_array['award_3_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingCommunicated3">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingCommunicated3Yes" name="OutstandingCommunicated3" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_3_outstanding_communicated'])) {if ($financial_report_array['award_3_outstanding_communicated'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input"id="OutstandingCommunicated3No" name="OutstandingCommunicated3" <?php if (!is_null($financial_report_array['award_3_outstanding_communicated'])) {if ($financial_report_array['award_3_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
                                                <input id="OutstandingSupportMomsClub3Yes" name="OutstandingSupportMomsClub3" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_3_outstanding_support_international'])) {if ($financial_report_array['award_3_outstanding_support_international'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingSupportMomsClub3No" name="OutstandingSupportMomsClub3" <?php if (!is_null($financial_report_array['award_3_outstanding_support_international'])) {if ($financial_report_array['award_3_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                </div>

                                 <h4>Description</h4>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc3" name="AwardDesc3"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_3_outstanding_project_desc'];}?></textarea>
                                 </div>
                                 <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_files']) echo "style=\"display: none;\"";} ?>>
                                    <label>Award Files for this Nomination (each file 5MB max):</label>
                                    <input type="file" class="demo1" name="Award3[]" id="Award3" accept=".pdf, .jpg, .jpeg">
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
                        <div class="box_brd_contentpad" id="Award4Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<4) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="form-group">
                                <label for="NominationType4">Select list:</label>
                                    <select class="form-control" id="NominationType4" name="NominationType4" onClick="ShowOutstandingCriteria(4)">
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
                            <div class="award_acc_con">
                                <div id="OutstandingCriteria4" style="display: none;">
                                <div class="col-sm-12">
                                    <h4>Outstanding Chapter Criteria</h4>
                                    <p><label for="OutstandingFollowByLaws4">Did you follow the Bylaws and all instructions from International?</label></p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingFollowByLaws4Yes" name="OutstandingFollowByLaws4" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_4_outstanding_follow_bylaws'])) {if ($financial_report_array['award_4_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingFollowByLaws4No" name="OutstandingFollowByLaws4" <?php if (!is_null($financial_report_array['award_4_outstanding_follow_bylaws'])) {if ($financial_report_array['award_4_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingWellRounded4">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingWellRounded4Yes" name="OutstandingWellRounded4" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_4_outstanding_well_rounded'])) {if ($financial_report_array['award_4_outstanding_well_rounded'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingWellRounded4No" name="OutstandingWellRounded4" <?php if (!is_null($financial_report_array['award_4_outstanding_well_rounded'])) {if ($financial_report_array['award_4_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingCommunicated4">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingCommunicated4Yes" name="OutstandingCommunicated4" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_4_outstanding_communicated'])) {if ($financial_report_array['award_4_outstanding_communicated'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input"id="OutstandingCommunicated4No" name="OutstandingCommunicated4" <?php if (!is_null($financial_report_array['award_4_outstanding_communicated'])) {if ($financial_report_array['award_4_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
                                                <input id="OutstandingSupportMomsClub4Yes" name="OutstandingSupportMomsClub4" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_4_outstanding_support_international'])) {if ($financial_report_array['award_4_outstanding_support_international'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingSupportMomsClub4No" name="OutstandingSupportMomsClub4" <?php if (!is_null($financial_report_array['award_4_outstanding_support_international'])) {if ($financial_report_array['award_4_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                </div>

                                 <h4>Description</h4>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc4" name="AwardDesc4"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_4_outstanding_project_desc'];}?></textarea>
                                 </div>

                                <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_files']) echo "style=\"display: none;\"";} ?>>
                                    <label>Award Files for this Nomination (each file 5MB max):</label>
                                    <input type="file" class="demo1" name="Award4[]" id="Award4" accept=".pdf, .jpg, .jpeg">
                                 </div>
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
                        <div class="box_brd_contentpad" id="Award5Panel" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<5) echo "none;"; else echo "block;";} else echo "none;";?>">
                            <div class="box_brd_title_box">
                                <div class="form-group">
                                <label for="NominationType5">Select list:</label>
                                    <select class="form-control" id="NominationType5" name="NominationType5" onClick="ShowOutstandingCriteria(5)">
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
                            <div class="award_acc_con">
                                <div id="OutstandingCriteria5" style="display: none;">
                                <div class="col-sm-12">
                                    <h4>Outstanding Chapter Criteria</h4>
                                    <p><label for="OutstandingFollowByLaws5">Did you follow the Bylaws and all instructions from International?</label></p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingFollowByLaws5Yes" name="OutstandingFollowByLaws5" type="radio" class="form-check-input" value="yes"<?php if (!is_null($financial_report_array['award_5_outstanding_follow_bylaws'])) {if ($financial_report_array['award_5_outstanding_follow_bylaws'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingFollowByLaws5No" name="OutstandingFollowByLaws5" <?php if (!is_null($financial_report_array['award_5_outstanding_follow_bylaws'])) {if ($financial_report_array['award_5_outstanding_follow_bylaws'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingWellRounded5">Did you run a well-rounded program for your members?</label><br>Speakers, discussions, a well-run children’s room (if your chapter has one during meetings), a variety of outings, playgroups, other activity groups, service projects, parties/member benefits kept under 15% of the dues received -- these are all taken into consideration. A chapter that has lots of activities for its mothers-of-infants, but nothing for the mothers of older children (or vice versa) would not be offering a well-rounded program.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingWellRounded5Yes" name="OutstandingWellRounded5" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_5_outstanding_well_rounded'])) {if ($financial_report_array['award_5_outstanding_well_rounded'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingWellRounded5No" name="OutstandingWellRounded5" <?php if (!is_null($financial_report_array['award_5_outstanding_well_rounded'])) {if ($financial_report_array['award_5_outstanding_well_rounded'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                <div class="col-sm-12">
                                    <p><label for="OutstandingCommunicated5">Did you communicate with your Coordinator?</label><br>Did you send in your newsletter regularly? Send updates? Return telephone calls? A chapter MUST communicate often and positively with their Coordinator to receive this award.</p>

                                        <div class="form-check form-check-radio">
                                            <label class="form-check-label">
                                                <input id="OutstandingCommunicated5Yes" name="OutstandingCommunicated5" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_5_outstanding_communicated'])) {if ($financial_report_array['award_5_outstanding_communicated'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input"id="OutstandingCommunicated5No" name="OutstandingCommunicated5" <?php if (!is_null($financial_report_array['award_5_outstanding_communicated'])) {if ($financial_report_array['award_5_outstanding_communicated'] == 0) echo "checked";} ?> value="no">
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
                                                <input id="OutstandingSupportMomsClub5Yes" name="OutstandingSupportMomsClub5" type="radio" class="form-check-input" value="yes" <?php if (!is_null($financial_report_array['award_5_outstanding_support_international'])) {if ($financial_report_array['award_5_outstanding_support_international'] == 1) echo "checked";} ?>>
                                                <span class="form-check-sign"></span>
                                                      Yes
                                            </label>
                                            <label class="form-check-label">
                                                <input type="radio" class="form-check-input" id="OutstandingSupportMomsClub5No" name="OutstandingSupportMomsClub5" <?php if (!empty($financial_report_array['award_5_outstanding_support_international'])) {if ($financial_report_array['award_5_outstanding_support_international'] == 0) echo "checked";} ?> value="no">
                                                <span class="form-check-sign"></span>
                                                      No
                                            </label>
                                        </div>

                                </div>
                                </div>

                                 <h4>Description</h4>
                                 <p>Please include a written description of your project/activities. Be sure to give enough information so that someone who is not familiar with your project or activity can see how wonderful it was! You may also attach any related photos or newspaper clippings. You may be contacted for more information, if necessary.</p>
                                 <div class="form-group">
                                    <textarea class="form-control" rows="20" id="AwardDesc5" name="AwardDesc5"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_5_outstanding_project_desc'];}?></textarea>
                                 </div>

                                <div class="form-group" <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_files']) echo "style=\"display: none;\"";} ?>>
                                    <label>Award Files for this Nomination (each file 5MB max):</label>
                                    <input type="file" class="demo1" name="Award5[]" id="Award5" accept=".pdf, .jpg, .jpeg">
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

                        <div class="box_brd_contentpad" id="AwardSignatureBlock" style="display: <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_nominations']<1) echo "none;"; else echo "block;";} else echo "none;";?>">
                              <div class="box_brd_title_box">
                                 <h4>ALL ENTRIES MUST INCLUDE THIS SIGNED AGREEMENT</h4>
                              </div>
                                <div class="award_acc_con">
                                    <p>I, THE UNDERSIGNED, AFFIRM THAT I HAVE THE RIGHT TO SUBMIT THE ENCLOSED ENTRY TO THE INTERNATIONAL MOMS CLUB FOR CONSIDERATION IN THEIR OUTSTANDING CHAPTER RECOGNITIONS, THAT THE ENCLOSED INFORMATION IS ACCURATE AND COMPLETE TO THE BEST OF MY ABILITY AND THAT I HAVE RECEIVED PERMISSION TO ENTER THIS INFORMATION FROM ANY OTHER MEMBERS WHO MAY HAVE CONTRIBUTED TO THIS ENTRY OR THE ORIGINAL ACTIVITY/PROJECT THAT IS BEING CONSIDERED. I UNDERSTAND THAT, WHETHER OR NOT MY CHAPTER RECEIVES A RECOGNITION, THE ENCLOSED ENTRY WILL BECOME THE PROPERTY OF THE INTERNATIONAL MOMS CLUB AND THAT THE INFORMATION, PICTURES, CLIPPINGS AND/OR OTHER MATERIALS ENCLOSED MAY BE SHARED WITH OTHER MOMS CLUB CHAPTERS OR USED IN ANY WAY THE INTERNATIONAL MOMS CLUB SEES FIT, WITH NO COMPENSATION TO ME, OTHERS INVOLVED IN THIS PROJECT AND/OR THE CHAPTER(S). NO ENTRIES OR SUBMITTED MATERIALS WILL BE RETURNED AND THE INTERNATIONAL MOMS CLUB MAY REASSIGN ANY ENTRY TO ANOTHER CATEGORY IF IT DEEMS NECESSARY. RECOGNITIONS WILL BE GIVEN IN THE VARIOUS CATEGORIES ACCORDING TO THE DECISION OF THE INTERNATIONAL MOMS CLUB. THE AWARDING OF RECOGNITIONS WILL BE ACCORDING TO MERIT, AND THE INTERNATIONAL MOMS CLUB MAY DECIDE NOT TO GIVE AN AWARD IN ANY OR ALL CATEGORIES IF IT SO CHOOSES. ALL DECISIONS OF THE INTERNATIONAL MOMS CLUB ARE FINAL. ANY RECOGNITIONS ARE OFFICIALLY PRESENTED TO THE LOCAL CHAPTERS, NOT THE INDIVIDUAL, AND RECOGNITIONS WILL NOT BE PERSONALIZED WITH ANY INDIVIDUAL’S NAME. REPLACEMENT RECOGNITIONS MAY OR MAY NOT BE MADE AVAILABLE AT INTERNATIONAL’S DISCRETION, AND IF A REPLACEMENT IS MADE BECAUSE OF AN ERROR IN THE ENTRY INFORMATION, THE COST WILL BE PAID IN ADVANCE BY THE LOCAL CHAPTER.</p>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="AwardsAgree" name="AwardsAgree" <?php if (isset($financial_report_array['award_agree']) && $financial_report_array['award_agree'] == 1) echo "checked"; ?>  required>I understand and agree to the above</label>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
                                        <input id="NominationSubmitor" name="NominationSubmitor" type="text" class="form-control" value="<?php echo $loggedInName; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="form-row form-group">
                       <div class="card-body">
                          <div class="col-md-12 text-center">
                             <button type="button" id="btn-step-11" class="btn btn-info btn-fill" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>

                            </div>
                       </div>
                    </div>
                     </section>
                    </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
                <!------End Step 11 ------>

                <!------Start Step 12 ------>
                <div class="accordion__item js-accordion-item <?php if($financial_report_array['farthest_step_visited'] =='12') echo "active";?>">
                    <div class="accordion-header js-accordion-header">SUBMIT REPORT</div>
                    <div class="accordion-body js-accordion-body ">
                        <section>
                            <div class="form-row form-group">
                            <p>Contact information for the person completing the reports for your chapter:</p>
                                <div class="col-md-6 float-left">
                                    <label for="CompletedName">
                                        Name (First & Last)
                                    </label><span class="field-required">*</span>
                                <input type="text" name="CompletedName" id="CompletedName" class="form-control" value="<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_name'];}?>" required >
                                </div>
                                <div class="col-md-6 float-left">
                                    <label for="CompletedEmail">
                                        Email Address
                                    </label><span class="field-required">*</span>
                                 <input type="text" name="CompletedEmail" id="CompletedEmail" class="form-control" value="<?php if (!is_null($financial_report_array)) {echo $financial_report_array['completed_email'];}?>" required >
                                </div>
                            </div>
                            <hr>
                            <div class="form-row form-group">
                                <div class="card-body">
                                    <div class="col-md-12 text-center">
                                        <button type="button" class="btn btn-info btn-fill" id="btn-step-12" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                                        <button type="button" class="btn btn-info btn-fill" id="final-submit" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-mail-forward fa-fw" aria-hidden="true" ></i>&nbsp; Submit</button>
                                    </div>
                                    <hr style="height:3px;border:none;color:#0c71c3;background-color:#0c71c3;" />
                                </div>
                            </div>
                        </section>
                    </div><!-- end of accordion body -->
                </div><!-- end of accordion item -->
                <!------End Step 12 ------>

                </div><!-- end of accordion -->
            </form>
        </div>
            <div class="col-md-12 text-center">
                <br>
                    @if($user_type !== 'outgoing')
                        <a href="{{ route('home') }}" class="btn btn-info btn-fill"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>
                    @endif
                    <button type="button" id="btn-save" class="btn btn-info btn-fill" <?php if($submitted =='1') echo "disabled"; ?>><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
                    <a href='{{ url("/board/financial/pdf", ["id" => Session::get("chapterid")]) }}' target="_blank" class="">
                    <button id="buttononclickdisable" class="btn btn-info btn-fill" onClick="this.disabled=true;" <?php if(!$submitted =='1') echo "disabled"; ?>><i class="fa fa-file-pdf-o fa-fw" aria-hidden="true" ></i>&nbsp; View PDF</button>
                    </a>
            </div>
        </div>
        <!-- Modal Popups for Uploading Files -->
        <div class="modal fade" id="modal-roster">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Roster</h4>
                    </div>
                    <div class="modal-body">
                        <form id="upload_roster" name="upload_roster" role="form" enctype="multipart/form-data" method="POST" action='{{ url("/files/storeRoster",$financial_report_array['chapter_id']) }}' >
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info btn-fill" id="btn-roster">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-statement1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Statement</h4>
                    </div>
                    <div class="modal-body">
                        <form id="upload_statement1" name="upload_statement1" role="form" enctype="multipart/form-data" method="POST" action='{{ url("/files/storeStatement1",$financial_report_array['chapter_id']) }}' >
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info btn-fill" id="btn-statement1">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-statement2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Additional Statement</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/storeStatement2/'. $financial_report_array['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info btn-fill" id="btn-statement2">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-990N">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload 990N Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/files/store990n/'. $financial_report_array['chapter_id']) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name='file' required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info btn-fill" id="btn-990N">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal Popups -->
    </div>
</div>
@endsection
@section('customscript')
<script>
    /* Disable fields and buttons  */
    $(document).ready(function () {
            $('#final-submit').show();  /*report status text (.show/.hide to change visibility)*/
    });

    $('.demo1').fileselect();

     $(window).on("load", function() {
        LoadSteps();
    });

    function getConfirmation() {
               var retVal = confirm("Do you want to continue ?");
               if( retVal == true ) {
                  document.getElementById("submitted").value='1';
                  return true;
               } else {
                  return false;
               }
            }
</script>
<script>
    var accordion = (function(){
    var $accordion = $('.js-accordion');
    var $accordion_header = $accordion.find('.js-accordion-header');
    var $accordion_item = $('.js-accordion-item');
    // default settings
    var settings = {
        speed: 400,   // animation speed
        oneOpen: false   // close all other accordion items if true
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
    // $("#full-print-div").hide();
    accordion.init({ speed: 300, oneOpen: true });

    $(".txt-num").keypress(function (e) {
        var key = e.charCode || e.keyCode || 0;
        if(key == 46){
         return true;
        }
        if (key < 48 || key > 58) {
            return false;
        }
    });

    $("#btn-step-1").click(function() {
        if (!EnsureRoster()) {
        return false;
        }
        $("#FurthestStep").val('1');
        $("#financial_report").submit();
    });
    $("#btn-step-2").click(function() {
        $("#FurthestStep").val('2');
        $("#financial_report").submit();
    });
    $("#btn-step-3").click(function() {
        if (!EnsureServiceProject()) {
        return false;
        }
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
    $("#btn-step-55").click(function() {
        if (!EnsureReRegistration()) {
        return false;
        }
        $("#FurthestStep").val('55');
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
        if (!EnsureReconciliation()) {
        return false;
        }
        $("#FurthestStep").val('8');
        $("#financial_report").submit();
    });
    $("#btn-step-9").click(function() {
        if (!EnsureChapterQuestions()) {
        return false;
        }
        $("#FurthestStep").val('9');
        $("#financial_report").submit();
    });
       $("#btn-step-10").click(function() {
        $("#FurthestStep").val('10');
        $("#financial_report").submit();
    });
    $("#btn-step-11").click(function() {
        var agreeChk = $("#TotalAwardNominations").val();
        if(agreeChk > 0){
            if (($("input[name='AwardsAgree']:checked").length)<=0) {
                alert("Please select I understand and agree check box");
                return false;
            }
            else{
                $("#FurthestStep").val('11');
                $("#financial_report").submit();
            }
        }else{
            $("#FurthestStep").val('11');
            $("#financial_report").submit();
        }

    });
        $("#btn-step-12").click(function() {
        if (!EnsureSubmitInformation()) {
        return false;
        }
        $("#FurthestStep").val('12');
        $("#financial_report").submit();
    });
        $("#btn-save").click(function() {
        $("#FurthestStep").val('13');
        $("#financial_report").submit();
    });
    $("#final-submit").click(function() {
        if (!EnsureRoster()) {
            return false;
        }
        else if (!EnsureServiceProject()) {
            return false;
        }
        else if (!EnsureReRegistration()) {
            return false;
        }
        else if (!EnsureReconciliation()) {
            return false;
        }
        else if (!EnsureChapterQuestions()) {
            return false;
        }
        else if (!EnsureSubmitInformation()) {
            return false;
        }
        var result = confirm("This will finalize and submit your report.  You will no longer be able to edit this report.  Do you wish to continue?");
        if (result) {
            $("#submitted").val('1');
            $("#FurthestStep").val('13');
            $("#financial_report").submit();
        }
    });
});

    function EnsureRoster(){
        if(document.getElementById('RosterPath').value==""){
            alert("Your chapter's roster was not uploaded in CHAPTER DUES section.");
            return false;
        }
        return true; // All checks passed, allow submission
    }

    function EnsureServiceProject(){
        if(document.getElementById('ServiceProjectDesc0').value==""){
            alert("Project Description is required in SERVICE PROJECT section.");
            $("#ServiceProjectDesc0").focus();
            return false;
        }
        else if(document.getElementById('ServiceProjectIncome0').value==""){
            alert("Income amount is required in SERVICE PROJECT section.");
            $("#ServiceProjectIncome0").focus();
            return false;
        }
        else if(document.getElementById('ServiceProjectSupplies0').value==""){
            alert("Supplies & Expenses amount is required in SERVICE PROJECT section.");
            $("#ServiceProjectSupplies0").focus();
            return false;
        }
        else if(document.getElementById('ServiceProjectDonatedCharity0').value==""){
            alert("Charity Donation amount is required in SERVICE PROJECT section.");
            $("#ServiceProjectDonatedCharity0").focus();
            return false;
        }
        else if(document.getElementById('ServiceProjectDonatedM2M0').value==""){
            alert("M2M Donation amount is required in SERVICE PROJECT section.");
            $("#ServiceProjectDonatedM2M0").focus();
            return false;
        }
        return true; // All checks passed, allow submission
    }

    function EnsureReRegistration(){
        if(document.getElementById('AnnualRegistrationFee').value==""){
            alert("Chapter Re-registration is required in INTERNATIONAL EVENTS & RE-REGISTRATION section.");
            $("#AnnualRegistrationFee").focus();
            return false;
        }
        return true; // All checks passed, allow submission
    }

    function EnsureReconciliation(){
        if(document.getElementById('BankStatementIncluded').value=="" && document.getElementById('StatementPath').value=="" ){
            alert("Your chapter's Bank Statement was not uploaded in the BANK RECONCILIATION section, but you indicated the file was attached.");
            return false;
        }
        return true; // All checks passed, allow submission
    }

    function EnsureChapterQuestions(){
        if(document.getElementById('FileIRS').value=="" && document.getElementById('990NPath').value=="" ){
            alert("Your chapter's 990N filing confirmation was not uploaded in the CHAPTER QUESTIONS section, but you indicated the file was attached.");
            return false;
        }
        return true; // All checks passed, allow submission
    }

    function EnsureSubmitInformation(){
        if(document.getElementById('CompletedName').value==""){
            alert("Please include the name of the person submitting the report.");
            return false;
        }
        else if(document.getElementById('CompletedEmail').value==""){
            alert("Please include the eamil address of the person submitting the report.");
            return false;
        }
        return true; // All checks passed, allow submission
    }
</script>

<script>
    // function ChargeDifferentMembers(ButtonID){
    //     document.getElementById("chapterid").value=ButtonID;
    //     return true;
    // }

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

        ChangedMeetingFees = document.getElementById("optChangeDues").value === "1";
        ChargedMembersDifferently = document.getElementById("optNewOldDifferent").value === "1";
        MembersReducedDues = document.getElementById("optNoFullDues").value === "1";

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


        TotalFees = TotalFees.toFixed(2);

        TotalMembers = NewMembers + RenewedMembers + MembersNoDues + AssociateMembers + PartialDuesMembers + NewMembers2 + RenewedMembers2;
        document.getElementById("TotalMembers").value = TotalMembers;


        document.getElementById("TotalDues").value = TotalFees;
        document.getElementById("SumMembershipDuesIncome").value = TotalFees;

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
        SupplyTotal = SupplyTotal.toFixed(2);
        OtherTotal  = OtherTotal.toFixed(2);

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
        cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ChildrensRoomSupplies" + ExpenseCount + "\" id=\"ChildrensRoomSupplies" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ChildrensRoomOther" + ExpenseCount + "\" id=\"ChildrensRoomOther" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('ChildrensExpenseRowCount').value = ExpenseCount;

        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
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

        cell1.innerHTML = "<div class=\"form-group\"><textarea class=\"form-control\" rows=\"4\" name=\"ServiceProjectDesc" + ExpenseCount + "\" id=\"ServiceProjectDesc" + ExpenseCount + "\"></textarea></div>";
        cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" class=\"form-control txt-num\"  min=\"0\"  step=\"0.01\" name=\"ServiceProjectIncome" + ExpenseCount + "\" id=\"ServiceProjectIncome" + ExpenseCount + "\" oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\" value=\"\"></div></div>";
        cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ServiceProjectSupplies" + ExpenseCount + "\" id=\"ServiceProjectSupplies" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ServiceProjectDonatedCharity" + ExpenseCount + "\" id=\"ServiceProjectDonatedCharity" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell5.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"ServiceProjectDonatedM2M" + ExpenseCount + "\" id=\"ServiceProjectDonatedM2M" + ExpenseCount + "\"  oninput=\"ChangeServiceProjectExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('ServiceProjectRowCount').value = ExpenseCount;
        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
            if (key < 48 || key > 58) {
                return false;
            }
        });
    }

    function DeleteServiceProjectRow(){
        var ExpenseCount = document.getElementById("ServiceProjectRowCount").value;
        if(ExpenseCount>1){
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

        document.getElementById("PartyIncomeTotal").value = IncomeTotal.toFixed(2);
        document.getElementById("PartyExpenseTotal").value = ExpenseTotal.toFixed(2);
        document.getElementById("SumPartyIncome").value = IncomeTotal.toFixed(2);
        document.getElementById("SumPartyExpense").value = ExpenseTotal.toFixed(2);

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
        cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"PartyIncome" + ExpenseCount + "\" id=\"PartyIncome" + ExpenseCount + "\"  oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"PartyExpenses" + ExpenseCount + "\" id=\"PartyExpenses" + ExpenseCount + "\"  oninput=\"ChangePartyExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('PartyExpenseRowCount').value = ExpenseCount;
        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
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

        cell1.innerHTML = "<div class=\"form-group\"><input maxlength=\"250\" type=\"text\" class=\"form-control\" name=OfficeDesc" + ExpenseCount + " id=OfficeDesc" + ExpenseCount + "></div>";
        cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"OfficeExpenses" + ExpenseCount + "\" id=\"OfficeExpenses" + ExpenseCount + "\"  oninput=\"ChangeOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('OfficeExpenseRowCount').value = ExpenseCount;
        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
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
        cell3.innerHTML = "<div class=\"form-group\"><input type=\"date\" class=\"form-control\" min='2021-07-01' max='2022-06-30' name=\"MonDonationDate" + ExpenseCount + "\" id=\"MonDonationDate" + ExpenseCount + "\"  oninput=\"ChangeChildrensRoomExpenses()\" onchange=\"IsValidDate(this)\"></div>";
        cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"DonationAmount" + ExpenseCount + "\" id=\"DonationAmount" + ExpenseCount + "\" oninput=\"ChangeDonationAmount()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('MonDonationRowCount').value = ExpenseCount;
        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
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
        cell3.innerHTML = "<div class=\"form-group\"><input type=\"date\" min='2021-07-01' max='2022-06-30' class=\"form-control\" name=\"NonMonDonationDate" + ExpenseCount + "\" id=\"NonMonDonationDate" + ExpenseCount + "\" onchange=\"IsValidDate(this)\"></div>";

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
        cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"InternationalEventIncome" + ExpenseCount + "\" id=\"InternationalEventIncome" + ExpenseCount + "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"InternationalEventExpense" + ExpenseCount + "\" id=\"InternationalEventExpense" + ExpenseCount + "\" oninput=\"ChangeInternationalEventExpense()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('InternationalEventRowCount').value = ExpenseCount;
        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
            if (key < 48 || key > 58) {
                return false;
            }
        });
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

        var SumTotalExpense=0;
        var SumTotalIncome=0;

        var SumTotalNetIncome=0;

        var SumPartyExpense=0;
        var SumPartyIncome=0;

        var SumInternationalEventExpense=0;
        var SumInternationalEventIncome=0;

        var SumMonetaryDonationIncome=0;
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

        SumTotalNetIncome = SumTotalIncome - SumTotalExpense;
        TreasuryBalanceNow = TreasuryBalance - SumTotalExpense + SumTotalIncome;

        document.getElementById("TotalNetIncome").value = SumTotalNetIncome.toFixed(2);
        document.getElementById("SumTotalNetIncome").value = SumTotalNetIncome.toFixed(2);
        document.getElementById("TreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);
        document.getElementById("TreasuryBalanceNowR").value = TreasuryBalanceNow.toFixed(2);
        document.getElementById("SumTreasuryBalanceNow").value = TreasuryBalanceNow.toFixed(2);

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
        cell2.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"OtherOfficeExpenses" + ExpenseCount + "\"  id=\"OtherOfficeExpenses" + ExpenseCount + "\"  oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell3.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"OtherOfficeIncome" + ExpenseCount + "\" id=\"OtherOfficeIncome" + ExpenseCount + "\"  oninput=\"ChangeOtherOfficeExpenses()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('OtherOfficeExpenseRowCount').value = ExpenseCount;

        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
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

        var BankBalanceNow = Number(document.getElementById("BankBalanceNow").value);

        TotalFees = Number(BankBalanceNow - PaymentTotal + DepositTotal).toFixed(2);
        document.getElementById("ReconciledBankBalance").value = TotalFees;
        TreasuryBalanceNow = Number(document.getElementById("TreasuryBalanceNow").value).toFixed(2);


        if(TotalFees != TreasuryBalanceNow){
            document.getElementById("ReconciledBankBalanceWarning").style.backgroundColor = "yellow";
            document.getElementById("ReconciledBankBalanceWarning").value = "Reconciled Bank Balance does not match treasury balance now. These numbers must match for your report to be in balance"
            document.getElementById("ReconciledBankBalanceWarning").style.borderStyle = "none";
        }
        else{
            document.getElementById("ReconciledBankBalanceWarning").style.backgroundColor = "transparent";
            document.getElementById("ReconciledBankBalanceWarning").value = ""
            document.getElementById("ReconciledBankBalanceWarning").style.borderStyle = "none";
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
        cell4.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"BankRecPaymentAmount" + ExpenseCount + "\" id=\"BankRecPaymentAmount" + ExpenseCount + "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";
        cell5.innerHTML = "<div class=\"form-group\"><div class=\"input-group\"><span class = \"input-group-addon\">$</span><input type=\"number\" onKeyPress=\"if(this.value.length==9) return false;\" min=\"0\"  step=\"0.01\" class=\"form-control txt-num\" name=\"BankRecDepositAmount" + ExpenseCount + "\" id=\"BankRecDepositAmount" + ExpenseCount + "\" oninput=\"ChangeBankRec()\" onkeydown=\"return event.keyCode !== 69\"></div></div>";

        ExpenseCount++;
        document.getElementById('BankRecRowCount').value = ExpenseCount;
        $(".txt-num").keypress(function (e) {
            var key = e.charCode || e.keyCode || 0;
            // only numbers
            if(key == 46){
             return true;
            }
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

// Sectiom 9 Questions - Explainations Rquired.
    function ToggleReceiveCompensationExplanation(){
        if (document.getElementById("ReceiveCompensation").value == "1"){   //Required if YES
            $('#ReceiveCompensationExplanation').addClass('tx-cls');
            document.getElementById("divReceiveCompensationExplanation").style.display = 'block';
        }
        else{
            $('#ReceiveCompensationExplanation').removeClass('tx-cls');
            document.getElementById("divReceiveCompensationExplanation").style.display = 'none';
        }
    }

    function ToggleFinancialBenefitExplanation(){
        if (document.getElementById("FinancialBenefit").value == "1"){   //Required if YES
            $('#FinancialBenefitExplanation').addClass('tx-cls');
            document.getElementById("divFinancialBenefitExplanation").style.display = 'block';
        }
        else{
            $('#FinancialBenefitExplanation').removeClass('tx-cls');
            document.getElementById("divFinancialBenefitExplanation").style.display = 'none';
        }
    }

    function ToggleInfluencePoliticalExplanation(){
        if (document.getElementById("InfluencePolitical").value == "1"){   //Required if YES
            $('#InfluencePoliticalExplanation').addClass('tx-cls');
            document.getElementById("divInfluencePoliticalExplanation").style.display = 'block';
        }
        else{
            $('#InfluencePoliticalExplanation').removeClass('tx-cls');
            document.getElementById("divInfluencePoliticalExplanation").style.display = 'none';
        }
    }

    function ToggleVoteAllActivitiesExplanation(){
        if (document.getElementById("VoteAllActivities").value == "0"){   //Required if NO
            $('#VoteAllActivitiesExplanation').addClass('tx-cls');
            document.getElementById("divVoteAllActivitiesExplanation").style.display = 'block';
        }
        else{
            $('#VoteAllActivitiesExplanation').removeClass('tx-cls');
            document.getElementById("divVoteAllActivitiesExplanation").style.display = 'none';
        }
    }

    function ToggleBoughtPinsExplanation(){
        if (document.getElementById("BoughtPins").value == "0"){   //Required if NO
            $('#BoughtPinsExplanation').addClass('tx-cls');
            document.getElementById("divBoughtPinsExplanation").style.display = 'block';
        }
        else{
            $('#BoughtPinsExplanation').removeClass('tx-cls');
            document.getElementById("divBoughtPinsExplanation").style.display = 'none';
        }
    }

    function ToggleBoughtMerchExplanation(){
        if (document.getElementById("BoughtMerch").value == "0"){   //Required if NO
            $('#BoughtMerchExplanation').addClass('tx-cls');
            document.getElementById("divBoughtMerchExplanation").style.display = 'block';
        }
        else{
            $('#BoughtMerchExplanation').removeClass('tx-cls');
            document.getElementById("divBoughtMerchExplanation").style.display = 'none';
        }
    }

    function ToggleOfferedMerchExplanation(){
        if (document.getElementById("OfferedMerch").value == "0"){   //Required if NO
            $('#OfferedMerchExplanation').addClass('tx-cls');
            document.getElementById("divOfferedMerchExplanation").style.display = 'block';
        }
        else{
            $('#OfferedMerchExplanation').removeClass('tx-cls');
            document.getElementById("divOfferedMerchExplanation").style.display = 'none';
        }
    }

    function ToggleByLawsAvailableExplanation(){
         if (document.getElementById("ByLawsAvailable").value == "0"){   //Required if NO
            $('#ByLawsAvailableExplanation').addClass('tx-cls');
            document.getElementById("divByLawsAvailableExplanation").style.display = 'block';
        }
        else{
            $('#ByLawsAvailableExplanation').removeClass('tx-cls');
            document.getElementById("divByLawsAvailableExplanation").style.display = 'none';
        }
    }

    function ToggleChildOutingsExplanation(){
        if (document.getElementById("ChildOutings").value == "0"){   //Required if NO
            $('#ChildOutingsExplanation').addClass('tx-cls');
            document.getElementById("divChildOutingsExplanation").style.display = 'block';
        }
        else{
            $('#ChildOutingsExplanation').removeClass('tx-cls');
            document.getElementById("divChildOutingsExplanation").style.display = 'none';
        }
    }

    function ToggleMotherOutingsExplanation(){
        if (document.getElementById("MotherOutings").value == "0"){   //Required if NO
            $('#MotherOutingsExplanation').addClass('tx-cls');
            document.getElementById("divMotherOutingsExplanation").style.display = 'block';
        }
        else{
            $('#MotherOutingsExplanation').removeClass('tx-cls');
            document.getElementById("divMotherOutingsExplanation").style.display = 'none';
        }
    }

    function ToggleMeetingSpeakersExplanation(){
        if (document.getElementById("MeetingSpeakers").value == "0"){   //Required if NO
            $('#MeetingSpeakersExplanation').addClass('tx-cls');
            document.getElementById("divMeetingSpeakersExplanation").style.display = 'block';
        }
        else{
            $('#MeetingSpeakersExplanation').removeClass('tx-cls');
            document.getElementById("divMeetingSpeakersExplanation").style.display = 'none';
        }
    }

    function ToggleActivityOtherExplanation(){
        if (document.getElementById("ActivityOther").value == "5"){   //Required if Other
            $('#ActivityOtherExplanation').addClass('tx-cls');
            document.getElementById("divActivityOtherExplanation").style.display = 'block';
        }
        else{
            $('#ActivityOtherExplanation').removeClass('tx-cls');
            document.getElementById("divActivityOtherExplanation").style.display = 'none';
        }
    }

    function ToggleContributionsNotRegNPExplanation(){
        if (document.getElementById("ContributionsNotRegNP").value == "1"){   //Required if YES
            $('#ContributionsNotRegNPExplanation').addClass('tx-cls');
            document.getElementById("divContributionsNotRegNPExplanation").style.display = 'block';
        }
        else{
            $('#ContributionsNotRegNPExplanation').removeClass('tx-cls');
            document.getElementById("divContributionsNotRegNPExplanation").style.display = 'none';
        }
    }

    function TogglePerformServiceProjectExplanation(){
        if (document.getElementById("PerformServiceProject").value == "0"){   //Required if NO
            $('#PerformServiceProjectExplanation').addClass('tx-cls');
            document.getElementById("divPerformServiceProjectExplanation").style.display = 'block';
        }
        else{
            $('#PerformServiceProjectExplanation').removeClass('tx-cls');
            document.getElementById("divPerformServiceProjectExplanation").style.display = 'none';
        }
    }

    function ToggleFileIRSExplanation(){
        if (document.getElementById("FileIRS").value == "0"){   //Required if NO
            $('#990NFile').hide();
            $('#FileIRSExplanation').addClass('tx-cls');
            document.getElementById("divFileIRSExplanation").style.display = 'block';
        }
        else{
            $('#990NFile').hide();
            $('#FileIRSExplanation').removeClass('tx-cls');
            document.getElementById("divFileIRSExplanation").style.display = 'none';
        }
    }

    function ToggleBankStatementIncludedExplanation(){
        if (document.getElementById("BankStatementIncluded").value == "0"){   //Required if NO
            $('#BankStatementIncludedExplanation').addClass('tx-cls');
            document.getElementById("divBankStatementIncludedExplanation").style.display = 'block';
        }
        else{
            $('#BankStatementIncludedExplanationn').removeClass('tx-cls');
            document.getElementById("divBankStatementIncludedExplanation").style.display = 'none';
        }
    }

// function isFieldVisible(fieldId) {
//     // Customize this function based on your visibility conditions
//     return document.getElementById(fieldId).style.display !== 'none';
// }

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

    function InputLoggedInPerson(){
    }

    // function ValidateFiles(){
    //     var fileError="";
    //     var sizeinbytes=0;

    //     if(document.getElementById('990NFiling').files.length>0){
    //         sizeinbytes = document.getElementById('990NFiling').files[0].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your 990N confirmation file is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }

    //     if(document.getElementById('RosterFile').files.length>0){
    //         sizeinbytes = document.getElementById('RosterFile').files[0].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your chapter roster file is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }

    //     inp = document.getElementById('Award1');
    //     for (var i = 0; i < inp.files.length; ++i) {
    //         sizeinbytes = inp.files[i].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your 1st award nomination file (" + inp.files[i].name + ") is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }

    //     inp = document.getElementById('Award2');
    //     for (var i = 0; i < inp.files.length; ++i) {
    //         sizeinbytes = inp.files[i].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your 2nd award nomination file (" + inp.files[i].name + ") is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }

    //     inp = document.getElementById('Award3');
    //     for (var i = 0; i < inp.files.length; ++i) {
    //         sizeinbytes = inp.files[i].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your 3rd award nomination file (" + inp.files[i].name + ") is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }

    //     inp = document.getElementById('Award4');
    //     for (var i = 0; i < inp.files.length; ++i) {
    //         sizeinbytes = inp.files[i].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your 4th award nomination file (" + inp.files[i].name + ") is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }

    //     inp = document.getElementById('Award5');
    //     for (var i = 0; i < inp.files.length; ++i) {
    //         sizeinbytes = inp.files[i].size;

    //         if(sizeinbytes>5000000){
    //             fileError = "Your 5th award nomination file (" + inp.files[i].name + ") is too large.  Please submit a file less than 5MB.";
    //             return fileError;
    //         }
    //     }
    // }
</script>
@endsection
