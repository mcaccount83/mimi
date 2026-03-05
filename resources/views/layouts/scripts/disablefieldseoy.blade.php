<script>
 document.addEventListener('DOMContentLoaded', function () {
    var submitted = @json($chEOYDocuments->financial_review_complete);
    var received = @json($chEOYDocuments->financial_report_received);
    var ITCondition = @json($ITCondition ?? false);
    var eoyTestCondition = @json($eoyTestCondition ?? false);
    var includeEoyConditions = @json($includeEoyConditions ?? false);
    var chConfId = @json($chConfId ?? null);
    var confId = @json($confId ?? null);
    var chActiveId = @json($chActiveId ?? null);
    var cdActiveId = @json($cdActiveId ?? null);
    var activeId = chActiveId ?? cdActiveId;

    var hasITAccess = ITCondition;
    var hasEoyTestCondition = eoyTestCondition && (confId == chConfId);
    var shouldEnable = (activeId != 0) && (
        hasITAccess || (includeEoyConditions && hasEoyTestCondition)
    );

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

    if (received != '1' && !shouldEnable) {
        disableButtons();
        disableFields();
    } else if (submitted == '1' && !shouldEnable) {
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
