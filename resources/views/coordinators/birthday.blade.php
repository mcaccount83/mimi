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
    <form method="POST" action='{{ route("coordinator.updatebirthday",$coordinatorDetails[0]->coordinator_id) }}'>
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
						<input type="text" name="cord_fname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" readonly>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label>
						<input type="text" name="cord_lname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" readonly>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input autocomplete="nope" name="cord_addr" class="form-control my-colorpicker1" rows="4" maxlength="250" required value="{{ $coordinatorDetails[0]->address }}	" readonly>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" class="form-control my-colorpicker1" maxlength="50" value="{{ $coordinatorDetails[0]->city }}" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" readonly>
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-4 col-xs-12">
					  <div class="form-group">
						<label>State</label><span class="field-required">*</span>
						<select name="cord_state" class="form-control select2" style="width: 100%;" required readonly>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}" {{$coordinatorDetails[0]->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
				
					<div class="col-sm-4 col-xs-12">
					  <div class="form-group">
						<label>Zip</label><span class="field-required">*</span>
						<input type="text" name="cord_zip" class="form-control my-colorpicker1" maxlength="10" value="{{ $coordinatorDetails[0]->zip }}" required onkeypress="return isNumber(event)" autocomplete="nope" readonly>
					  </div>
					</div>
				
					

                	<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Coordinator Birthday</label><span class="field-required">*</span>
						<input type="text" name="cord_bday" class="form-control my-colorpicker1" maxlength="10" value="{{ $coordinatorDetails[0]->birthday_month_id }} / {{ $coordinatorDetails[0]->birthday_day }}" required onkeypress="return isNumber(event)" autocomplete="nope" readonly>
					  </div>
					</div>
				    
				<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Date Card Sent</label>
						<input type="date" name="card_sent" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->card_sent }}" maxlength="50" onkeypress="return isAlphanumeric(event)">
					  </div>
					</div>
			      
					
				</div>
	  </div>
				</div>
		<!-- /.box-body -->
		<div class="box-body text-center">
            <button type="submit" class="btn btn-themeBlue margin">Save</button>
            <a href="{{ route('report.birthday') }}" class="btn btn-themeBlue margin">Back</a>
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