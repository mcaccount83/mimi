<script>
 document.addEventListener('DOMContentLoaded', function () {
    var submitted = @json($chEOYDocuments->financial_review_complete);
    var received = @json($chEOYDocuments->financial_report_received);

    function disableFields() {
        document.querySelectorAll('input, select, textarea').forEach(function(el) {
            if (!el.closest('#logout-form')) {
                el.disabled = true;
            }
        });
    }

    function disableButtons(exceptions = []) {
        document.querySelectorAll('button').forEach(function(el) {
            if (!el.closest('#logout-form') &&
                !exceptions.includes(el.id) &&
                !el.classList.contains('accordion-button') &&
                !el.hasAttribute('data-bs-toggle') &&
                !el.hasAttribute('data-lte-toggle')) {
                el.disabled = true;
            }
        });

         $('.keep-enabled').prop('disabled', false);
    }

    if (received != '1') {
        disableButtons();
        disableFields();
    } else if (submitted == '1') {
        disableButtons(['review-clear', 'financial-pdf', 'generate-pdf']);
        disableFields();
    }

    var allDisabled = true;
    document.querySelectorAll('input, select, textarea').forEach(function(el) {
        if (!el.closest('#logout-form') && !el.disabled) {
            allDisabled = false;
        }
    });

    document.querySelectorAll('.description').forEach(el => el.style.display = allDisabled ? 'block' : 'none');
});
</script>
