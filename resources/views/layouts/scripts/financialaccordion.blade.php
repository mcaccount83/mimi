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
        ChangedMeetingFees = optChangeDuesValue == "1";

        var optNewOldDifferentValue = document.querySelector('input[name="optNewOldDifferent"]:checked')?.value;
        ChargedMembersDifferently = optNewOldDifferentValue == "1";

        var optNoFullDuesValue = document.querySelector('input[name="optNoFullDues"]:checked')?.value;
        MembersReducedDues = optNoFullDuesValue == "1";

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

        ManditoryFees = parseFloat(document.getElementById("ManditoryMeetingFeesPaid").value.replace(/,/g, '')) || 0;
        VoluntaryFees = parseFloat(document.getElementById("VoluntaryDonationsPaid").value.replace(/,/g, '')) || 0;

        TotalFees = (ManditoryFees + VoluntaryFees).toFixed(2);

        document.getElementById("TotalMeetingRoomExpenses").value = TotalFees;
        document.getElementById("SumMeetingRoomExpense").value = TotalFees;

        ReCalculateSummaryTotal();
    }

    function ChangeMemberCount(){
        var ChangedMeetingFees = document.querySelector('input[name="optChangeDues"]:checked') && document.querySelector('input[name="optChangeDues"]:checked').value == "1";
        var ChargedMembersDifferently = document.querySelector('input[name="optNewOldDifferent"]:checked') && document.querySelector('input[name="optNewOldDifferent"]:checked').value == "1";
        var MembersReducedDues = document.querySelector('input[name="optNoFullDues"]:checked') && document.querySelector('input[name="optNoFullDues"]:checked').value == "1";

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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="ChildrensRoomDesc${ExpenseCount}" id="ChildrensRoomDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ChildrensRoomSupplies${ExpenseCount}" id="ChildrensRoomSupplies${ExpenseCount}" oninput="ChangeChildrensRoomExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ChildrensRoomOther${ExpenseCount}" id="ChildrensRoomOther${ExpenseCount}" oninput="ChangeChildrensRoomExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

        cell1.innerHTML = `<div class="mb-3"><textarea class="form-control" rows="4" name="ServiceProjectDesc${ExpenseCount}" id="ServiceProjectDesc${ExpenseCount}"></textarea></div>`;
        cell2.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectIncome${ExpenseCount}" id="ServiceProjectIncome${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectSupplies${ExpenseCount}" id="ServiceProjectSupplies${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell4.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectDonatedCharity${ExpenseCount}" id="ServiceProjectDonatedCharity${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell5.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="ServiceProjectDonatedM2M${ExpenseCount}" id="ServiceProjectDonatedM2M${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="PartyDesc${ExpenseCount}" id="PartyDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="PartyIncome${ExpenseCount}" id="PartyIncome${ExpenseCount}" oninput="ChangePartyExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="PartyExpenses${ExpenseCount}" id="PartyExpenses${ExpenseCount}" oninput="ChangePartyExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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
    var totalExpenses = 0;
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
    // Also remove this line as SumTotalChildrensRoomExpense is not defined in this function:
    document.getElementById("SumOperatingExpense").value = OperatingTotal.toFixed(2);
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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="OfficeDesc${ExpenseCount}" id="OfficeDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="OfficeExpenses${ExpenseCount}" id="OfficeExpenses${ExpenseCount}" oninput="ChangeOfficeExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="InternationalEventDesc${ExpenseCount}" id="InternationalEventDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="InternationalEventIncome${ExpenseCount}" id="InternationalEventIncome${ExpenseCount}" oninput="ChangeInternationalEventExpense()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="InternationalEventExpense${ExpenseCount}" id="InternationalEventExpense${ExpenseCount}" oninput="ChangeInternationalEventExpense()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="DonationDesc${ExpenseCount}" id="DonationDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="DonorInfo${ExpenseCount}" id="DonorInfo${ExpenseCount}"></div>`;
        cell3.innerHTML = `<div class="mb-3"><input type="date" class="form-control" name="MonDonationDate${ExpenseCount}" id="MonDonationDate${ExpenseCount}"></div>`;
        cell4.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="DonationAmount${ExpenseCount}" id="DonationAmount${ExpenseCount}" oninput="ChangeDonationAmount()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="NonMonDonationDesc${ExpenseCount}" id="NonMonDonationDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="NonMonDonorInfo${ExpenseCount}" id="NonMonDonorInfo${ExpenseCount}"></div>`;
        cell3.innerHTML = `<div class="mb-3"><input type="date" class="form-control" name="NonMonDonationDate${ExpenseCount}" id="NonMonDonationDate${ExpenseCount}"></div>`;

        ExpenseCount++;
        document.getElementById('NonMonDonationRowCount').value = ExpenseCount;
    }

    function DeleteNonMonDonationRow() {
        var ExpenseCount = document.getElementById("NonMonDonationRowCount").value;

        if (ExpenseCount > 1) {
            document.getElementById("donation-goods").deleteRow(ExpenseCount - 1);
            ExpenseCount--;        // Update the expense count
            document.getElementById('NonMonDonationRowCount').value = ExpenseCount;

            if (ExpenseCount == 1) {
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

        cell1.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="OtherOfficeDesc${ExpenseCount}" id="OtherOfficeDesc${ExpenseCount}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="OtherOfficeIncome${ExpenseCount}" id="OtherOfficeIncome${ExpenseCount}" oninput="ChangeOtherOfficeExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell3.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="OtherOfficeExpenses${ExpenseCount}" id="OtherOfficeExpenses${ExpenseCount}" oninput="ChangeOtherOfficeExpenses()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

        cell1.innerHTML = `<div class="mb-3"><input type="date" class="form-control" name="BankRecDate${ExpenseCount}" id="BankRecDate${ExpenseCount}" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $bank_rec_array[$row]['bank_rec_date'] ?? '' }}"></div>`;
        cell2.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="BankRecCheckNo${ExpenseCount}" id="BankRecCheckNo${ExpenseCount}"  oninput="ChangeBankRec()"></div>`;
        cell3.innerHTML = `<div class="mb-3"><input type="text" class="form-control" name="BankRecDesc${ExpenseCount}" id="BankRecDesc${ExpenseCount}"></div>`;
        cell4.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="BankRecPaymentAmount${ExpenseCount}" id="BankRecPaymentAmount${ExpenseCount}" oninput="ChangeBankRec()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;
        cell5.innerHTML = `<div class="mb-3"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="text" class="form-control" name="BankRecDepositAmount${ExpenseCount}" id="BankRecDepositAmount${ExpenseCount}" oninput="ChangeBankRec()" data-inputmask="'alias': 'currency', 'rightAlign': false, 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"></div></div>`;

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

            if (ExpenseCount == 1) {
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
        var SumOtherOperatingExpense = 0;
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
        SumOtherOperatingExpense = parseNumber(document.getElementById("SumOtherOperatingExpense").value);
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

</script>
<script>

window.addEventListener('load', function() {
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
    TogglePlaygroupsExplanation();
    ToggleParkDaysExplanation();
    ToggleSisterChapterExplanation();
    // toggleAwardBlocks();

});

    function ToggleReceiveCompensationExplanation() {
        var selectedRadio = document.querySelector('input[name="ReceiveCompensation"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null; /* Questions 1 */

        if (selectedValue == "1") {
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

        if (selectedValue == "1") {
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

        if (selectedValue == "1") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == null) {
            document.getElementById("divMeetingSpeakersTopics").style.display = 'none';
            return;
        }

        if (selectedValue == "0") {
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

        if (selectedValue == "1") {
            $('#SisterChapterExplanation').addClass('tx-cls');
            document.getElementById("divSisterChapterExplanation").style.display = 'block';
        } else {
            $('#SisterChapterExplanation').removeClass('tx-cls');
            document.getElementById("divSisterChapterExplanation").style.display = 'none';
        }
    }

    function TogglePlaygroupsExplanation() {
        var selectedRadio = document.querySelector('input[name="Playgroups"]:checked');
        var selectedValue = selectedRadio ? selectedRadio.value : null;

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "1") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
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

        if (selectedValue == "0") {
            $('#BankStatementIncludedExplanation').addClass('tx-cls');
            document.getElementById("divBankStatementIncludedExplanation").style.display = 'block'; // If "No" is selected
            document.getElementById("WheresTheMoney").style.display = 'block'; // If "No" is selected
        } else {
            $('#BankStatementIncludedExplanation').removeClass('tx-cls');
            document.getElementById("divBankStatementIncludedExplanation").style.display = 'none'; // If "Yes" is selected
            document.getElementById("WheresTheMoney").style.display = 'none'; // If "Yes" is selected
        }
    }

</script>
