<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropdownItems = document.querySelectorAll(".dropdown-item");
        const currentPath = window.location.pathname;

        dropdownItems.forEach(item => {
            const itemPath = new URL(item.href).pathname;

            if (itemPath == currentPath) {
                item.classList.add("active");
            }
        });
    });
</script>
