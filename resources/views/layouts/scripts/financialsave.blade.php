<script>
    /* Save & Submit Verification */
    $(document).ready(function() {
        function submitFormWithStep(step) {
            $("#submitted").val('0');  // Add this - ensure it's saving, not submitting
            $("#FurthestStep").val(step);
            // Trigger the form's submit event properly (not .submit() method)
            document.getElementById('financial_report').submit();
        }

        $("#btn-step-1").click(function() {
            if (!EnsureRoster()) return false;
            if (!EnsureMembers()) return false;
            submitFormWithStep(1);
        });
        $("#btn-step-2").click(function() {
            if (!EnsureMeetingQuestions()) return false;
            submitFormWithStep(2);
        });
        $("#btn-step-3").click(function() {
            if (!EnsureServiceProjectQuestions()) return false;
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
            if (!EnsureInternationalQuestions()) return false;
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
            submitFormWithStep(10);
        });
        $("#btn-step-11").click(function() {
            if (!EnsureIRSQuestions()) return false;
            submitFormWithStep(11);
        });
        $("#btn-step-12").click(function() {
            if (!EnsureChapterQuestions()) return false;
            submitFormWithStep(12);
        });
        $("#btn-step-13").click(function() {
            submitFormWithStep(13);
        });
        $("#btn-step-14").click(function() {
            submitFormWithStep(14);
        });
        $("#btn-save").click(function() {
            submitFormWithStep(15);
        });
    });

    $("#final-submit").click(function(e) {
        e.preventDefault();  // Add this to prevent double submission

        // Validation checks
        if (!EnsureRoster()) return false;
        if (!EnsureMembers()) return false;
        if (!EnsureMeetingQuestions()) return false;
        if (!EnsureServiceProjectQuestions()) return false;
        if (!EnsureServiceProject()) return false;
        if (!EnsureReRegistration()) return false;
        if (!EnsureInternationalQuestions()) return false;
        if (!EnsureReconciliationQuestions()) return false;
        if (!EnsureReconciliation()) return false;
        if (!EnsureIRSQuestions()) return false;
        if (!EnsureChapterQuestions()) return false;

        // Await EnsureBalance if it is an async function
        // if (!await EnsureBalance()) return false;

        // Use SweetAlert2 for the final confirmation
        Swal.fire({
            title: 'Final Confirmation',
            text: "This will finalize and submit your report. You will no longer be able to edit this report. Do you wish to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit Request',
            cancelButtonText: 'Cancel',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
                cancelButton: 'btn btn-sm btn-danger'
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
                        confirmButton: 'btn btn-sm btn-success'
                    }
                });

                // Set values and trigger form submit properly
                $("#submitted").val('1');
                $("#FurthestStep").val('16');

                // Trigger the form's submit event properly (not .submit() method)
                var form = document.getElementById('financial_report');
                form.submit();  // Use direct submit here since we already validated
            }
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
        if (amountReservedFromLastYear == '' || amountReservedFromLastYear == null) {
            missingFields.push("This Year's Beginning Balance");
        }
        if (bankBalanceNow == '' || bankBalanceNow == null) {
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
                    confirmButton: 'btn btn-sm btn-success',
                    cancelButton: 'btn btn-sm btn-danger'
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

    function EnsureMeetingQuestions() {
        var requiredQuestions = [
            'MeetingSpeakers', 'SpeakerFrequency', 'ChildrensRoom',
        ];

        // Mapping of internal names to user-friendly labels
        var questionLabels = {
            'MeetingSpeakers': 'Did the chapter have meeting speakers?',
            'SpeakerFrequency': 'Did the chapter have discussion topics at meetings?',
            'ChildrensRoom': 'Did the chapter have a children\'s room?',
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
            var message = `<p>The following questions in the MONTHLY MEETING EXPENSES section are required, please answer the required questions to continue.</p>
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

    function EnsureServiceProjectQuestions() {
        var requiredQuestions = [
            'PerformServiceProject', 'ContributionsNotRegNP'
        ];

        // Mapping of internal names to user-friendly labels
        var questionLabels = {
                'PerformServiceProject': 'Did the chapter perform at least one service project?',
                'ContributionsNotRegNP': 'Did the chapter make contributions to non-charities?',
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
            var message = `<p>The following questions in the SERVICE PROJECTS section are required, please answer the required questions to continue.</p>
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

    function EnsureInternationalQuestions() {
        var requiredQuestions = [
            'InternationalEvent'
        ];

        // Mapping of internal names to user-friendly labels
        var questionLabels = {
            'InternationalEvent': 'Did the chapter atend an International event?',
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
            var message = `<p>The following questions in the INTERNATIONAL EVENTS & RE-REGISTRATION section are required, please answer the required questions to continue.</p>
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

    function EnsureReconciliationQuestions() {
        var requiredQuestions = [
            'BankStatementIncluded'
        ];

        // Mapping of internal names to user-friendly labels
        var questionLabels = {
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
            var message = `<p>The following questions in the BANK RECONCILIATION section are required, please answer the required questions to continue.</p>
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

    function EnsureIRSQuestions() {
        var requiredQuestions = [
            'FileIRS'
        ];

        // Mapping of internal names to user-friendly labels
        var questionLabels = {
            'FileIRS': 'Is the 990N filed with the IRS?',
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
            var message = `<p>The following questions in the 990N IRS FILING section are required, please answer the required questions to continue.</p>
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

    function EnsureChapterQuestions() {
        var requiredQuestions = [
            'ByLawsAvailable', 'VoteAllActivities', 'ChildOutings', 'Playgroups',
            'ParkDays', 'MotherOutings', 'Activity[]', 'OfferedMerch', 'BoughtMerch',
            'BoughtPins', 'ReceiveCompensation', 'FinancialBenefit', 'InfluencePolitical',
            'SisterChapter'
        ];

        // Mapping of internal names to user-friendly labels
        var questionLabels = {
            'ByLawsAvailable': 'Were By-Laws made available to members?',
            'VoteAllActivities': 'Did the chapter vote on all activites?',
            'ChildOutings': 'Did the chapter have child focused outings?',
            'Playgroups': 'Did the chapter have playgroups?',
            'ParkDays': 'Did the chapter have scheuled park days?',
            'MotherOutings': 'Did the chapter have mother focused outings?',
            'Activity[]': 'Did the chapter have any actifity groups?',
            'OfferedMerch': 'Was MOMS Club merchandise offered to members?',
            'BoughtMerch': 'Did the chapter purchase MOMS Club merchandise?',
            'BoughtPins': 'Did the chapter purchase MOMS Club pins?',
            'ReceiveCompensation': 'Member compensation received for work with chapter?',
            'FinancialBenefit': 'Member benefit financially from position in chapter?',
            'InfluencePolitical': 'Infuence or support political legislation or org?',
            'SisterChapter': 'Did the chapter Sister a New Chapter?',
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
