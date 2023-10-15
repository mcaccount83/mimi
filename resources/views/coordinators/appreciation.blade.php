@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      Coordinator Appreciation
       <small>View</small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator Appreciation</li>
      </ol>
    </section>
 

    <!-- Main content -->
    <form method="POST" action='{{ route("coordinator.updateappreciation",$coordinatorDetails[0]->coordinator_id) }}'>
    @csrf
    <section class="content">
		<div class="row">
		<div class="col-md-12">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Coordinator</h3>
				</div>
				<div class="box-body">
				  <!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>First Name</label>
						<input type="text" name="cord_fname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label>
						<input type="text" name="cord_lname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-12 col-xs-12">
					  <div class="form-group">
						<label>Address</label>
						<input type="text" name="cord_address" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->address }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>City</label>
						<input type="text" name="cord_city" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->city }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>State</label>
						<input type="text" name="cord_state" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->state }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Countyry</label>
						<input type="text" name="cord_country" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->country }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Zip</label>
						<input type="text" name="cord_zip" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->zip }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
										<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_start_date" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Region</label>
						<select name="cord_region" class="form-control select2" style="width: 100%;" disabled>
						 <option value=""></option>
							@foreach($regionList as $reg)
							  <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Primary Position</label>
						<select name="cord_position" class="form-control select2" style="width: 100%;" disabled>
						   <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Secondary Position</label>
						<select name="cord_sec_position" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					

				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Appreciation Gift History</h3>
				</div>
				<div class="box-body">
				    <div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>< 1 Year</label>
						<input type="text" name="cord_year0" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year0 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>1 Year</label>
						<input type="text" name="cord_year1" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year1 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>2 Years</label>
						<input type="text" name="cord_year2" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year2 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
				<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>3 Years</label>
						<input type="text" name="cord_year3" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year3 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
									    <div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>4 Years</label>
						<input type="text" name="cord_year4" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year4 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>5 Years</label>
						<input type="text" name="cord_year5" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year5 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>6 Years</label>
						<input type="text" name="cord_year6" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year6 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
				<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>7 Years</label>
						<input type="text" name="cord_year7" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year7 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
										<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>8 Years</label>
						<input type="text" name="cord_year8" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year8 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
				<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>9 Years</label>
						<input type="text" name="cord_year9" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->recognition_year9 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
			              <div class="radio-chk">
                <div class="col-sm-6 col-xs-12">
                  <div class="form-group">
                    <label>Necklace</label>
                    <label style="display: block;"><input type="checkbox" name="cord_necklace" id="" class="ios-switch green bigswitch" {{ $coordinatorDetails[0]->recognition_necklace == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
					<div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Top Tier or Other Gifts Received</label>
                <textarea name="cord_toptier" class="form-control my-colorpicker1" rows="4" maxlength="200">{{ $coordinatorDetails[0]->recognition_toptier }}</textarea>
              </div>
              </div>
				</div>
	  </div>
					</div>
				</div>
		<!-- /.box-body -->
		<div class="box-body text-center">
            <button type="submit" class="btn btn-themeBlue margin">Save</button>
            <a href="{{ route('report.appreciation') }}" class="btn btn-themeBlue margin">Back</a>
				  </div>
					</div>
				</div>
        <!-- /.box-body -->
        </div>
    </section>
</form>
 
@endsection
@section('customscript')

@endsection
