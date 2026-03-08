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

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
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
                                    // request()->routeIs('forum.index')           => 'Forum Index',
                                    request()->routeIs('forum.recent')          => 'Recent',
                                    request()->routeIs('forum.unread')          => 'Unread Threads',
                                    request()->routeIs('forum.category.manage') => 'Manage',
                                    default                                     => 'Forum Menu',
                                };
                            @endphp
                            <div class="dropdown ms-3">
                                <button type="button" id="forumMenuDropdown" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ $forumMenuLabel }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="forumMenuDropdown">
                                    {{-- <li><a class="dropdown-item" href="{{ url(config('forum.frontend.router.prefix')) }}">Forum Index</a></li> --}}
                                    <li><a class="dropdown-item" href="{{ route('forum.recent') }}">Recent</a></li>
                                    @auth
                                        <li><a class="dropdown-item" href="{{ route('forum.unread') }}">Unread Threads</a></li>
                                    @endauth
                                    @can('moveCategories')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('forum.category.manage') }}">Manage Categories</a></li>
                                    @endcan
                                </ul>
                            </div>

                        </div>
                        <div class="card-body">
                            @include('forum::partials.breadcrumbs')
                            @include('forum::partials.alerts')
                            @yield('forum_content')  {{-- ← DIFFERENT NAME --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')

<!-- Just before closing </body> tag -->
<script>
    window.ForumApp = {};
</script>

<script src="https://cdn.jsdelivr.net/npm/vue@3.2.47/dist/vue.global.prod.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.1.0/dist/vuedraggable.umd.js"></script>

<script>
    // Define global app object
    window.ForumApp = {};

    // Wait for all scripts to load
    Promise.all([
        new Promise(resolve => {
            if (window.Vue) resolve();
            else document.querySelector('script[src*="vue"]').onload = resolve;
        }),
        new Promise(resolve => {
            if (window.vuedraggable) resolve();
            else document.querySelector('script[src*="vuedraggable"]').onload = resolve;
        })
    ]).then(() => {
        window.axios = axios;
        window.Vue = Vue;
        window.VueDraggable = window.vuedraggable;

        // Load forum.js
        const forumScript = document.createElement('script');
        forumScript.src = '{{ config('settings.base_url') }}resources/forum/blade-bootstrap/js/forum.js';
        document.body.appendChild(forumScript);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generic modal handler for all modals
        const allOpenButtons = document.querySelectorAll('[data-open-modal]');
        const allModals = document.querySelectorAll('[data-modal]');
        const mask = document.querySelector('.mask');

        if (allOpenButtons.length > 0) {
            // Add click handlers to all open buttons
            allOpenButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const modalId = this.getAttribute('data-open-modal');
                    const modal = document.querySelector(`[data-modal="${modalId}"]`);

                    if (modal) {
                        modal.classList.add('show');
                        modal.style.display = 'block';
                        if (mask) {
                            mask.style.display = 'block';
                            mask.classList.add('show');
                        }
                    }
                    return false;
                });
            });

            // Add close handlers to all modals
            allModals.forEach(modal => {
                const closeButtons = modal.querySelectorAll('[data-close-modal]');
                closeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        if (mask) {
                            mask.classList.remove('show');
                            mask.style.display = 'none';
                        }
                    });
                });
            });
        } else {
            console.log('Modal elements found:', {
                openButtons: allOpenButtons.length,
                modals: allModals.length,
                mask: mask ? 'yes' : 'no'
            });
        }
    });
    </script>

    @endpush
