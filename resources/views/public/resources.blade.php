@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">
    <div class="row">
    @include('boards.resources_columns', ['resources' => $resources, 'resourceCategories' => $resourceCategories])

    </div>
</div>
@endsection
