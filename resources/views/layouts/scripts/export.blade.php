<script>
function startExport(exportType, exportName) {
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

        'constantcontact': '{{ route("export.constantcontact") }}',
    };

    const route = routes[exportType];
    const useAjax = true;

    if (useAjax) {
        Swal.fire({
            title: `Exporting ${exportName}`,
            text: 'Please wait while your download is being prepared...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();

                $.ajax({
                    url: route,
                    type: 'GET',
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob, status, xhr) {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;

    // Try to get filename from Content-Disposition header
    let filename = exportType + '-export.csv'; // fallback
    const disposition = xhr.getResponseHeader('Content-Disposition');
    if (disposition) {
        const match = disposition.match(/filename[^;=\n]*=["']?([^"'\n]*)["']?/);
        if (match && match[1]) {
            filename = match[1].trim();
        }
    }

    a.download = filename;

                        document.body.appendChild(a);
                        window._suppressPleaseWait = true;
                        a.click();
                        window._suppressPleaseWait = false;

                        // Clean up
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);

                        Swal.fire({
                            title: 'Download Complete!',
                            text: `Your ${exportName} has been downloaded successfully.`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-sm btn-success'
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Download Failed',
                            text: 'There was a problem generating your export. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-sm btn-danger'
                            }
                        });
                    }
                });
            }
        });
    } else {
        Swal.fire({
            title: `Exporting ${exportName}`,
            text: 'Please wait while your download begins...',
            allowOutsideClick: false,
            showConfirmButton: true,
            confirmButtonText: 'Download Complete',
            customClass: {
                confirmButton: 'btn btn-sm btn-success'
            },
            didOpen: () => {
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                document.body.appendChild(iframe);
                iframe.src = route;
            }
        }).then((result) => {
            if (result.isConfirmed) {
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
