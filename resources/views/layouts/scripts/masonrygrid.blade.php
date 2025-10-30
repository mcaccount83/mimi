<script>
$(document).ready(function() {
    var elem = document.querySelector('.grid');
    var msnry = new Masonry(elem, {
        itemSelector: '.grid-item',
        columnWidth: 400, // Set a fixed column width (adjust as needed)
        gutter: 20, // Set gutter for spacing between items
        percentPosition: true
    });
});
</script>
