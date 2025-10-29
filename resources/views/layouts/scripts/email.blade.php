<script>
    function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    alert('This Email already used in the system. Please try with new one.');
                    $("#" + id).val('');
                    $("#" + id).focus();
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
    }

    function validateEmailsBeforeSubmit() {
    // Get the values from the input fields
    const emails = [
        $('#ch_pre_email').val().trim(),
    ];

    // Filter out empty emails and check for duplicates
    const emailSet = new Set();
    const duplicateEmails = [];

    emails.forEach(email => {
        if (email != '') {
            if (emailSet.has(email)) {
                // Check if the duplicate email is already in the array to avoid listing it multiple times
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
                confirmButton: 'btn-sm btn-success'
            }
        });
        return false;
    }
    return true;
}
</script>
