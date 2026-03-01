<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- jQuery as classic sync script - MUST be before Vite modules -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Flash Messages as meta tags --}}
    @if ($message = Session::get('success'))
        <meta name="flash-success" content="{{ $message }}">
    @endif
    @if ($message = Session::get('info'))
        <meta name="flash-info" content="{{ $message }}">
    @endif
    @if ($message = Session::get('warning'))
        <meta name="flash-warning" content="{{ $message }}">
    @endif
    @if ($message = Session::get('fail'))
        <meta name="flash-fail" content="{{ $message }}">
    @endif
    @if(View::shared('errors', false) != false && $errors->any())
        <meta name="flash-errors" content="<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>">
    @endif

    <script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(form => {
        form.setAttribute('autocomplete', 'off');
    });
});

    // window.onload = function () {
    //     if (window.history && window.history.pushState) {
    //         window.history.pushState('preventBack', null, '');
    //         window.onpopstate = function () {
    //             location.reload();
    //         };
    //     }
    // };
    </script>

    @include('layouts.styles.buttonsicons')
    @include('layouts.styles.coordsidebar')
    @include('layouts.styles.datatable')
    @include('layouts.styles.fonts')
    @include('layouts.styles.forms')
    @include('layouts.styles.sweetalert')

  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
    <div class="app-wrapper">
      <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!-- Navbar Link -->
            <li class="nav-item">
                <a class="nav-link"  href="https://momsclub.org/" target="_blank" >Return to Main Site</a>
            </li>
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
           <a href="{{ route('home') }}" class="brand-link d-flex justify-content-center align-items-center">
                <img src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" class="custom-logo">
            </a>
          <!--end::Brand Link-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">

            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">
              <!-- Menu for Logged In Users -->
                    @auth

                    @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                        @include('layouts.sidebars.coordinator')
                    @elseif ($userTypeId == \App\Enums\UserTypeEnum::BOARD)
                        @include('layouts.sidebars.board')
                    @elseif ($userTypeId == \App\Enums\UserTypeEnum::OUTGOING)
                        @include('layouts.sidebars.outgoing')
                    @elseif ($userTypeId == \App\Enums\UserTypeEnum::PENDING)
                        @include('layouts.sidebars.pending')
                    @endif


                        <li class="nav-item">
                            <a href="{{ route('logout') }}" class="nav-link"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-icon bi bi-box-arrow-right"></i>
                                <p>Logout</p>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>

                        @else
                        <!-- Menu for Non-Logged In Users -->

                        <br>
                       <li style="padding: 0 20px;">
                    @isset($url)
                    <form method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                    @else
                    <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                    @endisset

                    @csrf

                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="Email">
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                            <div class="input-group-text">
                                <span class="bi bi-envelope-fill"></span>
                            </div>
                    </div>

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Password">
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                            <div class="input-group-text">
                                <span class="bi bi-lock-fill"></span>
                            </div>
                    </div>

                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-secondary bg-gradient" onClick="this.form.submit(); this.disabled=true;">{{ __('Login') }}</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
                </li>
                 <li class="nav-item">
                            <a href="{{ route('login') }}" class="nav-link">
                                <i class="nav-icon fas fa-sign-in-alt"></i>
                                <p>Main Login Page</p>
                            </a>
                        </li>
                    </form>

                        </li>
             @endauth
                    </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->
      <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6"><h2 class="mb-0">@yield('page_title')</h2></div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                  <li class="breadcrumb-item active" aria-current="page">@yield('breadcrumb')</li>
                </ol></li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">

            @yield('content')

        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
       <footer class="app-footer">
            <strong>Copyright &copy;{{$currentYear}} <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights reserved.
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/flash.js'])

    @include('layouts.scripts.alert')
    @include('layouts.scripts.boards')
    @include('layouts.scripts.boardreport')
    @include('layouts.scripts.boardactivate')
    @include('layouts.scripts.checkboxes')
    @include('layouts.scripts.coordinatorlist')
    @include('layouts.scripts.datatable')
    @include('layouts.scripts.datetime')
    @include('layouts.scripts.email')
    @include('layouts.scripts.export')
    @include('layouts.scripts.information')
    @include('layouts.scripts.irs')
    @include('layouts.scripts.masks')
    @include('layouts.scripts.menu')
    @include('layouts.scripts.password')
    @include('layouts.scripts.pdfviewer')
    @include('layouts.scripts.probation')
    @include('layouts.scripts.resources')
    @include('layouts.scripts.sendemail')
    @include('layouts.scripts.uploads')
    @include('layouts.scripts.useractions')
    @include('layouts.scripts.website')

    @yield('customscript')
    @stack('scripts')

  </body>
  <!--end::Body-->
</html>
