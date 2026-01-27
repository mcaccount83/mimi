<script>
    document.querySelectorAll('.reset-password-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const userId = this.getAttribute('data-user-id');
        const newPassword = "TempPass4You";

        // Get fresh CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        console.log('CSRF Token:', csrfToken); // Check if token exists
        console.log('User ID:', userId);

        $.ajax({
            url: '{{ route('updatepassword') }}',
            type: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken  // Send in header instead
            },
            data: {
                user_id: userId,
                new_password: newPassword
            },
            success: function(result) {
                Swal.fire({
                    title: 'Success!',
                    text: result.message.replace('<br>', '\n'),
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    }
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("Status:", jqXHR.status);
                console.error("Response:", jqXHR.responseText);
                console.error("Error:", errorThrown);

                Swal.fire({
                    title: 'Error!',
                    text: 'Unable to reset password. Error: ' + jqXHR.status,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    }
                });
            }
        });
    });
});

    function showChangePasswordAlert(user_id) {
        Swal.fire({
            title: 'Change Password',
            html: `
                <form id="changePasswordForm">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="swal2-input" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="swal2-input" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="swal2-input" required>
                    </div>
                <input type="hidden" id="user_id" name="user_id" value="${user_id}">
                </form>
            `,
            confirmButtonText: 'Update Password',
            cancelButtonText: 'Cancel',
            showCancelButton: true,
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const user_id = Swal.getPopup().querySelector('#user_id').value;
                const currentPassword = Swal.getPopup().querySelector('#current_password').value;
                const newPassword = Swal.getPopup().querySelector('#new_password').value;
                const confirmNewPassword = Swal.getPopup().querySelector('#new_password_confirmation').value;

                // Validate input fields
                if (!currentPassword || !newPassword || !confirmNewPassword) {
                    Swal.showValidationMessage('Please fill out all fields');
                    return false;
                }

                if (newPassword != confirmNewPassword) {
                    Swal.showValidationMessage('New passwords do not match');
                    return false;
                }

                // Return the AJAX call as a promise to let Swal wait for it
                return $.ajax({
                    url: '{{ route("checkpassword") }}',  // Check current password route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        current_password: currentPassword
                    }
                }).then(response => {
                    if (!response.isValid) {
                        Swal.showValidationMessage('Current password is incorrect');
                        return false;
                    }
                    return {
                        user_id: user_id,
                        current_password: currentPassword,
                        new_password: newPassword,
                        new_password_confirmation: confirmNewPassword
                    };
                }).catch(() => {
                    Swal.showValidationMessage('Error verifying current password');
                    return false;
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    customClass: {
                        confirmButton: 'btn-sm btn-success'
                    },
                    didOpen: () => Swal.showLoading()
                });

                // Send the form data via AJAX to update the password
                $.ajax({
                    url: '{{ route("updatepassword") }}',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: result.value.user_id,
                        current_password: result.value.current_password,
                        new_password: result.value.new_password,
                        new_password_confirmation: result.value.new_password_confirmation
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your password has been updated.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn-sm btn-success'
                            }
                        });
                    },
                    error: function(jqXHR) {
                        Swal.fire({
                            title: 'Error!',
                            text: `Something went wrong: ${jqXHR.responseText}`,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn-sm btn-danger'
                            }
                        });
                    }
                });
            }
        });
    }
</script>
