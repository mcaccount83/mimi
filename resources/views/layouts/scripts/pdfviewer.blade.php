<script>
    function openPdfViewer(filePath) {
        var base_url = '{{ url("/pdf-viewer") }}';
        window.open(base_url + '?id=' + encodeURIComponent(filePath), '_blank');
    }
</script>
