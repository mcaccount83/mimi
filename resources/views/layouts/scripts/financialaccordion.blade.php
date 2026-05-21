<script>
    $(document).ready(function() {
        ChapterDuesQuestionsChange();
        ChangeChildrensRoomExpenses();
        ChangeMemberCount();
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
    });

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

        if(ChangedMeetingFees && ChargedMembersDifferently) {
            $('#lblTotalNewMembers').text('Total New Members (who paid OLD dues amount)');
            $('#lblTotalRenewedMembers').text('Total Renewed Members (who paid OLD dues amount)');
            $('#lblMemberDues').text('Dues collected per New Member (OLD Amount)');
            $('#lblNewMemberDues').text('Dues collected per New Member (NEW Amount)');
            $('#lblMemberDuesRenewal').text('Dues collected per Renewal Member (OLD Amount)');
            $('#lblNewMemberDuesRenewal').text('Dues collected per Renewal Member (NEW Amount)');
        } else if(ChangedMeetingFees) {
            $('#lblTotalNewMembers').text('Total New Members (who paid OLD dues amount)');
            $('#lblTotalRenewedMembers').text('Total Renewed Members (who paid OLD dues amount)');
            $('#lblMemberDues').text('Dues collected per Member (OLD Amount)');
            $('#lblNewMemberDues').text('Dues collected per Member (NEW Amount)');
        } else if(ChargedMembersDifferently) {
            $('#lblTotalNewMembers').text('Total New Members (who paid dues)');
            $('#lblTotalRenewedMembers').text('Total Renewed Members (who paid dues)');
            $('#lblMemberDues').text('Dues collected per New Member');
            $('#lblMemberDuesRenewal').text('Dues collected per Renewal Member');
        } else {
            $('#lblTotalNewMembers').text('Total New Members (who paid dues)');
            $('#lblTotalRenewedMembers').text('Total Renewed Members (who paid dues)');
            $('#lblMemberDues').text('Dues collected per Member');
        }

        ////////////////////////////////////////////////////////////
        if(ChangedMeetingFees){
            $('#newMemberDuesChanged').show();
            $('#membersChangedDues').show();
        }
        else{
            $('#newMemberDuesChanged').hide();
            $('#membersChangedDues').hide();
            $('#TotalNewMembersNewFee').val('');
            $('#TotalRenewedMembersNewFee').val('');
            $('#NewMemberDues').val('');
        }

        //////////////////////////////////////////////////////////
        if(ChargedMembersDifferently){
            $('#renewDues').show();
            $('#renewMemberDues').show();

            if(ChangedMeetingFees){
                $('#renewMemberDuesChanged').show();
            }
            else{
                $('#renewMemberDuesChanged').hide();
            }
        }
        else{
            $('#renewDues').hide();
            $('#renewMemberDues').hide();
            $('#renewMemberDuesChanged').hide();
            $('#MemberDuesRenewal').val('');
            $('#NewMemberDuesRenewal').val('');
        }

        ///////////////////////////////////////////////////////
        var Dues0 = $('#Dues0').prop('checked') ?? false;
        var Dues1 = $('#Dues1').prop('checked') ?? false;
        var Dues2 = $('#Dues2').prop('checked') ?? false;

        if(MembersReducedDues){
            if(Dues0){
                $('#waived').show();
            } else {
                $('#waived').hide();
                $('#MembersNoDues').val('');
            }

            if(Dues1){
                $('#partial').show();
            } else {
                $('#partial').hide();
                $('#TotalPartialDuesMembers').val('');
                $('#PartialDuesMemberDues').val('');
            }

            if(Dues2){
                $('#associate').show();
            } else {
                $('#associate').hide();
                $('#TotalAssociateMembers').val('');
                $('#AssociateMemberDues').val('');
            }
        } else {
            $('#waived').hide();
            $('#partial').hide();
            $('#associate').hide();
            $('#MembersNoDues').val('');
            $('#TotalPartialDuesMembers').val('');
            $('#PartialDuesMemberDues').val('');
            $('#TotalAssociateMembers').val('');
            $('#AssociateMemberDues').val('');
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
        var ChangedMeetingFees = $('input[name="optChangeDues"]:checked').val() == "1";
        var ChargedMembersDifferently = $('input[name="optNewOldDifferent"]:checked').val() == "1";
        var MembersReducedDues = $('input[name="optNoFullDues"]:checked').val() == "1";

        var NewMembers = Number($('#TotalNewMembers').val()) || 0;
        var RenewedMembers = Number($('#TotalRenewedMembers').val()) || 0;
        var NewMembers2 = Number($('#TotalNewMembersNewFee').val()) || 0;
        var RenewedMembers2 = Number($('#TotalRenewedMembersNewFee').val()) || 0;

        var MemberDues = Number($('#MemberDues').val().replace(/[^0-9.-]+/g,"")) || 0;
        var NewMemberDues = Number($('#NewMemberDues').val().replace(/[^0-9.-]+/g,"")) || 0;
        var MemberDuesRenewal = Number($('#MemberDuesRenewal').val().replace(/[^0-9.-]+/g,"")) || 0;
        var NewMemberDuesRenewal = Number($('#NewMemberDuesRenewal').val().replace(/[^0-9.-]+/g,"")) || 0;

        var MembersNoDues = Number($('#MembersNoDues').val()) || 0;
        var PartialDuesMembers = Number($('#TotalPartialDuesMembers').val()) || 0;
        var AssociateMembers = Number($('#TotalAssociateMembers').val()) || 0;

        var TotalMembers = NewMembers + RenewedMembers + MembersNoDues + AssociateMembers + PartialDuesMembers + NewMembers2 + RenewedMembers2;

        $('#TotalMembers').val(TotalMembers);

        var newMembersDues = NewMembers * MemberDues;
        var renewalMembersDues = RenewedMembers * MemberDues;
        var renewalMembersDuesDiff = RenewedMembers * MemberDuesRenewal;
        var newMembersDuesNew = NewMembers2 * NewMemberDues;
        var renewMembersDuesNew = RenewedMembers2 * NewMemberDues;
        var renewMembersNewDuesDiff = RenewedMembers2 * NewMemberDuesRenewal;
        var partialMembersDues = PartialDuesMembers * (Number($('#PartialDuesMemberDues').val().replace(/[^0-9.-]+/g,"")) || 0);
        var associateMembersDues = AssociateMembers * (Number($('#AssociateMemberDues').val().replace(/[^0-9.-]+/g,"")) || 0);

        var TotalFees;
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

        $('#TotalDues').val(TotalFees);
        $('#SumMembershipDuesIncome').val(TotalFees);

        ReCalculateSummaryTotal();
    }

    function ChangeChildrensRoomExpenses(){
        var SupplyTotal = 0;
        var OtherTotal = 0;

        $('#childrens-room tbody tr').each(function() {
            SupplyTotal += Number($(this).find('td:eq(1) input').val().replace(/,/g, '')) || 0;
            OtherTotal += Number($(this).find('td:eq(2) input').val().replace(/,/g, '')) || 0;
        });

        var TotalMisc = (SupplyTotal + OtherTotal).toFixed(2);
        SupplyTotal = SupplyTotal.toFixed(2);
        OtherTotal = OtherTotal.toFixed(2);

        $('#childrens-room tfoot input:eq(0)').val(SupplyTotal);
        $('#childrens-room tfoot input:eq(1)').val(OtherTotal);

        $('#SumChildrensOtherExpense').val(OtherTotal);
        $('#SumChildrensSuppliesExpense').val(SupplyTotal);

        var SumPaidSittersExpense = (Number($('#PaidBabySitters').val().replace(/,/g, '')) || 0).toFixed(2);
        $('#SumChildrensPaidSittersExpense').val(SumPaidSittersExpense);

        var TotalChildrensFees = (Number(TotalMisc) + Number(SumPaidSittersExpense)).toFixed(2);
        $('#SumTotalChildrensRoomExpense').val(TotalChildrensFees);
        $('#ChildrensRoomTotal').val(TotalChildrensFees);

        ReCalculateSummaryTotal();
    }

    function AddChildrenExpenseRow() {
        var ExpenseCount = parseInt($('#ChildrensExpenseRowCount').val(), 10);
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var tbody = $('#childrens-room tbody');
        var row = $('<tr>');

        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="ChildrensRoomDesc${ExpenseCount}" id="ChildrensRoomDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="ChildrensRoomSupplies${ExpenseCount}" id="ChildrensRoomSupplies${ExpenseCount}" oninput="ChangeChildrensRoomExpenses()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="ChildrensRoomOther${ExpenseCount}" id="ChildrensRoomOther${ExpenseCount}" oninput="ChangeChildrensRoomExpenses()" ${currencyMask} data-mask></div></div></td>`);

        tbody.append(row);

        ExpenseCount++;
        $('#ChildrensExpenseRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteChildrenExpenseRow() {
        var ExpenseCount = parseInt($('#ChildrensExpenseRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#childrens-room tbody tr:last').remove();
            ExpenseCount--;
            $('#ChildrensExpenseRowCount').val(ExpenseCount);
            ChangeChildrensRoomExpenses();
        }
    }

    function ChangeServiceProjectExpenses() {
        var ExpenseTotal = 0;
        var IncomeTotal = 0;
        var CharityTotal = 0;
        var M2MTotal = 0;

        $('#service-projects tbody tr').each(function() {
            IncomeTotal += Number($(this).find('td:eq(1) input').val().replace(/,/g, '')) || 0;
            ExpenseTotal += Number($(this).find('td:eq(2) input').val().replace(/,/g, '')) || 0;
            CharityTotal += Number($(this).find('td:eq(3) input').val().replace(/,/g, '')) || 0;
            M2MTotal += Number($(this).find('td:eq(4) input').val().replace(/,/g, '')) || 0;
        });

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);
        CharityTotal = CharityTotal.toFixed(2);
        M2MTotal = M2MTotal.toFixed(2);

        $('#service-projects tfoot input:eq(0)').val(IncomeTotal);
        $('#service-projects tfoot input:eq(1)').val(ExpenseTotal);
        $('#service-projects tfoot input:eq(2)').val(CharityTotal);
        $('#service-projects tfoot input:eq(3)').val(M2MTotal);

        $('#ServiceProjectIncomeTotal').val(IncomeTotal);
        $('#SumServiceProjectIncome').val(IncomeTotal);
        $('#SumServiceProjectExpense').val(ExpenseTotal);
        $('#SumDonationExpense').val(CharityTotal);
        $('#SumM2MExpense').val(M2MTotal);

        var TotalServiceProjectFees = (parseFloat(ExpenseTotal) + parseFloat(CharityTotal) + parseFloat(M2MTotal)).toFixed(2);
        $('#ServiceProjectExpenseTotal').val(TotalServiceProjectFees);
        $('#SumTotalServiceProjectExpense').val(TotalServiceProjectFees);

        ReCalculateSummaryTotal();
    }

    function AddServiceProjectRow() {
        var ExpenseCount = parseInt($('#ServiceProjectRowCount').val());
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><textarea class="form-control" rows="4" name="ServiceProjectDesc${ExpenseCount}" id="ServiceProjectDesc${ExpenseCount}"></textarea></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="ServiceProjectIncome${ExpenseCount}" id="ServiceProjectIncome${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="ServiceProjectSupplies${ExpenseCount}" id="ServiceProjectSupplies${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="ServiceProjectDonatedCharity${ExpenseCount}" id="ServiceProjectDonatedCharity${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="ServiceProjectDonatedM2M${ExpenseCount}" id="ServiceProjectDonatedM2M${ExpenseCount}" oninput="ChangeServiceProjectExpenses()" ${currencyMask} data-mask></div></div></td>`);

        $('#service-projects tbody').append(row);

        ExpenseCount++;
        $('#ServiceProjectRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteServiceProjectRow() {
        var ExpenseCount = parseInt($('#ServiceProjectRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#service-projects tbody tr:last').remove();
            ExpenseCount--;
            $('#ServiceProjectRowCount').val(ExpenseCount);
            ChangeServiceProjectExpenses();
        }
    }

    function ChangePartyExpenses() {
        var IncomeTotal = 0;
        var ExpenseTotal = 0;

        $('#party-expenses tbody tr').each(function() {
            IncomeTotal += Number($(this).find('td:eq(1) input').val().replace(/,/g, '')) || 0;
            ExpenseTotal += Number($(this).find('td:eq(2) input').val().replace(/,/g, '')) || 0;
        });

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);

        $('#party-expenses tfoot input:eq(0)').val(IncomeTotal);
        $('#party-expenses tfoot input:eq(1)').val(ExpenseTotal);

        $('#PartyIncomeTotal').val(IncomeTotal);
        $('#PartyExpenseTotal').val(ExpenseTotal);
        $('#SumPartyIncome').val(IncomeTotal);
        $('#SumPartyExpense').val(ExpenseTotal);

        ReCalculateSummaryTotal();
    }

    function AddPartyExpenseRow() {
        var ExpenseCount = parseInt($('#PartyExpenseRowCount').val());
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="PartyDesc${ExpenseCount}" id="PartyDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="PartyIncome${ExpenseCount}" id="PartyIncome${ExpenseCount}" oninput="ChangePartyExpenses()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="PartyExpenses${ExpenseCount}" id="PartyExpenses${ExpenseCount}" oninput="ChangePartyExpenses()" ${currencyMask} data-mask></div></div></td>`);

        $('#party-expenses tbody').append(row);

        ExpenseCount++;
        $('#PartyExpenseRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeletePartyExpenseRow() {
        var ExpenseCount = parseInt($('#PartyExpenseRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#party-expenses tbody tr:last').remove();
            ExpenseCount--;
            $('#PartyExpenseRowCount').val(ExpenseCount);
            ChangePartyExpenses();
        }
    }

    function ChangeOfficeExpenses(){
        var totalExpenses = 0;

        $('#office-expenses tbody tr').each(function() {
            totalExpenses += Number($(this).find('td:eq(1) input').val().replace(/,/g, '')) || 0;
        });

        $('#office-expenses tfoot input:eq(0)').val(totalExpenses.toFixed(2));

        var SumPrintingExpense = Number($('#PrintingCosts').val().replace(/,/g, '')) || 0;
        var SumPostageExpense = Number($('#PostageCosts').val().replace(/,/g, '')) || 0;
        var SumPinsExpense = Number($('#MembershipPins').val().replace(/,/g, '')) || 0;

        var OperatingTotal = totalExpenses + SumPrintingExpense + SumPostageExpense + SumPinsExpense;

        $('#SumOtherOperatingExpense').val(totalExpenses.toFixed(2));
        $('#SumPrintingExpense').val(SumPrintingExpense.toFixed(2));
        $('#SumPostageExpense').val(SumPostageExpense.toFixed(2));
        $('#SumPinsExpense').val(SumPinsExpense.toFixed(2));
        $('#SumOperatingExpense').val(OperatingTotal.toFixed(2));
        $('#TotalOperatingExpense').val(OperatingTotal.toFixed(2));

        ReCalculateSummaryTotal();
    }

    function AddOfficeExpenseRow() {
        var ExpenseCount = parseInt($('#OfficeExpenseRowCount').val());
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="OfficeDesc${ExpenseCount}" id="OfficeDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="OfficeExpenses${ExpenseCount}" id="OfficeExpenses${ExpenseCount}" oninput="ChangeOfficeExpenses()" ${currencyMask} data-mask></div></div></td>`);

        $('#office-expenses tbody').append(row);

        ExpenseCount++;
        $('#OfficeExpenseRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteOfficeExpenseRow() {
        var ExpenseCount = parseInt($('#OfficeExpenseRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#office-expenses tbody tr:last').remove();
            ExpenseCount--;
            $('#OfficeExpenseRowCount').val(ExpenseCount);
            ChangeOfficeExpenses();
        }
    }

    function ChangeReRegistrationExpense(){
        var ReRegistrationFee = Number($('#AnnualRegistrationFee').val().replace(/,/g, '')) || 0;
        $('#SumChapterReRegistrationExpense').val(ReRegistrationFee.toFixed(2));
        ReCalculateSummaryTotal();
    }

    function ChangeInternationalEventExpense(){
        var ExpenseTotal = 0;
        var IncomeTotal = 0;

        $('#international_events tbody tr').each(function() {
            IncomeTotal += Number($(this).find('td:eq(1) input').val().replace(/,/g, '')) || 0;
            ExpenseTotal += Number($(this).find('td:eq(2) input').val().replace(/,/g, '')) || 0;
        });

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);

        $('#international_events tfoot input:eq(0)').val(IncomeTotal);
        $('#international_events tfoot input:eq(1)').val(ExpenseTotal);

        $('#InternationalEventIncomeTotal').val(IncomeTotal);
        $('#InternationalEventExpenseTotal').val(ExpenseTotal);
        $('#SumInternationalEventIncome').val(IncomeTotal);
        $('#SumInternationalEventExpense').val(ExpenseTotal);

        ReCalculateSummaryTotal();
    }

    function AddInternationalEventRow() {
        var ExpenseCount = parseInt($('#InternationalEventRowCount').val());
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="InternationalEventDesc${ExpenseCount}" id="InternationalEventDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="InternationalEventIncome${ExpenseCount}" id="InternationalEventIncome${ExpenseCount}" oninput="ChangeInternationalEventExpense()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="InternationalEventExpense${ExpenseCount}" id="InternationalEventExpense${ExpenseCount}" oninput="ChangeInternationalEventExpense()" ${currencyMask} data-mask></div></div></td>`);

        $('#international_events tbody').append(row);

        ExpenseCount++;
        $('#InternationalEventRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteInternationalEventRow() {
        var ExpenseCount = parseInt($('#InternationalEventRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#international_events tbody tr:last').remove();
            ExpenseCount--;
            $('#InternationalEventRowCount').val(ExpenseCount);
            ChangeInternationalEventExpense();
        }
    }

    function ChangeDonationAmount() {
        var IncomeTotal = 0;

        $('#donation-income tbody tr').each(function() {
            IncomeTotal += Number($(this).find('td:eq(3) input').val().replace(/,/g, '')) || 0;
        });

        $('#donation-income tfoot input:eq(0)').val(IncomeTotal.toFixed(2));
        $('#DonationTotal').val(IncomeTotal);
        $('#SumMonetaryDonationIncome').val(IncomeTotal);
    }

    function AddMonDonationRow() {
        var ExpenseCount = parseInt($('#MonDonationRowCount').val());
        const dateMask = `data-inputmask='"alias": "datetime", "inputFormat": "mm/dd/yyyy"'`;
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="DonationDesc${ExpenseCount}" id="DonationDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="DonorInfo${ExpenseCount}" id="DonorInfo${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="MonDonationDate${ExpenseCount}" id="MonDonationDate${ExpenseCount}" ${dateMask} data-mask placeholder="mm/dd/yyyy"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="DonationAmount${ExpenseCount}" id="DonationAmount${ExpenseCount}" oninput="ChangeDonationAmount()" ${currencyMask} data-mask></div></div></td>`);

        $('#donation-income tbody').append(row);

        ExpenseCount++;
        $('#MonDonationRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteMonDonationRow() {
        var ExpenseCount = parseInt($('#MonDonationRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#donation-income tbody tr:last').remove();
            ExpenseCount--;
            $('#MonDonationRowCount').val(ExpenseCount);
            ChangeDonationAmount();
        }
    }

    function AddNonMonDonationRow() {
        var ExpenseCount = parseInt($('#NonMonDonationRowCount').val());
        const dateMask = `data-inputmask='"alias": "datetime", "inputFormat": "mm/dd/yyyy"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="NonMonDonationDesc${ExpenseCount}" id="NonMonDonationDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="NonMonDonorInfo${ExpenseCount}" id="NonMonDonorInfo${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="NonMonDonationDate${ExpenseCount}" id="NonMonDonationDate${ExpenseCount}" ${dateMask} data-mask placeholder="mm/dd/yyyy"></div></td>`);

        $('#donation-goods tbody').append(row);

        ExpenseCount++;
        $('#NonMonDonationRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteNonMonDonationRow() {
        var ExpenseCount = parseInt($('#NonMonDonationRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#donation-goods tbody tr:last').remove();
            ExpenseCount--;
            $('#NonMonDonationRowCount').val(ExpenseCount);
        }
    }

    function ChangeOtherOfficeExpenses() {
        var ExpenseTotal = 0;
        var IncomeTotal = 0;

        $('#other-office-expenses tbody tr').each(function() {
            IncomeTotal += Number($(this).find('td:eq(1) input').val().replace(/,/g, '')) || 0;
            ExpenseTotal += Number($(this).find('td:eq(2) input').val().replace(/,/g, '')) || 0;
        });

        IncomeTotal = IncomeTotal.toFixed(2);
        ExpenseTotal = ExpenseTotal.toFixed(2);

        $('#other-office-expenses tfoot input:eq(0)').val(IncomeTotal);
        $('#other-office-expenses tfoot input:eq(1)').val(ExpenseTotal);

        $('#OtherOfficeExpenseTotal').val(ExpenseTotal);
        $('#OtherOfficeIncomeTotal').val(IncomeTotal);
        $('#SumOtherIncome').val(IncomeTotal);
        $('#SumOtherExpense').val(ExpenseTotal);

        ReCalculateSummaryTotal();
    }

    function AddOtherOfficeExpenseRow() {
        var ExpenseCount = parseInt($('#OtherOfficeExpenseRowCount').val());
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="OtherOfficeDesc${ExpenseCount}" id="OtherOfficeDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="OtherOfficeIncome${ExpenseCount}" id="OtherOfficeIncome${ExpenseCount}" oninput="ChangeOtherOfficeExpenses()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="OtherOfficeExpenses${ExpenseCount}" id="OtherOfficeExpenses${ExpenseCount}" oninput="ChangeOtherOfficeExpenses()" ${currencyMask} data-mask></div></div></td>`);

        $('#other-office-expenses tbody').append(row);

        ExpenseCount++;
        $('#OtherOfficeExpenseRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteOtherOfficeExpenseRow() {
        var ExpenseCount = parseInt($('#OtherOfficeExpenseRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#other-office-expenses tbody tr:last').remove();
            ExpenseCount--;
            $('#OtherOfficeExpenseRowCount').val(ExpenseCount);
            ChangeOtherOfficeExpenses();
        }
    }

    function TreasuryBalanceChange() {
        var TreasuryBalance = parseFloat($('#AmountReservedFromLastYear').val().replace(/,/g, '')) || 0;
        $('#AmountReservedFromLastYear').val(TreasuryBalance.toFixed(2));
        ReCalculateSummaryTotal();
    }

    function ChangeBankRec() {
        var PaymentTotal = 0;
        var DepositTotal = 0;

        $('#bank-rec tbody tr').each(function() {
            PaymentTotal += parseFloat($(this).find('input[name^="BankRecPaymentAmount"]').val()?.replace(/,/g, '') || 0) || 0;
            DepositTotal += parseFloat($(this).find('input[name^="BankRecDepositAmount"]').val()?.replace(/,/g, '') || 0) || 0;
        });

        var BankBalanceNow = parseFloat($('#BankBalanceNow').val().replace(/,/g, '')) || 0;
        var TotalFees = (BankBalanceNow - PaymentTotal + DepositTotal).toFixed(2);
        $('#ReconciledBankBalance').val(TotalFees);

        var TreasuryBalanceNow = parseFloat($('#TreasuryBalanceNow').val().replace(/,/g, '')) || 0;

        if (TotalFees != TreasuryBalanceNow) {
            $('#ReconciliationAlert').show();
            $('#ReconciledBankBalanceWarning')
                .text('Reconciled Bank Balance does not match treasury balance now. These numbers must match for your report to be in balance')
                .css('border-style', 'none');
        } else {
            $('#ReconciliationAlert').hide();
        }
    }

    function AddBankRecRow(){
        var ExpenseCount = parseInt($('#BankRecRowCount').val());
        const currencyMask = `data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'`;
        const dateMask = `data-inputmask='"alias": "datetime", "inputFormat": "mm/dd/yyyy"'`;

        var row = $('<tr>');
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="BankRecDate${ExpenseCount}" id="BankRecDate${ExpenseCount}" ${dateMask} data-mask placeholder="mm/dd/yyyy"></div></td>`);
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="BankRecCheckNo${ExpenseCount}" id="BankRecCheckNo${ExpenseCount}" oninput="ChangeBankRec()"></div></td>`);
        row.append(`<td><div class="mb-3"><input type="text" class="form-control" name="BankRecDesc${ExpenseCount}" id="BankRecDesc${ExpenseCount}"></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="BankRecPaymentAmount${ExpenseCount}" id="BankRecPaymentAmount${ExpenseCount}" oninput="ChangeBankRec()" ${currencyMask} data-mask></div></div></td>`);
        row.append(`<td><div class="mb-3"><div class="input-group"><span class="input-group-text">$</span><input type="text" class="form-control" name="BankRecDepositAmount${ExpenseCount}" id="BankRecDepositAmount${ExpenseCount}" oninput="ChangeBankRec()" ${currencyMask} data-mask></div></div></td>`);

        $('#bank-rec tbody').append(row);

        ExpenseCount++;
        $('#BankRecRowCount').val(ExpenseCount);

        Inputmask().mask(row.find('[data-inputmask]'));
    }

    function DeleteBankRecRow() {
        var ExpenseCount = parseInt($('#BankRecRowCount').val(), 10);

        if (ExpenseCount > 1) {
            $('#bank-rec tbody tr:last').remove();
            ExpenseCount--;
            $('#BankRecRowCount').val(ExpenseCount);
            ChangeBankRec();
        }
    }

    function ReCalculateSummaryTotal() {
        function parseNumber(value) {
            return Number((value || '0').replace(/,/g, '')) || 0;
        }

        var SumMeetingRoomExpense = parseNumber($('#SumMeetingRoomExpense').val());
        var SumMembershipDuesIncome = parseNumber($('#SumMembershipDuesIncome').val());
        var SumTotalChildrensRoomExpense = parseNumber($('#SumTotalChildrensRoomExpense').val());
        var ServiceIncomeTotal = parseNumber($('#SumServiceProjectIncome').val());
        var ServiceExpenseTotal = parseNumber($('#SumTotalServiceProjectExpense').val());
        var SumPartyIncome = parseNumber($('#SumPartyIncome').val());
        var SumPartyExpense = parseNumber($('#SumPartyExpense').val());
        var SumOtherIncome = parseNumber($('#SumOtherIncome').val());
        var SumOtherExpense = parseNumber($('#SumOtherExpense').val());
        var SumOtherOperatingExpense = parseNumber($('#SumOtherOperatingExpense').val());
        var SumOperatingExpense = parseNumber($('#SumOperatingExpense').val());
        var SumInternationalEventExpense = parseNumber($('#SumInternationalEventExpense').val());
        var SumInternationalEventIncome = parseNumber($('#SumInternationalEventIncome').val());
        var SumMonetaryDonationIncome = parseNumber($('#SumMonetaryDonationIncome').val());
        var SumChapterReRegistrationExpense = parseNumber($('#SumChapterReRegistrationExpense').val());
        var TreasuryBalance = parseNumber($('#AmountReservedFromLastYear').val());

        var SumTotalExpense = SumTotalChildrensRoomExpense + SumMeetingRoomExpense + ServiceExpenseTotal + SumOtherExpense + SumPartyExpense + SumOperatingExpense + SumInternationalEventExpense + SumChapterReRegistrationExpense;
        var SumTotalIncome = ServiceIncomeTotal + SumOtherIncome + SumPartyIncome + SumMembershipDuesIncome + SumInternationalEventIncome + SumMonetaryDonationIncome;
        var TreasuryBalanceNow = TreasuryBalance - SumTotalExpense + SumTotalIncome;
        var SumTotalNetIncome = SumTotalIncome - SumTotalExpense;

        $('#SumTotalExpense').val(SumTotalExpense.toFixed(2));
        $('#SumTotalIncome').val(SumTotalIncome.toFixed(2));
        $('#TotalNetIncome').val(SumTotalNetIncome.toFixed(2));
        $('#SumTotalNetIncome').val(SumTotalNetIncome.toFixed(2));
        $('#TreasuryBalanceNow').val(TreasuryBalanceNow.toFixed(2));
        $('#TreasuryBalanceNowR').val(TreasuryBalanceNow.toFixed(2));

        ChangeBankRec();
    }
</script>

<script>
    $(document).ready(function() {
        ToggleNotFullDuesExplanation();
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
    });

    function getRadioValue(name) {
        return $('input[name="' + name + '"]:checked').val() ?? null;
    }

    function toggleExplanation(divId, condition) {
        condition ? $('#' + divId).show() : $('#' + divId).hide();
    }

    function ToggleNotFullDuesExplanation() {
        if (getRadioValue('optNoFullDues') != "1") {
            $('#divNoFullDues').hide();
            $('#Dues0, #Dues1, #Dues2').prop('checked', false);
        } else {
            $('#divNoFullDues').show();
        }
        ChapterDuesQuestionsChange();
    }

    function ToggleReceiveCompensationExplanation() {
        toggleExplanation('divReceiveCompensationExplanation', getRadioValue('ReceiveCompensation') == "1");
    }

    function ToggleFinancialBenefitExplanation() {
        toggleExplanation('divFinancialBenefitExplanation', getRadioValue('FinancialBenefit') == "1");
    }

    function ToggleInfluencePoliticalExplanation() {
        toggleExplanation('divInfluencePoliticalExplanation', getRadioValue('InfluencePolitical') == "1");
    }

    function ToggleVoteAllActivitiesExplanation() {
        toggleExplanation('divVoteAllActivitiesExplanation', getRadioValue('VoteAllActivities') == "0");
    }

    function ToggleBoughtPinsExplanation() {
        toggleExplanation('divBoughtPinsExplanation', getRadioValue('BoughtPins') == "0");
    }

    function ToggleBoughtMerchExplanation() {
        toggleExplanation('divBoughtMerchExplanation', getRadioValue('BoughtMerch') == "0");
    }

    function ToggleOfferedMerchExplanation() {
        toggleExplanation('divOfferedMerchExplanation', getRadioValue('OfferedMerch') == "0");
    }

    function ToggleByLawsAvailableExplanation() {
        toggleExplanation('divByLawsAvailableExplanation', getRadioValue('ByLawsAvailable') == "0");
    }

    function ToggleChildOutingsExplanation() {
        toggleExplanation('divChildOutingsExplanation', getRadioValue('ChildOutings') == "0");
    }

    function ToggleMotherOutingsExplanation() {
        toggleExplanation('divMotherOutingsExplanation', getRadioValue('MotherOutings') == "0");
    }

    function ToggleMeetingSpeakersExplanation() {
        var val = getRadioValue('MeetingSpeakers');
        if (val == null || val == "0") {
            $('#divMeetingSpeakersTopics').hide();
        } else {
            $('#divMeetingSpeakersTopics').show();
        }
    }

    function ToggleSisterChapterExplanation() {
        toggleExplanation('divSisterChapterExplanation', getRadioValue('SisterChapter') == "1");
    }

    function TogglePlaygroupsExplanation() {
        toggleExplanation('divPlaygroupsExplanation', getRadioValue('Playgroups') == "0");
    }

    function ToggleParkDaysExplanation() {
        toggleExplanation('divParkDaysExplanation', getRadioValue('ParkDays') == "0");
    }

    function ToggleActivityOtherExplanation() {
        toggleExplanation('divActivityOtherExplanation', $('input[name="Activity[]"][value="5"]').is(':checked'));
    }

    function ToggleContributionsNotRegNPExplanation() {
        toggleExplanation('divContributionsNotRegNPExplanation', getRadioValue('ContributionsNotRegNP') == "1");
    }

    function TogglePerformServiceProjectExplanation() {
        toggleExplanation('divPerformServiceProjectExplanation', getRadioValue('PerformServiceProject') == "0");
    }

    function ToggleFileIRSExplanation() {
        toggleExplanation('divFileIRSExplanation', getRadioValue('FileIRS') == "0");
    }

    function ToggleBankStatementIncludedExplanation() {
        var show = getRadioValue('BankStatementIncluded') == "0";
        toggleExplanation('divBankStatementIncludedExplanation', show);
        show ? $('#WheresTheMoney').show() : $('#WheresTheMoney').hide();
    }
</script>
