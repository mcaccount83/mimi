@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Boundary Issues&nbsp;<small>(View)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Boundary Issues</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("eoyreports.eoyupdateboundaries",$chapterList[0]->id) }}'>
    @csrf
   <section class="content">
     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Boundary Issues Reported by Chapter</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control"  required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()" disabled>
              </div>
              </div>
              <!-- /.form group -->
            <div class="col-sm-4">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
             <div class="col-sm-12">
              <div class="form-group">
                <label>Current Boundaires in MIMI</label>
                <input type="text" name="ch_territory" class="form-control" value="{{ $chapterList[0]->territory }}"  required onkeypress="return isAlphanumeric(event)" disabled >
			</div>
			</div>
			<div class="col-sm-12">
              <div class="form-group">
                <label>Current Boundaires in MIMI (edit HERE)</label>
                <input type="text" name="ch_territory" class="form-control" value="{{ $chapterList[0]->territory }}"  required onkeypress="return isAlphanumeric(event)"  >
			</div>
			</div>

			<div class="col-sm-12">
              <div class="form-group">
                <label>Boundary Issues Reported by Chapter</label>
                <input type="text" name="ch_issue" class="form-control" value="{{ $chapterList[0]->boundary_issue_notes }}"  required onkeypress="return isAlphanumeric(event)" disabled >
			</div>
			</div>

            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <label class="custom-control-label" for="ch_resolved">Boundary Issues Resolved</label>
                    <input type="checkbox" name="ch_resolved" id="ch_resolved" class="custom-control-input" {{$chapterList[0]->boundary_issue_resolved ? 'checked' : '' }} />

                    </div>
                </div>

              </div>


              <div class="card-body text-center">
                          <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>

              <a href="{{ route('eoyreports.eoyboundaries') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>

              </div>
              </div>
              </div>
            </div>
        </div>
    </div>
    </section>
    </form>
    @endsection

    @section('customscript')
    <script>

        </script>

        @endsection

