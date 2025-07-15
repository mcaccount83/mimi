<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRS Subordinate Updates</title>
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

    /* Update table styling */
    .update-table th,
    .update-table td {
        border: 1px solid black;
        padding: 4px 6px;
        font-size: 11px;
        text-align: left;
            table-layout: auto !important;

    }

    .update-table th {
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
            <center>
                <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
            </center>
            <br>
            <p>{{ $pdfData['todayDate'] }}</p>
            <p>Internal Revenue Service<br>
                Ogden, UT  84201</p>
            <p><b>Subordinate Corrections</b><br>
                Taxpayer ID: 77-0125681<br>
                Gen Number: 3706</p>
            @if(count($pdfData['wrongDateList']) > 0)
                <p>Below is a list of subordinates unable to file their 990N electronic postcards due to an incorrect fiscal year listed. <b><u>All MOMS Club chapters have the same fiscal year of July 1st-June 30th.</u></b> Please update the fiscal year on these subordinates so that they can complete their required filings.</p>
                <table class="update-table">
                    <thead>
                        <tr>
                            <th>EIN#</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pdfData['wrongDateList'] as $chapter)
                            <tr>
                                <td>{{ $chapter->ein ?? '' }}</td>
                                <td>{{ $chapter->name ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if(count($pdfData['notFoundList']) > 0)
            <p>Below is a list of subordinates unable to file their 990N electronic postcards due to a "Not Found" status in the IRS system. <u><b>These chapters are good standing and should be added to our list of subordinates.  All chapters have the same fiscal year of July 1st-June 30th.</u></b></p>
            <table class="add-table">
                <thead>
                    <tr>
                        <th>EIN#</th>
                        <th>Name</th>
                        <th>Pres Name</th>
                        <th>Pres Address</th>
                        <th>Pres City</th>
                        <th>Pres State</th>
                        <th>Pres Zip</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pdfData['notFoundList'] as $chapter)
                        <tr>
                            <td>{{ $chapter->ein ?? '' }}</td>
                            <td>{{ $chapter->name ?? '' }}</td>
                            <td>{{ ($chapter->pres_first_name ?? '') . ' ' . ($chapter->pres_last_name ?? '') }}</td>
                            <td>{{ $chapter->pres_address ?? '' }}</td>
                            <td>{{ $chapter->pres_city ?? '' }}</td>
                            <td>{{ $chapter->pres_state ?? '' }}</td>
                            <td>{{ $chapter->pres_zip ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            <p>Thank you for your assistance in this matter.  If you have any questions, please contact me by phone or email.</p>
            <br>
            <p>Sincerely,</p>
            <br>
            <br>
            <p>{{ $pdfData['einName'] }}<br>
                EIN/990N Compliance<br>
                {{ $pdfData['einEmail'] }}<br>
                {{ $pdfData['einPhone'] }}<br>
                International MOMS Club<sub>&reg;</sub></p>
        </div>
</body>
</html>
