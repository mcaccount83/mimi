@extends('layouts.public_theme')

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
                                    <div class="col-md-12"><br><br></div>
                                    <h2 class="text-center">New Chapter Inquiry</h2>
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

                  <h3 class="profile-username text-center">Your Inquiry has been Successfully Submitted!</h3>
                <br>
                    <p>
                        Thank you for your inquiry. Our inquiries coordinator will be in touch soon.
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
