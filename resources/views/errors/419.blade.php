<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>419 Page expired</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  {{-- <link rel="stylesheet" href="{{ config('settings.base_url') }}coordinator_theme/dist/css/adminlte.min.css"> --}}

  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    .content-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      margin: 0;
    }
    .error-page {
      text-align: center;
    }
  </style>

</head>
<body class="layout-top-nav">
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

    <!-- Main content -->
    <section class="content">
      <div class="error-page">
        <h2 class="headline text-warning"> 419</h2>

        <div class="error-content">
          <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page expired.</h3>

          <p>
            We will work on fixing that right away.<br>
            Meanwhile, you may <a href="{{ route('home') }}">return to the home page.</a>
        </p>
        </div>
        <!-- /.error-content -->
      </div>
      <!-- /.error-page -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
</body>

<!-- jQuery -->
<script src="{{ config('settings.base_url') }}coordinator_theme/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ config('settings.base_url') }}coordinator_theme/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ config('settings.base_url') }}coordinator_theme/dist/js/adminlte.min.js"></script>
</body>
</html>
