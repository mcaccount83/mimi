<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if (isset($thread_title))
            {{ $thread_title }} —
        @endif
        @if (isset($category))
            {{ $category->title }} —
        @endif
        {{ trans('forum::general.home_title') }}
    </title>

    @if (app()->environment('local'))
    @vite(['resources/forum/blade-bootstrap/css/forum.css', 'resources/forum/blade-bootstrap/js/forum.js'])
@else
    <link rel="stylesheet" href="{{ asset('public/build/assets/forum-BWCxAm8t.css') }}">
    <script src="{{ asset('public/build/assets/forum-0-39qVx8.js') }}" defer></script>
@endif


</head>
<body>
    {{-- <body style="background-color: #f0f0f0 !important;" class="hold-transition layout-top-nav"> --}}
        <div class="wrapper">
            @php
                $user = Auth::user();
                $userTypes = $user ? getUserType($user->user_type) : [
                    'coordinator' => false,
                    'board' => false,
                    'outgoing' => false,
                ];
            @endphp

              <!-- Navbar -->
      <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container">

            <!-- Left navbar links -->
            <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                @if($userTypes['board'])
                <ul class="navbar-nav">
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('home')}}" >
                        <span class="no-icon">MIMI Profile</span>
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
                </ul>
                {{-- @endif --}}
                @elseif($userTypes['coordinator'])
                <ul class="navbar-nav">
                    <li class="nav-item">
                    <a class="nav-link" href="{{ route('home')}}" >
                        <span class="no-icon">Coordinator Dashboard</span>
                    </a>
                    </li>
                </ul>
                @else
                <ul class="navbar-nav">
                </ul>
                @endif
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
                <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" style="width: 75px; height: 75px;">
            </a>
            @php
                $currentUrl = request()->url();
                $validUrls = [
                    url(config('forum.frontend.router.prefix')),
                    url(route('forum.category.manage'))
                ];
            @endphp

            @if (in_array($currentUrl, $validUrls))
                <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix')) }}">MOMS Club Forums</a>
            @elseif (!isset($category))
                @if (isset($thread))
                    <a class="navbar-brand" href="{{ Forum::route('category.show', $thread->category) }}">
                        <h2>{{ $thread->category->title }}</h2>
                    </a>
                @elseif (isset($threads))
                    @if($userTypes['coordinator'])
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.vollistLink')) }}">
                            Vollist
                        </a>
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                            | {{ config('forum.frontend.router.boardlistYear') }} BoardList
                        </a>
                    @else
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                            <h2>{{ config('forum.frontend.router.boardlistYear') }} BoardList</h2>
                        </a>
                    @endif
                @endif
                {{-- @elseif (isset($post))
                    @if($userTypes['coordinator'])
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.vollistLink')) }}">
                            Vollist
                        </a>
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                            | {{ config('forum.frontend.router.boardlistYear') }} BoardList
                        </a>
                    @else
                        <a class="navbar-brand" href="{{ url(config('forum.frontend.router.prefix') . config('forum.frontend.router.boardlistLink')) }}">
                            <h2>{{ config('forum.frontend.router.boardlistYear') }} BoardList</h2>
                        </a>
                    @endif
                @endif --}}
            @else

                <a class="navbar-brand" href="{{ Forum::route('category.show', $category) }}"><h2>{{ $category->title }}</h2></a>
            @endif
            {{-- <button class="navbar-toggler" type="button" :class="{ collapsed: isCollapsed }" @click="isCollapsed = !isCollapsed">
                <span class="navbar-toggler-icon"></span>
            </button> --}}
            <div class="collapse navbar-collapse" :class="{ show: !isCollapsed }">
                <ul class="navbar-nav me-auto">
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="{{ url(config('forum.frontend.router.prefix')) }}">{{ trans('forum::general.index') }}</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('forum.recent') }}">{{ trans('forum::threads.recent') }}</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forum.unread') }}">{{ trans('forum::threads.unread_updated') }}</a>
                        </li>
                    @endauth
                    @can ('moveCategories')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forum.category.manage') }}">{{ trans('forum::general.manage') }}</a>
                        </li>
                    @endcan
                </ul>
                {{-- <ul class="navbar-nav">
                    @if (Auth::check())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" @click="isUserDropdownCollapsed = !isUserDropdownCollapsed">
                                {{ $username }}
                            </a>
                            <div class="dropdown-menu" :class="{ show: !isUserDropdownCollapsed }" aria-labelledby="navbarDropdownMenuLink">
                                <a class="dropdown-item" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Log out
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/login') }}">Log in</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/register') }}">Register</a>
                        </li>
                    @endif
                </ul> --}}
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


    <script>
        window.defaultCategoryColor = '{{ config('forum.frontend.default_category_color') }}';
    </script>
</body>
</html>
