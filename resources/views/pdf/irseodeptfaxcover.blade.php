<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['title'] }}</title>
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
    </style>
</head>
<body>
    <div class="keep-together" style="page-break-inside: avoid;">
        <center><h1>FAX</h1></center>
        <table>
            <tr>
                <td class="label">DATE:</td>
                <td>{{ $pdfData['currentDateWords'] }}</td>
            </tr>
            <tr>
                <td class="label">PAGES:</td>
                <td> {{ $pdfData['totalPages'] }} Pages (including cover)</td>
            </tr>
            <tr>
                <td class="label">TO:</td>
                <td>Internal Revenue Service, EO Entity</td>
            </tr>
            <tr>
                <td class="label">FAX:</td>
                <td>855-214-7520</td>
            </tr>
            <tr>
                <td class="label">FROM:</td>
                <td>International MOMS Club<br>
                    {{ $pdfData['einName'] }}, EIN/990N Compliance</td>
            </tr>
            <tr>
                <td class="label">EMAIL:</td>
                <td>{{ $pdfData['einEmail'] }}</td>
            </tr>
            <tr>
                <td class="label">PHONE:</td>
                <td>{{ $pdfData['einPhone'] }}</td>
            </tr>
            <tr>
                <td class="label">FAX:</td>
                <td>254-237-2791</td>
            </tr>
            <tr>
                <td class="label">REGARDING:</td>
                <td>International MOMS Club<br>
                    Taxpayer ID Number:  77-0125681<br>
                    GEN Number:  3706</td>
            </tr>
        </table>
        <table>
            <tr>
                <td>
                    <br>
                    {!! $pdfData['message'] !!}<br>
                    <br>
                    Thank you.<br>
                    <br>
                    {{ $pdfData['followPages'] }} pages to follow.<br>
                    <br>
                </td>
            </tr>
        </table>
   </div>

    <!-- Fixed footer -->
    <div class="footer">
        The information contained in this transmission may contain confidential information, including patient information protected under federal and state law. This document is intended for the Internal Revenue Service only. If you are not the intended recipient, you are hereby notified that any disclosure or distribution of this information is in violation of HIPAA confidentiality and prohibited. If you are not the intended recipient, please contact the sender by reply email and delete all copies. This fax disclaimer is present on the cover sheet and serves as a warning to ensure privacy.
    </div>

</body>
</html>

