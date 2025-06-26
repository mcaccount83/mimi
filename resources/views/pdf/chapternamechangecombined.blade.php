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
    <!-- Fax Cover Page -->
    @include('pdf.chapternamechangefaxcover', ['pdfData' => $pdfData])

    <div class="page-break"></div>

    <!-- Name Change Letter Page -->
    @include('pdf.chapternamechange', ['pdfData' => $pdfData])
</body>
</html>
