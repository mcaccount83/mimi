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
                    console.log("AJAX result:", result);
                    $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                    console.log("AJAX error:", exception);
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
