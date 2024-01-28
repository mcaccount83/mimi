@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
      <h1>
        Boundary Issues
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Boundary Issues</li>
      </ol>
    </section>
    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.updateboundary",$chapterList[0]->id) }}'>
    @csrf
   <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box card">
            <div class="box-header with-border">
              <h3 class="box-title">Chapter</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()" disabled>
              </div>
              </div>
              <!-- /.form group -->
            <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2" style="width: 100%;" required disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2" style="width: 100%;" required disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
             <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Current Boundaires in MIMI</label>
                <input type="text" name="ch_territory" class="form-control my-colorpicker1" value="{{ $chapterList[0]->territory }}"  required onkeypress="return isAlphanumeric(event)" disabled >
			</div>
			</div>
			<div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Current Boundaires in MIMI (edit HERE)</label>
                <input type="text" name="ch_territory" class="form-control my-colorpicker1" value="{{ $chapterList[0]->territory }}"  required onkeypress="return isAlphanumeric(event)"  >
			</div>
			</div>

			<div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Boundary Issues Reported by Chapter</label>
                <input type="text" name="ch_issue" class="form-control my-colorpicker1" value="{{ $chapterList[0]->boundary_issue_notes }}"  required onkeypress="return isAlphanumeric(event)" disabled >
			</div>
			</div>
			              <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Boundary Issues Resolved</label>
                    <label style="display: block;"><input type="checkbox" name="ch_resolved" id="" class="ios-switch green bigswitch" {{$chapterList[0]->boundary_issue_resolved == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>


                </div>
              </div>
              </div>


              <div class="box-body text-center">
                          <button type="submit" class="btn btn-themeBlue margin"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>

              <a href="{{ route('report.issues') }}" class="btn btn-themeBlue margin"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>

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


	var pcid = $("#pcid").val();
	if(pcid !=""){
		$.ajax({
            url: '{{ url("/checkreportid/") }}' + '/' + pcid,
            type: "GET",
            success: function(result) {
				$("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }
        </script>

        @endsection

