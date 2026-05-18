<script>
window.pleaseWait = (function () {
    const THRESHOLD_MS = 1500;
    let overlayTimer = null;
    let overlayActive = false;

    function showOverlay() {
        if (typeof Swal === 'undefined') return;
        overlayActive = true;
        Swal.fire({
            title: 'Please Wait',
            text: 'Page is taking a moment to load...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }

    document.addEventListener('click', function (e) {
        if (window._suppressPleaseWait) return;
        const link = e.target.closest('a');
        if (!link) return;
        if (!link.getAttribute('href')) return;
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

    window.addEventListener('pageshow', function () {
        if (overlayTimer) clearTimeout(overlayTimer);
        if (typeof Swal !== 'undefined' && overlayActive) Swal.close();
        overlayActive = false;
        overlayTimer = null;
    });

    // Exposed navigate function for window.location.href buttons
    window.navigateTo = function(url) {
        if (window._suppressPleaseWait) {
            window.location.href = url;
            return;
        }
        overlayTimer = setTimeout(showOverlay, THRESHOLD_MS);
        window.location.href = url;
    };

    return {
        cancel: function () {
            if (overlayTimer) clearTimeout(overlayTimer);
            overlayTimer = null;
            overlayActive = false;
            if (typeof Swal !== 'undefined') Swal.close();
        }
    };
})();
</script>
