@extends('layouts.mimi_theme')

@if (isset($thread_title))
    @section('page_title', $thread_title)
    @section('breadcrumb', $thread_title)
@elseif (isset($category))
    @if ( $category->title == 'BoardList')
        @section('page_title', $fiscalYear.' '.$category->title)
    @else
        @section('page_title', $category->title)
    @endif
    @section('breadcrumb', $category->title)
@else
    @section('page_title', trans('forum::general.home_title'))
    @section('breadcrumb', trans('forum::general.home_title'))
@endif

@push('styles')
    @vite(['resources/forum/blade-bootstrap/css/forum.css'])
@endpush

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                @if(isset($chDetails))
                    <input type="hidden" id="ch-details-id" value="{{ $chDetails->id }}">
                @endif
                <div class="col-12">
                    <div class="card card-outline card-primary">
                            <div class="card-header d-flex align-items-center">
                                <div class="dropdown">
                                    <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @if (isset($category) || isset($thread_title))
                                            @if ($category->title == 'BoardList')
                                                {{ $fiscalYear }} {{ $category->title }}
                                            @else
                                                {{ $category->title }}
                                            @endif
                                        @else
                                            {{ trans('forum::general.home_title') }}
                                        @endif
                                    </h3>
                                    @include('layouts.dropdown_menus.menu_forum')
                                </div>
                                @include('layouts.dropdown_menus.menu_forum_extra')
                            </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="v-navbar" style="display:none">
                                <div class="collapse navbar-collapse" :class="{ show: !isCollapsed }"></div>
                            </div>
                            @include ('forum::partials.breadcrumbs')
                            @include ('forum::partials.alerts')
                            @yield('forum_content')  {{-- ← DIFFERENT NAME --}}
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="mask"></div>
@endsection
@push('scripts')
@vite(['resources/forum/blade-bootstrap/js/forum.js'])

<!-- Just before closing </body> tag -->
<script>
    window.defaultCategoryColor = '{{ config('forum.frontend.default_category_color') }}';
</script>
@endpush
