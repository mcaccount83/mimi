<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Define the sections we need to handle
    const sections = ['pre', 'avp', 'mvp', 'trs', 'sec'];

    // Special state IDs that should show the country field
    const specialStates = [52, 53, 54, 55];

    // Process each section
    sections.forEach(section => {
        const stateDropdown = document.getElementById(`ch_${section}_state`);
        const countryContainer = document.getElementById(`ch_${section}_country-container`);
        const countrySelect = document.getElementById(`ch_${section}_country`);

        // Only proceed if all elements exist
        if (stateDropdown && countryContainer && countrySelect) {
            // Function to toggle country field visibility
            function toggleCountryField() {
                const selectedStateId = parseInt(stateDropdown.value) || 0;

                if (specialStates.includes(selectedStateId)) {
                    countryContainer.style.display = 'flex';
                    countrySelect.setAttribute('required', 'required');
                } else {
                    countryContainer.style.display = 'none';
                    countrySelect.removeAttribute('required');
                    countrySelect.value = "";
                }
            }

            // Set initial state
            toggleCountryField();

            // Add event listener
            stateDropdown.addEventListener('change', toggleCountryField);
        }
    });
});

// Function to handle show/hide logic for vacant checkboxes
function handleVacantCheckbox(checkboxId, fieldClass) {
    var fields = $("." + fieldClass);

    $("#" + checkboxId).change(function () {
        if ($(this).prop("checked")) {
            fields.hide().find('input, select, textarea').prop('required', false).val(null);
        } else {
            fields.show().find('input, select, textarea').prop('required', true);
        }
    });

    // Initial show/hide logic on page load
    if ($("#" + checkboxId).prop("checked")) {
        fields.hide().find('input, select, textarea').prop('required', false).val(null);
    } else {
        fields.show().find('input, select, textarea').prop('required', true);
    }
}

// Apply the logic for each checkbox with a specific class
handleVacantCheckbox("MVPVacant", "mvp-field");
handleVacantCheckbox("AVPVacant", "avp-field");
handleVacantCheckbox("SecVacant", "sec-field");
handleVacantCheckbox("TreasVacant", "trs-field");

</script>
