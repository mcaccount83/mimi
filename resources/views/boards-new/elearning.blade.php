@extends('layouts.coordinator_theme')

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
                        <h3>eLearning Library - Board Courses</h3>
                    </div>
                        <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                        <div class="col-md-12">
                            @if(isset($boardCoursesByCategory) && count($boardCoursesByCategory) > 0)
                                @foreach($boardCoursesByCategory as $categorySlug => $categoryData)
                                        <h4 class="mb-2">
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
                                @endforeach
                            @else
                                <p>No courses found for your user type.</p>
                            @endif
                        </div>
                    </div>



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


