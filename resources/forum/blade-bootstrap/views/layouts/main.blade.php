@extends('layouts.mimi_theme')

@if (isset($thread_title))
    @section('page_title', $thread_title)
    @section('breadcrumb', $thread_title)
@elseif (isset($category))
    @section('page_title', $category->title)
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
                                        {{ $category->title}}
                                    @else
                                        {{ trans('forum::general.home_title')}}
                                    @endif
                                </h3>
                                @include('layouts.dropdown_menus.menu_forum')
                            </div>

                             @php
                                $forumMenuLabel = match(true) {
                                    request()->routeIs('forum.recent')                    => 'Recent',
                                    request()->routeIs('forum.unread')                    => 'Unread Threads',
                                    request()->routeIs('forum.category.manage')           => 'Manage Categories',
                                    request()->routeIs('forum.pending-approval.threads')  => 'Threads pending approval',
                                    request()->routeIs('forum.pending-approval.posts')    => 'Posts pending approval',
                                    default                                               => 'Forum Menu',
                                };
                            @endphp
                            <div class="dropdown ms-3">
                                <button type="button" id="forumMenuDropdown" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $forumMenuLabel }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="forumMenuDropdown">
                                    <li><a class="dropdown-item" href="{{ route('forum.recent') }}">Recent</a></li>
                                    @auth
                                        <li><a class="dropdown-item" href="{{ route('forum.unread') }}">Unread Threads</a></li>
                                    @endauth
                                    @if (Gate::allows('moveCategories') || Gate::allows('approveThreads') || Gate::allows('approvePosts'))
                                        <li><hr class="dropdown-divider"></li>
                                        @can('moveCategories')
                                            <li><a class="dropdown-item" href="{{ route('forum.category.manage') }}">Manage Categories</a></li>
                                        @endcan
                                        @can('approveThreads')
                                            <li><a class="dropdown-item" href="{{ route('forum.pending-approval.threads') }}">{{ trans('forum::threads.pending_approval') }}
                                                @if($pendingThreadsCount > 0)
            <span class="badge bg-danger ms-2">{{ $pendingThreadsCount }} Pending</span>
        @endif</a></li>
                                        @endcan
                                        @can('approvePosts')
                                            <li><a class="dropdown-item" href="{{ route('forum.pending-approval.posts') }}">{{ trans('forum::posts.pending_approval') }}
                                                @if($pendingPostsCount > 0)
            <span class="badge bg-danger ms-2">{{ $pendingPostsCount }} Pending</span>
        @endif</a></li>
                                        @endcan
                                    @endif
                                </ul>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="v-navbar" style="display:none">
                                <div class="collapse navbar-collapse" :class="{ show: !isCollapsed }"></div>
                            </div>
                            @include ('forum::partials.breadcrumbs')
                            @include ('forum::partials.alerts')
                            @yield('forum_content')  {{-- ← DIFFERENT NAME --}}
                        </div>
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
