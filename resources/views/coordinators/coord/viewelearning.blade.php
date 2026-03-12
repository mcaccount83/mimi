@extends('layouts.mimi_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Appreciation & Recognition')

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updaterecognition",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <div class="card card-primary card-outline">
                 <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                        <h3 class="mb-0">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                        <p class="mb-0">{{ $conferenceDescription }} Conference
                            @if ($regionLongName != "None")
                                , {{ $regionLongName }} Region
                            @endif
                        </p>
                    </div>
                  <ul class="list-group list-group-flush mb-3">
                      <li class="list-group-item">
                        @include('coordinators.partials.coordinatorpositions')
                      </li>
                      <li class="list-group-item">
                          @include('coordinators.partials.coordinatordates')
                      </li>
                <li class="list-group-item mt-3">
                     @include('coordinators.partials.coordinatorstatus')
                </li>
                  </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <div class="card-header bg-transparent border-0">
                <h3>eLearning Course Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                       <div class="row">
    @forelse($coursesByCategory as $categorySlug => $categoryData)
        <div class="col-md-6">
            <label class="mb-2">{{ $categoryData['name'] }}</label>
            <ul class="list-unstyled mb-3">
                @foreach($categoryData['courses'] as $course)
                    <li class="mb-2 d-flex align-items-center gap-2">
                        <span>{{ $course['title']['rendered'] }}</span>
                        @if(!empty($course['progress']) && $course['progress']['status'] === 'completed')
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
                        @elseif(!empty($course['progress']) && $course['progress']['status'] === 'in_progress')
                            <div>
                                <div class="progress" style="height: 8px; width: 80px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $course['progress']['percent'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $course['progress']['percent'] }}% ({{ $course['progress']['steps_completed'] }}/{{ $course['progress']['steps_total'] }})</small>
                            </div>
                        @else
                            <span class="badge bg-secondary">Not Started</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <p class="text-muted">No courses found.</p>
    @endforelse
</div>

                        </div>
                    </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordreports.coordrptelearning') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-gift-fill me-2"></i>Back to eLearning Report</button>
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('coordinators.view', ['id' => $cdDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-people-fill me-2"></i>Back to Coordinator Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection

