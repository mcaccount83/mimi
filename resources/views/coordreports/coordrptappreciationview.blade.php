@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Appreciation&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Appreciation</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("coordreports.updatecoordrptappreciation",$coordinatorDetails[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Coordinator</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>First Name</label>
						<input type="text" name="cord_fname" class="form-control" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Name</label>
						<input type="text" name="cord_lname" class="form-control" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-12">
					  <div class="form-group">
						<label>Address</label>
						<input type="text" name="cord_address" class="form-control" value="{{ $coordinatorDetails[0]->address }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>City</label>
						<input type="text" name="cord_city" class="form-control" value="{{ $coordinatorDetails[0]->city }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>State</label>
						<input type="text" name="cord_state" class="form-control" value="{{ $coordinatorDetails[0]->state }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>Countyry</label>
						<input type="text" name="cord_country" class="form-control" value="{{ $coordinatorDetails[0]->country }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>Zip</label>
						<input type="text" name="cord_zip" class="form-control" value="{{ $coordinatorDetails[0]->zip }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
										<div class="col-sm-6">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_start_date" class="form-control" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Region</label>
						<select name="cord_region" class="form-control select2-sb4" style="width: 100%;" disabled>
						 <option value=""></option>
							@foreach($regionList as $reg)
							  <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Primary Position</label>
						<select name="cord_position" class="form-control select2-sb4-sb4" style="width: 100%;" disabled>
						   <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Secondary Position</label>
						<select name="cord_sec_position" class="form-control select2-sb4" style="width: 100%;" disabled>
						  <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
                </div>
            </div>


            <div class="card-header">
                <h3 class="card-title">Appreciation Gift History</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
      <!-- /.form group -->
				    <div class="col-sm-3">
					  <div class="form-group">
						<label>< 1 Year</label>
						<input type="text" name="cord_year0" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year0 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>1 Year</label>
						<input type="text" name="cord_year1" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year1 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>2 Years</label>
						<input type="text" name="cord_year2" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year2 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
				<div class="col-sm-3">
					  <div class="form-group">
						<label>3 Years</label>
						<input type="text" name="cord_year3" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year3 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
									    <div class="col-sm-3">
					  <div class="form-group">
						<label>4 Years</label>
						<input type="text" name="cord_year4" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year4 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>5 Years</label>
						<input type="text" name="cord_year5" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year5 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>6 Years</label>
						<input type="text" name="cord_year6" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year6 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
				<div class="col-sm-3">
					  <div class="form-group">
						<label>7 Years</label>
						<input type="text" name="cord_year7" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year7 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
										<div class="col-sm-3">
					  <div class="form-group">
						<label>8 Years</label>
						<input type="text" name="cord_year8" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year8 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
				<div class="col-sm-3">
					  <div class="form-group">
						<label>9 Years</label>
						<input type="text" name="cord_year9" class="form-control" value="{{ $coordinatorDetails[0]->recognition_year9 }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>

                    <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="cord_necklace" id="cord_necklace" class="custom-control-input"  {{$coordinatorDetails[0]->recognition_necklace ? 'checked' : '' }} />
                        <label class="custom-control-label" for="cord_necklace" >Necklace</label>
                  </div>
                </div>
                <div class="col-sm-12">&nbsp;</div>
                  <div class="col-sm-12">
              <div class="form-group">
                <label>Top Tier or Other Gifts Received</label>
                <textarea name="cord_toptier" class="form-control" rows="4" maxlength="200">{{ $coordinatorDetails[0]->recognition_toptier }}</textarea>
              </div>
              </div>
				</div>

		<!-- /.box-body -->
		<div class="card-body text-center">
            <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
            <a href="{{ route('coordreports.coordrptappreciation') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
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

@endsection
