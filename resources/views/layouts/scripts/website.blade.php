<script>
     document.addEventListener('DOMContentLoaded', function() {
    const websiteUrl = document.getElementById('ch_website');
    const statusContainer = document.getElementById('ch_webstatus-container');
    const websiteStatus = document.getElementById('ch_webstatus');

    // Only proceed if all elements exist
    if (websiteUrl && statusContainer && websiteStatus) {

        // Function to toggle status field visibility
        function toggleStatusField() {
            const urlValue = websiteUrl.value.trim();

            if (urlValue != '' && urlValue != 'http://') {
                // Show status field if URL has a meaningful value
                statusContainer.style.display = 'flex';
                websiteStatus.setAttribute('required', 'required');
            } else {
                // Hide status field if URL is empty or just the default "http://"
                statusContainer.style.display = 'none';
                websiteStatus.removeAttribute('required');
                websiteStatus.value = ""; // Clear the selection
            }
        }

        // Set initial state on page load
        toggleStatusField();

        // Add event listeners for real-time updates
        websiteUrl.addEventListener('input', toggleStatusField);
        websiteUrl.addEventListener('change', toggleStatusField);
    }
});

document.addEventListener("DOMContentLoaded", function() {
    const websiteField = document.getElementById("ch_website");
    const statusField = document.getElementById("ch_webstatus");

    websiteField.addEventListener("input", function() {
        // Enable options 2 and 3, disable options 1 and 2
        Array.from(statusField.options).forEach(option => {
            if (["0", "1"].includes(option.value)) {
                option.disabled = true;
            } else if (["2", "3"].includes(option.value)) {
                option.disabled = false;
            }
        });
    });
});
</script>
