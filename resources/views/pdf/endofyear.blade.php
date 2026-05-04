<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End of Year Information</title>
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

    /* Zap table styling */
    .zap-table th,
    .zap-table td {
        border: 1px solid black;
        padding: 4px 6px;
        font-size: 11px;
        text-align: left;
            table-layout: auto !important;

    }

    .zap-table th {
        background-color: #eee;
    }

    /* Add table styling */
    .add-table th,
    .add-table td {
        border: 1px solid black;
        padding: 4px 6px;
        font-size: 12px;
        text-align: left;
        word-wrap: break-word;

    }

    .add-table th {
        background-color: #dff0d8;
    }

    /* Table structure helpers */
    thead {
        display: table-header-group;
    }

    tbody {
        display: table-row-group;
    }

    .label {
        font-weight: bold;
        width: 150px;
        background-color: #f5f5f5;
    }

    /* First page footer only */
    .footer {
        position: fixed;
        bottom: 20px;
        left: 20px;
        right: 20px;
        font-size: 10px;
        line-height: 1.3;
        text-align: justify;
        border-top: 1px solid #ccc;
        padding-top: 10px;
        background-color: white;
    }

    /* Hide footer on all pages except first */
    @media print {
        @page {
            margin: 1in;
        }

        @page :first {
            /* Footer only on first page */
        }

        @page :not(:first) {
            /* Hide footer on subsequent pages */
        }

        /* Alternative approach - hide footer after page break */
        .page-break ~ * .footer {
            display: none !important;
        }
    }

    /* Page-specific styling */
    .first-page {
        position: relative;
    }

    .subsequent-page {
        position: relative;
    }

    .subsequent-page .footer {
        display: none !important;
    }
</style>

</head>
<body>
        <div class="keep-together" style="page-break-inside: avoid;">
            {{-- <center>
                <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
            </center>
            <br> --}}
            <center><h2>International MOMS Club<br>
                End of Year Information</h2></center>

                <p>As of <b>@formatDate($fiscalYearStartDate)</b></p>
                <table class="zap-table">
                    <thead>
                        <tr>
                            <th>Chapters</th>
                            <th>Coordinators</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> {{ $pdfData['totalChapterCount'] }} </td>
                            <td> {{ $pdfData['totalCoordCount'] }} </td>
                        </tr>
                    </tbody>
                </table>

                <br>

                <p>As of Today <b>(@formatDate($currentDate))</b></p>
                <table class="zap-table">
                    <thead>
                        <tr>
                            <th>Chapters</th>
                            <th>Coordinators</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td> {{ $pdfData['activeChapterCountCurrent'] }} </td>
                            <td> {{ $pdfData['activeCoordCountCurrent'] }} </td>
                        </tr>
                    </tbody>
                </table>


        </div>
</body>
</html>
