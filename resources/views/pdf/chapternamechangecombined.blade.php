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
    <!-- Fax Cover Page -->
    @include('pdf.faxcoverirsnamechange-content', ['pdfData' => $pdfData])

    <div class="page-break"></div>

    <!-- Name Change Letter Page -->
    @include('pdf.chapternamechange-content', ['pdfData' => $pdfData])
</body>
</html>
