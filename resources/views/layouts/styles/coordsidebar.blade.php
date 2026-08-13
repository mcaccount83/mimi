<style>
    /* Sidebar font size */
    .app-sidebar .sidebar-wrapper {
        font-size: 0.95rem !important;
        height: calc(100vh - 4rem); /* adjust 4rem to match your navbar/brand-link height */
        overflow-y: auto;
        overflow-x: hidden;
    }

    .app-sidebar .nav-link {
        font-size: 0.95rem !important;
        line-height: 1.2 !important;
    }

    .sidebar-menu .nav-icon {
    font-size: 1.3rem !important;
    }

    /* Logo size and position in sidebar */
    .custom-logo {
        width: 70px; /* Adjust width as needed */
        height: 70px; /* Adjust height as needed */
        display: block;
        margin: 10px auto; /* Centers horizontally and adds top and bottom margin */
    }

    .brand-link {
        display: flex;
        justify-content: center; /* Centers items horizontally */
        align-items: center; /* Centers items vertically */
        margin-top: 10px; /* Adds top margin */
    }

    .notification-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        font-size: 12px;
        line-height: 18px;
        background-color: var(--bs-success);
        color: var(--bs-white);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-item.position-relative {
        position: relative;
    }

    /* Optional: Add hover effect */
    .notification-badge:hover {
        background-color: var(--bs-success);
    }
</style>
