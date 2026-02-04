<script>
    function showRosterUploadModal(chapter_id) {

    Swal.fire({
        title: 'Upload Chapter Roster',
        html: `
            <form id="uploadRosterForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadRosterForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeRoster', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Roster uploaded successfully!',
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

function showStatement1UploadModal(chapter_id) {

    Swal.fire({
        title: 'Upload Statement',
        html: `
            <form id="uploadStatement1Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement1Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement1', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Statement uploaded successfully!',
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

function showStatement2UploadModal(chapter_id) {

    Swal.fire({
        title: 'Upload Additional Statement',
        html: `
            <form id="uploadStatement2Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement2Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement2', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Additional Statement uploaded successfully!',
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

function show990NUploadModal(chapter_id) {

    Swal.fire({
        title: 'Upload 990N',
        html: `
            <form id="upload990NForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('upload990NForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/store990n', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: '990N uploaded successfully!',
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

function showAwardUploadModal(chapter_id) {

    Swal.fire({
        title: 'Upload Award Attachments',
        html: `
            <form id="uploadAwardForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadAwardForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeAward', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Award attachments uploaded successfully!',
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

function showGrantUploadModal(id) {

    Swal.fire({
        title: 'Upload Grant Photos',
        html: `
            <form id="uploadGrantPhotos" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadGrantPhotos'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storePhotos', '') }}' + '/' + id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Photos uploaded successfully!',
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
