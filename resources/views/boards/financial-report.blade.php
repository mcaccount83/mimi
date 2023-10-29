<!DOCTYPE html>
<html>
<head>
    <title>Financial Report | {{$pdfData['chapter_name'] }}, {{$pdfData['state']}}</title>
</head>
<body>
    <center><h3>MOMS Club of {{$pdfData['chapter_name'] }}, {{$pdfData['state']}}<br>
         <?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</h3></center>

<b><u>SECTION 1 - CHAPTER DUES</u></b><br>



<hr>
<b><u>SECTION 2 - MONTHLY MEETING EXPENSES</u></b><br>
Meeting Room Fees:  {{ isset($pdfData['manditory_meeting_fees_paid']) ? '$'.$pdfData['manditory_meeting_fees_paid'] : '$0.00' }}<br>
Voluntary Donations Paid:  {{ isset($pdfData['voluntary_donations_paid']) ? '$'.$pdfData['voluntary_donations_paid'] : '$0.00' }}<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Meeting Room Expenses:  {{ '$'.($pdfData['manditory_meeting_fees_paid'] + $pdfData['voluntary_donations_paid'] ?? 0.00) }}</b><br>
<br>
Paid Babysitter Expenses:  {{ isset($pdfData['paid_baby_sitters']) ? '$'.$pdfData['paid_baby_sitters'] : '$0.00' }}<br>
Children's Room Miscellaneous Expenses:<br>
<table width="100%" class="table table-bordered" id="childrens-room">
    <thead>
        <tr>
            <td><u>Description</u></td>
            <td><u>Supplies</u></td>
            <td><u>Other Expenses</u></td>
        </tr>
    </thead>
    <tbody>
        <?php
            $childrens_room = null;
            $totalSupplies = 0;
            $totalOtherExpenses = 0;

            if (isset($pdfData['childrens_room_expenses'])) {
                $blobData = base64_decode($pdfData['childrens_room_expenses']);
                $childrens_room = unserialize($blobData);

                if ($childrens_room === false) {
                    echo "Error: Failed to unserialize data.";
                } else {
                    foreach ($childrens_room as $row) {
                        echo "<tr>";
                        echo "<td><div class=\"form-group\"><p class=\"form-group\">" . $row['childrens_room_desc'] . "</p></div></td>";
                        echo "<td><div class=\"form-group\"><p class=\"form-group\">" . ($row['childrens_room_supplies'] ? "$" . number_format($row['childrens_room_supplies'], 2) : "$0.00") . "</p></div></td>";
                        echo "<td><div class=\"form-group\"><p class=\"form-group\">" . ($row['childrens_room_other'] ? "$" . number_format($row['childrens_room_other'], 2) : "$0.00") . "</p></div></td>";
                        echo "</tr>";

                        $totalSupplies += floatval($row['childrens_room_supplies']);
                        $totalOtherExpenses += floatval($row['childrens_room_other']);
                    }
                }
            } else {
                echo "No data available.";
            }
            $totalChildrensRoomExpenses = $totalSupplies + $totalOtherExpenses;
            ?>
    </tbody>
</table>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Children's Room Expenses:  {{ '$'.($pdfData['paid_baby_sitters'] + $totalChildrensRoomExpenses ?? 0.00) }}</b><br>
<hr>
<b><u>SECTION 3 - SERVICE PROJECTS</u></b><br>

<hr>
<b><u>SECTION 4 - PARTIES & MEMBER BENEFITS</u></b><br>


<hr>
<b><u>SECTION 5 - OFFICE & OPERATING EXPENSES</u></b><br>

<hr>
<b><u>SECTION 6 - DONATIONS TO YOUR CHAPTER</u></b><br>


<hr>
<b><u>SECTION 7 - OTHER INCOME & EXPENSES</u></b><br>


<hr>
<b><u>SECTION 8 - BANK RECONCILIATION</u></b><br>

<hr>
<b><u>SECTION 9 - TAX EXEMPT & CHAPTER QUESTIONS</u></b><br>




</body>
</html>

<script>




</script>
