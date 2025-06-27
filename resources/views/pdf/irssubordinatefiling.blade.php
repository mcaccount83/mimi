<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRS Subordinate Filing</title>
    <style>
        @page {
            size: landscape;
            margin: 0.5in 0.5in 0.25in 0.5in; /* top, right, bottom, left */
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 50px; /* leave space for fixed header */
            padding-bottom: 30px; /* leave space for page numbers */
            font-size: 10px;
        }

        .page-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
        }

        .header-line {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .header-left {
            display: table-cell;
            text-align: left;
            width: 50%;
            font-weight: bold;
            font-size: 12px;
        }

        .header-right {
            display: table-cell;
            text-align: right;
            width: 50%;
            font-weight: bold;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 6px 8px;
            text-align: left;
            font-size: 9px;
        }

        th {
            background-color: #f5f5f5;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }
    </style>
</head>
<body>
    <!-- Page header shown on every page -->
    <div class="page-header">
        <div class="header-line">
            <span class="header-left">SUBORDINATE UPDATES TO GROUP</span>
            <span class="header-right">INTERNATIONAL MOMS CLUB</span>
        </div>
        <div class="header-line">
            {{-- <span class="header-left">{{ strtoupper($month ?? 'MONTH') }} {{ $year ?? 'YEAR' }}</span> --}}
            <span class="header-left">{{ $startFormatted }} - {{ $todayFormatted }}</span>
            <span class="header-right">TAX ID#77-0125681 GEN# 3706</span>
        </div>
    </div>

    <!-- Main table content -->
    <table>
        <thead>
            <tr>
                <th>Notes</th>
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
            @foreach($chapterList as $chapter)
                <tr>
                    <td>{{ $chapter->notes_column ?? '' }}</td>
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
</body>
</html>
