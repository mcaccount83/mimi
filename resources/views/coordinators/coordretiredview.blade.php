@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Details&nbsp;<small>(View)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->

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
						<input type="text" name="cord_fname" class="form-control" value="{{ $coordinatorDetails[0]->first_name }}" disabled>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Name</label>
						<input type="text" name="cord_lname" class="form-control" value="{{ $coordinatorDetails[0]->last_name }}" disabled>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12">
						<div class="form-group">
							<label>Street Address</label>
							<textarea name="cord_addr" class="form-control" rows="4" disabled>{{ $coordinatorDetails[0]->address }}</textarea>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3">
						<div class="form-group">
							<label>City</label>
							<input type="text" name="cord_city" class="form-control" value="{{ $coordinatorDetails[0]->city }}" disabled>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3">
					  <div class="form-group">
						<label>State</label>
						<select name="cord_state" class="form-control select2-sb4" style="width: 100%;" disabled>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}" {{$coordinatorDetails[0]->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3">
					  <div class="form-group">
						<label>Zip</label>
						<input type="text" name="cord_zip" class="form-control" value="{{ $coordinatorDetails[0]->zip }}" disabled>
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>Country</label>
						<select id="cord_country" name="cord_country" class="form-control select2-sb4" style="width: 100%;" disabled>
						<option value="">Select Country</option>
							@foreach($countryArr as $con)
							  <option value="{{$con->short_name}}" {{$coordinatorDetails[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12">
					  <div class="form-group">
						<label>Email</label>
						<input type="email" name="cord_email" id="cord_email" class="form-control"  value="{{ $coordinatorDetails[0]->email }}" required autocomplete="nope">
						<input type="hidden" name="cord_email2" id="cord_email_chk" value="{{ $coordinatorDetails[0]->email }}">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Phone</label>
						<input type="text" name="cord_phone" class="form-control" value="{{ $coordinatorDetails[0]->phone }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Alternate Phone</label>
						<input type="text" name="cord_altphone" class="form-control" value="{{ $coordinatorDetails[0]->alt_phone }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Birthday Month</label>
						<select name="cord_month" class="form-control select2-sb4" style="width: 100%;" disabled>
						  <option value="">Select Month</option>
						  @foreach($foundedMonth as $key=>$val)

							  <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
						  @endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Birthday Day</label>
						<input type="number" name="cord_day" class="form-control" min="1" max="31" value="{{ $coordinatorDetails[0]->birthday_day }}" disabled>
						</div>
					</div>
				</div>
            </div>

            <div class="card-header">
                <h3 class="card-title">&nbsp;</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
      <!-- /.form group -->
      <div class="col-sm-6">
						<div class="form-group">
						<label>Primary Position</label>
						<select name="cord_month" class="form-control select2-sb4" style="width: 100%;" disabled>
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
						<select name="cord_month" class="form-control select2-sb4" style="width: 100%;" disabled>
						  <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Region</label>
						<select name="cord_month" class="form-control select2-sb4" style="width: 100%;" disabled>
						 <option value=""></option>
							@foreach($regionList as $reg)
							  <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
						<label>Conference</label>
						<select name="cord_month" class="form-control select2-sb4" style="width: 100%;" disabled>
						  <option value=""></option>
						  @foreach($confList as $con)
							  <option value="{{$con->id}}" {{$coordinatorDetails[0]->conference_id == $con->id  ? 'selected' : ''}}>{{$con->conference_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
						<label>Reports To</label>
						<select name="cord_month" class="form-control select2-sb4" style="width: 100%;" disabled>
						  <option value=""></option>
						   @foreach($primaryCoordinatorList as $pcl)
							  <option value="{{$pcl->cid}}" {{$coordinatorDetails[0]->report_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_phone" class="form-control" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Promotion Date</label>
						<input type="text" name="cord_altphone" class="form-control" value="{{ $coordinatorDetails[0]->last_promoted }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6">
					<div class="radio-chk">
							<div class="form-group">
							<label>On Leave Of Absense</label>
							<label style="display: block;"><input  type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" /><div><div></div></div>
							</label>
						</div>
					</div>
					</div>
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Leave Date</label>
						<input type="text" name="cord_altphone" class="form-control" value="{{ $coordinatorDetails[0]->leave_date }}" disabled>
					  </div>
					</div>
                </div>
            </div>

<div class="card-header">
                <h3 class="card-title">&nbsp;</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
      <!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Updated By</label>
						<input type="text" name="cord_phone" class="form-control" value="{{ $coordinatorDetails[0]->last_updated_by }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Updated Date</label>
						<input type="text" name="cord_altphone" class="form-control" value="{{ $coordinatorDetails[0]->last_updated_date }}" disabled>
					  </div>
					</div>
				</div>
            </div>

		<!-- /.box-body -->
		<div class="card-body text-center">
		<a href="{{ route('coordinators.coordretired') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</a>
			<a href='{{ route('coordinators.updatecoordunretire', $coordinatorDetails[0]->id) }}' class="btn bg-gradient-primary"><i class="fas fa-user-plus" ></i>&nbsp;&nbsp;&nbsp;UnRetire</a>
		{{-- <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;save</button> --}}
		</div>
        <!-- /.box-body -->
        </div>
    </div>
</div>
</div>
    </section>
</form>

@endsection
@section('customscript')
<script>

   // Disable fields and buttons
   $(document).ready(function () {
            $('input, select, textarea').prop('disabled', true);
    });

</script>
@endsection

