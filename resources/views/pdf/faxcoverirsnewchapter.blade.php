<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} | Chapter Name Change</title>
    <style>
        .page-break {
            page-break-after: always;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid black;
        }

        td {
            padding: 8px 12px;
            text-align: left;
        }

        .label {
            font-weight: bold;
            width: 150px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="keep-together" style="page-break-inside: avoid;">
        <center><h1>FAX</h1></center>
        <table>
            <tr>
                <td class="label">DATE:</td>
                <td>{{ $pdfData['todayDate'] }}</td>
            </tr>
            <tr>
                <td class="label">PAGES:</td>
                <td>2 (including cover)</td>
            </tr>
            <tr>
                <td class="label">TO:</td>
                <td>Internal Revenue Service</td>
            </tr>
            <tr>
                <td class="label">FAX:</td>
                <td>855-641-6935</td>
            </tr>
            <tr>
                <td class="label">FROM:</td>
                <td>International MOMS Club<br>
                {{ $pdfData['ccName'] }}, Conference Coordinator</td>
            </tr>
            <tr>
                <td class="label">EMAIL:</td>
                <td>{{ $pdfData['ccEmail'] }}</td>
            </tr>
            <tr>
                <td class="label">PHONE:</td>
                <td>{{ $pdfData['ccPhone'] }}</td>
            </tr>
            <tr>
                <td class="label">FAX:</td>
                <td>254-237-2791</td>
            </tr>
            <tr>
                <td class="label">REGARDING:</td>
                <td>SS-4 APPLICATION FOR SUBORDINATE</td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    SS4 – EIN application for (1) subordinates.<br>
                    <br>
                    Please note, <b>this is a new chapter, in no way affiliated with any former chapter that may have been registered in the same town, or with the same name in the past.<b>
                    Other than this newly registered chapter, there is currently no International MOMS Club subordinate in {{ $pdfData['chapterCity'] }}, {{ $pdfData['chapterState'] }}.
                    <br>
                    Thank you.<br>
                    <br>
                    1 page to follow.<br>
                    <br>
                </td>
            </tr>
        </table>
    <h6>
        The information contained in this transmission may contain confidential information, including patient information protected under federal and state law. This document is intended for the Internal Revenue Service only. If you are not the intended recipient, you are hereby notified that any disclosure or distribution of this information is in violation of HIPAA confidentiality and prohibited. If you are not the intended recipient, please contact the sender by reply email and delete all copies. This fax disclaimer is present on the cover sheet and serves as a warning to ensure privacy.
    </h6>
    </div>
</body>
</html>

