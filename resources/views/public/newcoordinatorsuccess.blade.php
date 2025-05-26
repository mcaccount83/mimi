@extends('layouts.public_theme')

<style>

</style>

@section('content')
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" method="POST" action='{{ route("public.updatenewchapter") }}'>
                        @csrf

                        <div class="col-md-12">
                            <div class="card card-widget widget-user">
                                <div class="widget-user-header bg-primary">
                                    <div class="widget-user-image">
                                        <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $thisDate = \Illuminate\Support\Carbon::now();
                                    @endphp
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Coordinator Application</h2>
                                    </h2>

                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                    <div class="col-md-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                 <!-- /.card-header -->
                            <div class="row">
                                <div class="col-md-12">

                  <h3 class="profile-username text-center">Your Application has been Successfully Submitted!</h3>
                <br>
                  <p>
                   We are excited that you have decided to become a MOMS Club Coordinator!  You will receive a follow up email from your Conference Coordinator to discuss your area's
                   specific needs and where you best fit into their team. However, if you have any questions in the meantime, please do not hesitate to reach out and ask!<br>
                    </p>
                </div>
            </div>

            <hr>

    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>

</div>
<!-- /.container- -->
@endsection
@section('customscript')
<script>

</script>
@endsection
