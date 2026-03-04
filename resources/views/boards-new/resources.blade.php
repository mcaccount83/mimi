@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

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

             @include('boards-new.partials.resources_accordion', ['resources' => $resources, 'resourceCategories' => $resourceCategories])

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
@endsection
@section('customscript')
@php $disableMode = 'disable-all'; @endphp
@include('layouts.scripts.disablefields')
@endsection
