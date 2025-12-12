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
                                    <h2 class="text-center">New Chapter Application</h2>
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
                    Here are a few things to keep in mind as you start your MOMS Club journey.
                    <ul>
                        <li>All chapters are in PENDING status until reviewed by our Coordintaor Team.</li>
                        <li>After review, you will receive an email from your Coordinator to establish initial communication as well as verify/set your official chapter name
                            and boundaries.</li>
                        <li>After communication has been established, your credit card will be charged, your chapter will move to ACTIVE status and your official MOMS Club chapter manual will be shipped.</li>
                        <li>There are no refunds after the payment has been processed.</li>
                        <li>Check your application status any time by logging into MIMI with the credentials you set up during the application process. </li>
                        <li>In MIMI you will also see your Coordinator's contact information.  If you do not hear from them within a week of submitting your application, please reach out to them
                            directly as sometimes messages do end up in spam.</li>
                        <li>After your chapter has moved to ACTIVE status you'll see your MIMI options change to allow more access and infomration, but your login credentials will remain the same.</li>
                    </ul>
                    Log into MIMI - <a href="http://momsclub.org/mimi" target="_blank">http://momsclub.org/mimi</a><br>
                    Return to the Main Website - <a href="http://momsclub.org" target="_blank">http://momsclub.org</a>
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
