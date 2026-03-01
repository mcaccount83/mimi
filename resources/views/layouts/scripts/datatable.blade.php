<script>
document.addEventListener('DOMContentLoaded', function() {
    var chapterTable = $('#chapterlist').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
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

    applyDateMask();
    applyPhoneMask();
    applyHttpMask();

    chapterTable.on('draw', function() {
        applyDateMask();
        applyPhoneMask();
        applyHttpMask();
    });

    coordinatorTable.on('draw', function() {
        applyDateMask();
    });
});
</script>
