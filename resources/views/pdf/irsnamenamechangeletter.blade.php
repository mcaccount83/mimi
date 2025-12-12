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
            <br>
            <p>{{ $pdfData['currentDateWords'] }}</p>
            <br>
            <p>Internal Revenue Service<br>
                Ogden, UT  84201</p>
            <p><b>Subordinate Update/Name Change</b><br>
                Taxpayer ID: 77-0125681<br>
                Gen Number: 3706</p>
            <p>The below subordinate has changed their name and contact. Their IRS records need to be updated so they can request a new EIN verification letter. Below is their updated information:</p>
            <p><b>{{ $pdfData['chapterEIN'] }}</b><br>
                MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }}<br>
                (formerly known as MOMS Club of {{ $pdfData['chNamePrev'] }}, {{ $pdfData['chapterState'] }})<br>
                c/o {{$pdfData['presName']}}<br>
                {{$pdfData['presAddress']}}<br>
                {{$pdfData['presCity']}}, {{$pdfData['presState']}} {{$pdfData['presZip']}}</p>
            <p>Thank you for your assistance in this matter.  If you have any questions, please contact me by phone or email.  Unless I receive information from your office
                to the contrary, I will instruct our subordinate to contact the IRS for a new EIN verification letter after {{ $pdfData['twoMonthsDateWords'] }}.</p>
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
    </div>
    </body>
</html>
