<style>
    .page-break {
        page-break-after: always;
    }

    /* Shared base styling */
    table {
        border-collapse: collapse;
        width: 100%;
        margin: 20px 0;
    }

    /* Cover table stylilng */
    .cover-table th,
    .cover-table td {
        border: 1px solid black;
        padding: 8px 12px;
        text-align: left;
            table-layout: auto !important;

    }

    .cover-table th {
        background-color: #eee;
    }

    /* List table styling */
    .list-table th,
    .list-table td {
        border: 1px solid black;
        padding: 4px 6px;
        font-size: 12px;
        text-align: left;
        word-wrap: break-word;

    }

    .list-table th {
        background-color: #dff0d8;
    }

    /* Table structure helpers */
    thead {
        display: table-header-group;
    }

    tbody {
        display: table-row-group;
    }
