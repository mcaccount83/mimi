<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css" rel="stylesheet">
    <link href="{{ config('settings.base_url') }}resources/forum/blade-bootstrap/css/forum.css" rel="stylesheet">

    <title>
        @if (isset($thread_title))
            {{ $thread_title }} —
        @endif
        @if (isset($category))
            {{ $category->title }} —
        @endif
        {{ trans('forum::general.home_title') }}
    </title>

    {{-- @vite(['resources/forum/blade-bootstrap/css/forum.css', 'resources/forum/blade-bootstrap/js/forum.js']) --}}



</head>
<body>
        <div class="wrapper">
            @php
                $user = Auth::user();
                $userTypes = $user ? getUserType($user->user_type) : [
                    'coordinator' => false,
                    'board' => false,
                    'outgoing' => false,
                    'disbanded' => false,
                ];
            @endphp

              <!-- Navbar -->
      <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">

            <!-- Left navbar links -->
            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                <ul class="navbar-nav">
                    @if($userTypes['board'])
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home')}}" >
                                <span class="no-icon">MIMI Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                                <span class="no-icon">BoardList</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('board.resources')}}" >
                                <span class="no-icon">Chapter Resources</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://momsclub.org/elearning/" target="_blank">
                                <span class="no-icon">eLearning Library</span>
                            </a>
                        </li>
                    @elseif($userTypes['coordinator'])
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home')}}" >
                                <span class="no-icon">Coordinator Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.coordinatorlistLink')) }}">
                                <span class="no-icon">CoordinatorList</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                                <span class="no-icon">BoardList</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.publicannouncementslink')) }}">
                            <span class="no-icon">Public Announcements</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Right navbar links -->
            <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                @if (Auth::check())
                    <li class="nav-item">
                        <a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Log out
                        </a>
                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/login') }}">Log in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/register') }}">Register</a>
                    </li>
                @endif
            </ul>
          </div>
        </nav>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <div class="content">

    <nav class="v-navbar navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a href="{{ route('home') }}" class="band-link">
                <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 75px; height: 75px; margin-right: 10px">
            </a>
            @php
                $currentUrl = request()->url();
                $validUrls = [
                    url(config('forum.frontend.router.prefix')),
                    url(route('forum.category.manage'))
                ];
            @endphp

            @if (in_array($currentUrl, $validUrls))
                <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix')) }}"><h2>List Index</h2></a>
            @elseif (!isset($category))
                @if (isset($thread))
                    <a class="navbar-brand" href="{{ Forum::route('category.show', $thread->category) }}">
                        <h2>{{ $thread->category->title }}</h2>
                    </a>
                @elseif (isset($threads))
                    @if($userTypes['coordinator'])
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.coordinatorlistLink')) }}">
                            <h2>CoordinatorList</h2>
                        </a>
                        {{-- <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                            | {{ config('forum.frontend.router.boardlistYear') }} BoardList
                        </a> --}}
                    @else
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                            <h2>{{ config('forum.frontend.router.boardlistYear') }} BoardList</h2>
                        </a>
                    @endif
                @endif
            @else

                <a class="navbar-brand" href="{{ Forum::route('category.show', $category) }}"><h2>{{ $category->title }}</h2></a>
            @endif
            <div class="collapse navbar-collapse" :class="{ show: !isCollapsed }">
                <ul class="navbar-nav me-auto">
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ route('forum.recent') }}">{{ trans('forum::threads.recent') }}</a>
                    </li> --}}
                    {{-- @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forum.unread') }}">{{ trans('forum::threads.unread_updated') }}</a>
                        </li>
                    @endauth --}}
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forum.unread') }}">Unread Threads</a>
                        </li>
                    @endauth
                    @can ('moveCategories')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forum.category.manage') }}">{{ trans('forum::general.manage') }}</a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
    </nav>

    <div id="main" class="container">
        @include ('forum::partials.breadcrumbs')
        @include ('forum::partials.alerts')

        @yield('content')
    </div>

    <div class="mask"></div>

</div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div><center>
            Copyright &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>
            <a href="https://momsclub.org/" target="_blank">MOMS Club</a>. &nbsp;All rights reserved.
        </center></div>

  @yield('footer')

</footer>

</div>
</div>
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
</body>
</html>
