<style>
    .email-column a {
        display: inline-block;
        text-decoration: none;
        color: inherit;
    }

    .board-info {
        display: table;
        width: 100%;
        margin-bottom: 15px;
    }
    .info-row {
        display: table-row;
    }
    .info-label, .info-label-empty {
        display: table-cell;
        width: 150px;
        padding: 2px 10px 2px 0;
        vertical-align: top;
    }
    .info-label {
        font-weight: bold;
    }
    .info-data {
        display: table-cell;
        padding: 2px 0;
    }

    /* Fix DataTables controls layout */
    .dt-length {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .dt-length label {
        margin-bottom: 0;
        white-space: nowrap;
        order: 2;  /* moves label AFTER the select */
    }

    .dt-length select {
        width: auto !important;
        order: 1;
    }

    .dt-search {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .dt-search label {
        margin-bottom: 0;
        white-space: nowrap;
    }

    .dt-search input {
        width: auto !important;
    }

    /* Fix pagination active page - remove blue */
    .dt-paging .page-item.active .page-link {
        background-color: transparent !important;
        border-color: #dee2e6 !important;
        color: #212529 !important;
        font-weight: bold;
    }
</style>
