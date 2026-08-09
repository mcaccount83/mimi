<script>
    function previewCampaign(url, label) {
        document.getElementById('emailPreviewLabel').innerText = label + ' — Preview';
        const frame = document.getElementById('emailPreviewFrame');
        frame.src = '';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                frame.srcdoc = html;
                new bootstrap.Modal(document.getElementById('emailPreviewModal')).show();
            })
            .catch(() => {
                Swal.fire('Error', 'Could not load email preview.', 'error');
            });
    }
</script>
