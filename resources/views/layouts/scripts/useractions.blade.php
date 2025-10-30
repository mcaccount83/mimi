<script>
    function subscribe(categoryId, userId) {
        Swal.fire({
            title: 'Subscribe to List',
            html: `
                <p>User will be subscribed to the selected list. Please confirm by pressing OK.</p>
                <input type="hidden" id="user_id" name="user_id" value="${userId}">
                <input type="hidden" id="category_id" name="category_id" value="${categoryId}">
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
                const categoryId = Swal.getPopup().querySelector('#category_id').value;

                return {
                    user_id: userId,
                    category_id: categoryId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('forum.subscribecategory') }}',
                    type: 'POST',
                    data: {
                        user_id: data.user_id,
                            category_id: data.category_id,
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
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
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

    function unsubscribe(categoryId, userId) {
        Swal.fire({
            title: 'Subscribe to List',
            html: `
                <p>Coordinator will be subscribed to the selected list. Please confirm by pressing OK.</p>

                <input type="hidden" id="user_id" name="user_id" value="${userId}">
                <input type="hidden" id="category_id" name="category_id" value="${categoryId}">
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
                const categoryId = Swal.getPopup().querySelector('#category_id').value;

                return {
                    user_id: userId,
                    category_id: categoryId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                $.ajax({
                    url: '{{ route('forum.unsubscribecategory') }}',
                    type: 'POST',
                    data: {
                            user_id: data.user_id,
                            category_id: data.category_id,
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
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
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

    function showUserInactiveModel() {
    Swal.fire({
        title: 'Make All Disbanded Users Inactive',
        html: `
            <p>This will make all disbanded users inactive.  They will no longer have access to edit the Disbanding Checklist and/or Final Fiancial Report</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },

    }).then((result) => {
        if (result.isConfirmed) {

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
                        url: '{{ route('techreports.resetdisbandedusers') }}',
                        type: 'POST',
                        data: {
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

function showResetProbationSubmisionModel() {
    Swal.fire({
        title: 'Reset Quarterly Reports for all Chapters',
        html: `
            <p>This will reset the quarterly data inputs for all chapters who have made submissions for the past year.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },

    }).then((result) => {
        if (result.isConfirmed) {

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
                        url: '{{ route('techreports.resetProbationSubmission') }}',
                        type: 'POST',
                        data: {
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

    function showDisbandChapterModal(chapterid) {
        Swal.fire({
            title: 'Chapter Disband Reason',
            html: `
                <p>Marking a chapter as disbanded will remove the logins for all board members and remove the chapter. Please enter the reason for disbanding and press OK.</p>
                <div style="display: flex; align-items: center; ">
                    <input type="text" id="disband_reason" name="disband_reason" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
                </div>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterid}">
                <br>
                <div class="custom-control custom-switch">
                    <input type="checkbox" id="disband_letter" class="custom-control-input">
                    <label class="custom-control-label" for="disband_letter">Send Disband Letter to Chapter</label>
                </div>
                <br>
                <div id="letterTypeContainer" style="display: none;">
                    <p>Select the type of letter to generate and send:</p>
                    <select id="letterType" class="form-control">
                        <option value="general">Disband Letter - General</option>
                        <option value="did_not_start">Disband Letter - Did Not Start</option>
                        <option value="no_report">Disband Letter - No EOY Reports</option>
                        <option value="no_payment">Disband Letter - No Re-Reg Payment</option>
                        <option value="no_communication">Disband Letter - No Communication</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            didOpen: () => {
                // Add event listener to checkbox
                document.getElementById('disband_letter').addEventListener('change', function() {
                    const letterTypeContainer = document.getElementById('letterTypeContainer');
                    letterTypeContainer.style.display = this.checked ? 'block' : 'none';
                });
            },
            preConfirm: () => {
                const disbandReason = Swal.getPopup().querySelector('#disband_reason').value;
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;
                const disbandLetter = Swal.getPopup().querySelector('#disband_letter').checked;
                const letterType = Swal.getPopup().querySelector('#letterType').value;

                if (!disbandReason) {
                    Swal.showValidationMessage('Please enter the reason for disbanding.');
                    return false;
                }

                return {
                    disband_reason: disbandReason,
                    chapter_id: chapterId,
                    disband_letter: disbandLetter,
                    letterType
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
                            url: '{{ route('chapters.updatechapdisband') }}',
                            type: 'POST',
                            data: {
                                reason: data.disband_reason,
                                letter: data.disband_letter ? '1' : '0',
                                chapterid: data.chapter_id,
                                letterType: data.letterType,
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

    // Function to unzap Chapter via AJAX
    function unZapChapter(chapterid) {
        Swal.fire({
            title: 'UnZap Chapter',
            html: `
                <p>Unzapping a chapter will reactivate the logins for all board members and readd the chapter.</p>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterid}">

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

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('chapters.updatechapterunzap') }}',
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

    function chapApprove(chapterId, chapterRegion) {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Approving a chapter application will create their MIMI login, request their @momsclub.org email address, add them to the BoardList, and PublicList as well as give
                    them access to Board elearning. Please verify this is what you want to do by pressing OK.</p>
                <input type="hidden" id="chapter_id" name="chapter_id" value="${chapterId}">
                <input type="hidden" id="ch_region" name="ch_region" value="${chapterRegion}">
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
                const chapterRegion = Swal.getPopup().querySelector('#ch_region').value;

                if (chapterRegion == 0 || chapterRegion == null) {
                    Swal.showValidationMessage('Please select region.');
                    return false;
                }

                return {
                    chapter_id: chapterId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('chapters.updateapprove') }}',
                    type: 'POST',
                    data: {
                        chapter_id: data.chapter_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Check if response is JSON (success) or HTML (redirect with error)
                        if (response && typeof response == 'object') {
                            if (response.success) {
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
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        } else {
                            // If response is not JSON, it's likely a redirect (success case)
                            // Check if the response contains success indicators
                            Swal.fire({
                                title: 'Success!',
                                text: 'Chapter approved successfully.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                window.location.href = '{{ route('chapters.view', ['id' => 'chapterId']) }}';
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        let errorMessage = 'Something went wrong, Please try again.';

                        // Try to parse error response
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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

    function chapDecline(chapterId) {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Declining a chapter application will mark them as inactive and remove them from all chapter lists. Please enter the reason for declining and press OK.</p>
                <div style="display: flex; align-items: center; ">
                    <input type="text" id="disband_reason" name="disband_reason" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
                </div>
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
                const disbandReason = Swal.getPopup().querySelector('#disband_reason').value;
                const chapterId = Swal.getPopup().querySelector('#chapter_id').value;

                if (!disbandReason) {
                    Swal.showValidationMessage('Please enter the reason for declining.');
                    return false;
                }

                return {
                    disband_reason: disbandReason,
                    chapter_id: chapterId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('chapters.updatedecline') }}',
                    type: 'POST',
                    data: {
                        disband_reason: data.disband_reason,
                        chapter_id: data.chapter_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Check if response is JSON (success) or HTML (redirect with error)
                        if (response && typeof response == 'object') {
                            if (response.success) {
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
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        } else {
                            // If response is not JSON, it's likely a redirect (success case)
                            // Check if the response contains success indicators
                            Swal.fire({
                                title: 'Success!',
                                text: 'Chapter declined.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        let errorMessage = 'Something went wrong, Please try again.';

                        // Try to parse error response
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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

    function onLeaveCoordinator(coordId) {
        Swal.fire({
            title: 'Coordinator On Leave',
            html: `
                <p>This will mark the coordinator On Leave. Please confirm by pressing OK.</p>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
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

                return {
                    coord_id: coordId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('coordinators.updateonleave') }}',
                    type: 'POST',
                    data: {
                            coord_id: data.coord_id,
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
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
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

    function removeLeaveCoordinator(coordId) {
        Swal.fire({
            title: 'Coordinator Remove from Leave',
            html: `
                <p>This will remove the coordinator from Leave. Please confirm by pressing OK.</p>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
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

                return {
                    coord_id: coordId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                $.ajax({
                    url: '{{ route('coordinators.updateremoveleave') }}',
                    type: 'POST',
                    data: {
                            coord_id: data.coord_id,
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
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
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

    function retireCoordinator(coordId) {
        Swal.fire({
            title: 'Coordinator Retire Reason',
            html: `
                <p>Marking a coordinator as retired will remove their login. Please enter the reason for retiring and press OK.</p>
                <div style="display: flex; align-items: center; ">
                    <input type="text" id="reason_retired" name="reason_retired" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
                </div>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const retireReason = Swal.getPopup().querySelector('#reason_retired').value;
                const coordId = Swal.getPopup().querySelector('#coord_id').value;

                if (!retireReason) {
                    Swal.showValidationMessage('Please enter the reason for retiring.');
                    return false;
                }

                return {
                    reason_retired: retireReason,
                    coord_id: coordId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                $.ajax({
                    url: '{{ route('coordinators.updateretire') }}',
                    type: 'POST',
                    data: {
                            reason_retired: data.reason_retired,
                            coord_id: data.coord_id,
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
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
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

    function unRetireCoordinator(coordId) {
        Swal.fire({
            title: 'Reactivate Coordinator',
            html: `
                <p>Reactivating a coordinator as retired will reset their login. Please verify this is what you want to do by pressing OK.</p>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
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

                return {
                    coord_id: coordId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('coordinators.updateunretire') }}',
                    type: 'POST',
                    data: {
                            coord_id: data.coord_id,
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
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
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

    function appApprove(coordId, coordUserId) {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Approving a coordinator application will create their MIMI login, request their @momsclub.org email address, add them to the BoardList, VolList and PublicList as well as give
                    them access to Google Drive and Coordinator elearning. Please verify this is what you want to do by pressing OK.</p>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
                <input type="hidden" id="coord_userid" name="coord_userid" value="${coordUserId}">
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
                const coordUserId = Swal.getPopup().querySelector('#coord_userid').value;
                return {
                    coord_id: coordId,
                    coord_userid: coordUserId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('coordinators.updateapprove') }}',
                    type: 'POST',
                    data: {
                        coord_id: data.coord_id,
                        coord_userid: data.coord_userid,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Check if response is JSON (success) or HTML (redirect with error)
                        if (response && typeof response == 'object') {
                            if (response.success) {
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
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    }
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        } else {
                            // If response is not JSON, it's likely a redirect (success case)
                            // Check if the response contains success indicators
                            Swal.fire({
                                title: 'Success!',
                                text: 'Coordinator approved successfully.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                window.location.href = '{{ route('coordinators.view', ['id' => 'coorId']) }}';
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        let errorMessage = 'Something went wrong, Please try again.';

                        // Try to parse error response
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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

    function appReject(coordId, coordUserId) {
        Swal.fire({
            title: 'Are you sure?',
            html: `
                <p>Rejecting a coordinator application will mark them as inactive and remove them from all coordinator lists. Please enter the reason for rejecting and press OK.</p>
                <div style="display: flex; align-items: center; ">
                    <input type="text" id="reason_retired" name="reason_retired" class="swal2-input" placeholder ="Enter Reason" required style="width: 100%;">
                </div>
                <input type="hidden" id="coord_id" name="coord_id" value="${coordId}">
                <input type="hidden" id="coord_userid" name="coord_userid" value="${coordUserId}">
            `,
            showCancelButton: true,
            confirmButtonText: 'OK',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            },
            preConfirm: () => {
                const retiredReason = Swal.getPopup().querySelector('#reason_retired').value;
                const coordId = Swal.getPopup().querySelector('#coord_id').value;
                const coordUserId = Swal.getPopup().querySelector('#coord_userid').value;

                if (!retiredReason) {
                    Swal.showValidationMessage('Please enter the reason for rejecting.');
                    return false;
                }

                return {
                    reason_retired: retiredReason,
                    coord_id: coordId,
                    coord_userid: coordUserId,
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;

                // Perform the AJAX request
                $.ajax({
                    url: '{{ route('coordinators.updatereject') }}',
                    type: 'POST',
                    data: {
                        reason_retired: data.reason_retired,
                        coord_id: data.coord_id,
                        coord_userid: data.coord_userid,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // Check if response is JSON (success) or HTML (redirect with error)
                        if (response && typeof response == 'object') {
                            if (response.success) {
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
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn-sm btn-success'
                                    }
                                });
                            }
                        } else {
                            // If response is not JSON, it's likely a redirect (success case)
                            // Check if the response contains success indicators
                            Swal.fire({
                                title: 'Success!',
                                text: 'Coordinator application rejected.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        }
                    },
                    error: function(jqXHR, exception) {
                        let errorMessage = 'Something went wrong, Please try again.';

                        // Try to parse error response
                        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                            errorMessage = jqXHR.responseJSON.message;
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
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
</script>
