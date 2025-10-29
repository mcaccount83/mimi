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
</script>
