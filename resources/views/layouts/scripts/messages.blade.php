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

@if(View::shared('errors', false) != false && $errors->any())
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
