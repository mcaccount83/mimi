<script>
    function customSuccessAlert(message) {
        Swal.fire({
            title: 'Success!',
            html: message,
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
            },
            buttonsStyling: false
        });
    }

    function customWarningAlert(message) {
        return Swal.fire({
            title: 'Oops!',
            html: message,
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
            },
            buttonsStyling: false
        });
    }

    function customInfoAlert(message) {
        Swal.fire({
            title: 'Did You Know?',
            html: message,
            icon: 'info',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
            },
            buttonsStyling: false
        });
    }

    function customErrorAlert(message) {
        Swal.fire({
            title: 'Oops!',
            html: message,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-sm btn-success',
            },
            buttonsStyling: false
        });
    }
</script>
