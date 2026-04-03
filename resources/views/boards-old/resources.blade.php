@extends('layouts.board_theme')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

            <div class="col-md-12">
                 <div class="card">
                    <div class="card bg-primary">
                        <div class="card-body text-center">
                            <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                        </div>
                    </div>
                    <div class="card-body">
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center"> General Chapter Resources</h4>
                        </div>
                      </div>
                <!-- /.card -->
                </div>
            </div>
        </div>

            <div class="row">
                <div class="col-md-12">
                        <div class="card card-primary card-outline">
                    <div class="card-body">
                        <!-- /.card-header -->
                    <div class="card-body">
             @include('boards.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories])
            <br>
            <div class="card-body text-center mt-3">
                        @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Profile</button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Profile</a>
                    @endif
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board.viewelearning', ['id' => $chDetails->id]) }}'"><i class="bi bi-mortarboard-fill me-2" ></i>eLearning Library</button>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>
    </div>
<!-- /.container- -->
@endsection

