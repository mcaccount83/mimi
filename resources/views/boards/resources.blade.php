@extends('layouts.board_theme')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
         <!-- Widget: user widget style 1 -->
         <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                        </div>
                        <div class="card-body">

                    <div class="col-md-12"><br><br></div>
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center"> General Chapter Resources</h4>
                        </div>
                    </div>
                </div>

        <div class="container-fluid">
            <div class="row">
             @include('boards.resources_columns', ['resources' => $resources, 'resourceCategories' => $resourceCategories])
            <br>
            <div class="card-body text-center">
                        @if ($userType == 'coordinator')
                        <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back to Profile</a>
                    @endif
                    <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('board.viewelearning', ['id' => $chDetails->id]) }}'"><i class="fas fa-graduation-cap mr-2" ></i>eLearning Library</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container- -->
@endsection

