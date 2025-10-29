<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('ch_status');
        const probationLabel = document.getElementById('probationLabel');
        const probationField = document.getElementById('probationField');
        const probationSelect = document.getElementById('ch_probation');

        // Function to toggle probation section visibility
        function toggleProbationSection() {
            const selectedStatusId = parseInt(statusSelect.value);
            if (selectedStatusId >= 5 && selectedStatusId != '') {
                probationLabel.style.display = '';
                probationField.style.display = '';
                probationSelect.setAttribute('required', 'required');
            } else {
                probationLabel.style.display = 'none';
                probationField.style.display = 'none';
                probationSelect.removeAttribute('required');
            }
        }

        // Initial toggle based on current value
        toggleProbationSection();

        // Add event listener for future changes
        statusSelect.addEventListener('change', toggleProbationSection);
    });

    function showProbationLetterModal(chapterName, chapterId) {
        Swal.fire({
            title: 'Generate Probation Letter',
            html: `
                <p>This will generate a Probation/Warning letter to be emailed to the full board and all coordinators for <b>${chapterName}</b>.</p>
                <p>Select the type of letter to generate and send:</p>
                <select id="letterType" class="form-control">
                    <option value="no_report">Probation Letter - No EOY Reports</option>
                    <option value="no_payment">Probation Letter - No Re-Reg Payment</option>
                    <option value="probation_party">Probation Letter - Excess Party Expenses</option>
                    <option value="warning_party">Warning Letter - Excess Party Expenses</option>
                </select>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            `,
            showCancelButton: true,
            confirmButtonText: 'Generate Letter',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const letterType = Swal.getPopup().querySelector('#letterType').value;
                return { chapterId, letterType };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we generate your letter.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        $.ajax({
                            url: '{{ route('pdf.generateProbationLetter') }}',
                            type: 'POST',
                            data: {
                                chapterId: data.chapterId,
                                letterType: data.letterType,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                }).then(() => {
                                    location.reload(); // Reload the page to reflect changes
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    }

    function showProbationReleaseModal(chapterName, chapterId) {
        Swal.fire({
            title: 'Generate Probation Letter',
            html: `
                <p>This will generate a Probation Release letter to be emailed to the full board and all coordinators for <b>${chapterName}</b>.</p>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            `,
            showCancelButton: true,
            confirmButtonText: 'Generate Letter',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const letterType = "probation_release";

                return { chapterId, letterType };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we generate your letter.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        $.ajax({
                            url: '{{ route('pdf.generateProbationLetter') }}',
                            type: 'POST',
                            data: {
                                chapterId: data.chapterId,
                                letterType: data.letterType,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                }).then(() => {
                                    location.reload(); // Reload the page to reflect changes
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    }
</script>
