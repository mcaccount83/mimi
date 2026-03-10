@extends('layouts.mimi_theme')

@section('page_title', 'Resources')
@section('breadcrumb', 'eLearning Library')

<style>
    #accordion-courses .accordion-button::after {
    display: none;
}
</style>
@section('content')
   <!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                  <div class="dropdown">
                      <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        eLearning Library
                      </h3>
                      @include('layouts.dropdown_menus.menu_resources')
                  </div>
              </div>
              <!-- /.card-header -->
          <div class="card-body">



    <h4>Coordinator Courses</h4>
 <div class="row">
    @include('coordinators.partials.elearning_coord_accordion')
    </div>
<br>
    <h4>Board Member Courses</h4>
 <div class="row">
    @include('partials.elearning_board_accordion')
    </div>

 </div>
   </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
