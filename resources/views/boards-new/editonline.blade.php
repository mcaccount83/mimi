@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'Chapter Profile')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

            <form id="boardinfo" method="POST" action="{{ route('board-new.updateonline',$chDetails->id) }}">
                        @csrf

             <div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                             <h3>Online Accounts</h3>
                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                           <!-- /.form group -->
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Website:</label>
                                <div class="col-sm-6">
                                    <input type="text" name="ch_website" id="ch_website" class="form-control"
                                        value="{{$chDetails->website_url}}"
                                        placeholder="Chapter Website">
                                </div>
                            </div>

                            <!-- Website Status Container - Hidden by default -->
                            <div class="row mb-3" id="ch_webstatus-container" style="display: none;">
                                <label class="col-sm-2 col-form-label">Link Status:</label>
                                <div class="col-sm-3">
                                    <select name="ch_webstatus" id="ch_webstatus" class="form-control" style="width: 100%;">
                                        <option value="">Select Status</option>
                                        @foreach($allWebLinks as $status)
                                            <option value="{{$status->id}}"
                                                @if($chDetails->website_status == $status->id) selected @endif>
                                                {{$status->link_status}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        <!-- /.form group -->
                        <div class="row mb-3">
                             <label class="col-sm-2 col-form-label">Social Media:</label>
                                <div class="col-sm-2">
                                <input type="text" name="ch_onlinediss" id="ch_onlinediss" class="form-control" value="{{ $chDetails->egroup }}"  placeholder="Forum/Group/App" >
                                </div>
                                <div class="col-sm-2">
                                <input type="text" name="ch_social1" id="ch_social1" class="form-control" value="{{ $chDetails->social1 }}" placeholder="Facebook"  >
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="ch_social2" id="ch_social2" class="form-control" value="{{ $chDetails->social2 }}"  placeholder="Twitter" >
                                </div>
                                <div class="col-sm-2">
                                    <input type="text" name="ch_social3" id="ch_social3" class="form-control" value="{{ $chDetails->social3 }}"  placeholder="Instagram" >
                                </div>
                        </div>

                          <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                        <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save</button>
                       </div>
                        </div>

            </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

            </form>

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
