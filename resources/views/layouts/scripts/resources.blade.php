<script>
$(document).ready(function() {
    // Only run if the element exists on this page
    if ($('.fileType').length) {
        $('.fileType').change(function() {
            var selectedType = $(this).val();
            var resourceId = $(this).attr('id').replace('fileType', '');

            if (selectedType == '1') {
                // Document to Download
                $('.versionField').show();
                $('.filePathField').show();
                $('.fileUpload').show();
                $('.filePathBlock').show();
                $('.linkField').hide();
                $('.routeField').hide();
            } else if (selectedType == '2') {
                // Link to Webpage
                $('.linkField').show();
                $('.versionField').hide();
                $('.filePathField').hide();
                $('.filePathBlock').hide();
                $('.fileUpload').hide();
                $('.routeField').hide();
            } else if (selectedType == '3') {
                // Laravel Route
                $('.routeField').show();
                $('.linkField').hide();
                $('.versionField').hide();
                $('.filePathField').hide();
                $('.filePathBlock').hide();
                $('.fileUpload').hide();
            }
        });
    }

    if ($('#fileTypeNew').length) {
        $('.linkFieldNew').hide();
        $('.routeFieldNew').hide();
        $('.versionFieldNew').hide();
        $('.fileUploadNew').hide();

        $('#fileTypeNew').change(function() {
            var selectedType = $(this).val();

            if (selectedType == '1') {
                // Document to Download
                $('.versionFieldNew').show();
                $('.fileUploadNew').show();
                $('.linkFieldNew').hide();
                $('.routeFieldNew').hide();
            } else if (selectedType == '2') {
                // Link to Webpage
                $('.linkFieldNew').show();
                $('.versionFieldNew').hide();
                $('.fileUploadNew').hide();
                $('.routeFieldNew').hide();
            } else if (selectedType == '3') {
                // Laravel Route
                $('.routeFieldNew').show();
                $('.linkFieldNew').hide();
                $('.versionFieldNew').hide();
                $('.fileUploadNew').hide();
            }
        });
    }
});

function addToolkitFile() {
    var fileCategoryNew = document.getElementById('fileCategoryNew').value;
    var fileNameNew = document.getElementById('fileNameNew').value;
    var fileDescriptionNew = document.getElementById('fileDescriptionNew').value;
    var fileTypeNew = document.getElementById('fileTypeNew').value;
    var fileVersionNew = document.getElementById('fileVersionNew').value;
    var linkNew = document.getElementById('linkNew').value;
    var routeNew = document.getElementById('routeNew').value;  // ADD THIS LINE

    // Initialize an array to collect validation errors
    let validationErrors = [];

    // Collect validation errors
    if (fileCategoryNew == '') {
        validationErrors.push('Category is Required.');
    }
    if (fileNameNew == '') {
        validationErrors.push('Name is Required.');
    }
    if (fileNameNew.length > 50) {
        validationErrors.push('Name cannot exceed 50 characters.');
    }
    if (fileDescriptionNew == '') {
        validationErrors.push('Description is Required.');
    }
    if (fileDescriptionNew.length > 500) {
        validationErrors.push('Description cannot exceed 500 characters.');
    }
    if (fileTypeNew == '') {
        validationErrors.push('File Type is Required.');
    }
    if (fileTypeNew == '1' && fileVersionNew == '') {
        validationErrors.push('File Version is Required.');
    }
    if (fileTypeNew == '2' && linkNew == '') {
        validationErrors.push('Link is Required.');
    }
    if (fileTypeNew == '3' && routeNew == '') {  // ADD THIS VALIDATION
        validationErrors.push('Route Name is Required.');
    }

    // Check if there are any validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: validationErrors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
        return false;
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize the FormData object
    var formData = new FormData();
    formData.append('fileCategoryNew', fileCategoryNew);
    formData.append('fileNameNew', fileNameNew);
    formData.append('fileDescriptionNew', fileDescriptionNew);
    formData.append('fileTypeNew', fileTypeNew);

    if (fileTypeNew == '1') {
        formData.append('fileVersionNew', fileVersionNew);
    } else if (fileTypeNew == '2') {
        formData.append('LinkNew', linkNew);
    } else if (fileTypeNew == '3') {  // ADD THIS BLOCK
        formData.append('routeNew', routeNew);
    }

    // Send an AJAX request to Laravel backend to create a new resource
    $.ajax({
        url: '{{ route('resources.addtoolkit') }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            var id = response.id;
            var fileType = response.file_type;

            // Check if file type requires further processing
            if (fileType == '1') {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the toolkit.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData();
                formData.append('file', document.getElementById('fileUploadNew').files[0]);

                // Send an AJAX request to upload the file to Google Drive
                $.ajax({
                    url: '{{ route('store.toolkit', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Toolkit & File added successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'File upload failed. Please try again.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Success!',
                    text: 'Toolkit added successfully.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Toolkit add failed. Please try again.', 'error');
            console.error(xhr.responseText);
        }
    });

    // Close the modal
    $('#modal-task').modal('hide');

    return false;
}

function updateToolkitFile(id) {
    var file = document.getElementById('fileUpload' + id).files[0];
    var fileDescription = document.getElementById('fileDescription' + id).value;
    var fileType = document.getElementById('fileType' + id).value;
    var fileVersion = document.getElementById('fileVersion' + id).value;
    var link = document.getElementById('link' + id).value;
    var route = document.getElementById('route' + id).value;  // ADD THIS LINE

    var formData = new FormData();
    formData.append('file', file);
    formData.append('fileDescription', fileDescription);
    formData.append('fileType', fileType);
    formData.append('fileVersion', fileVersion);
    formData.append('link', link);
    formData.append('route', route);  // ADD THIS LINE

    // Initialize an array to collect validation errors
    let validationErrors = [];

    // Collect validation errors
    if (fileDescription == '') {
        validationErrors.push('Description is Required.');
    }
    if (fileDescription.length > 500) {
        validationErrors.push('Description cannot exceed 500 characters.');
    }
    if (fileType == '2' && link == '') {
        validationErrors.push('Link is Required.');
    }
    if (fileType == '3' && route == '') {  // ADD THIS VALIDATION
        validationErrors.push('Route Name is Required.');
    }

    // Check if there are any validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: validationErrors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
        return false;
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the task
    $.ajax({
        url: '{{ route('resources.updatetoolkit', ':id') }}'.replace(':id', id),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            // Check if file type requires further processing
            if (fileType == '1') {
                // Show the processing Swal
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the resources.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                // Send an AJAX request to upload the file to Google Drive
                $.ajax({
                    url: '{{ route('store.toolkit', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Toolkit & File updated successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'File upload failed. Please try again.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Success!',
                    text: 'Resource updated successfully.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Toollkit update failed. Please try again.', 'error');
            console.error(xhr.responseText);
        }
    });

    // Close the modal
    $('#editResourceModal' + id).modal('hide');

    return false;
}

function addResourceFile() {
    var fileCategoryNew = document.getElementById('fileCategoryNew').value;
    var fileNameNew = document.getElementById('fileNameNew').value;
    var fileDescriptionNew = document.getElementById('fileDescriptionNew').value;
    var fileTypeNew = document.getElementById('fileTypeNew').value;
    var fileVersionNew = document.getElementById('fileVersionNew').value;
    var linkNew = document.getElementById('linkNew').value;
    var routeNew = document.getElementById('routeNew').value;  // ADD THIS LINE

    // Initialize an array to collect validation errors
    let validationErrors = [];

    // Collect validation errors
    if (fileCategoryNew == '') {
        validationErrors.push('Category is Required.');
    }
    if (fileNameNew == '') {
        validationErrors.push('Name is Required.');
    }
    if (fileNameNew.length > 50) {
        validationErrors.push('Name cannot exceed 50 characters.');
    }
    if (fileDescriptionNew == '') {
        validationErrors.push('Description is Required.');
    }
    if (fileDescriptionNew.length > 500) {
        validationErrors.push('Description cannot exceed 500 characters.');
    }
    if (fileTypeNew == '') {
        validationErrors.push('File Type is Required.');
    }
    if (fileTypeNew == '1' && fileVersionNew == '') {
        validationErrors.push('File Version is Required.');
    }
    if (fileTypeNew == '2' && linkNew == '') {
        validationErrors.push('Link is Required.');
    }
    if (fileTypeNew == '3' && routeNew == '') {  // ADD THIS VALIDATION
        validationErrors.push('Route Name is Required.');
    }

    // Check if there are any validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: validationErrors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
        return false;
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize the FormData object
    var formData = new FormData();
    formData.append('fileCategoryNew', fileCategoryNew);
    formData.append('fileNameNew', fileNameNew);
    formData.append('fileDescriptionNew', fileDescriptionNew);
    formData.append('fileTypeNew', fileTypeNew);

    if (fileTypeNew == '1') {
        formData.append('fileVersionNew', fileVersionNew);
    } else if (fileTypeNew == '2') {
        formData.append('LinkNew', linkNew);
    } else if (fileTypeNew == '3') {  // ADD THIS BLOCK
        formData.append('routeNew', routeNew);
    }

    // Send an AJAX request to Laravel backend to create a new resource
    $.ajax({
        url: '{{ route('resources.addresources') }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            var id = response.id;
            var fileType = response.file_type;

            // Check if file type requires further processing
            if (fileType == '1') {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the toolkit.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData();
                formData.append('file', document.getElementById('fileUploadNew').files[0]);

                // Send an AJAX request to upload the file to Google Drive
                $.ajax({
                    url: '{{ route('store.resources', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Resource & File added successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'File upload failed. Please try again.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Success!',
                    text: 'Resource added successfully.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Resource add failed. Please try again.', 'error');
            console.error(xhr.responseText);
        }
    });

    // Close the modal
    $('#modal-task').modal('hide');

    return false;
}

function updateResourceFile(id) {
    var file = document.getElementById('fileUpload' + id).files[0];
    var fileDescription = document.getElementById('fileDescription' + id).value;
    var fileType = document.getElementById('fileType' + id).value;
    var fileVersion = document.getElementById('fileVersion' + id).value;
    var link = document.getElementById('link' + id).value;
    var route = document.getElementById('route' + id).value;  // ADD THIS LINE

    var formData = new FormData();
    formData.append('file', file);
    formData.append('fileDescription', fileDescription);
    formData.append('fileType', fileType);
    formData.append('fileVersion', fileVersion);
    formData.append('link', link);
    formData.append('route', route);  // ADD THIS LINE

    // Initialize an array to collect validation errors
    let validationErrors = [];

    // Collect validation errors
    if (fileDescription == '') {
        validationErrors.push('Description is Required.');
    }
    if (fileDescription.length > 500) {
        validationErrors.push('Description cannot exceed 500 characters.');
    }
    if (fileType == '2' && link == '') {
        validationErrors.push('Link is Required.');
    }
    if (fileType == '3' && route == '') {  // ADD THIS VALIDATION
        validationErrors.push('Route Name is Required.');
    }

    // Check if there are any validation errors
    if (validationErrors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: validationErrors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
        return false;
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the task
    $.ajax({
        url: '{{ route('resources.updateresources', ':id') }}'.replace(':id', id),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            // Check if file type requires further processing
            if (fileType == '1') {
                // Show the processing Swal
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the resources.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                // Send an AJAX request to upload the file to Google Drive
                $.ajax({
                    url: '{{ route('store.resources', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Resource & File updated successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'File upload failed. Please try again.', 'error');
                        console.error(xhr.responseText);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Success!',
                    text: 'Resource updated successfully.',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error!', 'Resource update failed. Please try again.', 'error');
            console.error(xhr.responseText);
        }
    });

    // Close the modal
    $('#editResourceModal' + id).modal('hide');

    return false;
}

 function addBugTask() {
    var taskNameNew = document.getElementById('taskNameNew').value;
    var taskDetailsNew = document.getElementById('taskDetailsNew').value;
    var taskPriorityNew = document.getElementById('taskPriorityNew').value;

     // Initialize an array to collect validation errors
     let validationErrors = [];

      // Collect validation errors
        if (taskNameNew == '') {
            validationErrors.push('Name is Required.');
        }
        if (taskNameNew.length > 50) {
            validationErrors.push('Name cannot exceed 50 characters.');
        }
        if (taskDetailsNew == '') {
            validationErrors.push('Details are Required.');
        }
        if (taskDetailsNew.length > 255) {
            validationErrors.push('Details cannot exceed 255 characters.');
        }

     // Check if there are any validation errors
     if (validationErrors.length > 0) {
            Swal.fire({
                title: 'Error!',
                html: validationErrors.join('<br>'),
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'btn btn-danger'
                }
            });
            return false; // Prevent form submission
        }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize the FormData object
        var formData = new FormData();
        formData.append('taskNameNew', taskNameNew);
        formData.append('taskDetailsNew', taskDetailsNew);
        formData.append('taskPriorityNew', taskPriorityNew);

    // Send an AJAX request to Laravel backend to create a new task
    $.ajax({
        url: '{{ route('resources.addbugs') }}',
        method: 'POST',
            data: formData,
            processData: false, // Required for FormData
            contentType: false, // Required for FormData
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                Swal.fire({
                                title: 'Success!',
                                text: 'Bug added successfully.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', 'Bug add failed. Please try again.', 'error');
                            console.error(xhr.responseText);
                        }
                    });

    // Close the modal
    $('#modal-task').modal('hide');

    // Prevent form submission
    return false;
}

  function updateBugTask(id) {
    var taskDetails = document.getElementById('taskDetails' + id).value;
    var taskNotes = document.getElementById('taskNotes' + id).value;
    var taskStatus = document.getElementById('taskStatus' + id).value;
    var taskPriority = document.getElementById('taskPriority' + id).value;

    var formData = new FormData();
    formData.append('taskDetails', taskDetails);
    formData.append('taskNotes', taskNotes);
    formData.append('taskStatus', taskStatus);
    formData.append('taskPriority', taskPriority);

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the task
    $.ajax({
        url: '{{ route('resources.updatebugs', ':id') }}'.replace(':id', id),
        method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Task updated successfully.',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload(); // Reload the page to reflect changes
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error!', 'Task update failed. Please try again.', 'error');
                        console.error(xhr.responseText);
                    }
                });

    // Close the modal
    $('#editTaskModal' + id).modal('hide');

    // Prevent form submission
    return false;
}


</script>
