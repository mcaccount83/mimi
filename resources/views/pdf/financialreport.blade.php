<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .keep-together {
        page-break-inside: avoid;
    }
    </style>
    <title>{{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }} | <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</title>
</head>
<body>
    <center><h2>MOMS Club of {{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }}<br>
    <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</h2></center>
    EIN: {{ $pdfData['ein'] }}<br>
    Boundaries: {{ $pdfData['boundaries'] }}<br>

    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>CHAPTER DUES</b>
    <hr>
    </div>
    <table width="75%">
        <tbody>
           <tr><td>Did your chapter change dues this year?</td>
           <td><strong>{{ $pdfData ['changed_dues'] == 1 ? 'YES' : 'NO' }} </strong></td></tr>
           <tr><td>Did your chapter charge different amounts for new and returning members?</td>
           <td><strong>{{ $pdfData ['different_dues'] == 1 ? 'YES' : 'NO' }} </strong></td></tr>
           <tr><td>Did your chapter have any members who didn't pay full dues?</td>
           <td><strong>{{ $pdfData ['not_all_full_dues'] == 1 ? 'YES' : 'NO' }} </strong></td></tr>
           </tbody>
        </table>
    <br>
    <table width="100%">
        <tbody>
            @if ($pdfData['changed_dues'] != 1)
                <tr><td>New Members:</td>
                    <td>{{ $pdfData['total_new_members'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                @if ($pdfData['different_dues'] != 1)
                <td>Dues Collected:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member']) }}</td></tr>
                @endif
                @if ($pdfData['different_dues'] == 1)
                <td>New Dues Collected:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member']) }}</td></tr>
                @endif
                <tr><td>Renewed Members:</td>
                    <td>{{ $pdfData['total_renewed_members'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                @if ($pdfData['different_dues'] == 1)
                <td>Renewal Dues Collected:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member_renewal']) }}</td></tr>
                @endif
            @endif
            @if ($pdfData['changed_dues'] == 1)
                <tr><td>New Members (OLD dues amount):</td>
                    <td>{{ $pdfData['total_new_members'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                @if ($pdfData['different_dues'] != 1)
                <td>Dues Collected (OLD dues amount):</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member']) }}</td></tr>
                @endif
                @if ($pdfData['different_dues'] == 1)
                <td>New Dues Collected (OLD dues amount):</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member']) }}</td></tr>
                @endif
                <tr><td>Renewed Members (OLD dues amount):</td>
                    <td>{{ $pdfData['total_renewed_members'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                @if ($pdfData['different_dues'] == 1)
                <td>Renewal Dues Collected (OLD dues amount):</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member_renewal']) }}</td></tr>
                @endif
                <tr><td>New Members (NEW dues amount):</td>
                    <td>{{ $pdfData['total_new_members_changed_dues'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                @if ($pdfData['different_dues'] != 1)
                <td>Dues Collected (NEW dues amount):</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member_new_changed']) }}</td></tr>
                @endif
                @if ($pdfData['different_dues'] == 1)
                <td>New Dues Collected (NEW dues amount):</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member_new_changed']) }}</td></tr>
                @endif
                <tr><td>Renewed Members (NEW dues amount):</td>
                    <td>{{ $pdfData['total_renewed_members_changed_dues'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                @if ($pdfData['different_dues'] == 1)
                <td>Renewal Dues Collected (NEW dues amount):</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['dues_per_member_renewal_changed']) }}</td></tr>
                @endif
            @endif
            @if ($pdfData['not_all_full_dues'] == 1)
                <tr><td>Members Who Paid No Dues:</td>
                    <td>{{ $pdfData['members_who_paid_no_dues'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td></tr>
                <tr><td>Members Who Paid Partial Dues:</td>
                    <td>{{ $pdfData['members_who_paid_partial_dues'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                <td>Partial Dues Collected:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['total_partial_fees_collected']) }}</td></tr>
                <tr><td>Assiciate Members:</td>
                    <td>{{ $pdfData['total_associate_members'] }}</td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                <td>Associate Dues Collected:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['associate_member_fee']) }}</td></tr>
            @endif
        </tbody>
    </table>
            <?php
            $newMembers = $pdfData['total_new_members'] * $pdfData['dues_per_member'];
            $renewalMembers = $pdfData['total_renewed_members'] * $pdfData['dues_per_member'];
            $renewalMembersDiff = $pdfData['total_renewed_members'] * $pdfData['dues_per_member_renewal'];
            $renewMembersOld = $pdfData['total_renewed_members'] * $pdfData['dues_per_member_renewal'];
            $newMembersNew = $pdfData['total_new_members_changed_dues'] * $pdfData['dues_per_member_new_changed'];
            $renewMembersNew = $pdfData['total_renewed_members_changed_dues'] * $pdfData['dues_per_member_renewal_changed'];
            $renewMembersNewDiff = $pdfData['total_renewed_members'] * $pdfData['dues_per_member_renewal_changed'];
            $partialMembers = $pdfData['members_who_paid_partial_dues'] * $pdfData['total_partial_fees_collected'];
            $associateMembers = $pdfData['total_associate_members'] * $pdfData['associate_member_fee'];

            $totalMembers = $pdfData['total_new_members'] + $pdfData['total_renewed_members'] + $pdfData['total_new_members_changed_dues'] + $pdfData['total_renewed_members_changed_dues']
                    + $pdfData['members_who_paid_partial_dues'] + $pdfData['total_associate_members'];

            if ($pdfData['different_dues'] == 1 && $pdfData['changed_dues'] != 1) {
                $totalDues = $newMembers + $renewalMembersDiff + $partialMembers + $associateMembers;
            } elseif ($pdfData['different_dues'] == 1 && $pdfDatay['changed_dues'] == 1) {
                $totalDues = $newMembers + $renewalMembersDiff + $newMembersNew + $renewMembersNewDiff + $partialMembers + $associateMembers;
            } elseif ($pdfData['different_dues'] != 1 && $pdfData['changed_dues'] == 1) {
                $totalDues = $newMembers + $renewalMembers + $newMembersNew + $renewMembersNew + $partialMembers + $associateMembers;
            } else {
                $totalDues = $newMembers + $renewalMembers + $partialMembers + $associateMembers;
            }
        ?>
    <br>
    <table width="50%">
        <tbody>
            <tr><td><strong>Total Members:</strong></td>
                <td><strong>{{ $totalMembers }}</strong></td></tr>
            <tr><td><strong>Total Dues Collected:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalDues) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>MONTHLY MEETING EXPENSES</b>
    <hr>
    </div>
    <table width="50%">
        <tbody>
            <tr><td>Meeting Room Fees:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['manditory_meeting_fees_paid']) }}</td></tr>
            <tr><td>Voluntary Donations Paid:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['voluntary_donations_paid']) }}</td></tr>
            <tr><td><strong>Total Meeting Room Expenses:</strong></td>
                    <td><strong>{{ '$'.sprintf('%0.2f',$pdfData['manditory_meeting_fees_paid'] + $pdfData['voluntary_donations_paid']) }}</b></strong></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td></tr>
            <tr><td>Paid Babysitter Expense:</td>
                    <td>{{ '$'.sprintf('%0.2f',$pdfData['paid_baby_sitters']) }}</tr>
                <tr><td>Children's Room Miscellaneous:</td>
                    <td></td></tr>
        </tbody>
    </table>
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

                if (isset($pdfData['childrens_room_expenses'])) {
                    $blobData = base64_decode($pdfData['childrens_room_expenses']);
                    $childrens_room = unserialize($blobData);

                    if ($childrens_room === false) {
                        echo "Error: Failed to unserialize data.";
                    } else {
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
                echo "<tr>";
                echo "<td><strong>Total</strong></td>";
                echo "<td><strong>$" . number_format($totalChildrenSupplies, 2) . "</strong></td>";
                echo "<td><strong>$" . number_format($totalChildrenOther, 2) . "</strong></td>";
                echo "</tr>";
                    }
                } else {
                    echo "No data available.";
                }
                $totalChildrensRoomExpenses = $totalChildrenSupplies + $totalChildrenOther;
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%">
        <tbody>
                <tr><td><strong>Total Children's Room Expenses:</strong></td>
                    <td><strong>{{ '$'.sprintf('%0.2f',$pdfData['paid_baby_sitters'] + $totalChildrensRoomExpenses) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>SERVICE PROJECTS</b>
    <hr>
    </div>
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

                if (isset($pdfData['service_project_array'])) {
                    $blobData = base64_decode($pdfData['service_project_array']);
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
                echo "<tr>";
                echo "<td><strong>Total</strong></td>";
                echo "<td><strong>$" . number_format($totalServiceIncome, 2) . "</strong></td>";
                echo "<td><strong>$" . number_format($totalServiceSupplies, 2) . "</strong></td>";
                echo "<td><strong>$" . number_format($totalServiceCharity, 2) . "</strong></td>";
                echo "<td><strong>$" . number_format($totalServiceM2M, 2) . "</strong></td>";
                echo "</tr>";
                    }
                } else {
                    echo "No data available.";
                }
                $totalServiceProjectExpenses = $totalServiceSupplies + $totalServiceCharity + $totalServiceM2M;
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%" >
        <tbody>
            <tr><td><strong>Total Service Project Income:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalServiceIncome) }}</strong></td></tr>
            <tr><td><strong>Total Service Project Expenses:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalServiceProjectExpenses) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>PARTIES & MEMBER BENEFITS</b>
    <hr>
    </div>
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

                if (isset($pdfData['party_expense_array'])) {
                    $blobData = base64_decode($pdfData['party_expense_array']);
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
                echo "<tr>";
                echo "<td><strong>Total</strong></td>";
                echo "<td><strong>$" . number_format($totalPartyIncome, 2) . "</strong></td>";
                echo "<td><strong>$" . number_format($totalPartyExpense, 2) . "</strong></td>";
                echo "</tr>";
                    }
                } else {
                    echo "No data available.";
                }

                if ($totalDues == 0) {
    $partyPercentage = 0;
} else {
    $partyPercentage = ($totalPartyExpense - $totalPartyIncome) / $totalDues;
}
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%">
        <tbody>
            <tr><td><strong>Total Member Benefit Income:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalPartyIncome) }}</strong></td></tr>
            <tr><td><strong>Total Member Benefit Expenses:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalPartyExpense) }}</strong></td></tr>
            <tr><td><strong>Member Benefit/Dues Income Percentage:</strong></td>
                <td><strong>{{ number_format($partyPercentage * 100, 2) }}%</strong></td>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>OFFICE & OPERATING EXPENSES</b>
    <hr>
    </div>
        <table width="100%">
            <tr>
                <td>Printing Costs:  {{ '$'.sprintf('%0.2f',$pdfData['office_printing_costs']) }}<br></td>
                <td>Postage Costs:  {{ '$'.sprintf('%0.2f',$pdfData['office_postage_costs']) }}<br></td>
                <td>Membership Pins:  {{ '$'.sprintf('%0.2f',$pdfData['office_membership_pins_cost']) }}<br></td>
            </tr>
        </tbody>
    </table>
    <table width="75%" >
        <thead>
            <tr>
                <td>Other Office & Operating Expenses:</td>
                <td>&nbsp;</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $other_office_expenses = null;
                $totalOfficeExpense = 0;

                if (isset($pdfData['office_other_expenses'])) {
                    $blobData = base64_decode($pdfData['office_other_expenses']);
                    $other_office_expenses = unserialize($blobData);

                    if ($other_office_expenses === false) {
                        echo "Error: Failed to unserialize data.";
                    } else {
                        foreach ($other_office_expenses as $row) {
                            echo "<tr>";
                            echo "<td>" . $row['office_other_desc'] . "</td>";
                            echo "<td>" . ($row['office_other_expense'] ? "$" . number_format($row['office_other_expense'], 2) : "$0.00") . "</td>";
                            echo "</tr>";

                            $totalOfficeExpense += floatval($row['office_other_expense']);
                        }
                         // Total row
                echo "<tr>";
                echo "<td><strong>Total</strong></td>";
                echo "<td><strong>$" . number_format($totalOfficeExpense, 2) . "</strong></td>";
                echo "</tr>";
                    }
                } else {
                    echo "No data available.";
                }
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%" >
        <tbody>
            <tr><td><strong>Total Office/Operating Expenses:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$pdfData['office_printing_costs'] + $pdfData['office_postage_costs'] +
                    $pdfData['office_membership_pins_cost'] + $totalOfficeExpense) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>INTERNATIONAL EVENTS & RE-REGISTRATION</b>
    <hr>
    </div>
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

                if (isset($pdfData['international_event_array'])) {
                    $blobData = base64_decode($pdfData['international_event_array']);
                    $international_event_array = unserialize($blobData);

                    if ($international_event_array === false) {
                        echo "Error: Failed to unserialize data.";
                    } else {
                        foreach ($international_event_array as $row) {
                            echo "<tr>";
                            echo "<td>" . $row['intl_event_desc'] . "</td>";
                            echo "<td>" . ($row['intl_event_income'] ? "$" . number_format($row['intl_event_income'], 2) : "$0.00") . "</td>";
                            echo "<td>" . ($row['intl_event_expenses'] ? "$" . number_format($row['intl_event_expenses'], 2) : "$0.00") . "</td>";
                            echo "</tr>";

                            $totalEventIncome += floatval($row['intl_event_income']);
                            $totalEventExpense += floatval($row['intl_event_expenses']);;
                        }
                        // Total row
                    echo "<tr>";
                    echo "<td><strong>Total</strong></td>";
                    echo "<td><strong>$" . number_format($totalEventIncome, 2) . "</strong></td>";
                    echo "<td><strong>$" . number_format($totalEventExpense, 2) . "</strong></td>";
                    echo "</tr>";
                        }
                } else {
                    echo "No data available.";
                }
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%"  >
        <tbody>
            <tr><td><strong>Total Event Registration Income:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalEventIncome) }}</strong></td></tr>
            <tr><td><b>Total Event Registration Expenses:</b></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalEventExpense) }}</strong></td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td><strong>Chapter Re-Registration:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$pdfData['annual_registration_fee']) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>DONATIONS TO YOUR CHAPTER</b>
    <hr>
    </div>
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
                $monetary_dontations_to_chapter = null;
                $totalDonationAmount = 0;

                if (isset($pdfData['monetary_donations_to_chapter'])) {
                    $blobData = base64_decode($pdfData['monetary_donations_to_chapter']);
                    $monetary_dontations_to_chapter = unserialize($blobData);

                    if ($monetary_dontations_to_chapter === false) {
                        echo "Error: Failed to unserialize data.";
                    } else {
                        foreach ($monetary_dontations_to_chapter as $row) {
                            echo "<tr>";
                            echo "<td>" . $row['mon_donation_desc'] . "</td>";
                            echo "<td>" . $row['mon_donation_info'] . "</td>";
                            echo "<td>" . $row['mon_donation_date'] . "</td>";
                            echo "<td>" . ($row['mon_donation_amount'] ? "$" . number_format($row['mon_donation_amount'], 2) : "$0.00") . "</td>";
                            echo "</tr>";

                            $totalDonationAmount += floatval($row['mon_donation_amount']);
                        }
                         // Total row
                echo "<tr>";
                echo "<td><strong>Total</strong></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td><strong>$" . number_format($totalDonationAmount, 2) . "</strong></td>";
                echo "</tr>";
                    }
                } else {
                    echo "No data available.";
                }
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%" >
        <tbody>
            <tr><td><strong>Total Monetary Donations:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f', $totalDonationAmount) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <table width="75%" style="border-collapse: collapse;">
        <thead>
            <tr>
                <td><strong>Non-Monetary Donations</strong></td> </tr>
            <tr style="border-bottom: 1px solid #333;">
                <td>Purpose of Donation</td>
                <td>Donor Name/Address</td>
                <td>Date</td>
            </tr>
        </thead>
        <tbody>
            <?php
                $non_monetary_dontations_to_chapter = null;

                if (isset($pdfData['non_monetary_donations_to_chapter'])) {
                    $blobData = base64_decode($pdfData['non_monetary_donations_to_chapter']);
                    $non_monetary_dontations_to_chapter = unserialize($blobData);

                    if ($non_monetary_dontations_to_chapter === false) {
                        echo "Error: Failed to unserialize data.";
                    } else {
                        foreach ($non_monetary_dontations_to_chapter as $row) {
                            echo "<tr>";
                            echo "<td>" . $row['nonmon_donation_desc'] . "</td>";
                            echo "<td>" . $row['nonmon_donation_info'] . "</td>";
                            echo "<td>" . $row['nonmon_donation_date'] . "</td>";
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "No data available.";
                }
                ?>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>OTHER INCOME & EXPENSES</b>
    <hr>
    </div>
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

                if (isset($pdfData['other_income_and_expenses_array'])) {
                    $blobData = base64_decode($pdfData['other_income_and_expenses_array']);
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
                echo "<tr>";
                echo "<td><strong>Total</strong></td>";
                echo "<td><strong>$" . number_format($totalOtherIncome, 2) . "</strong></td>";
                echo "<td><strong>$" . number_format($totalOtherExpenses, 2) . "</strong></td>";
                echo "</tr>";
                    }
                } else {
                    echo "No data available.";
                }
                ?>
        </tbody>
    </table>
    <br>
    <table width="50%" >
        <tbody>
            <tr><td><strong>Total Other Income:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalOtherIncome) }}</strong></td></tr>
            <tr><td><strong>Total Other Expenses:</strong></td>
                <td><strong>{{ '$'.sprintf('%0.2f',$totalOtherExpenses) }}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>FINANCIAL SUMMARY</b>
    <hr>
    </div>
    <?php
        $totalIncome = $totalDues + $totalServiceIncome + $totalPartyIncome + $totalDonationAmount + $totalEventIncome + $totalOtherIncome;
        $totalExpenses = $pdfData['manditory_meeting_fees_paid'] + $pdfData['voluntary_donations_paid'] + $pdfData['paid_baby_sitters'] + $totalChildrensRoomExpenses + $totalServiceProjectExpenses
                + $totalPartyExpense + $pdfData['office_printing_costs'] + $pdfData['office_postage_costs'] +
                    $pdfData['office_membership_pins_cost'] + $totalOfficeExpense + $pdfData['annual_registration_fee'] + $totalEventExpense + $totalOtherExpenses;
        $treasuryBalance = $pdfData ['amount_reserved_from_previous_year'] + $totalIncome - $totalExpenses
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
            <tr><td><strong>TOTAL INCOME:</strong></td>
            <td><strong>{{ '$'.sprintf('%0.2f',$totalIncome) }}</strong></td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td><strong>EXPENSES<strong></td></tr>
            <tr><td style="border-top: 1px solid #333;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Meeting Room Expenses</td>
            <td style="border-top: 1px solid #333;">{{ '$'.sprintf('%0.2f',$pdfData['manditory_meeting_fees_paid'] + $pdfData['voluntary_donations_paid']) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expenses:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Supplies</td>
            <td>{{ '$'.sprintf('%0.2f',$totalChildrenSupplies) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paid Sitters</td>
            <td>{{ '$'.sprintf('%0.2f',$pdfData['paid_baby_sitters'])  }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
            <td>{{ '$'.sprintf('%0.2f',$totalChildrenOther) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Children's Room Expense Total</td>
            <td>{{ '$'.sprintf('%0.2f',$pdfData['paid_baby_sitters'] + $totalChildrensRoomExpenses) }}</td></tr>
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
            <td>{{ '$'.sprintf('%0.2f',$pdfData['office_printing_costs']) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Postage</td>
            <td>{{ '$'.sprintf('%0.2f',$pdfData['office_postage_costs']) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Membership Pins</td>
            <td>{{ '$'.sprintf('%0.2f',$pdfData['office_membership_pins_cost']) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other</td>
            <td>{{ '$'.sprintf('%0.2f',$totalOfficeExpense) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Office/Operating Expense Total</td>
            <td>{{ '$'.sprintf('%0.2f',$pdfData['office_printing_costs'] + $pdfData['office_postage_costs'] +
                $pdfData['office_membership_pins_cost'] + $totalOfficeExpense) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Annual Chapter Re-registration Fee</td>
            <td>{{ '$'.sprintf('%0.2f',$pdfData['annual_registration_fee'])  }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;International Event Registration</td>
            <td>{{ '$'.sprintf('%0.2f',$totalEventExpense) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Expenses</td>
            <td>{{ '$'.sprintf('%0.2f',$totalOtherExpenses) }}</td></tr>
            <tr><td><strong>TOTAL EXPENSES</strong></td>
            <td><strong>{{ '$'.sprintf('%0.2f',$totalExpenses) }}</strong></td></tr>
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
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>BANK RECONCILIATION</b>
    <hr>
    </div>
    <table width="100%" >
        <tbody>
            <tr><td>Beginning Balance<td>
                    <td><strong>{{ '$'.($pdfData ['amount_reserved_from_previous_year'])}}</strong></td>
                <td>Ending Bank Statement Balance<td>
                    <td><strong>{{ '$'.($pdfData ['bank_balance_now'])}}</strong></td></tr>
            <tr><td>Profit (Loss)<td>
                    <td><strong>
                    @php
                        $netAmount = $totalIncome - $totalExpenses;
                        $formattedAmount = ($netAmount < 0) ? '($' . number_format(abs($netAmount), 2) . ')' : '$' . number_format($netAmount, 2);
                    @endphp
                    {{ $formattedAmount }}</strong></td>
                <td></td><td></td></tr>
            <tr><td>Ending Balance (Treasury Balance Now)<td>
                    <td><strong>{{ '$'.number_format($treasuryBalance, 2)}}</strong></td>
                    <td></td><td></td></tr>
        </tbody>
    </table>
    <br>
    <table width="100%" style="border-collapse: collapse;">
        <thead>
            <tr>Reconcilliation Transactons</tr>
            <tr style="border-bottom: 1px solid #333;">
                    <td>Date</td>
                    <td>Check No.</td>
                    <td>Transaction Desc.</td>
                    <td>Payment Amount</td>
                    <td>Deposit Amount</td>
                </tr>
            </thead>
            <tbody>
            <?php
            $totalPayments = 0;
            $totalDeposits = 0;

                if (isset($pdfData['bank_reconciliation_array'])) {
                    $blobData = base64_decode($pdfData['bank_reconciliation_array']);
                    $bank_rec_array = unserialize($blobData);

                    if ($bank_rec_array === false) {
                        echo "Error: Failed to unserialize data.";
                    } else {
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
                    }
                } else {
                    echo "No data available.";
                }
                $totalReconciliation = - $totalPayments + $totalDeposits;
                ?>
            </tbody>
    </table>
    <br>
    <table width="50%" >
        <tbody>
            <tr>*NOTE: Reconciled Bank Statement & Treasury Balance Now MUST match for Financial Report to be in Balance.</tr>
            <tr><td>Reconciled Bank Statement</td>
                <td><strong>{{ '$'.sprintf('%0.2f',$pdfData ['bank_balance_now'] + $totalReconciliation) }}</strong></td></tr>
            <tr><td>Treasury Balance Now</td>
                <td><strong>{{ '$'.number_format($treasuryBalance, 2)}}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>CHAPTER QUESTIONS</b>
    <hr>
    </div>
    <table>
         <tbody>
            <tr><td>1.</td>
                <td>Did anyone in your chapter receive any compensation or pay for their work with your chapter?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['receive_compensation'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['receive_compensation_explanation']}}</strong></td></tr>
            <tr><td>2.</td>
                <td>Did any officer, member or family of a member benefit financially in any way from the member's position with your chapter?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['financial_benefit'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['financial_benefit_explanation']}}</strong></td></tr>
            <tr><td>3.</td>
                <td>Did your chapter attempt to influence any national, state/provincial, or local legislation, or support any other organization that did?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['influence_political'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['influence_political_explanation']}}</strong></td></tr>
            <tr><td>4.</td>
                <td>Did your chapter vote on all activities and expenditures during the fiscal year?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['vote_all_activities'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['vote_all_activities_explanation']}}</strong></td></tr>
            <tr><td>5.</td>
                <td>Did you purchase pins from International?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['purchase_pins'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['purchase_pins_explanation']}}</strong></td></tr>
            <tr><td>6.</td>
                <td>Did you purchase any merchandise from International other than pins?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['bought_merch'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['bought_merch_explanation']}}</strong></td></tr>
            <tr><td>7.</td>
                <td>Did you offer or inform your members about MOMS Club merchandise?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['offered_merch'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['offered_merch_explanation']}}</strong></td></tr>
            <tr><td>8.</td>
                <td>Did you make the Bylaws and/or manual available for any chapter members that requested them?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['bylaws_available'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['bylaws_available_explanation']}}</strong></td></tr>
            <tr><td>9.</td>
                <td>Did you have a children's room with babysitters?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['childrens_room_sitters'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['childrens_room_sitters_explanation']}}</strong></td></tr>
            <tr><td>10.</td>
                <td>Did you have playgroups? If so, how were they arranged.</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['playgroups'] == 1 ? 'YES   Arranged by Age' : (['playgroups'] == 2 ? 'YES   Multi-aged Groups' : 'NO') }}</strong></td></tr>
            <tr><td>11.</td>
                <td>Did you have any child focused outings or activities?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['child_outings'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['child_outings_explanation']}}</strong></td></tr>
            <tr><td>12.</td>
                <td>Did you have any mother focused outings or activities?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['mother_outings'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['mother_outings_explanation']}}</strong></td></tr>
            <tr><td>13.</td>
                <td>Did you have speakers at any meetings?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['meeting_speakers'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['meeting_speakers_explanation']}}</strong></td></tr>
            <tr><td>14.</td>
                <td>If you had speakers, check any of the topics that were covered:</td></tr>
                <tr><td></td>
                    <td><strong>
                        @php
                        $meetingSpeakersArray = [];
                        if (isset($pdfData['meeting_speakers_array']) && !is_null($pdfData['meeting_speakers_array'])) {
                            $decodedArray = json_decode($pdfData['meeting_speakers_array'], true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $meetingSpeakersArray = $decodedArray;
                            } else {
                                \Log::error('JSON decoding error: ' . json_last_error_msg(), ['data' => $pdfData['meeting_speakers_array']]);
                            }
                        }

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
                            return $meetingSpeakersMapping[$value] ?? 'Unknown';
                        }, $meetingSpeakersArray)) }}
                    @else
                        N/A
                    @endif


                    </strong></td></tr>
                    <tr><td>15.</td>
                        <td>Did you have any discussion topics at your meetings? If yes, how often?</td></tr>
                    <tr><td></td>
                        <td><strong>{{ $pdfData['discussion_topic_frequency'] == 1 ? '1-3 Times' : ($pdfData['discussion_topic_frequency'] == 2 ? '4-6 Times' :
                            ($pdfData['discussion_topic_frequency'] == 3 ? '7-9 Times' : ($pdfData['discussion_topic_frequency'] == 4 ? '10+ Times' : 'NO'))) }}</strong></td></tr>
                    <tr><td>16.</td>
                        <td>Did your chapter have scheduled park days? If yes, how often?</td></tr>
                    <tr><td></td>
                        <td><strong>{{ $pdfData['park_day_frequency'] == 1 ? '1-3 Times' : ($pdfData['park_day_frequency'] == 2 ? '4-6 Times' :
                            ($pdfData['park_day_frequency'] == 3 ? '7-9 Times' : ($pdfData['park_day_frequency'] == 4 ? '10+ Times' : 'NO'))) }}</strong></td></tr>

            <tr><td>17.</td>
                <td>Did your chapter have any of the following activity groups?</td></tr>
                <tr><td></td>
                <td><strong>
   @php
    $activityArray = [];
    if (isset($pdfData['activity_array']) && !is_null($pdfData['activity_array'])) {
        $decodedArray = json_decode($pdfData['activity_array'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $activityArray = $decodedArray;
        } else {
            \Log::error('JSON decoding error: ' . json_last_error_msg(), ['data' => $pdfData['activity_array']]);
        }
    }

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
        return $activityMapping[$value] ?? 'Unknown';
    }, $activityArray)) }}
@else
    N/A
@endif


                </strong></td></tr>
            <tr><td>18.</td>
                <td>Did your chapter make any contributions to any organization or individual that is not registered with the government as a charity?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['contributions_not_registered_charity'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['contributions_not_registered_charity_explanation']}}</strong></td></tr>
            <tr><td>19.</td>
                <td>Did your chapter perform at least one service project to benefit mothers or children?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['at_least_one_service_project'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['at_least_one_service_project_explanation']}}</strong></td></tr>
            <tr><td>20.</td>
                <td>Did your chapter sister another chapter?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['sister_chapter'] == 1 ? 'YES' : 'NO' }}</strong></td></tr>
            <tr><td>21.</td>
                <td>Did your chapter attend an International Event?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['international_event'] == 1 ? 'YES' : 'NO' }}</strong></td></tr>
            <tr><td>22.</td>
                <td>Did your chapter file their IRS 990N?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['file_irs'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['file_irs_explanation']}}</strong></td></tr>
            <tr><td>23.</td>
                <td>Is a copy of your chapter's most recent bank statement included with the copy of this report that you are submitting to International?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['bank_statement_included'] == 1 ? 'YES' : 'NO' }}&nbsp;&nbsp;  {{ $pdfData ['bank_statement_included_explanation']}}</strong></td></tr>
            <tr><td>24.</td>
                <td>If your group does not have any bank accounts, where is the chapter money kept?</td></tr>
            <tr><td></td>
                <td><strong>{{ $pdfData ['wheres_the_money'] ?? 'N/A'}}</strong></td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>SUBMISSION INFORMATION</b>
    <hr>
    Submitted by: {{ $pdfData ['completed_name']}}<br>
    Email: {{ $pdfData ['completed_email']}}<br>
    Date: {{ $pdfData ['submitted']}}
</div>
</body>
</html>
