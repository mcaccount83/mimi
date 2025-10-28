<script>
//Cusotmize AJAX Popups to Match Theme
    function customSuccessAlert(message) {
            Swal.fire({
                title: 'Success!',
                html: message,
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-success', // Match your theme button class
                },
                buttonsStyling: false // Disable default button styling
            });
        }

    function customWarningAlert(message) {
        Swal.fire({
            title: 'Oops!',
            html: message,
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customInfoAlert(message) {
        Swal.fire({
            title: 'Did You Know?',
            html: message,
            icon: 'info',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customErrorAlert(message) {
        Swal.fire({
            title: 'Oops!',
            html: message,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }
</script>
