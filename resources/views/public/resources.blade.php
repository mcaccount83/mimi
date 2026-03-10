@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">
    <div class="row">
    {{-- @include('boards.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories]) --}}
    @include('partials.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories, 'fiscalYearEOY' => $fiscalYearEOY,
            'thisYearEOY' => $thisYearEOY, 'lastYearEOY' => $lastYearEOY,])
    </div>
</div>

@endsection
