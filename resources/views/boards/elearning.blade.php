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
                        <h2 class="text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h3 class="text-center">eLearning Library - Board Courses</h3>
                    </div>
                </div>
                <!-- /.card -->
                </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary card-outline">
                         <div class="card-body">
                        <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                        <div class="col-md-12">
                            @if(isset($boardCoursesByCategory) && count($boardCoursesByCategory) > 0)
                                @foreach($boardCoursesByCategory as $categorySlug => $categoryData)
                                        <h4 class="text-lg font-bold mb-2">
                                            {{ $categoryData['name'] }}
                                        </h4>
                                        <ul class="space-y-2">
                                            @foreach($categoryData['courses'] as $course)
                                                <li class="mb-2 d-flex align-items-center gap-2">
                                                    <a href="{{ $course['auto_login_url'] }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 text-lg">
                                                        {{ $course['title']['rendered'] }}
                                                    </a>
                                                    @if(!empty($course['progress']) && $course['progress']['status'] === 'completed')
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
                                                    @elseif(!empty($course['progress']) && $course['progress']['status'] === 'in_progress')
                                                        <div class="progress mt-2" style="height: 8px;">
                                                            <div class="progress-bar bg-primary" style="width: {{ $course['progress']['percent'] }}%"></div>
                                                        </div>
                                                        <small class="text-muted">{{ $course['progress']['percent'] }}% complete ({{ $course['progress']['steps_completed'] }}/{{ $course['progress']['steps_total'] }} steps)</small>
                                                    @else
                                                        <span class="badge bg-secondary">Not Started</span>
                                                    @endif
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

                <br>
                <div class="card-body text-center mt-3">
                          @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                                <button type="button" id="btn-back" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Profile</button>
                            @else
                                <a href="{{ route('home') }}" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-arrow-left-short"></i><i class="bi bi-house-fill me-2"></i>Back to Profile</a>
                            @endif
                        <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board.viewresources', ['id' => $chDetails->id]) }}'"><i class="bi bi-briefcase-fill me-2" ></i>Chapter Resources</button>
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


