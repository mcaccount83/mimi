<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{config('app.name')}}</title>

     <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free/css/all.min.css"> --}}
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/solid.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/brands.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/css/css/v5-font-face.css" rel="stylesheet" />
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<!-- Bootstrap Switch -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/bootstrap-switch/css/bootstrap-switch.min.css">
    <!-- BS Stepper -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/bs-stepper/css/bs-stepper.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.css">
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
    .disabled-link {
pointer-events: none; /* Prevent click events */
cursor: default; /* Change cursor to default */
color: #6c757d; /* Muted color */
}
</style>

</head>
<body>

<body style="background-color: #f0f0f0 !important;" class="hold-transition layout-top-nav">
    <div class="wrapper">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <!-- Main content -->
    <div class="content">
        @if ($message = Session::get('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    @if ($message = Session::get('info'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'info',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'warning',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

    @if ($message = Session::get('fail'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: @json($message),
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>
    @endif

        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'There were some errors!',
                        html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                        showConfirmButton: true,
                    });
                });
            </script>
        @endif

        @yield('content')
    </div>

  <!-- Main Footer -->
  {{-- <footer class="main-footer"> --}}
    <!-- To the right -->
    {{-- <div class="float-right d-none d-sm-inline">
        Copyright &copy;
        <script>
            document.write(new Date().getFullYear())
        </script>
        <a href="https://momsclub.org/" target="_blank">MOMS Club</a>. &nbsp;All rights reserved.
    </div> --}}
    <!-- Default to the left -->

  {{-- </footer> --}}
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ config('settings.base_url') }}theme/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome -->
<script defer src="{{ config('settings.base_url') }}theme/plugins/fontawesome-free-6.7.2/js/all.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- Bootstrap Switch -->
<script src="{{ config('settings.base_url') }}theme/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="{{ config('settings.base_url') }}theme/plugins/bs-stepper/js/bs-stepper.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- InputMask -->
<script src="{{ config('settings.base_url') }}theme/plugins/moment/moment.min.js"></script>
<script src="{{ config('settings.base_url') }}theme/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="{{ config('settings.base_url') }}theme/plugins/daterangepicker/daterangepicker.js"></script>
</body>


    <script>
        $(document).ready(function() {
            $("input[data-bootstrap-switch]").bootstrapSwitch();
        });
    </script>

<script>
   $(function () {
    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy hh:mm:ss
    $('#datemask2').inputmask('mm/dd/yyyy hh:mm:ss', { 'placeholder': 'mm/dd/yyyy hh:mm:ss' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#datepicker').datetimepicker({
        format: 'L'
    });
     //Date picker
     $('#datepicker1').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })

    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })


  })
</script>

<script>
    function openPdfViewer(filePath) {
        var base_url = '{{ url("/pdf-viewer") }}';
        window.open(base_url + '?id=' + encodeURIComponent(filePath), '_blank');
    }
</script>

<script>
     function convertDateFormat(dateString) {
        var parts = dateString.split('-');
        return parts[1] + '/' + parts[2] + '/' + parts[0];
    }

    function applyDateMask() {
        $('.date-mask').each(function() {
            var originalDate = $(this).text();
            var formattedDate = convertDateFormat(originalDate);
            $(this).text(formattedDate);
        });
        Inputmask({"mask": "99/99/9999"}).mask(".date-mask");
    }

    $(document).ready(function() {
        var table = $('#coordinatorlist').DataTable();

        applyDateMask();

        table.on('draw', function() {
            applyDateMask();
        });
    });


    function applyPhoneMask() {
        Inputmask({"mask": "(999) 999-9999"}).mask(".phone-mask");
    }

    function applyHttpMask() {
        Inputmask({"mask": "http://*{1,250}"}).mask(".http-mask");
    }

    $(document).ready(function() {
        var table = $('#chapterlist').DataTable();

        applyPhoneMask();

        table.on('draw', function() {
            applyPhoneMask();
        });

        applyDateMask();

        table.on('draw', function() {
            applyDateMask();
        });

        applyHttpMask();

        table.on('draw', function() {
            applyHttpMask();
        });

    });

      function applyHttpMask() {
          Inputmask({"mask": "http://*{1,250}"}).mask(".http-mask");
      }

        //Cusotmize AJAX Popups to Match Theme
    function customSuccessAlert(message) {
        Swal.fire({
            title: 'Success',
            html: message,
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customWarningAlert(message) {
        Swal.fire({
            title: 'Warning',
            html: message,
            icon: 'warning',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customInfoAlert(message) {
        Swal.fire({
            title: 'Info',
            html: message,
            icon: 'info',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

    function customErrorAlert(message) {
        Swal.fire({
            title: 'Error',
            html: message,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'btn-sm btn-success', // Match your theme button class
            },
            buttonsStyling: false // Disable default button styling
        });
    }

</script>

@yield('customscript')

@stack('scripts')

</html>
