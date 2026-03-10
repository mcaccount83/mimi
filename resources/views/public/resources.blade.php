@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">
    <div class="row">
    {{-- @include('boards.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories]) --}}
    @include('partials.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories,
            'fiscalYear' => $fiscalYear,
            'fiscalYearEOY' => $fiscalYearEOY,
            'thisYearEOY' => $thisYearEOY,
            'lastYearEOY' => $lastYearEOY,
            'displayEOYTESTING' => $displayEOYTESTING,
            'displayEOYLIVE' => $displayEOYLIVE,
            'displayBoardRptLIVE' => $displayBoardRptLIVE,
            'displayFinancialRptLIVE' => $displayFinancialRptLIVE,
            'displayEINInstructionsLIVE' => $displayEINInstructionsLIVE,
            'yearColumnName' => $yearColumnName,
            'boardReportName' => $boardReportName,
            'financialReportName' => $financialReportName,
            'financialPDFName' => $financialPDFName,
            'irsFilingName' => $irsFilingName
            ])
    </div>
</div>

@endsection
