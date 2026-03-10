@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">
    <div class="row">
    {{-- @include('boards.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories]) --}}
    @include('partials.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories])

    </div>
</div>

@endsection
