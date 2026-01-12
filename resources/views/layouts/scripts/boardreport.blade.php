<script>
    //Boundary Visibility
    if (document.querySelector('input[name="BoundaryStatus"]')) {
        ShowBoundaryError();
    }

    function ShowBoundaryError() {
        var selectedRadio = document.querySelector('input[name="BoundaryStatus"]:checked');

        // Exit if no radio buttons exist or none are checked
        if (!selectedRadio) {
            return;
        }

        var selectedValue = selectedRadio.value;

        if (selectedValue == "1") {
            $('#BoundaryIssue').addClass('tx-cls');
            document.getElementById("divBoundaryIssue").style.display = 'block';
        } else {
            $('#BoundaryIssue').removeClass('tx-cls');
            document.getElementById("divBoundaryIssue").style.display = 'none';
        }
    }

    function validateBeforeSubmit() {
        // Check if a boundary status is selected
        const selectedBoundary = document.querySelector('input[name="BoundaryStatus"]:checked');

        if (!selectedBoundary) {
            Swal.fire({
                icon: 'error',
                title: 'Boundary Issue Required',
                text: 'Please indicate whether your listed boundaries are correct.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-danger'
                }
            });
            return false;
        }

        // If "No" (value 1) is selected, check if the issue field is filled out
        if (selectedBoundary.value == "1") {
            const boundaryIssue = document.getElementById("BoundaryIssue");
            if (!boundaryIssue || !boundaryIssue.value.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Boundary Issue Required',
                    text: 'Please indicate which part of the Boundaries do NOT match your records.',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-danger'
                    }
                });
                if (boundaryIssue) boundaryIssue.focus();
                return false;
            }
        }

        // Get the values from the input fields
        const emails = [
            $('#ch_pre_email').val()?.trim() || '',
            $('#ch_avp_email').val()?.trim() || '',
            $('#ch_mvp_email').val()?.trim() || '',
            $('#ch_trs_email').val()?.trim() || '',
            $('#ch_sec_email').val()?.trim() || ''
        ];

        // Filter out empty emails and check for duplicates
        const emailSet = new Set();
        const duplicateEmails = [];

        emails.forEach(email => {
            if (email != '') {
                if (emailSet.has(email)) {
                    if (!duplicateEmails.includes(email)) {
                        duplicateEmails.push(email);
                    }
                } else {
                    emailSet.add(email);
                }
            }
        });

        // If duplicates are found, show an alert
        if (duplicateEmails.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Emails Found',
                html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>') + '<br>Please correct them before submitting.',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-danger'
                }
            });
            return false;
        }

        return true;
    }
</script>
