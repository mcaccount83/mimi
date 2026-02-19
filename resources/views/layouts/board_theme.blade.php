<!doctype htms>
<htms lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/htms; charset=utf-8" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- jQuery as classic sync script - MUST be before Vite modules -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vite Compiled Assets -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/flash.js'])

    <!-- Google Recaptcha -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>

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
    window.onload = function () {
        if (window.history && window.history.pushState) {
            window.history.pushState('preventBack', null, '');
            window.onpopstate = function () {
                location.reload();
            };
        }
    };
</script>

<style>
    .ms-2 {
        margin-left: 0.5rem !important; /* Adjust the margin to control spacing for Vacant Buttons */
    }

    .form-check-input:checked ~ .form-check-label {
        color: black; /* Label color when toggle is ON for Vacant Buttons */
    }

    .form-check-input:not(:checked) ~ .form-check-label {
        color: #b0b0b0; /* Subdued label color when toggle is OFF for Vacant Buttons */
        opacity: 0.6;   /* Optional: Adds a subdued effectfor Vacant Buttons */
    }

    .disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #6c757d; /* Muted color */
    }

    .board-info {
        display: table;
        width: 100%;
        margin-bottom: 15px;
    }
    .info-row {
        display: table-row;
    }
    .info-label, .info-label-empty {
        display: table-cell;
        width: 150px;
        padding: 2px 10px 2px 0;
        vertical-align: top;
    }
    .info-label {
        font-weight: bold;
    }
    .info-data {
        display: table-cell;
        padding: 2px 0;
    }

    h1, h2, h3, h4, h5, h6 {
    font-weight: normal !important;
}

h1 { font-size: 2rem !important; }
h2 { font-size: 1.75rem !important; }
h3 { font-size: 1.5rem !important; }
h4 { font-size: 1.25rem !important; }
h5 { font-size: 1rem !important; }
h6 { font-size: 0.875rem !important; }
</style>

</head>
  <!--end::Head-->
  <!--begin::Body-->
<body style="background-color: #f0f0f0 !important;" class="hold-transition layout-top-nav">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        @php
            $user = Auth::user();
            $userName = $user->first_name.' '.$user->last_name;
            $userEmail = $user->email;
            $userTypeId = $user->type_id;
        @endphp

    <!-- Navbar -->
  <nav class="app-header navbar navbar-expand-md navbar-light navbar-white">
        <div class="container-fluid">

    @if($userTypeId == \App\Enums\UserTypeEnum::BOARD)

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('home')}}" >
                <span class="no-icon">MIMI Profile</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('board.viewresources', ['id' => $chDetails->id]) }}'">
                <span class="no-icon">Chapter Resources</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://momsclub.org/elearning/" target="_blank">
                <span class="no-icon">eLearning Library</span>
            </a>
          </li>
        </ul>
      </div>
      @endif

    @if($userTypeId != \App\Enums\UserTypeEnum::COORD)
      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ms-auto">

        <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               <span class="no-icon">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

      </ul>
      @endif

      @if($userTypeId == \App\Enums\UserTypeEnum::COORD)
      @php
          // Assuming you're already on a chapter edit page and the 'id' is available in the route.
          $id = request()->route('id'); // Get the current chapter ID from the route
      @endphp

      @if ($id) <!-- Check if $id is not null -->
          <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ms-auto">
              <li class="nav-item">
                @if ($chDetails->active_status == \App\Enums\ChapterStatusEnum::PENDING)
                <a class="nav-link" href="{{ route('chapters.editpending', ['id' => $id]) }}">
                      <span class="no-icon">Back to Coordinator View / Chapter Details</span>
                  </a>
                @else
                <a class="nav-link" href="{{ route('chapters.view', ['id' => $id]) }}">
                      <span class="no-icon">Back to Coordinator View / Chapter Details</span>
                  </a>
                @endif
              </li>
          </ul>
      @endif
  @endif

    </div>
  </nav>
  <!-- /.navbar -->

 <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
      <!--begin::Container-->
          <div class="container-fluid">
        @if($ITCondition == 1 )
            <p class="description text-center"><span style="color: red;">You are Viewing Chapter Pages as an Admin Coordinator -- All Information is Editable just as it is for Chapter Members.</p>
        @elseif($userTypeId == \App\Enums\UserTypeEnum::COORD && $ITCondition != 1)
            <p class="description text-center"><span style="color: red;">You are Viewing Chapter Pages as a Coordinator -- All Information is READ ONLY.</p>
        @endif
      </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->

           @yield('content')

     </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
    <!--begin::Copyright-->
    <div class="float-end d-none d-sm-inline">
        Copyright &copy;{{$currentYear}} <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights reserved.
    </div>
    <!--end::Copyright-->
  </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

<!-- Sript Functions -->
@include('layouts.scripts.alert')
@include('layouts.scripts.bdcoordinatorlist')
@include('layouts.scripts.boards')
@include('layouts.scripts.boardreport')
@include('layouts.scripts.datetime')
@include('layouts.scripts.email')
@include('layouts.scripts.masks')
@include('layouts.scripts.password')
@include('layouts.scripts.pdfviewer')
@include('layouts.scripts.uploads')
@include('layouts.scripts.recaptcha')

@include('layouts.scripts.website')

@yield('customscript')
@stack('scripts')

</body>
</htms>
