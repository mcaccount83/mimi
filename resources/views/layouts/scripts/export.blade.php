<script>
function startExport(exportType, exportName) {
    // Define routes for each export type
        const routes = {
            'chapter': '{{ route("export.chapter") }}',
            'zapchapter': '{{ route("export.zapchapter") }}',
            'coordinator': '{{ route("export.coordinator", 0) }}',
            'retiredcoordinator': '{{ route("export.retiredcoordinator") }}',
            'appreciation': '{{ route("export.appreciation") }}',
            'chaptercoordinator': '{{ route("export.chaptercoordinator") }}',
            'reregoverdue': '{{ route("export.rereg") }}',
            'einstatus': '{{ route("export.einstatus") }}',
            'eoystatus': '{{ route("export.eoystatus") }}',

            'intchapter': '{{ route("export.intchapter") }}',
            'intzapchapter': '{{ route("export.intzapchapter") }}',
            'intcoordinator': '{{ route("export.intcoordinator", 0) }}',
            'intretiredcoordinator': '{{ route("export.intretcoordinator") }}',
            'intreregoverdue': '{{ route("export.intrereg") }}',
            'inteinstatus': '{{ route("export.inteinstatus") }}',
            'intirsfiling': '{{ route("export.intirsfiling") }}',
            'inteoystatus': '{{ route("export.inteoystatus") }}',
        };

        // Get the route for this export type
        const route = routes[exportType];

        // Check if we need to use AJAX (recommended approach that gives better control)
        const useAjax = true;

        if (useAjax) {
            // Show processing dialog
            Swal.fire({
                title: `Exporting ${exportName}`,
                text: 'Please wait while your download is being prepared...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    // Use AJAX to request the file and track its progress
                    $.ajax({
                        url: route,
                        type: 'GET',
                        xhrFields: {
                            responseType: 'blob' // Important for handling file downloads
                        },
                        success: function(blob) {
                            // Create a URL for the blob
                            const url = window.URL.createObjectURL(blob);

                            // Create a temporary link and trigger the download
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;

                            // Set filename from Content-Disposition header if available, otherwise use a default
                            const filename = exportType + '-export.csv'; // Default filename
                            a.download = filename;

                            document.body.appendChild(a);
                            a.click();

                            // Clean up
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);

                            // Show success message
                            Swal.fire({
                                title: 'Download Complete!',
                                text: `Your ${exportName} has been downloaded successfully.`,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn-sm btn-success'
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            // Show error message if download fails
                            Swal.fire({
                                title: 'Download Failed',
                                text: 'There was a problem generating your export. Please try again.',
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
        } else {
            // Fallback approach using iframe if AJAX doesn't work with your server setup
            // Show processing dialog
            Swal.fire({
                title: `Exporting ${exportName}`,
                text: 'Please wait while your download begins...',
                allowOutsideClick: false,
                showConfirmButton: true,
                confirmButtonText: 'Download Complete',
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                },
                didOpen: () => {
                    // Create a hidden iframe to handle the download
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);

                    // Start the download
                    iframe.src = route;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed download is complete
                    Swal.fire({
                        title: 'Download Complete!',
                        text: `Your ${exportName} has been downloaded successfully.`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    }
</script>
