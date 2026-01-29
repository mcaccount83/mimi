<script>
    $(function () {
        $('#chapterlist').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [] // Use your Laravel query order
        });

        $('#coordinatorlist').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [] // Use your Laravel query order
        });
    });

    $(document).ready(function() {
        var table = $('#coordinatorlist').DataTable();

        applyDateMask();

        table.on('draw', function() {
            applyDateMask();
        });
    });

    $(document).ready(function() {
        var table = $('#chapterlist').DataTable();

        applyPhoneMask();

        table.on('draw', function() {
            applyPhoneMask();
        });

        applyDateMask();

        table.on('draw', function() {
            applyDateMask();
        });

        applyHttpMask();

        table.on('draw', function() {
            applyHttpMask();
        });

    });
</script>
