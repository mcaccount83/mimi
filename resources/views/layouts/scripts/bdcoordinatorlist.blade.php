<script>
$(document).ready(function() {
    // Only run if the element exists on this page
    if ($('#pcid').length) {
        var pcid = $("#pcid").val();
        if (pcid != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list/") }}' + '/' + pcid,
                type: "GET",
                success: function (result) {
                    $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                    console.error("Failed to load coordinator list:", exception);
                    $("#display_corlist").html('<p class="text-danger">Unable to load coordinator list. Please refresh the page.</p>');
                }
            });
        }
    }

    $('.cls-pswd').on('keypress', function(e) {
        if (e.which == 32)
            return false;
    });

});
</script>
