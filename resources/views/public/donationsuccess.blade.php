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
                                    <h2 class="text-center">Sustaining Chapter & Mother-to-Mother Fund Donations</h2>
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

                  <h3 class="profile-username text-center">Your Donation has been Received!</h3>
                <br>
                  <p>
                    Thank you for your donation.<br>
                    <br>
                     If you made a sustaining chapter donation, your contribution will make it possible for us to extend the MOMS Club opportunity to more and more mothers, as well as maintain our high quality of support for the MOMS Club chapters, not just in your local area, but across the country and around the world.<br>
                    <br>
                    If you made a donation to the Mother-to-Mother Fund, your contribution will be added to the fund for use when a personal or natural disaster strikes MOMS Club members.<br>
                    <br>
                    Your support of the International MOMS Club is both generous and most appreciated!<br>
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
