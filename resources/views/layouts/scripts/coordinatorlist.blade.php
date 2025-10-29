<script>
$(document).ready(function() {
    // Only run if the element exists on this page
    if ($('#ch_primarycor').length) {
        function loadCoordinatorList(coorId) {
            if (coorId != "") {
                $.ajax({
                    url: '{{ url("/load-coordinator-list") }}' + '/' + coorId,
                    type: "GET",
                    success: function(result) {
                        $("#display_corlist").html(result);
                    },
                    error: function (jqXHR, exception) {
                        console.log("Error: ", jqXHR, exception);
                    }
                });
            }
        }

        var selectedCoorId = $("#ch_primarycor").val();
        loadCoordinatorList(selectedCoorId);

        $("#ch_primarycor").change(function() {
            var selectedValue = $(this).val();
            loadCoordinatorList(selectedValue);
        });
    }
});

function filterCoordinators() {
    const regionDropdown = document.getElementById('ch_region');
    const selectedRegion = regionDropdown.value; // Get the selected region ID
    const primaryCorDropdown = document.getElementById('ch_primarycor'); // Coordinator dropdown

    // Filter options based on the selected region
    Array.from(primaryCorDropdown.options).forEach(option => {
        if (
            option.value == "" || // Always show the default empty option
            option.dataset.regionId == selectedRegion || // Match the selected region
            option.dataset.regionId == "0" // Always include region_id = 0
        ) {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });

    // Reset the selected value if it's no longer valid
    if (primaryCorDropdown.value != "" &&
        primaryCorDropdown.querySelector(`option[value="${primaryCorDropdown.value}"]`).style.display == "none") {
        primaryCorDropdown.value = "";
    }
}

// Attach the event listener to the region dropdown
document.getElementById('ch_region').addEventListener('change', filterCoordinators);

// Run the filtering logic on page load
document.addEventListener('DOMContentLoaded', filterCoordinators);


</script>
