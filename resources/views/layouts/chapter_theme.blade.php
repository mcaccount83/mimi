<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="{{ asset('chapter_theme/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('chapter_theme/css/light-bootstrap-dashboard.css?v=2.0.1') }}" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="{{ asset('chapter_theme/css/demo.css') }}" rel="stylesheet" />
    <link href="{{ asset('chapter_theme/css/custom.css') }}" rel="stylesheet" />
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css'>
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

<body>
    <div class="wrapper">
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg " color-on-scroll="500">
                <div class=" container-fluid  ">
                    <a class="navbar-brand" href="#"> <img src="{{ asset('chapter_theme/img/logo.png') }}" alt=""> </a>
                    <div class="navbar-collapse justify-content-end" id="navigation">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home')}}" >
                                    <span class="no-icon">Chapter Profile</span>
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
                            <li class="">
                                <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                                           document.getElementById('logout-form').submit();">
                                                      <span>        {{ __('Logout') }}</span>

                                 </a>
                                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                              @csrf
                                 </form>
                              </li>
						</ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content">
                @yield('content')
            </div>
            <footer class="footer">
                <div class="container">
                    <nav>
                        <p class="copyright text-center">
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script>
                            <a href="https://momsclub.org/" target="_blank">MOMS Club</a>
                        </p>
                    </nav>
                </div>
            </footer>
        </div>
    </div>
</body>
<!--   Core JS Files   -->
<script src="{{ asset('chapter_theme/js/jquery.3.2.1.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('chapter_theme/js/popper.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('chapter_theme/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="{{ asset('chapter_theme/js/bootstrap-switch.js') }}"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!--  Chartist Plugin  -->
<script src="{{ asset('chapter_theme/js/chartist.min.js') }}"></script>
<!--  Notifications Plugin    -->
<script src="{{ asset('chapter_theme/js/bootstrap-notify.js') }}"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="{{ asset('chapter_theme/js/light-bootstrap-dashboard.js?v=2.0.1') }}" type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="{{ asset('chapter_theme/js/demo.js') }}"></script>
<script src="{{ asset('chapter_theme/js/bootstrap-fileselect.js') }}"></script>
<script src="{{ asset('chapter_theme/js/bootstrap-datepicker.min.js') }}"></script>
@yield('customscript')

</html>
