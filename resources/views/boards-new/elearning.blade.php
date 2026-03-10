@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
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

             <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>eLearning Library</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">
 <div class="row">
    @include('partials.elearning_board_accordion')
    </div>

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
@if($userTypeId == \App\Enums\UserTypeEnum::COORD)
    @php $disableMode = 'disable-all'; @endphp
    @include('layouts.scripts.disablefields')
@endif
@endsection

