<script>
// document.addEventListener('DOMContentLoaded', function() {
//     if (document.getElementById('ch_primarycor')) {
//         function loadCoordinatorList(coorId) {
//             if (coorId != "") {
//                 $.ajax({
//                     url: '{{ url("/load-coordinator-list") }}' + '/' + coorId,
//                     type: "GET",
//                     success: function(result) {
//                         $("#display_corlist").html(result);
//                     },
//                     error: function (jqXHR, exception) {
//                         console.log("Error: ", jqXHR, exception);
//                     }
//                 });
//             }
//         }

//         var selectedCoorId = document.getElementById('ch_primarycor').value;
//         loadCoordinatorList(selectedCoorId);

//         document.getElementById('ch_primarycor').addEventListener('change', function() {
//             loadCoordinatorList(this.value);
//         });
//     }

//     if (document.getElementById('ch_region') && document.getElementById('ch_primarycor')) {
//         filterCoordinators();
//     }
// });

if (document.getElementById('ch_primarycor')) {
    var input = document.getElementById('ch_primarycor');
    var selectedCoorId = input.value;
    var format = input.dataset.format || 'default';
    loadCoordinatorList(selectedCoorId, format);

    input.addEventListener('change', function() {
        loadCoordinatorList(this.value, format);
    });
}

function loadCoordinatorList(coorId, format = 'default') {
    if (coorId != "") {
        $.ajax({
            url: '{{ url("/load-coordinator-list") }}' + '/' + coorId,
            type: "GET",
            success: function(result) {
                var str = '';
                result.forEach(function(cor) {
                    var nameDisplay = cor.active
                        ? `<a href='mailto:${cor.email}' target='_top'>${cor.name}</a>`
                        : cor.name + '/Retired';

                    if (format == 'inline') {
                        str += `<label class='me-2'>${cor.title}</label>${nameDisplay} ${cor.position}<br>`;
                    } else {
                        str += `<b>${cor.title}</b><span class='float-end'>${nameDisplay} ${cor.position}</span><br>`;
                    }
                });
                $("#display_corlist").html(str);
            },
            error: function(jqXHR, exception) {
                console.log("Error: ", jqXHR, exception);
            }
        });
    }
}

function filterCoordinators() {
    const regionDropdown = document.getElementById('ch_region');
    const primaryCorDropdown = document.getElementById('ch_primarycor');
    if (!regionDropdown || !primaryCorDropdown) return;

    const selectedRegion = regionDropdown.value;
    Array.from(primaryCorDropdown.options).forEach(option => {
        if (option.value == "" || option.dataset.regionId == selectedRegion || option.dataset.regionId == "0") {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });

    if (primaryCorDropdown.value != "" &&
        primaryCorDropdown.querySelector(`option[value="${primaryCorDropdown.value}"]`).style.display == "none") {
        primaryCorDropdown.value = "";
    }
}

const regionElement = document.getElementById('ch_region');
if (regionElement) {
    regionElement.addEventListener('change', filterCoordinators);
}
</script>
