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
        "order": []
    });
    var coordinatorTable = $('#coordinatorlist').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": []
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
