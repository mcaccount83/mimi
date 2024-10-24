@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Birthday&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Birthday</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("coordreports.updatecoordrptbirthdays",$coordinatorDetails[0]->id) }}'>
    @csrf
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
						<input type="text" name="cord_fname" class="form-control" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" readonly>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Name</label>
						<input type="text" name="cord_lname" class="form-control" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" readonly>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input autocomplete="nope" name="cord_addr" class="form-control" rows="4" maxlength="250" required value="{{ $coordinatorDetails[0]->address }}	" readonly>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-4">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" class="form-control" maxlength="50" value="{{ $coordinatorDetails[0]->city }}" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" readonly>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-4">
					  <div class="form-group">
						<label>State</label><span class="field-required">*</span>
						<select name="cord_state" class="form-control select2-sb4" style="width: 100%;" required readonly>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}" {{$coordinatorDetails[0]->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<div class="col-sm-4">
					  <div class="form-group">
						<label>Zip</label><span class="field-required">*</span>
						<input type="text" name="cord_zip" class="form-control" maxlength="10" value="{{ $coordinatorDetails[0]->zip }}" required onkeypress="return isNumber(event)" autocomplete="nope" readonly>
					  </div>
					</div>
                	<div class="col-sm-6">
					  <div class="form-group">
						<label>Coordinator Birthday</label><span class="field-required">*</span>
						<input type="text" name="cord_bday" class="form-control" maxlength="10" value="{{ $coordinatorDetails[0]->birthday_month_id }} / {{ $coordinatorDetails[0]->birthday_day }}" required onkeypress="return isNumber(event)" autocomplete="nope" readonly>
					  </div>
					</div>

				<div class="col-sm-6">
					  <div class="form-group">
						<label>Date Card Sent</label>
						<input type="date" name="card_sent" class="form-control" value="{{ $coordinatorDetails[0]->card_sent }}" >
					  </div>
					</div>

				</div>
		<!-- /.box-body -->
		<div class="card-body text-center">
            <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
            <a href="{{ route('coordreports.coordrptbirthdays') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
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
