<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/dist/css/skins/_all-skins.min.css') }}">
    <!-- Morris chart -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/morris.js/morris.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/jvectormap/jquery-jvectormap.css') }}">
    <!-- Date Picker -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
	<!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/plugins/iCheck/all.css') }}">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/dist/css/custom.css') }}">
    <!-- Custom CSS for Financial Report -->
    <link rel="stylesheet" href="{{ asset('chapter_theme/css/custom_financial.css') }}">
    <!-- Data Table -->
    <link rel="stylesheet" href="{{ asset('coordinator_theme/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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
  <header class="main-header">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>M</b>C</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b><img src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt=""></b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
           <li class="dropdown user user-menu">
            <a href="https://momsclub.org/" target="_blank" class="hidden-xs">Return to Main Site</a>
          </li>
         </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header"></li>

        @php

        @endphp

        {{-- <li class="{{ Request::is('boardlist') ? 'active' : '' }}  ">
            <a href="{{ route('boardlist.index') }}">
              <i class="fa fa-list"></i> <span>BoardList</span>
            </a>
          </li>

          <li class="{{ Request::is('#') ? 'active' : '' }}  ">
            <a href="{{ route('boardlist.index') }}">
              <i class="fa fa-files-o"></i> <span>Discussion Topics</span>
            </a>
          </li>

          <li class="{{ Request::is('#') ? 'active' : '' }}  ">
            <a href="{{ route('boardlist.index') }}">
              <i class="fa fa-files-o"></i> <span>My Posts</span>
            </a>
          </li> --}}




          <li class="{{ Request::is('coordinator/dashboard') ? 'active' : '' }}  ">
            <a href="{{ route('coordinator.showdashboard') }}">
              <i class="fa fa-home"></i> <span>MIMI (Chapter Profile)</span>
            </a>
          </li>

        <div class="too" style="padding-left:15px; padding-top:9px;"><a href="https://momsclub.org/" target="_blank"><i class="fa fa-globe"></i>&nbsp;&nbsp;&nbsp;&nbsp;MOMS Club Website</a></div>

        <div class="too" style="padding-left:15px; padding-top:20px;"><a href="https://momsclub.org/resources/" target="_blank"><i class="fa fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Chapter Resources</a></div>

        <div class="too" style="padding-left:15px; padding-top:20px;"><a href="https://momsclub.org/elearning/" target="_blank"><i class="fa fa-graduation-cap"></i>&nbsp;&nbsp;eLearning Library</a></div>

        <div class="too" style="padding-left:15px; padding-top: 20px;">

        <li class="">
          <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i> <span> &nbsp;&nbsp; {{ __('Logout') }}</span>

           </a>
		    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
           </form>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
  <div class="content-wrapper">
   @yield('content')
   </div>
  <footer class="main-footer">
        <strong>Copyright &copy; <?php echo date('Y');?> <a href="https://momsclub.org/" target="_blank">MOMS Club</a>.</strong> All rights
    reserved.
  </footer>
</div>
</body>

<!-- jQuery 3 -->
<script src="{{ asset('coordinator_theme/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- DataTables -->
<script src="{{ asset('coordinator_theme/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('coordinator_theme/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<!-- Morris.js charts -->
<script src="{{ asset('coordinator_theme/bower_components/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/bower_components/morris.js/morris.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset('coordinator_theme/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('coordinator_theme/bower_components/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('coordinator_theme/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('coordinator_theme/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset('coordinator_theme/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<!-- Slimscroll -->
<script src="{{ asset('coordinator_theme/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{ asset('coordinator_theme/plugins/iCheck/icheck.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('coordinator_theme/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('coordinator_theme/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('coordinator_theme/dist/js/pages/dashboard.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('coordinator_theme/dist/js/demo.js') }}"></script>

<script>
   //iCheck for checkbox and radio inputs
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass   : 'iradio_minimal-blue'
    })
    //Red color scheme for iCheck
    $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
      checkboxClass: 'icheckbox_minimal-red',
      radioClass   : 'iradio_minimal-red'
    })
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false
    })
</script>

<script type="text/javascript">
//format dates in tables to display as MM-DD-YYYY but stil sort correctly
function initializeDataTable(selector, options, columnDefs) {
    $(selector).DataTable({
        ...options,
        columnDefs: columnDefs.map(def => {
            if (def.type === 'date') {
                return {
                    ...def,
                    render: function (data, type, row) {
                        if (type === 'sort') {
                            return row[def.targets]; // Use original date for sorting
                        }
                        return def.format ? moment(data).format(def.format) : data || def.noPayment || ''; // Format date for display if specified
                    }
                };
            }
            return def;
        })
    });
}

$(document).ready(function() {
    initializeDataTable('#chapterlist', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false
    }, []);

    initializeDataTable('#chapterlist_reReg', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 6, type: 'date', format: null },
        {
            targets: 7, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_einStatus', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 2, type: 'date', format: null },
    ]);

    initializeDataTable('#chapterlist_inteinStatus', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 3, type: 'date', format: null },
    ]);

    initializeDataTable('#chapterlist_large', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        {
            targets: 4, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_donation', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        {
            targets: 4,
            render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }, // Note the comma here
        {
            targets: 6,
            render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#chapterlist_review', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        {
            targets: 9, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);

    initializeDataTable('#coordinatorlist', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false
    }, []);

    initializeDataTable('#coordinatorlist_birthday', {
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        order: [[0, 'asc']]
    }, [
        { targets: 5, type: 'date', format: null },
        {
            targets: 6, render: function(data, type, row) {
                if (type === 'display' && data === 'Invalid date') {
                    return null;
                }
                return data;
            }
        }
    ]);
});
</script>
@yield('customscript')
</html>
