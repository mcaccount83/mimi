<script>
    /* Save & Submit Verification */
    $(document).ready(function() {
        function submitFormWithStep(step) {
            $("#FurthestStep").val(step);
            document.getElementById('financial_report').submit();
        }

        $("#btn-step-1").click(function() {
            if (!CheckMembers()) return false;
            submitFormWithStep(1);
        });
        $("#btn-step-2").click(function() {
            submitFormWithStep(2);
        });
        $("#btn-step-3").click(function() {
            if (!CheckService()) return false;
            submitFormWithStep(3);
        });
        $("#btn-step-4").click(function() {
            if (!CheckParties()) return false;
            submitFormWithStep(4);
        });
        $("#btn-step-5").click(function() {
            submitFormWithStep(5);
        });
        $("#btn-step-6").click(function() {
            if (!CheckInternational()) return false;
            submitFormWithStep(6);
        });
        $("#btn-step-7").click(function() {
            submitFormWithStep(7);
        });
        $("#btn-step-8").click(function() {
            submitFormWithStep(8);
        });
        $("#btn-step-9").click(function() {
            if (!CheckFinancial()) return false;
            submitFormWithStep(9);
        });
        $("#btn-step-10").click(function() {
            if (!CheckReconciliation()) return false;
            submitFormWithStep(10);
        });
        $("#btn-step-11").click(function() {
            if (!Check990N()) return false;
            submitFormWithStep(11);
        });
        $("#btn-step-12").click(function() {
            if (!CheckQuestions()) return false;
            submitFormWithStep(12);
        });
        $("#btn-step-13").click(function() {
            var assignedReviewer = document.getElementById('AssignedReviewer').value;
            if (assignedReviewer == null || assignedReviewer == '') {
                customWarningAlert('Please select a Reviewer');
                document.getElementById('AssignedReviewer').focus();
                return false;
            }
            submitFormWithStep(0);
        });
    });

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

    function CheckMembers() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkRosterAttached"]:checked')) {
        missingQuestions.push("Is the chapter roster attached?");
    }
    if (!document.querySelector('input[name="checkRenewalSeemsRight"]:checked')) {
        missingQuestions.push("Does the renewal count seem right?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the CHAPTER DUES section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-members');
        return false;
    }
    return true;
}

function CheckService() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkServiceProject"]:checked')) {
        missingQuestions.push("Is the service project verified?");
    }
    if (!document.querySelector('input[name="checkM2MDonation"]:checked')) {
        missingQuestions.push("Is the M2M donation verified?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the SERVICE PROJECTS section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-service');
        return false;
    }
    return true;
}

function CheckParties() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkPartyPercentage"]:checked')) {
        missingQuestions.push("Is the party percentage verified?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the PARTIES & MEMBER BENEFITS section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-parties');
        return false;
    }
    return true;
}

function CheckInternational() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkAttendedTraining"]:checked')) {
        missingQuestions.push("Did the chapter attend training?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the INTERNATIONAL EVENTS section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-reconciliation');
        return false;
    }
    return true;
}

function CheckFinancial() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkTotalIncome"]:checked')) {
        missingQuestions.push("Is the total income verified?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the FINANCIAL SUMMARY section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-financial');
        return false;
    }
    return true;
}

function CheckReconciliation() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkBeginningBalance"]:checked')) {
        missingQuestions.push("Is the beginning balance verified?");
    }
    if (!document.querySelector('input[name="checkBankStatementIncluded"]:checked')) {
        missingQuestions.push("Is the bank statement included?");
    }
    if (!document.querySelector('input[name="checkBankStatementMatches"]:checked')) {
        missingQuestions.push("Does the bank statement match?");
    }
    if (!document.getElementById('post_balance')) {
        missingQuestions.push("Is the post balance entered?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the RECONCILIATION section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-reconciliation');
        return false;
    }
    return true;
}

function Check990N() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkCurrent990NAttached"]:checked')) {
        missingQuestions.push("Is the current 990N attached?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the 990N IRS FILING section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-reconciliation');
        return false;
    }
    return true;
}

function CheckQuestions() {
    var missingQuestions = [];

    if (!document.querySelector('input[name="checkPurchasedPins"]:checked')) {
        missingQuestions.push("Were MOMS Club pins purchased?");
    }
    if (!document.querySelector('input[name="checkPurchasedMCMerch"]:checked')) {
        missingQuestions.push("Was MOMS Club merchandise purchased?");
    }
    if (!document.querySelector('input[name="checkOfferedMerch"]:checked')) {
        missingQuestions.push("Was merchandise offered to members?");
    }
    if (!document.querySelector('input[name="checkBylawsMadeAvailable"]:checked')) {
        missingQuestions.push("Were bylaws made available to members?");
    }
    if (!document.querySelector('input[name="checkSisteredAnotherChapter"]:checked')) {
        missingQuestions.push("Did the chapter sister another chapter?");
    }

    if (missingQuestions.length > 0) {
        var missingQuestionsText = missingQuestions.map(question => `<li>${question}</li>`).join('');
        var message = `<p>The following questions in the CHAPTER QUESTIONS section are required, please answer the required questions to continue.</p>
            <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                ${missingQuestionsText}
            </ul>`;
        customWarningAlert(message);
        accordion.openAccordionItem('accordion-header-questions');
        return false;
    }
    return true;
}

</script>
