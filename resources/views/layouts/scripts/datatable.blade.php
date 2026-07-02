<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global default - runs for EVERY table on EVERY page
$.fn.dataTable.defaults.initComplete = function() {
    var container = $(this.api().table().container());
    container.find('.row').first().after(
        '<div class="dt-sort-tip"><i class="bi bi-info-circle me-1"></i>Hold <kbd>Shift</kbd> while clicking a column header to sort by multiple columns</div>'
    );
};

    var chapterTable = $('#chapterlist').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "orderMulti": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [],
        "layout": {
            "topStart": "pageLength",
            "topEnd": "search",
            "bottomStart": "info",
            "bottomEnd": "paging"
        }
    });

    var coordinatorTable = $('#coordinatorlist').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "orderMulti": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [],
        "layout": {
            "topStart": "pageLength",
            "topEnd": "search",
            "bottomStart": "info",
            "bottomEnd": "paging"
        }
    });
});
</script>
