<script>
    (function () {
        const THRESHOLD_MS = 1500;
        let swalShown = false;
        let loadComplete = false;

        const timer = setTimeout(function () {
            if (!loadComplete) {
                swalShown = true;
                Swal.fire({
                    title: 'Please Wait',
                    text: 'Loading page, this may take a moment...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        }, THRESHOLD_MS);

        window.addEventListener('load', function () {
            loadComplete = true;
            clearTimeout(timer);
            if (swalShown) {
                Swal.close();
            }
        });
    })();
</script>
