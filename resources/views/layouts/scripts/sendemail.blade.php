<script>
    function confirmSendEOYRptReminder() {
        return confirm('This action will send a Late Notice to all chapters who have not submitted their Board Election Report OR their Financial Report, excluding those with an extension or an assigned reviewer. \n\nAre you sure you want to send the EOY Late Notices?');
    }

    function confirmSendBoardRptReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Board Election Report. \n\nAre you sure you want to send the Board Elecion Report Reminders?');
    }

    function confirmSendFinancialRptReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Financial Report, excluding those with an extension or wtih an assigned reviewer. \n\nAre you sure you want to send the Financial Report Reminders?');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.email-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const messageId = this.dataset.messageId;
                const fullMessage = document.getElementById(messageId).value;

                showChapterEmailModal(
                    this.dataset.chapterName,
                    this.dataset.chapterId,
                    this.dataset.userName,
                    this.dataset.userPosition,
                    this.dataset.userConfName,
                    this.dataset.userConfDesc,
                    this.dataset.predefinedSubject,
                    fullMessage
                );
            });
        });
    });

    function showChapterEmailModal(chapterName, chapterId, userName, userPosition, userConfName, userConfDesc, predefinedSubject = '', predefinedMessage = '') {
        Swal.fire({
            title: 'Chapter Email Message',
            html: `
                <p>This will send your message to the full board and full coordinator list for <b>${chapterName}</b>.<br>
                <small>To send a message through your email client go to: Chapter Details->Documents->Blank Email to Board.</small></p>
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <input type="text" id="email_subject" name="email_subject" class="swal2-input" placeholder="Enter Subject" required style="width: 100%; margin: 0 !important;" value="${predefinedSubject}">
                </div>
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><br><b>MOMS Club of ${chapterName}:</b></p>
                </div>
                <div style="width: 100%; margin-bottom: 10px;">
                    <textarea id="email_message" name="email_message" class="rich-editor" ${predefinedMessage ? '' : 'placeholder="Email Message"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedMessage}</textarea>
                </div>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><b>MCL,</b><br>
                    ${userName}<br>
                    ${userPosition}<br>
                    ${userConfName}, ${userConfDesc}<br>
                    International MOMS Club</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger',
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#email_message').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            $(this).val(contents);
                        }
                    }
                });

                if (!document.getElementById('swal-wide-popup-style')) {
                    const style = document.createElement('style');
                    style.id = 'swal-wide-popup-style';
                    style.innerHTML = `
                        .swal-wide-popup {
                            width: 80% !important;
                            max-width: 800px !important;
                        }
                        .note-editor {
                            margin-bottom: 10px !important;
                            width: 100% !important;
                        }
                        .note-editable {
                            text-align: left !important;
                        }
                        .note-editing-area {
                            width: 100% !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            },
            preConfirm: () => {
                const subject = Swal.getPopup().querySelector('#email_subject').value;
                // Get the HTML content from Summernote
                const message = $('#email_message').summernote('code');
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                if (!subject) {
                    Swal.showValidationMessage('Please enter subject.');
                    return false;
                }
                if (!message) {
                    Swal.showValidationMessage('Please enter message.');
                    return false;
                }
                return {
                    email_subject: subject,
                    email_message: message,
                    chapter_id: chapterId,
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
                        $.ajax({
                            url: '{{ route('chapters.sendchapter') }}',
                            type: 'POST',
                            data: {
                                subject: data.email_subject,
                                message: data.email_message,
                                chapterId: data.chapter_id,
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

    function showChapterSetupModalBlank() {
    Swal.fire({
        title: 'Chapter Startup Details',
        html: `
            <p>This will send the initial chapter startup email to the potential founder to facilitate the discussion on boundaries and name. This will NOT add the new chapter to MIMI. Please enter the founder's information as well as the additional boundary and name details to include in the email and press OK to send.</p>
            <div class="name-fields-container" style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <input type="text" id="founder_first_name" name="founder_first_name" class="swal2-input" placeholder="Founder's First Name" required style="width: calc(50% - 3px); margin: 0 5px 0 0 !important; box-sizing: border-box;">
                <input type="text" id="founder_last_name" name="founder_last_name" class="swal2-input" placeholder="Founder's Last Name" required style="width: calc(50% - 3px); margin: 0 !important; box-sizing: border-box;">
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <input type="text" id="founder_email" name="founder_email" class="swal2-input" placeholder="Founder Email" required style="width: 100%; margin: 0 !important; box-sizing: border-box;">
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="boundary_details" name="boundary_details" class="swal2-textarea" placeholder="Boundary Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
            <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                <textarea id="name_details" name="name_details" class="swal2-textarea" placeholder="Name Details" required style="width: 100%; height: 80px; margin: 0 !important; box-sizing: border-box;"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const founderFirstName = Swal.getPopup().querySelector('#founder_first_name').value;
            const founderLastName = Swal.getPopup().querySelector('#founder_last_name').value;
            const founderEmail = Swal.getPopup().querySelector('#founder_email').value;
            const boundaryDetails = Swal.getPopup().querySelector('#boundary_details').value;
            const nameDetails = Swal.getPopup().querySelector('#name_details').value;

            if (!founderEmail) {
                Swal.showValidationMessage('Please enter the founders email address.');
                return false;
            }
            if (!founderFirstName) {
                Swal.showValidationMessage('Please enter the founders first name.');
                return false;
            }
            if (!founderLastName) {
                Swal.showValidationMessage('Please enter the founders last name.');
                return false;
            }
            if (!boundaryDetails) {
                Swal.showValidationMessage('Please enter the boundary details.');
                return false;
            }
            if (!nameDetails) {
                Swal.showValidationMessage('Please enter the chapter name details.');
                return false;
            }

            return {
                founder_email: founderEmail,
                founder_first_name: founderFirstName,
                founder_last_name: founderLastName,
                boundary_details: boundaryDetails,
                name_details: nameDetails,
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
                        url: '{{ route('chapters.sendstartup') }}',
                        type: 'POST',
                        data: {
                            founderEmail: data.founder_email,
                            founderFirstName: data.founder_first_name,
                            founderLastName: data.founder_last_name,
                            boundaryDetails: data.boundary_details,
                            nameDetails: data.name_details,
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

    function showChapterSetupEmailModal(chapterId, userName, userPosition, userConfName, userConfDesc, predefinedBoundaries = '', predefinedName = '') {
    Swal.fire({
        title: 'Chapter Startup Message',
        html: `
            <p>This will send the initial chapter startup email to potential founder to facilitate the discussion on boundaries and name. Please enter additional boundary and name details to include in the email and press OK to send.</p>
            <div style="width: 100%; margin-bottom: 10px;">
                <p><br><b>Boundary Details/Options</b></p>
                <textarea id="boundary_details" name="boundary_details" class="rich-editor" ${predefinedBoundaries ? '' : 'placeholder="Boundary Details/Options"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedBoundaries}</textarea>
            </div>
            <div style="width: 100%; margin-bottom: 10px;">
                <p><br><b>Name Details/Options</b></p>
                <textarea id="name_details" name="name_details" class="rich-editor" ${predefinedName ? '' : 'placeholder="Name Details/Options"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedName}</textarea>
            </div>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            <div style="width: 100%; margin-bottom: 10px; text-align: left;">
            <p><b>MCL,</b><br>
                ${userName}<br>
                ${userPosition}<br>
                ${userConfName}, ${userConfDesc}<br>
                International MOMS Club</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger',
            popup: 'swal-wide-popup'
        },
        didOpen: () => {
            $('#boundary_details').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        $(this).val(contents);
                    }
                }
            });

            $('#name_details').summernote({
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                callbacks: {
                    onChange: function(contents) {
                        $(this).val(contents);
                    }
                }
            });

            if (!document.getElementById('swal-wide-popup-style')) {
                const style = document.createElement('style');
                style.id = 'swal-wide-popup-style';
                style.innerHTML = `
                    .swal-wide-popup {
                        width: 80% !important;
                        max-width: 800px !important;
                    }
                    .note-editor {
                        margin-bottom: 10px !important;
                        width: 100% !important;
                    }
                    .note-editable {
                        text-align: left !important;
                    }
                    .note-editing-area {
                        width: 100% !important;
                    }
                `;
                document.head.appendChild(style);
            }
        },
        preConfirm: () => {
            const boundaryDetails = $('#boundary_details').summernote('code');
            const nameDetails = $('#name_details').summernote('code');
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            if (!boundaryDetails) {
                Swal.showValidationMessage('Please enter boundary details.');
                return false;
            }

            if (!nameDetails) {
                Swal.showValidationMessage('Please enter name details.');
                return false;
            }

            return {
                chapter_id: chapterId,
                boundary_details: boundaryDetails,
                name_details: nameDetails,
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

                        $.ajax({
                            url: '{{ route('chapters.sendstartup') }}',
                            type: 'POST',
                            data: {
                                chapterId: data.chapter_id,
                                boundaryDetails: data.boundary_details,
                                nameDetails: data.name_details,
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
                                    location.reload();
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

    function showNewChapterEmailModal(chapterId) {
    Swal.fire({
        title: 'New Chapter Email',
        html: `
            <p>This will automatically send the New Chapter Email to the full board and coordinator team. It will include their Letter of Good Standing and Group Exemption Letter.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Send',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
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
                            url: '{{ route('chapters.sendnewchapter') }}',
                            type: 'POST',
                            data: {
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

    function showChapterReRegEmailModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Re-Registration Reminder',
        html: `
            <p>This will send the regular re-registration reminder for <b>${chapterName}</b> to the full board and all coordinators.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
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
                            url: '{{ route('chapters.sendchapterrereg') }}',
                            type: 'POST',
                            data: {
                                chapterId: data.chapter_id,
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

    function showChapterReRegLateEmailModal(chapterName, chapterId) {
    Swal.fire({
        title: 'Chapter Re-Registration Late Notice',
        html: `
            <p>This will send the regular re-registration late notice for <b>${chapterName}</b> to the full board and all coordinators.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
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

                        $.ajax({
                            url: '{{ route('chapters.sendchapterrereglate') }}',
                            type: 'POST',
                            data: {
                                chapterId: data.chapter_id,
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

// Functions for Coordinator Email Modals
    function showCoordEmailModal(coordFName, coordLName, coordId, userName, userPosition, userConfName, userConfDesc, predefinedSubject = '', predefinedMessage = '') {
        Swal.fire({
            title: 'Coordinator Email Message',
            html: `
                <p>This will send your message to <b>${coordFName} ${coordLName}</b> and their full upline.<br>
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <input type="text" id="email_subject" name="email_subject" class="swal2-input" placeholder="Enter Subject" required style="width: 100%; margin: 0 !important;" value="${predefinedSubject}">
                </div>
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><br><b>${coordFName}:</b></p>
                </div>
                <div style="width: 100%; margin-bottom: 10px;">
                    <textarea id="email_message" name="email_message" class="rich-editor" ${predefinedMessage ? '' : 'placeholder="Email Message"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedMessage}</textarea>
                </div>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><b>MCL,</b><br>
                    ${userName}<br>
                    ${userPosition}<br>
                    ${userConfName}, ${userConfDesc}<br>
                    International MOMS Club</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger',
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#email_message').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            $(this).val(contents);
                        }
                    }
                });

                if (!document.getElementById('swal-wide-popup-style')) {
                    const style = document.createElement('style');
                    style.id = 'swal-wide-popup-style';
                    style.innerHTML = `
                        .swal-wide-popup {
                            width: 80% !important;
                            max-width: 800px !important;
                        }
                        .note-editor {
                            margin-bottom: 10px !important;
                            width: 100% !important;
                        }
                        .note-editable {
                            text-align: left !important;
                        }
                        .note-editing-area {
                            width: 100% !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            },
            preConfirm: () => {
                const subject = Swal.getPopup().querySelector('#email_subject').value;
                const message = $('#email_message').summernote('code');
                const coordId = Swal.getPopup().querySelector('#coord_id').value;
                if (!subject) {
                    Swal.showValidationMessage('Please enter subject.');
                    return false;
                }
                if (!message) {
                    Swal.showValidationMessage('Please enter message.');
                    return false;
                }
                return {
                    email_subject: subject,
                    email_message: message,
                    coord_id: coordId,
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

                        $.ajax({
                            url: '{{ route('coordinators.sendcoord') }}',
                            type: 'POST',
                            data: {
                                subject: data.email_subject,
                                message: data.email_message,
                                coordId: data.coord_id,
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

    function showCoordUplineEmailModal(userCoordId, userName, userPosition, userConfName, userConfDesc, predefinedSubject = '', predefinedMessage = '') {
        Swal.fire({
            title: 'Coordinator Email Message',
            html: `
                <p>This will send your message to all Coordinators reporting to <b>${userName}</b>.<br>
                    <small>This does not include any Coordinaors marked "On Leave".</small>
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <input type="text" id="email_subject" name="email_subject" class="swal2-input" placeholder="Enter Subject" required style="width: 100%; margin: 0 !important;" value="${predefinedSubject}">
                </div>
                <div style="width: 100%; margin-bottom: 10px;">
                    <textarea id="email_message" name="email_message" class="rich-editor" ${predefinedMessage ? '' : 'placeholder="Email Message"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedMessage}</textarea>
                </div>
                <input type="hidden" id="coord_id" name="coord_id" value="${userCoordId}">
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><b>MCL,</b><br>
                    ${userName}<br>
                    ${userPosition}<br>
                    ${userConfName}, ${userConfDesc}<br>
                    International MOMS Club</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger',
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#email_message').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            $(this).val(contents);
                        }
                    }
                });

                if (!document.getElementById('swal-wide-popup-style')) {
                    const style = document.createElement('style');
                    style.id = 'swal-wide-popup-style';
                    style.innerHTML = `
                        .swal-wide-popup {
                            width: 80% !important;
                            max-width: 800px !important;
                        }
                        .note-editor {
                            margin-bottom: 10px !important;
                            width: 100% !important;
                        }
                        .note-editable {
                            text-align: left !important;
                        }
                        .note-editing-area {
                            width: 100% !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            },
            preConfirm: () => {
                const subject = Swal.getPopup().querySelector('#email_subject').value;
                const message = $('#email_message').summernote('code');
                const userCoordId = Swal.getPopup().querySelector('#coord_id').value;

                if (!subject) {
                    Swal.showValidationMessage('Please enter subject.');
                    return false;
                }

                if (!message) {
                    Swal.showValidationMessage('Please enter message.');
                    return false;
                }

                return {
                    email_subject: subject,
                    email_message: message,
                    coord_id: userCoordId,
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

                        $.ajax({
                            url: '{{ route('coordinators.sendcoordup') }}',
                            type: 'POST',
                            data: {
                                subject: data.email_subject,
                                message: data.email_message,
                                userCoordId: data.coord_id,
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

    function showBigSisterEmailModal(chapterId) {
    Swal.fire({
        title: 'Big Sister Welcome Email',
        html: `
            <p>This will automatically send the Big Sister Welcome Email to the Big Sister, her Mentoring Coordinator and Full Coordinator Team.</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Send',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                chapter_id: chapterId,
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

                        $.ajax({
                            url: '{{ route('coordinators.sendbigsister') }}',
                            type: 'POST',
                            data: {
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

    function showDeleteChapterModal(chapterId, chapterName, activeStatus) {
    Swal.fire({
        title: 'Chapter Deletion',
        html: `
            <p>This will remove the chapter "<strong>${chapterName}</strong>" and all board members from the database.</p>
            <p>PLEASE USE CAUTION, this cannot be undone!!</p>
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
            <input type="hidden" id="active_status" name="active_status" value="${activeStatus}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
       preConfirm: () => {
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
            const activeStatus = Swal.getPopup().querySelector('#active_status').value;

            return {
                chapter_id: chapterId,
                active_status: activeStatus
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
                url: '{{ route('techreports.updatechapterdelete') }}',
                type: 'POST',
                data: {
                    chapterid: data.chapter_id,
                    activeStatus: data.active_status,  // Add this line
                    _token: '{{ csrf_token() }}'
                },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Chapter successfully deleted.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload();
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

function showDeleteCoordModal(coordId, firstName, lastName, activeStatus) {
    Swal.fire({
        title: 'Coordinator Deletion', // Changed from "Chapter Deletion"
        html: `
            <p>This will remove the coordinator "<strong>${firstName} ${lastName}</strong>" from the database.</p>
            <p>PLEASE USE CAUTION, this cannot be undone!!</p>
            <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
            <input type="hidden" id="active_status" name="active_status" value="${activeStatus}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const coordId = Swal.getPopup().querySelector('#coord_id').value;
            const activeStatus = Swal.getPopup().querySelector('#active_status').value;

            return {
                coord_id: coordId,
                active_status: activeStatus
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
                        url: '{{ route('techreports.updatecoordinatordelete') }}',
                        type: 'POST',
                        data: {
                            coordid: data.coord_id,
                            activeStatus: data.active_status, // Add this line
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Coordinator successfully deleted.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload();
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

function showDeleteUserModal(userId, firstName, lastName) {
    Swal.fire({
        title: 'User Deletion', // Changed from "Chapter Deletion"
        html: `
            <p>This will remove the user "<strong>${firstName} ${lastName}</strong>" from the database.</p>
            <p>PLEASE USE CAUTION, this cannot be undone!!</p>
            <input type="hidden" id="user_id" name="user_id" value="${userId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const userId = Swal.getPopup().querySelector('#user_id').value;

            return {
                user_id: userId,
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
                        url: '{{ route('userreports.updateuserdelete') }}',
                        type: 'POST',
                        data: {
                            userid: data.user_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'User successfully deleted.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload();
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

function showNoChapterInquiryEmailModal(inquiryId, firstName, lastName) {
    Swal.fire({
        title: 'No Chapter',
        html: `
            <p>This will automatically send a message to <strong>${firstName} ${lastName}</strong> letting them know there is no chapter in their area and providing more details about starting a chapter.</p>
            <input type="hidden" id="inquiry_id" name="inquiry_id" value="${inquiryId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Send',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },  // <-- Added missing comma here
        preConfirm: () => {
            const inquiryId = Swal.getPopup().querySelector('#inquiry_id').value;

            return {
                inquiry_id: inquiryId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;  // <-- Added this line

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
                        url: '{{ route('inquiries.sendnochapter') }}',
                        type: 'POST',
                        data: {
                            inquiryId: data.inquiry_id,  // <-- Fixed variable name
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
                                window.location.href = '{{ route('inquiries.inquiryapplication') }}';
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

function showYesChapterInquiryEmailModal(inquiryId, firstName, lastName, chapterName, chapterId) {
        Swal.fire({
        title: 'No Chapter',
        html: `
            <p>This will automatically send a message to <strong>${firstName} ${lastName}</strong> letting them know they live in the boundaries for the <strong>${chapterName}</strong> chapter.</p>
            <p>This will also automatically send a message to the <strong>${chapterName}</strong> chapter with <strong>${firstName}'s</strong> contact information.</p>
            <input type="hidden" id="inquiry_id" name="inquiry_id" value="${inquiryId}">
            <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
        `,
        showCancelButton: true,
        confirmButtonText: 'Send',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },  // <-- Added missing comma here
        preConfirm: () => {
            const inquiryId = Swal.getPopup().querySelector('#inquiry_id').value;
            const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

            return {
                inquiry_id: inquiryId,
                chapter_id: chapterId,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;  // <-- Added this line

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
                        url: '{{ route('inquiries.sendyeschapter') }}',
                        type: 'POST',
                        data: {
                            inquiryId: data.inquiry_id,
                            chapterId: data.chapter_id,
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
                                window.location.href = '{{ route('inquiries.inquiryapplication') }}';
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

function showChapterInquiryEmailModal(chapterName, chapterId, inquiryId, userName, userPosition, userConfName, userConfDesc, predefinedSubject = '', predefinedMessage = '') {
        Swal.fire({
            title: 'Chapter Email Message',
            html: `
                <p>This will send your message to the inquiries email address for <b>${chapterName}</b>.<br>
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <input type="text" id="email_subject" name="email_subject" class="swal2-input" placeholder="Enter Subject" required style="width: 100%; margin: 0 !important;" value="${predefinedSubject}">
                </div>
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><br><b>MOMS Club of ${chapterName}:</b></p>
                </div>
                <div style="width: 100%; margin-bottom: 10px;">
                    <textarea id="email_message" name="email_message" class="rich-editor" ${predefinedMessage ? '' : 'placeholder="Email Message"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedMessage}</textarea>
                </div>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
                <input type="hidden" id="inquiry_id" name="inquiry_id" value="${inquiryId}">
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><b>MCL,</b><br>
                    ${userName}<br>
                    ${userPosition}<br>
                    ${userConfName}, ${userConfDesc}<br>
                    International MOMS Club</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger',
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#email_message').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            $(this).val(contents);
                        }
                    }
                });

                if (!document.getElementById('swal-wide-popup-style')) {
                    const style = document.createElement('style');
                    style.id = 'swal-wide-popup-style';
                    style.innerHTML = `
                        .swal-wide-popup {
                            width: 80% !important;
                            max-width: 800px !important;
                        }
                        .note-editor {
                            margin-bottom: 10px !important;
                            width: 100% !important;
                        }
                        .note-editable {
                            text-align: left !important;
                        }
                        .note-editing-area {
                            width: 100% !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            },
            preConfirm: () => {
                const subject = Swal.getPopup().querySelector('#email_subject').value;
                // Get the HTML content from Summernote
                const message = $('#email_message').summernote('code');
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const inquiryId = Swal.getPopup().querySelector('#inquiry_id').value;
                if (!subject) {
                    Swal.showValidationMessage('Please enter subject.');
                    return false;
                }
                if (!message) {
                    Swal.showValidationMessage('Please enter message.');
                    return false;
                }
                return {
                    email_subject: subject,
                    email_message: message,
                    chapter_id: chapterId,
                    inquiry_id: inquiryId,
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
                        $.ajax({
                            url: '{{ route('inquiries.sendchapter') }}',
                            type: 'POST',
                            data: {
                                subject: data.email_subject,
                                message: data.email_message,
                                chapterId: data.chapter_id,
                                inquiryId: data.inquiry_id,
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

    function showMemberInquiryEmailModal(inquiryId, inquiryFirstName, inquiryLastName, userName, userPosition, userConfName, userConfDesc, predefinedSubject = '', predefinedMessage = '') {
        Swal.fire({
            title: 'Chapter Email Message',
            html: `
                <p>This will send your message to <b>${inquiryFirstName} ${inquiryLastName}</b>.<br>
                <div style="display: flex; align-items: center; width: 100%; margin-bottom: 10px;">
                    <input type="text" id="email_subject" name="email_subject" class="swal2-input" placeholder="Enter Subject" required style="width: 100%; margin: 0 !important;" value="${predefinedSubject}">
                </div>
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><br><b>${inquiryFirstName}:</b></p>
                </div>
                <div style="width: 100%; margin-bottom: 10px;">
                    <textarea id="email_message" name="email_message" class="rich-editor" ${predefinedMessage ? '' : 'placeholder="Email Message"'} required style="width: 100%; height: 150px; margin: 0 !important; box-sizing: border-box;">${predefinedMessage}</textarea>
                </div>
                <input type="hidden" id="inquiry_id" name="inquiry_id" value="${inquiryId}">
                <div style="width: 100%; margin-bottom: 10px; text-align: left;">
                <p><b>MCL,</b><br>
                    ${userName}<br>
                    ${userPosition}<br>
                    ${userConfName}, ${userConfDesc}<br>
                    International MOMS Club</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger',
                popup: 'swal-wide-popup'
            },
            didOpen: () => {
                $('#email_message').summernote({
                    height: 150,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['link']]
                    ],
                    callbacks: {
                        onChange: function(contents) {
                            $(this).val(contents);
                        }
                    }
                });

                if (!document.getElementById('swal-wide-popup-style')) {
                    const style = document.createElement('style');
                    style.id = 'swal-wide-popup-style';
                    style.innerHTML = `
                        .swal-wide-popup {
                            width: 80% !important;
                            max-width: 800px !important;
                        }
                        .note-editor {
                            margin-bottom: 10px !important;
                            width: 100% !important;
                        }
                        .note-editable {
                            text-align: left !important;
                        }
                        .note-editing-area {
                            width: 100% !important;
                        }
                    `;
                    document.head.appendChild(style);
                }
            },
            preConfirm: () => {
                const subject = Swal.getPopup().querySelector('#email_subject').value;
                // Get the HTML content from Summernote
                const message = $('#email_message').summernote('code');
                const inquiryId = Swal.getPopup().querySelector('#inquiry_id').value;
                if (!subject) {
                    Swal.showValidationMessage('Please enter subject.');
                    return false;
                }
                if (!message) {
                    Swal.showValidationMessage('Please enter message.');
                    return false;
                }
                return {
                    email_subject: subject,
                    email_message: message,
                    inquiry_id: inquiryId,
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
                        $.ajax({
                            url: '{{ route('inquiries.sendmember') }}',
                            type: 'POST',
                            data: {
                                subject: data.email_subject,
                                message: data.email_message,
                                inquiryId: data.inquiry_id,
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

</script>

