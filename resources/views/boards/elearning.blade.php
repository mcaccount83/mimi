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
                        <h2 class="text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center">eLearning Library - Board Courses</h4>


                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>

                <div class="col-md-12">
    <div class="card card-primary card-outline">

      <div class="col-md-12">
    <div class="card-body">
        @if(isset($boardCoursesByCategory) && count($boardCoursesByCategory) > 0)
            @foreach($boardCoursesByCategory as $categorySlug => $categoryData)
                <div class="mb-4">
                    <h4 class="text-lg font-bold mb-2">
                        {{ $categoryData['name'] }}
                    </h4>
                    <ul class="space-y-2">
                        @foreach($categoryData['courses'] as $course)
                            <li>
                                <a href="{{ $course['auto_login_url'] }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-lg">
                                    {{ $course['title']['rendered'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        @else
            <p>No courses found for your user type.</p>
        @endif
    </div>
</div>
        </div>
    </div>
   <br>
                <div class="card-body text-center">
                         @if ($userTypeId == 'coordinator')
                            <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                        @else
                            <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back to Profile</a>
                        @endif
                        <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('board.viewresources', ['id' => $chDetails->id]) }}'"><i class="fas fa-toolbox mr-2" ></i>Chapter Resources</button>
                    </div>

    </div>
    <!-- /.container- -->
@endsection


