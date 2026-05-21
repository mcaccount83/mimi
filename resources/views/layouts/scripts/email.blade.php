<script>
function validateEmailFormat(selectors) {
    const emails = selectors
        .filter(selector => $(selector).length > 0)
        .filter(selector => $(selector).is(':visible'))
        .map(selector => ({ selector, value: $(selector).val().trim() }))
        .filter(({ value }) => value !== '');

    const invalidEmails = [];
    emails.forEach(({ value }) => {
        if (!isValidEmail(value)) invalidEmails.push(value);
    });

    if (invalidEmails.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email Format',
            html: 'The following emails are not valid: <br>' + invalidEmails.join('<br>'),
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-sm btn-success' }
        });
        return false;
    }
    return true;
}

function validateEmailsBeforeSubmit() {
    const selectors = [
        '#ch_pre_email', '#ch_avp_email', '#ch_mvp_email',
        '#ch_trs_email', '#ch_sec_email', '#ch_bor_email',
        '#cord_email', '#cord_sec_email', '#email'
    ];

    if (!validateEmailFormat(selectors)) return false;

    // Duplicate check
    const emails = selectors
        .filter(selector => $(selector).length > 0)
        .filter(selector => $(selector).is(':visible'))
        .map(selector => ({ selector, value: $(selector).val().trim() }))
        .filter(({ value }) => value !== '');

    const emailSet = new Set();
    const duplicateEmails = [];
    emails.forEach(({ value }) => {
        if (emailSet.has(value)) {
            if (!duplicateEmails.includes(value)) duplicateEmails.push(value);
        } else {
            emailSet.add(value);
        }
    });

    if (duplicateEmails.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Duplicate Emails Found',
            html: 'The following emails are duplicates: <br>' + duplicateEmails.join('<br>'),
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-sm btn-success' }
        });
        return false;
    }
    return true;
}
</script>
