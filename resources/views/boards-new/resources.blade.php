@extends('layouts.mimi_theme')

@section('content')
     <div class="container">
        <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
              <div class="card-body">
                <div class="card-header text-center bg-transparent">
                    <h2 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                    {{-- <p class="mb-0">{{ $conferenceDescription }} Conference, {{ $conferenceDescription }} Region --}}
                        <br>
                        <h3>General Chapter Resources</h3>
                    </div>
                        <!-- /.card-header -->
                    <div class="card-body">
             @include('boards-new.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories])

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

