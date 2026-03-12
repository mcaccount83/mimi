<script>
    // function checkDuplicateEmail(email, id) {
    //     $.ajax({
    //         url: '{{ url("/checkemail/") }}' + '/' + email,
    //         type: "GET",
    //         success: function(result) {
    //             if (result.exists) {
    //                 alert('This Email already used in the system. Please try with new one.');
    //                 $("#" + id).val('');
    //                 $("#" + id).focus();
    //             }
    //         },
    //         error: function(jqXHR, exception) {
    //             console.error("Error checking email: ", exception);
    //         }
    //     });
    // }

    function validateEmailsBeforeSubmit() {
    // Collect only email fields that exist on this page
    const emailSelectors = [
        '#ch_pre_email',
        '#ch_avp_email',
        '#ch_mvp_email',
        '#ch_trs_email',
        '#ch_sec_email',
        '#ch_bor_email',
        '#cord_email',
        '#email'
    ];

    const emails = emailSelectors
        .filter(selector => $(selector).length > 0)
        .filter(selector => $(selector).is(':visible'))  // ← skip hidden/vacant fields
        .map(selector => ({ selector, value: $(selector).val().trim() }))
        .filter(({ value }) => value !== '');

    // Check for duplicates
    const emailSet = new Set();
    const duplicateEmails = [];

    emails.forEach(({ value }) => {
        if (emailSet.has(value)) {
            if (!duplicateEmails.includes(value)) {
                duplicateEmails.push(value);
            }
        } else {
            emailSet.add(value);
        }
    });

    if (duplicateEmails.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Emails Found',
            html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>') + '<br>Please correct them before submitting.',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-sm btn-success'
            }
        });
        return false;
    }
    return true;
}
</script>
