<script>
    function showEODeptCoverSheetModal() {
    Swal.fire({
        title: 'IRS EO Department Fax',
        html: `
            <p>This will generate the Fax Coversheet for the IRS EO Department. Enter the total number of pages (including the coversheet) to be faxed as well as
                a brief message describing the contents of the fax.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            <div style="display: flex; align-items: center;">
                <textarea id="email_message" name="email_message" class="swal2-textarea" placeholder="Enter Message" required style="width: 100%; height: 150px;"></textarea>
            </div>
            `,
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const totalPages = Swal.getPopup().querySelector('#total_pages').value;
            const emailMessage = Swal.getPopup().querySelector('#email_message').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            if (!emailMessage || emailMessage.trim() == '') {
                Swal.showValidationMessage('Please enter a message');
                return false;
            }

            return {
                total_pages: totalPages,
                email_message: emailMessage,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.eodeptfaxcover') }}?pages=${data.total_pages}&message=${encodeURIComponent(data.email_message)}&title=${encodeURIComponent('IRS EO Department Fax')}`;
            window.open(url, '_blank');
        }
    });
}

function showIRSUpdatesModal() {
    Swal.fire({
        title: 'IRS Updates to EO Dept',
        html: `
            <p>This will generate the Fax Coversheet for the IRS EO Department. Enter the total number of pages (including the coversheet) to be faxed as well as
                a brief message describing the contents of the fax.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            <div style="display: flex; align-items: center;">
                <input type="date" id="from_date" name="from_date" class="swal2-input" required style="width: 100%;">
            </div>
            `,
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const totalPages = Swal.getPopup().querySelector('#total_pages').value;
            const fromDate = Swal.getPopup().querySelector('#from_date').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            if (!fromDate || fromDate.trim() == '') {
                Swal.showValidationMessage('Please enter a start date for report');
                return false;
            }

            return {
                total_pages: totalPages,
                from_date: fromDate,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.combinedirsupdates') }}?pages=${data.total_pages}&date=${data.from_date}`;
            window.open(url, '_blank');
        }
    });
}

function showSubordinateFilingModal() {
    Swal.fire({
        title: 'IRS Updates to EO Dept',
        html: `
            <p>This will generate the Fax Coversheet for the IRS EO Department. Enter the total number of pages (including the coversheet) to be faxed as well as
                a brief message describing the contents of the fax.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            <div style="display: flex; align-items: center;">
                <input type="date" id="from_date" name="from_date" class="swal2-input" required style="width: 100%;">
            </div>
            `,
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const totalPages = Swal.getPopup().querySelector('#total_pages').value;
            const fromDate = Swal.getPopup().querySelector('#from_date').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            if (!fromDate || fromDate.trim() == '') {
                Swal.showValidationMessage('Please enter a start date for report');
                return false;
            }

            return {
                total_pages: totalPages,
                from_date: fromDate,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.combinedsubordinatefiling') }}?pages=${data.total_pages}&date=${data.from_date}`;
            window.open(url, '_blank');
        }
    });
}

function showIRSFilingCorrectionsModal() {
    Swal.fire({
        title: 'IRS 990N Filing Corrections to EO Dept',
        html: `
            <p>This will generate the Fax Coversheet & Letter for the IRS EO Department listing 990N Filing Corrections. Enter the total number of pages (including the coversheet) to be faxed.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            `,
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const totalPages = Swal.getPopup().querySelector('#total_pages').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            return {
                total_pages: totalPages,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.combinedirsfilingcorrections') }}?pages=${data.total_pages}`;
            window.open(url, '_blank');
        }
    });
}

function updateEIN(chapterId) {
    // Check if the chapter already has an EIN
    $.ajax({
        url: '{{ route('chapters.checkein') }}',
        type: 'GET',
        data: {
            chapter_id: chapterId
        },
        success: function(response) {
            if (response.ein) {
                // Show a warning if an EIN already exists
                Swal.fire({
                    title: 'Warning!',
                    text: 'This chapter already has an EIN. Do you want to replace it?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, replace it',
                    cancelButtonText: 'No',
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                        cancelButton: 'btn-sm btn-danger'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed to input the new EIN
                        promptForNewEIN(chapterId);
                    }
                });
            } else {
                // No existing EIN, proceed directly
                promptForNewEIN(chapterId);
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error!',
                text: 'Unable to check the existing EIN. Please try again later.',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                }
            });
        }
    });
}

function promptForNewEIN(chapterId) {
    Swal.fire({
        title: 'Enter EIN',
        html: `
            <p>Please enter the EIN for the chapter.</p>
            <div style="display: flex; align-items: center; ">
                <input type="text" id="ein" name="ein" class="swal2-input" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask placeholder="Enter EIN" required style="width: 100%;">
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            <br>
            <div class="custom-control custom-switch">
                <input type="checkbox" id="chapter_ein" class="custom-control-input">
                <label class="custom-control-label" for="chapter_ein">Send EIN Notification to Chapter</label>
            </div>
            <br>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const ein = Swal.getPopup().querySelector('#ein').value;
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const chapterEIN = Swal.getPopup().querySelector('#chapter_ein').checked;

            if (!ein) {
                Swal.showValidationMessage('Please enter the new EIN.');
                return false;
            }

            return {
                ein: ein,
                chapter_id: chapterId,
                chapter_ein: chapterEIN,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger'
                },
                didOpen: () => {
                    Swal.showLoading();

                    // Perform the AJAX request
                    $.ajax({
                        url: '{{ route('chapters.updateein') }}',
                        type: 'POST',
                        data: {
                            ein: data.ein,
                            notify: data.chapter_ein ? '1' : '0',
                            chapterid: data.chapter_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                showConfirmButton: false,  // Automatically close without "OK" button
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, Please try again.',
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

function showFileUploadModal(chapter_id) {

    Swal.fire({
        title: 'Upload EIN Letter',
        html: `
            <form id="uploadEINForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadEINForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeEIN', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'File uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

</script>
