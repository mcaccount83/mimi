<script>
    $(document).ready(function() {
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
    });
</script>
