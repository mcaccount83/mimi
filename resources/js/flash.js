document.addEventListener('DOMContentLoaded', function() {
    // These values get passed from Blade via a meta tag we'll add to the layout
    const flashSuccess = document.querySelector('meta[name="flash-success"]')?.content;
    const flashInfo = document.querySelector('meta[name="flash-info"]')?.content;
    const flashWarning = document.querySelector('meta[name="flash-warning"]')?.content;
    const flashFail = document.querySelector('meta[name="flash-fail"]')?.content;
    const flashErrors = document.querySelector('meta[name="flash-errors"]')?.content;

    if (flashSuccess) {
        Swal.fire({ position: 'top-end', icon: 'success', title: flashSuccess, showConfirmButton: false, timer: 1500 });
    }
    if (flashInfo) {
        Swal.fire({ position: 'top-end', icon: 'info', title: flashInfo, showConfirmButton: false, timer: 1500 });
    }
    if (flashWarning) {
        Swal.fire({ position: 'top-end', icon: 'warning', title: flashWarning, showConfirmButton: false, timer: 1500 });
    }
    if (flashFail) {
        Swal.fire({ position: 'top-end', icon: 'error', title: flashFail, showConfirmButton: false, timer: 1500 });
    }
    if (flashErrors) {
        Swal.fire({ position: 'top-end', icon: 'error', title: 'There were some errors!', html: flashErrors, showConfirmButton: true });
    }
});
