@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">
    <div class="row">
    {{-- @include('boards.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories]) --}}
    @include('partials.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories,
            'fiscalYearRange' => $fiscalYearRange,
            'reportYearRange' => $reportYearRange,
            'reportYearStart' => $reportYearStart,
            'reportYearEnd' => $reportYearEnd,
            'yearColumnName' => $yearColumnName,
            'boardReportName' => $boardReportName,
            'financialReportName' => $financialReportName,
            'financialPDFName' => $financialPDFName,
            'irsFilingName' => $irsFilingName,
            'displayEOYTESTING' => ($display_testing && ! $display_live),
            'displayEOYLIVE' => ($display_live && $currentMonth >= 5 && $currentMonth <= 12),
            'displayBoardRptLIVE' => ($display_live && $currentMonth >= 5 && $currentMonth <= 9),
            'displayFinancialRptLIVE' => ($display_live && $currentMonth >= 6 && $currentMonth <= 12),
            'displayEINInstructionsLIVE' => ($display_live && $currentMonth >= 7 && $currentMonth <= 12),
            ])
    </div>
</div>

@endsection
