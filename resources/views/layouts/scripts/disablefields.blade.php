<script>
    var chActiveId = @json($chActiveId ?? null);
    var cdActiveId = @json($cdActiveId ?? null);
    var activeId = chActiveId ?? cdActiveId;
    var coordinatorCondition = @json($coordinatorCondition ?? false);
    var chPcId = @json($chPcId ?? null);
    var coorId = @json($coorId ?? null);
    var chConfId = @json($chConfId ?? null);
    var confId = @json($confId ?? null);
    var supervisingCoordinatorCondition = @json($supervisingCoordinatorCondition ?? false);
    var cdConfId = @json($cdConfId ?? null);
    var webReviewCondition = @json($webReviewCondition ?? false);
    var ITCondition = @json($ITCondition ?? false);
    var eoyTestCondition = @json($eoyTestCondition ?? false);
    var eoyReportCondition = @json($eoyReportCondition ?? false);
    var includeEoyConditions = @json($includeEoyConditions ?? false);
    var includeWebReviewCondition = @json($includeWebReviewCondition ?? false);

    var hasCoordinatorAccess = coordinatorCondition && (confId == chConfId);
    var hasSupervisingCoordinatorAccess = supervisingCoordinatorCondition && (confId == cdConfId);
    var hasWebReviewCondition = webReviewCondition && (confId == chConfId);
    var hasITAccess = ITCondition;
    var hasEoyTestCondition = eoyTestCondition && (confId == chConfId);
    var hasEoyReportCondition = eoyReportCondition && (confId == chConfId);

    var shouldEnable = (activeId != 0) && (
        hasCoordinatorAccess ||
        hasSupervisingCoordinatorAccess ||
        hasITAccess ||
        (includeWebReviewCondition && hasWebReviewCondition) ||
        (includeEoyConditions && hasEoyTestCondition) ||
        (includeEoyConditions && hasEoyReportCondition)
    );

    document.addEventListener('DOMContentLoaded', function () {
        if (!shouldEnable) {
            $('input, select, textarea, button').prop('disabled', true);

            $('a[href^="mailto:"]').each(function() {
                $(this).addClass('disabled-link');
                $(this).attr('href', 'javascript:void(0);');
                $(this).on('click', function(e) {
                    e.preventDefault();
                });
            });

            $('.keep-enabled').prop('disabled', false);
        }
    });
</script>
