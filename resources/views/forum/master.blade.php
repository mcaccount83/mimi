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

    <!-- Bootstrap (https://github.com/twbs/bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Feather icons (https://github.com/feathericons/feather) -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

    <!-- Vue (https://github.com/vuejs/vue) -->
    @if (config('app.debug'))
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    @else
        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    @endif

    <!-- Axios (https://github.com/axios/axios) -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <!-- Pickr (https://github.com/Simonwep/pickr) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

    <!-- Sortable (https://github.com/SortableJS/Sortable) -->
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.10.1/Sortable.min.js"></script>
    <!-- Vue.Draggable (https://github.com/SortableJS/Vue.Draggable) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.23.2/vuedraggable.umd.min.js"></script>

     <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/font-awesome/css/font-awesome.min.css') }}">

    <style>
    body
    {
        height: 100%;
        background: #f8fafc;
    }

    .logo {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%; /
        padding-top: 10px;
        margin-bottom: -10px;
    }

    .logo-lg img {
        width: 45px;
        display: block;
    }

    .logo-padding {
        padding-top: 5px;
    }

    textarea
    {
        min-height: 200px;
    }

    table tr td
    {
        white-space: nowrap;
    }

    a
    {
        text-decoration: none;
    }

    .deleted
    {
        opacity: 0.65;
    }

    #main
    {
        padding: 2em;
    }

    .shadow-sm
    {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card.category
    {
        margin-bottom: 1em;
    }

    .list-group .list-group
    {
        min-height: 1em;
        margin-top: 1em;
    }

    .btn svg.feather
    {
        width: 16px;
        height: 16px;
        stroke-width: 3px;
        vertical-align: -2px;
    }

    .modal-title svg.feather
    {
        margin-right: .5em;
        vertical-align: -3px;
    }

    .category .subcategories
    {
        background: #fff;
    }

    .category > .list-group-item
    {
        z-index: 1000;
    }

    .category .subcategories .list-group-item:first-child
    {
        border-radius: 0;
    }

    .timestamp
    {
        border-bottom: 1px dotted var(--bs-gray);
        cursor: help;
    }

    .fixed-bottom-right
    {
        position: fixed;
        right: 0;
        bottom: 0;
    }

    .fade-enter-active, .fade-leave-active
    {
        transition: opacity .3s;
    }
    .fade-enter, .fade-leave-to
    {
        opacity: 0;
    }

    .mask
    {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: rgba(50, 50, 50, .2);
        opacity: 0;
        transition: opacity .2s ease;
        z-index: 1020;
    }
    .mask.show
    {
        opacity: 1;
    }

    .form-check
    {
        user-select: none;
    }

    .sortable-chosen
    {
        background: var(--bs-light);
    }

    @media (max-width: 575.98px)
    {
        #main
        {
            padding: 1em;
        }
    }

    .icon-padding {
        padding-right: 5px;
    }
    </style>
    <script>
        window.onload = function () {
            if (window.history && window.history.pushState) {
                window.history.pushState('preventBack', null, '');
                window.onpopstate = function () {
                    location.reload();
                };
            }
        };
    </script>
</head>

<body class="hold-transition skin-blue sidebar-mini fixed">
    <div class="wrapper">
        @php
            $adminReportCondition = ($positionid == 13 || $secpositionid == 13);  //*IT Coordinator
            $listAdminCondition = ($positionid == 23 || $secpositionid == 23);  //*ListAdmin
            $user = auth()->user();
            if ($user) {
                $userType = $user->user_type;
                $isActive = $user->is_active;
            }
        @endphp
        <header class="main-header">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <a class="navbar-brand" href="{{ url(config('forum.web.router.prefix')) }}">BoardList</a>
                    @auth
                    @if (($userType == 'coordinator' || $userType == 'board') && $isActive == 1)
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url(config('forum.web.router.prefix')) }}">{{ trans('forum::general.index') }}</a>
                        </li>
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
                    @endif
                    @endauth
                  </nav>
        </header>
 <!-- Left side column. contains the logo and sidebar -->
 <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="logo">
        <span class="logo-lg logo-padding"><b><img src="{{ asset('coordinator_theme/dist/img/logo.png') }}" alt="MIMI"></b></span>
    </a>
    <hr>
<!-- Sidebar -->
<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu">
@auth
@if (isset($user) && ($userType == 'coordinator' || $userType == 'board') && $isActive == 1)
<li class="nav-item menu-open {{ Request::is('boardlist') ? 'active' : '' }} {{ Request::is('boardlist/*') ? 'active' : '' }}">
    <a href="{{ url(config('forum.web.router.prefix')) }}"
        class="nav-link {{ Request::is('boardlist') ? 'active' : '' }} {{ Request::is('boardlist/*') ? 'active' : '' }}">
      <i class="fa fa-home icon-padding"></i>
      <p>BoardList</p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item {{ Request::is('boardlist') ? 'active' : '' }}">
        <a href="{{ url(config('forum.web.router.prefix')) }}" class="nav-link {{ Request::is('boardlist') ? 'active' : '' }}">
          <i class="fa fa-list icon-padding"></i>
          <p>Index</p>
        </a>
      </li>
      <li class="nav-item {{ Request::is('boardlist/recent') ? 'active' : '' }}">
        <a href="{{ route('forum.recent') }}" class="nav-link {{ Request::is('boardlist/recent') ? 'active' : '' }}">
          <i class="fa fa-list icon-padding"></i>
          <p>Recent</p>
        </a>
      </li>
      <li class="nav-item {{ Request::is('boardlist/unread') ? 'active' : '' }}">
        <a href="{{ route('forum.unread') }}" class="nav-link {{ Request::is('boardlist/unread') ? 'active' : '' }}">
          <i class="fa fa-list icon-padding"></i>
          <p>Unread</p>
        </a>
      </li>
      @if ($adminReportCondition || $listAdminCondition)
        <li class="nav-item {{ Request::is('boardlist/manage') ? 'active' : '' }}">
            <a href="{{ route('forum.category.manage') }}" class="nav-link {{ Request::is('boardlist/manage') ? 'active' : '' }}">
            <i class="fa fa-list icon-padding"></i>
            <p>Manage</p>
            </a>
        </li>
     @endif
    </ul>
  </li>
<hr>
  <li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link">
      <i class="fa fa-user icon-padding"></i>
      <p>MIMI Chapter Profile</p>
    </a>
  </li>
  @endif
  @endauth
  <li class="nav-item">
    <a href="https://momsclub.org" class="nav-link">
      <i class="fa fa-globe icon-padding"></i>
      <p>MOMS Club Website</p>
    </a>
  </li>
  @auth
  @if (isset($user) && ($userType == 'coordinator' || $userType == 'board') && $isActive == 1)
    <li class="nav-item">
    <a href="https://momsclub.org/elearning/" class="nav-link">
      <i class="fa fa-graduation-cap icon-padding"></i>
      <p>eLearning Library</p>
    </a>
  </li>
  @endif
    <li class="nav-item">
    <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();
                                               document.getElementById('logout-form').submit();">
                          <i class="fa fa-sign-out icon-padding"></i> <span>        {{ __('Logout') }}</span>
     </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                  @csrf
     </form>
        </li>
@endauth
    </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<div class="content-wrapper">
    <div id="main" class="container">
        @auth
        @if (isset($user) && ($userType == 'coordinator' || $userType == 'board') && $isActive == 1)
        @include('forum.partials.breadcrumbs')
        @include('forum.partials.alerts')
        @yield('content')
        @endif
        @endauth

        @if (isset($user) && $userType == 'outgoing')
        <div class="col-md-12">
            <center><p>The BoardList is the only official MOMS Club sanctioned group and is for our current
                <?php echo $a = date('Y'); echo "-"; echo $a + 1;?> local chapter board members only.</p></center>
            </div>
        @endif

        @guest
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                <center><p>The BoardList is the only official MOMS Club sanctioned group and is for our current
                    <?php echo $a = date('Y'); echo "-"; echo $a + 1;?> local chapter board members only.</p></center>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ isset($url) ? ucwords($url) : ""}} {{ __('Login') }}</div>
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="card-body">
                             @isset($url)
                                <form id="loginForm" method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                                @else
                                <form id="loginForm" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                                @endisset
                                @csrf
                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember Me') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-primary" onClick="this.form.submit(); this.disabled=true;">
                                            {{ __('Login') }}
                                        </button>
                                        @if (Route::has('password.request'))
                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                {{ __('Forgot Your Password?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endguest

    </div>
</div>
    <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y');?> <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights
    reserved.
  </footer>
</div>
    <div class="mask"></div>
</body>

    <script>
    new Vue({
        el: '.v-navbar',
        name: 'Navbar',
        data: {
            isCollapsed: true,
            isUserDropdownCollapsed: true
        },
        methods: {
            onWindowClick (event) {
                const ignore = ['navbar-toggler', 'navbar-toggler-icon', 'dropdown-toggle'];
                if (ignore.some(className => event.target.classList.contains(className))) return;
                if (! this.isCollapsed) this.isCollapsed = true;
                if (! this.isUserDropdownCollapsed) this.isUserDropdownCollapsed = true;
            }
        },
        created: function () {
            window.addEventListener('click', this.onWindowClick);
        }
    });

    const mask = document.querySelector('.mask');

    function findModal (key)
    {
        const modal = document.querySelector(`[data-modal=${key}]`);

        if (! modal) throw `Attempted to open modal '${key}' but no such modal found.`;

        return modal;
    }

    function openModal (modal)
    {
        modal.style.display = 'block';
        mask.style.display = 'block';
        setTimeout(function()
        {
            modal.classList.add('show');
            mask.classList.add('show');
        }, 200);
    }

    document.querySelectorAll('[data-open-modal]').forEach(item =>
    {
        item.addEventListener('click', event =>
        {
            event.preventDefault();

            openModal(findModal(event.currentTarget.dataset.openModal));
        });
    });

    document.querySelectorAll('[data-modal]').forEach(modal =>
    {
        modal.addEventListener('click', event =>
        {
            if (! event.target.hasAttribute('data-close-modal')) return;

            modal.classList.remove('show');
            mask.classList.remove('show');
            setTimeout(function()
            {
                modal.style.display = 'none';
                mask.style.display = 'none';
            }, 200);
        });
    });

    document.querySelectorAll('[data-dismiss]').forEach(item =>
    {
        item.addEventListener('click', event => event.currentTarget.parentElement.style.display = 'none');
    });

    document.addEventListener('DOMContentLoaded', event =>
    {
        const hash = window.location.hash.substr(1);
        if (hash.startsWith('modal='))
        {
            openModal(findModal(hash.replace('modal=','')));
        }

        feather.replace();

        const input = document.querySelector('input[name=color]');

        if (! input) return;

        const pickr = Pickr.create({
            el: '.pickr',
            theme: 'classic',
            default: input.value || null,

            swatches: [
                '{{ config('forum.web.default_category_color') }}',
                '#f44336',
                '#e91e63',
                '#9c27b0',
                '#673ab7',
                '#3f51b5',
                '#2196f3',
                '#03a9f4',
                '#00bcd4',
                '#009688',
                '#4caf50',
                '#8bc34a',
                '#cddc39',
                '#ffeb3b',
                '#ffc107'
            ],

            components: {
                preview: true,
                hue: true,
                interaction: {
                    input: true,
                    save: true
                }
            },

            strings: {
                save: 'Apply'
            }
        });

        pickr
            .on('save', instance => pickr.hide())
            .on('clear', instance =>
            {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            })
            .on('cancel', instance =>
            {
                const selectedColor = instance
                    .getSelectedColor()
                    .toHEXA()
                    .toString();

                input.value = selectedColor;
                input.dispatchEvent(new Event('change'));
            })
            .on('change', (color, instance) =>
            {
                const selectedColor = color
                    .toHEXA()
                    .toString();

                input.value = selectedColor;
                input.dispatchEvent(new Event('change'));
            });
    });
    </script>
    {{-- @yield('footer') --}}
{{-- </body> --}}
</html>
