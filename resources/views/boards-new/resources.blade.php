@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Resources')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

             <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>General Chapter Resources</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                            <div class="card-body text-center mb-3">
                                <a href="https://momsclub.org" target="_blank" rel="noopener noreferrer" class="btn btn-primary bg-gradient mb-2">
                                    <i class="bi bi-globe-americas me-2"></i>Main MC Website</a>
                                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('board-new.viewelearning', ['id' => $chDetails->id]) }}'"><i class="bi bi-mortarboard-fill me-2"></i>eLearning Library</button>
                            </div>
             @include('partials.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories])

               </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
            </div>
            <!-- /.col -->
       </div>
    </div>
    <!-- /.container- -->
      @include('partials.resources_accordion_models', ['resources' => $resources, 'resourceCategories' => $resourceCategories])

@endsection
@section('customscript')

@endsection
