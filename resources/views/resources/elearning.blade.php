@extends('layouts.coordinator_theme')

@section('page_title', 'eLearning Library')
@section('breadcrumb', 'eLearning Library')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
   <!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                  <div class="dropdown">
                      <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        eLearning Library
                      </h3>
                      @include('layouts.dropdown_menus.menu_resources')
                  </div>
              </div>
              <!-- /.card-header -->
          <div class="card-body">
        <div class="row">
            <p>&nbsp;&nbsp;eLearning Courses available for Coordinators and Board Members.</p>
        </div>


        <div class="row">

        <div class="col-md-12">

                    <div class="row">
                        <div class="col-md-4">

                        <div class="grid-item">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Coordinator Courses</h3>
                                </div>
                                <div class="card-body">

                           @if(isset($coordinatorCoursesByCategory) && count($coordinatorCoursesByCategory) > 0)
                                @foreach($coordinatorCoursesByCategory as $coordinatorCategorySlug => $categoryData)
                                    <div class="mb-4">
                                        <h4 class="text-lg font-bold mb-2">
                                            {{ $categoryData['name'] }}
                                        </h4>
                                        <ul class="space-y-2">
                                            @foreach($categoryData['courses'] as $coordinatorCourse)
                                                <li>
                                                    <a href="{{ $coordinatorCourse['auto_login_url'] }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 text-lg">
                                                        {{ $coordinatorCourse['title']['rendered'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            @else
                                <p>No coordinator courses found for your user type.</p>
                            @endif
                        </div>
                                      </div>
                            </div>
                        </div>

                    <div class="col-md-4">
                        <div class="grid-item">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Board Courses</h3>
                                </div>
                                <div class="card-body">
                                    @if(isset($boardCoursesByCategory) && count($boardCoursesByCategory) > 0)
                                        @foreach($boardCoursesByCategory as $boardCategorySlug => $categoryData)
                                            <div class="mb-4">
                                                <h4 class="text-lg font-bold mb-2">
                                                    {{ $categoryData['name'] }}
                                                </h4>
                                                <ul class="space-y-2">
                                                    @foreach($categoryData['courses'] as $boardCourse)
                                                        <li>
                                                            <a href="{{ $boardCourse['auto_login_url'] }}" target="_blank"
                                                            class="text-blue-600 hover:text-blue-800 text-lg">
                                                                {{ $boardCourse['title']['rendered'] }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>No board courses found for your user type.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
                </div>
            </div>

        </div>
        </div>

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});



</script>
@endsection
