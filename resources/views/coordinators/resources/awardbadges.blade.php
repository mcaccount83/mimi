@extends('layouts.mimi_theme')

@section('page_title', 'Resources')
@section('breadcrumb', 'Award Badges')

@section('content')
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                 <div class="card card-outline card-primary">
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Award Badges
                        </h3>
                         @include('layouts.dropdown_menus.menu_resources')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">

        <div class="row">
            <div class="col-12 mb-2">
            Award Badges available for EOY Reports.
            </div>
            @if($canEditFiles)
                <div class="col-12 mb-2">
                    <button type="button" class="btn btn-success bg-gradient btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-add-badge"><i class="bi bi-plus-lg me-2"></i>Add Award</button>
                </div>
            @endif
        </div>

        <div class="row">
             @include('coordinators.partials.awards_accordion', ['reportYears' => $reportYears, 'eoyAwards' => $eoyAwards])
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

  @include('coordinators.partials.awards_accordion_models', ['reportYears' => $reportYears, 'eoyAwards' => $eoyAwards])

</section>
<!-- /.content -->
@endsection
@section('customscript')

@endsection
