@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      International Coordinator List
       <small>Edit</small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator List</li>
      </ol>
    </section>
    <!-- Main content -->
    <form method="POST" action='{{ route("coordinator.update",$coordinatorDetails[0]->coordinator_id) }}'">
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
						<label>First Name</label><span class="field-required">*</span>
						<input type="text" name="cord_fname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required  autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required oautocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input autocomplete="nope" name="cord_addr" class="form-control my-colorpicker1" rows="4" maxlength="250" required value="{{ $coordinatorDetails[0]->address }}	">
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" class="form-control my-colorpicker1" maxlength="50" value="{{ $coordinatorDetails[0]->city }}" required autocomplete="nope">
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>State</label><span class="field-required">*</span>
						<select name="cord_state" class="form-control select2" style="width: 100%;" required>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}" {{$coordinatorDetails[0]->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Country</label><span class="field-required">*</span>
						<select id="cord_country" name="cord_country" class="form-control select2" style="width: 100%;" required>
						<option value="">Select Country</option>
							@foreach($countryArr as $con)
							  <option value="{{$con->short_name}}" {{$coordinatorDetails[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<div class="col-sm-3 col-xs-12">
					  <div class="form-group">
						<label>Zip</label><span class="field-required">*</span>
						<input type="text" name="cord_zip" class="form-control my-colorpicker1" maxlength="10" value="{{ $coordinatorDetails[0]->zip }}" required  autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Email</label><span class="field-required">*</span>
						<input type="email" name="cord_email" id="cord_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" maxlength="50" value="{{ $coordinatorDetails[0]->email }}" required autocomplete="nope">
						<input type="hidden" id="cord_email_chk" value="{{ $coordinatorDetails[0]->email }}">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Secondary Email</label>
						<input type="email" name="cord_sec_email" id="cord_sec_email" class="form-control my-colorpicker1"   maxlength="50" value="{{ $coordinatorDetails[0]->sec_email }}" autocomplete="nope">
						<input type="hidden" id="cord_email_chk" value="{{ $coordinatorDetails[0]->sec_email }}">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Phone</label><span class="field-required">*</span>
						<input type="text" name="cord_phone" id="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->phone }}" maxlength="12" required  autocomplete="nope">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Alternate Phone</label>
						<input type="text" name="cord_altphone" id="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->alt_phone }}" maxlength="12" autocomplete="nope">
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Birthday Month</label><span class="field-required">*</span>
						<select name="cord_month" class="form-control select2" style="width: 100%;" required>
						  <option value="">Select Month</option>
						  @foreach($foundedMonth as $key=>$val)

							  <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
						  @endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Birthday Day</label><span class="field-required">*</span>
						<input type="number" name="cord_day" class="form-control my-colorpicker1" min="1" max="31" value="{{ $coordinatorDetails[0]->birthday_day }}" required>
						</div>
					</div>
				</div>
				<div class="box-header with-border mrg-t-10"></div>
					<div class="box-body">
						<div class="col-sm-6 col-xs-12">
							<div class="form-group">
							<label class="mrg-b-25">Coordinators Directly Reporting to {{ $coordinatorDetails[0]->first_name }} :</label>

							<table id="coordinator-list" width="100%">
								<thead>
								   <tr>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Position</th>
										<th></th>
								   </tr>
								</thead>
								<tbody>
								<?php
								$row_count=count($directReportTo);
								for ($row = 0; $row < $row_count; $row++){
									echo "<tr>";
										echo "<td>".$directReportTo[$row]->cor_f_name."</td>";
										echo "<td>".$directReportTo[$row]->cor_l_name."</td>";
										echo "<td>".$directReportTo[$row]->pos."</td>";
									echo "</tr>";
								}
								?>
								</tbody>
							</table>
							</div>
						</div>
						<div class="col-sm-6 col-xs-12">
							<div class="form-group">
							<label class="mrg-b-25">Primary Coordinator For :</label>

							<table id="coordinator-list" width="100%">
								<thead>
								   <tr>
										<th>State</th>
										<th>Chapter Name</th>
								   </tr>
								</thead>
								<tbody>
								<?php
								$row_count=count($directChapterTo);
								for ($row = 0; $row < $row_count; $row++){
									echo "<tr>";
									echo "<td>".$directChapterTo[$row]->st_name."</td>";
										echo "<td>".$directChapterTo[$row]->ch_name."</td>";

									echo "</tr>";
								}
								?>
								</tbody>
							</table>
							</div>
						</div>

					</div>

				<div class="box-header with-border mrg-t-10"></div>
				<div class="box-body">
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Primary Position</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
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
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Region</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						 <option value=""></option>
							@foreach($regionList as $reg)
							  <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Conference</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
						  @foreach($confList as $con)
							  <option value="{{$con->id}}" {{$coordinatorDetails[0]->conference_id == $con->id  ? 'selected' : ''}}>{{$con->conference_name}}</option>
							@endforeach
						</select>
						</div>
					</div>

					<div class="col-sm-12 col-xs-12">
					  <div class="form-group">
						<label>Home Chapter</label>
						<input type="text" name="cord_chapter" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->home_chapter }}" maxlength="50" " autocomplete="nope" disabled>
					  </div>
					</div>

					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
						<label>Reports To</label>
						<select name="cord_month" class="form-control select2" style="width: 100%;" disabled>
						  <option value=""></option>
						   @foreach($primaryCoordinatorList as $pcl)
							  <option value="{{$pcl->cid}}" {{$coordinatorDetails[0]->report_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Promotion Date</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_promoted }}" disabled>
					  </div>
					</div>


					<div class="col-sm-6 col-xs-12">
					<div class="radio-chk">
							<div class="form-group">
							<label>On Leave Of Absense</label>
							<label style="display: block;"><input disabled type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$coordinatorDetails[0]->on_leave == '1'  ? 'checked' : ''}}/><div><div></div></div>
							</label>
						</div>
					</div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Leave Date</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->leave_date }}" disabled>
					  </div>
					</div>

					<div class="clearfix"></div>

					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Updated By</label>
						<input type="text" name="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_updated_by }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Updated Date</label>
						<input type="text" name="cord_altphone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_updated_date }}" disabled>
					  </div>
					</div>
				</div>
            </div>
		</div>

		<!-- /.box-body -->
		<div class="box-body text-center">



			<a href="{{ route('coordinator.list') }}" class="btn btn-themeBlue margin">Back</a>
		</div>

        <!-- /.box-body -->
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

