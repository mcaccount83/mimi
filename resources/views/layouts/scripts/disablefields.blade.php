<script>
    var $chActiveId = @json($chActiveId ?? null);
    var $cdActiveId = @json($cdActiveId ?? null);

    // Use chapter active status for chapter pages, coordinator active status for coordinator pages
    var activeId = $chActiveId ?? $cdActiveId;

    var $coordinatorCondition = @json($coordinatorCondition ?? false);
    var $chActiveId = @json($chActiveId ?? null);
    var $coordinatorCondition = @json($coordinatorCondition ?? false);
    var $chPcId = @json($chPcId ?? null);
    var $coorId = @json($coorId ?? null);
    var $chConfId = @json($chConfId ?? null);
    var $confId = @json($confId ?? null);
    var $cdActiveId = @json($cdActiveId ?? null);
    var $supervisingCoordinatorCondition = @json($supervisingCoordinatorCondition ?? false);
    var $cdConfId = @json($cdConfId ?? null);

    var hasCoordinatorAccess = $coordinatorCondition && ($confId == $chConfId);
    var hasSupervisingCoordinatorAccess = $supervisingCoordinatorCondition && ($confId == $cdConfId);

    var $webReviewCondition = @json($webReviewCondition ?? false);
    var hasWebReviewCondition = $webReviewCondition && ($confId == $chConfId);
    var $ITCondition = @json($ITCondition ?? false);
    var hasITAccess = $ITCondition;

    // Variables & Conditions for specific pages
    var $eoyTestCondition = @json($eoyTestCondition ?? false);
    var $eoyReportCondition = @json($eoyReportCondition ?? false);
    var hasEoyTestCondition = $eoyTestCondition && ($confId == $chConfId);
    var hasEoyReportCondition = $eoyReportCondition && ($confId == $chConfId);

    // Check which conditions to include (passed from parent view)
    var includeEoyConditions = @json($includeEoyConditions ?? false);
    var includeWebReviewCondition = @json($includeWebReviewCondition ?? false);

    // Build shouldEnable based on included conditions
    var shouldEnable = (activeId != 0) && (
        hasCoordinatorAccess ||
        hasSupervisingCoordinatorAccess ||
        hasITAccess ||
        (includeWebReviewCondition && hasWebReviewCondition) ||
        (includeEoyConditions && hasEoyTestCondition) ||
        (includeEoyConditions && hasEoyReportCondition)
    );

    $(document).ready(function () {
        if (!shouldEnable) {
            $('input, select, textarea, button').prop('disabled', true);

            $('a[href^="mailto:"]').each(function() {
                $(this).addClass('disabled-link');
                $(this).attr('href', 'javascript:void(0);');
                $(this).on('click', function(e) {
                    e.preventDefault();
                });
            });

            // Re-enable buttons with 'keep-enabled' class
            $('.keep-enabled').prop('disabled', false);
        }
    });
</script>
