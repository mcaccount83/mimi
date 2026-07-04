<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.bulk-action-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.dataset.action;
            const label = this.dataset.label;
            const isRemove = this.classList.contains('btn-danger');

            Swal.fire({
                title: 'Are you sure?',
                text: label,
                icon: isRemove ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: isRemove ? 'Yes, remove them' : 'Yes, add them',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: isRemove ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-success',
                    cancelButton: 'btn btn-sm btn-secondary'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: action,
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        customClass: { confirmButton: 'btn btn-sm btn-success' }
                                    }).then(() => location.reload());
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: xhr.responseJSON?.message ?? 'Something went wrong. Please try again.',
                                        icon: 'error',
                                        confirmButtonText: 'OK',
                                        customClass: { confirmButton: 'btn btn-sm btn-success' }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        });
    });
});
</script>
