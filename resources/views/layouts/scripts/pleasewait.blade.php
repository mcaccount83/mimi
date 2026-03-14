<script>
    (function () {
        const THRESHOLD_MS = 1500;
        let overlayTimer = null;

        function showOverlay() {
            if (typeof Swal === 'undefined') return;
            Swal.fire({
                title: 'Please Wait',
                text: 'Page is taking a moment to load...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Start timer when user clicks a link or submits a form
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (!link) return;
            if (link.target === '_blank') return;
            if (link.getAttribute('href') === '#') return;
            if (link.getAttribute('href')?.startsWith('javascript')) return;
            if (link.getAttribute('href')?.startsWith('mailto:')) return;
            if (link.getAttribute('href')?.startsWith('tel:')) return;
            if (link.dataset.noWait !== undefined) return;
            if (link.dataset.bsToggle === 'tab') return;
            if (link.dataset.bsToggle === 'pill') return;
            if (link.dataset.bsToggle === 'collapse') return;

            overlayTimer = setTimeout(showOverlay, THRESHOLD_MS);
        });

        document.addEventListener('submit', function (e) {
            overlayTimer = setTimeout(showOverlay, THRESHOLD_MS);
        });

        // Close if browser back/forward cache restores the page
        window.addEventListener('pageshow', function () {
            if (overlayTimer) clearTimeout(overlayTimer);
            if (typeof Swal !== 'undefined') Swal.close();
        });
    })();
</script>
