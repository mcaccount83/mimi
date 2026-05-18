document.addEventListener('DOMContentLoaded', function() {
    const flashSuccess = document.querySelector('meta[name="flash-success"]')?.content;
    const flashInfo = document.querySelector('meta[name="flash-info"]')?.content;
    const flashWarning = document.querySelector('meta[name="flash-warning"]')?.content;
    const flashFail = document.querySelector('meta[name="flash-fail"]')?.content;
    const flashErrors = document.querySelector('meta[name="flash-errors"]')?.content;

    if (flashSuccess) {
        Swal.fire({ position: 'top-end', icon: 'success', title: flashSuccess, showConfirmButton: false, timer: 2500, timerProgressBar: true });
    }
    if (flashInfo) {
        Swal.fire({ position: 'top-end', icon: 'info', title: flashInfo, showConfirmButton: false, timer: 2500, timerProgressBar: true });
    }
    if (flashWarning) {
        Swal.fire({ position: 'top-end', icon: 'warning', title: flashWarning, showConfirmButton: false, timer: 2500, timerProgressBar: true });
    }
    if (flashFail) {
        Swal.fire({ position: 'top-end', icon: 'error', title: flashFail, showConfirmButton: false, timer: 2500, timerProgressBar: true });
    }
    if (flashErrors) {
        Swal.fire({ position: 'top-end', icon: 'error', title: 'There were some errors!', html: flashErrors, showConfirmButton: true });
    }
});
