@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Wrapper. Contains page content -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Role&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Role</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form id="role" method="POST" action='{{ route("coordinator.updaterole",$coordinatorDetails[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">

        @php
            $bigSisterCondition = ($position_id == 1 );  //*Big Sister
        @endphp

                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Personal Information</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                      <!-- /.form group -->
				    <div class="col-sm-6">
					  <div class="form-group">
						<label>First Name</label><span class="field-required">*</span>
						<input type="text" name="cord_fname" class="form-control" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" readonly>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" class="form-control" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" readonly>
					  </div>
					</div>

                  <div class="col-sm-4">
                    <div class="form-group">
                    <label>Reports To</label><span class="field-required">*</span>
                    <select name="cord_report_pc" id="cord_report_pc" class="form-control select2-sb4" style="width: 100%;" required>

                       @foreach($primaryCoordinatorList as $pcl)
                          <option value="{{$pcl->cid}}" {{$coordinatorDetails[0]->report_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="OldReportPC" value="{{$coordinatorDetails[0]->report_id}}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                <label>Conference</label><span class="field-required">*</span>
                <select name="cord_conf" id= "cord_conf" class="form-control select2-sb4" style="width: 100%;" required>

                  @foreach($confList as $con)
                      <option value="{{$con->id}}" {{$coordinatorDetails[0]->conference_id == $con->id  ? 'selected' : ''}}>{{$con->conference_name}}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                <label>Region</label><span class="field-required">*</span>
                <select name="cord_region" id="cord_region" class="form-control select2-sb4" style="width: 100%;" required>
                    <option value="0" {{$coordinatorDetails[0]->region_id == 0  ? 'selected' : ''}}>None</option>
                    @foreach($regionList as $reg)
                      <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
                    @endforeach
                </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                <label>Primary Position for Display</label><span class="field-required">*</span>
                <select name="cord_disp_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)">
                    <option value=""></option>
                    @foreach($positionList as $pos)
                      <option value="{{$pos->id}}" {{$coordinatorDetails[0]->position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
                    @endforeach
                </select>
                </div>
                <input type="hidden" name="OldPrimaryPosition" value="{{$coordinatorDetails[0]->position_id}}">
            </div>
					<div class="col-sm-4">
						<div class="form-group">
						<label>Primary Position for MIMI Purposes</label><span class="field-required">*</span>
						<select name="cord_pri_pos" id="cord_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" required>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
						<input type="hidden" name="OldPrimaryPosition" value="{{$coordinatorDetails[0]->position_id}}">
						<input type="hidden" name="submit_type" id="submit_type" value="" />
						<input type="hidden" name="userid" id="userid" value="{{$coordinatorDetails[0]->user_id}}" />
                        <input type="hidden" name="coordinator_id" id="coordinator_id" value="{{$coordinatorDetails[0]->id}}" />
						<input type="hidden" name="coordName" value="{{$coordinatorDetails[0]->first_name }} {{$coordinatorDetails[0]->last_name}}" />
						<input type="hidden" name="coordConf" value="{{$coordinatorDetails[0]->conference_id}}" />
						<input type="hidden" name="email" value="{{$coordinatorDetails[0]->email}}" />
						<input type="hidden" name="RetireReason" id="RetireReason" value="" />
					</div>

					<div class="col-sm-4">
						<div class="form-group">
						<label>Secondary Position</label>
						<select name="cord_sec_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)">
							<option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
						<input type="hidden" name="OldSecPosition" value="{{$coordinatorDetails[0]->sec_position_id}}">
					</div>


                    <div class="col-sm-4">
                        <div class="form-group">
                          <label>Coordinator Start Date</label>
                          <input type="text" name="cord_phone" class="form-control" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
                        </div>
                      </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <label>Last Promotion Date</label>
                      <input type="text" name="CoordinatorPromoteDate" id="CoordinatorPromoteDate" class="form-control" value="{{ $lastPromoted }}" readonly>
                    </div>
                    <input type="hidden" name="CoordinatorPromoteDateNew" id="CoordinatorPromoteDateNew"  value="{{$lastPromoted}}"/>
                  </div>
                  <div class="col-sm-2">
					<div class="radio-chk">
							<div class="form-group">
							<label>On Leave Of Absense</label>
							<label style="display: block;"><input disabled type="checkbox" name="SecVacant" id="SecVacant" class="ios-switch green bigswitch" {{$coordinatorDetails[0]->on_leave == '1'  ? 'checked' : ''}}/><div><div></div></div>
							</label>
						</div>
					</div>
					</div>
					<div class="col-sm-2">
					  <div class="form-group">
						<label>Leave Date</label>
						<input type="text" name="cord_altphone" class="form-control" value="{{ $coordinatorDetails[0]->leave_date }}" disabled>
					  </div>
					</div>
                </div>
            </div>

            <div class="col-12 text-center">
                <p><b>Primary Position for Display</b> is required and used to display Coordinator's Title in Correspondence and Reporting Tree.<br>
            <b>Primary Position for MIMI Purposes</b> is required adn used for assigning chapters to a Coordintoar and for all Menus/Visibility options to works correctly based on the role.<br>
                    For assigning chapters, Coordinators should be assigned a MIMI role of BS, AC, AC, ARC, RC, ACC or CC.<br>
            <b>Seconary Position</b> is optional and used to display Coordinator's Title in Correspondene and for all Menus/Visilbity options based on the role.</p>
        </div>

					<div class="card-header">
                        <h3 class="card-title">&nbsp;</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-12">
                <div class="form-group">
                    <label>Coordinators Directly Reporting to {{ $coordinatorDetails[0]->first_name }}:</label>
                    <table id="coordinator-list" class="nowraptable" width="100%">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Change To Report To</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $coordinator_list = DB::table('coordinators as cd')
                                    ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                    ->join('region', 'cd.region_id', '=', 'region.id')
                                    ->where('cd.report_id', $coordinatorDetails[0]->id)
                                    ->where('cd.is_active', 1)
                                    ->get();

                                $coordinator_options = DB::table('coordinators as cd')
                                    ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                    ->where(function ($query) use ($coordinatorDetails) {
                                        $query->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                                            ->where('cd.position_id', '>=', 1)
                                            ->where('cd.position_id', '<=', 6)
                                            ->where('cd.is_active', 1);
                                    })
                                    ->orWhere(function ($query) use ($coordinatorDetails) {
                                        $query->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                                            ->where('cd.position_id', 25)
                                            ->where('cd.is_active', 1);
                                    })
                                    ->orderBy('cd.first_name')
                                    ->orderBy('cd.last_name')
                                    ->get();

                                $row_count = count($coordinator_list);
                            @endphp

                            @foreach ($coordinator_list as $index => $coordinator)
                                <tr>
                                    <td>{{ $coordinator->cor_f_name }}</td>
                                    <td>{{ $coordinator->cor_l_name }}</td>
                                    <td>
                                        <select name="Report{{ $index }}" id="Report{{ $index }}" required>
                                            @foreach ($coordinator_options as $option)
                                                <option value="{{ $option->cid }}"
                                                    {{ $option->cid == $coordinatorDetails[0]->id ? 'selected' : '' }}>
                                                    {{ $option->cor_f_name }} {{ $option->cor_l_name }} ({{ $option->pos }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td style="display:none;">
                                        <input type="hidden" name="CoordinatorIDRow{{ $index }}" id="CoordinatorIDRow{{ $index }}" value="{{ $coordinator->cid }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="CoordinatorCount" id="CoordinatorCount" value="{{ $row_count }}" />
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-8">
                <div class="form-group">
                    <label>Select Direct Report To</label>
                    <select name="SelectCoordinator" id="SelectCoordinator" class="form-control" style="width: 100%;" onChange="ActivateCoordinatorButton(this)">
                        <option value=""></option>
                        @foreach ($directReportTo as $pcl)
                            <option value="{{ $pcl->cid }}">{{ $pcl->cor_f_name }} {{ $pcl->cor_l_name }} ({{ $pcl->pos }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="button" class="btn bg-gradient-primary" id="AssignCoordinator" disabled onclick="AddCoordinator()">Assign Coordinator</button>
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
						<div class="col-sm-12">
							<div class="form-group mrg-b-30">
							<label>Coordinator is Primary For :</label>

							<table id="chapter-list" class="nowraptable" width="100%">
								<thead>
								   <tr>
										<th>State</th>
										<th>Chapter Name</th>
										<th>Change Primary To</th>
										<th></th>
								   </tr>
								</thead>
								<tbody>
									<?php

									$chapter_list = DB::table('chapters')
                                        ->select('chapters.id', 'state.state_short_name as state', 'chapters.name as name')
                                        ->join('state', 'chapters.state', '=', 'state.id')
                                        ->where('primary_coordinator_id', $coordinatorDetails[0]->id)
                                        ->where('chapters.is_active', 1)
                                        ->orderBy('state.state_short_name')
                                        ->orderBy('chapters.name')
                                        ->get();

                                    if($coordinatorDetails[0]->region_id ==0){
                                        $coordinator_options = DB::table('coordinators as cd')
                                        ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                                        ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                        ->where(function ($query) use ($coordinatorDetails) {
                                            $query->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                                                ->where('cd.position_id', '>=', 1)
                                                ->where('cd.position_id', '<=', 6)
                                                ->where('cd.is_active', 1);
                                        })
                                        ->orWhere(function ($query) use ($coordinatorDetails) {
                                    $query->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                                        ->where('cd.position_id', 25)
                                        ->where('cd.is_active', 1);
                                        })
                                        ->orderBy('cd.first_name')
                                        ->orderBy('cd.last_name')
                                        ->get();

                                        }else{
                                    $coordinator_options = DB::table('coordinators as cd')
                                        ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos')
                                        ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                        ->where(function ($query) use ($coordinatorDetails) {
                                            $query->where('cd.region_id', $coordinatorDetails[0]->region_id)
                                                ->where(function ($query) {
                                                    $query->where('cd.position_id', '>=', 1)
                                                        ->where('cd.position_id', '<=', 5)
                                                        ->where('cd.is_active', 1);
                                                });
                                        })
                                        ->orWhere(function ($query) use ($coordinatorDetails) {
                                            $query->where('cd.position_id', 6)
                                                ->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                                                ->where('cd.is_active', 1);
                                        })
                                        ->orWhere(function ($query) use ($coordinatorDetails) {
                                            $query->where('cd.position_id', 25)
                                                ->where('cd.conference_id', $coordinatorDetails[0]->conference_id)
                                                ->where('cd.is_active', 1);
                                        })
                                        ->orderBy('cd.first_name')
                                        ->orderBy('cd.last_name')
                                        ->get();

									}
									$row_countCO=count($coordinator_options);

									$chapter_count=count($chapter_list);
									for ($row = 0; $row < $chapter_count; $row++){
										echo "<tr>";
										echo " <td>" . $chapter_list[$row]->state . "</td> \n";
										echo " <td>" . $chapter_list[$row]->name . "</td> \n";
										echo "<td><select name=\"PCID" . $row . "\" id=\"PCID" . $row . "\" required>";
										for ($row1 = 0; $row1 < $row_countCO; $row1++){
												$sel ='';
												if($coordinator_options[$row1]->cid == $coordinatorDetails[0]->id)
													$sel ='selected';
												echo "<option value='".$coordinator_options[$row1]->cid."' $sel >".$coordinator_options[$row1]->cor_f_name.' '.$coordinator_options[$row1]->cor_l_name.' ('.$coordinator_options[$row1]->pos.')'."</option>";
											}
											echo "</select></td>";
										echo " <td style=\"display:none;\"> <input type=\"hidden\" name=\"ChapterIDRow" . $row . "\" id=\"ChapterIDRow" . $row . "\" value=" . $chapter_list[$row]->id . "></td> \n";

										echo "</tr>";
									}

								?>
								</tbody>
							</table>
							<input type="hidden" name="ChapterCount" id="ChapterCount" value="<?php echo $chapter_count; ?>" />
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-8">
						  <div class="form-group">
							<label>Select Chapter</label>
							<select name="SelectChapter" class="form-control select2-sb4" style="width: 100%;" id="SelectChapter" onChange="ActivateChapterButton(this)">
						  <option value="" ></option>
						   @foreach($primaryChapterList as $pcl)
							  <option value="{{$pcl->id}}">{{$pcl->state}} - {{$pcl->chapter_name}}</option>
							@endforeach
						</select>
						  </div>
						</div>
						<div class="col-sm-4">
						  <div class="form-group">
                            <label>&nbsp;</label><br>
						  <button type="button" class="btn bg-gradient-primary" id="AssignChapter" name="AssignChapter" disabled onclick="AddChapter()">Assign Chapter</button>
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
			<button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
			<button type="button" class="btn bg-gradient-primary" onclick="ConfirmCancel(this);"><i class="fas fa-undo" ></i>&nbsp; Reset</button>

			<a href='{{ route("coordinator.edit",$coordinatorDetails[0]->id) }}' class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>

		</div>
		<div class="card-body text-center">
			<?php if ($coordinatorDetails[0]->on_leave) {?>
			<button type="button" class="btn bg-gradient-primary" id="remove-leave"><i class="fas fa-user-plus" ></i>&nbsp; Remove Volunteer from Leave</button> <?php }
			else { ?>
            <button type="button" class="btn bg-gradient-primary" id="leave" onclick="ConfirmLeave()"><i class="fas fa-user-minus" ></i>&nbsp; Put Volunteer on Leave</button>
			{{-- <button type="submit" class="btn bg-gradient-primary" onclick="return PreRetireValidate(true)"><i class="fas fa-user-minus" ></i>&nbsp; Put Volunteer on Leave</button> --}}
			<?php } ?>
            <button type="button" class="btn bg-gradient-primary" id="retire" onclick="ConfirmRetire()"><i class="fas fa-user-times" ></i>&nbsp; Retire Volunteer</button>
			{{-- <button type="submit" class="btn bg-gradient-primary" onclick="return PreRetireValidate()"><i class="fas fa-user-times" ></i>&nbsp; Retire Volunteer</button> --}}
        </div>

            <?php if ($bigSisterCondition) { ?>
                <div class="card-body text-center">
                Be sure to save all Region, Reporting and Chapter information before sending Welcome Letter to a new Big Sister.<br>
                <button type="button" class="btn bg-gradient-primary" id="letter"><i class="fas fa-envelope"></i>&nbsp; Send Big Sister Welcome Email</button>
            </div>
            <?php } ?>
        </div>
    </div>
        <!-- /.box-body -->
        </div>
    </div>
</div>
    </section>
</form>

@endsection
@section('customscript')
<script>
 $(document).ready(function() {
	$('select[name="cord_conf"]').on('change', function() {
            var confID = $(this).val();
			if(confID) {
                $.ajax({
                    url: '{{ url("/get.region/") }}' + '/' + confID,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
						$('select[name="cord_region"]').empty();
						$('select[name="cord_report"]').empty();
						$('select[name="SelectCoordinator"]').empty();
						$('#cord_region').html(data.html);
                    }
				});
		    }else{
                $('select[name="cord_region"]').empty();
            }
        });

		$('select[name="cord_region"]').on('change', function() {
            var regID = $(this).val();
			var confID = $('#cord_conf').val();
			var posID = $('#cord_pos').val();
            if(confID) {
               	$.ajax({
                    url: '{{ url("/get.reporting") }}',
                    type: "GET",
                    dataType: "json",
					data: {conf_id: confID, reg_id: regID, pos_id: posID},
                    success:function(data) {
						$('select[name="cord_report"]').empty();
						$('select[name="SelectCoordinator"]').empty();
						$('#cord_report').html(data.html);
                    }
				});
            }else{
                $('select[name="cord_report"]').empty();
            }
        });

		$('select[name="cord_region"]').on('change', function() {
            var regID = $('#cord_region').val();
			var confID = $('#cord_conf').val();
			var posID = $('#cord_pos').val();
            if(confID) {
               	$.ajax({
                    url: '{{ url("/get.directreport") }}',
                    type: "GET",
                    dataType: "json",
					data: {conf_id: confID, reg_id: regID, pos_id: posID},
                    success:function(data) {
						$('select[name="SelectCoordinator"]').empty();
						$('#SelectCoordinator').html(data.html);
                    }
				});
            }else{
                $('select[name="SelectCoordinator"]').empty();
            }
        });

		$('select[name="cord_pri_pos"]').on('change', function() {
            var regID = $('#cord_region').val();
			var confID = $('#cord_conf').val();
			var posID = $('#cord_pos').val();
            if(confID) {
               	$.ajax({
                    url: '{{ url("/get.directreport") }}',
                    type: "GET",
                    dataType: "json",
					data: {conf_id: confID, reg_id: regID, pos_id: posID},
                    success:function(data) {
						$('select[name="SelectCoordinator"]').empty();
						$('#SelectCoordinator').html(data.html);
                    }
				});
            }else{
                $('select[name="SelectCoordinator"]').empty();
            }
        });

		$('select[name="cord_region"]').on('change', function() {
            var regID = $('#cord_region').val();
			var confID = $('#cord_conf').val();
			var posID = $('#cord_pos').val();
            if(confID) {
               	$.ajax({
                    url: '{{ url("/get.chapterprimary") }}',
                    type: "GET",
                    dataType: "json",
					data: {conf_id: confID, reg_id: regID, pos_id: posID},
                    success:function(data) {
						$('select[name="SelectChapter"]').empty();
						$('#SelectChapter').html(data.html);
                    }
				});
            }else{
                $('select[name="SelectChapter"]').empty();
            }
        });

   $("#remove-leave").click(function() {
		$("#submit_type").val('RemoveLeave');
		$("#role").submit();
	});

    $("#letter").click(function() {
		$("#submit_type").val('Letter');
		$("#role").submit();
	});

});

    function ConfirmCancel(element) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Any unsaved changes will be lost. Do you want to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, continue',
            cancelButtonText: 'No, stay here',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Reload the page
                location.reload();
            } else {
                // Do nothing if the user cancels
                return false;
            }
        });
    }

var iChapterCount = <?php echo $chapter_count; ?>;
var iCoordinatorCount = <?php echo $row_count; ?>;

	function AddCoordinator(){

		var table=document.getElementById("coordinator-list");
		var newchapter = document.getElementById("SelectCoordinator");

		var row = table.insertRow(-1);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);

		var strChapter = getSelectedText('SelectCoordinator');
		var nCoordinatorID = getSelectedValue('SelectCoordinator');

		cell1.innerHTML = strChapter.substr(0,strChapter.indexOf(" "));
		cell2.innerHTML = strChapter.substring(strChapter.indexOf(" ")+1, strChapter.indexOf(" ("));
		var confid = $('#cord_conf').val();

		<?php
		$conid = $coordinatorDetails[0]->id;
		$confid = $coordinatorDetails[0]->conference_id;

			$coordinator_options = DB::table('coordinators as cd')
                    ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as posi')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->where(function ($query) use ($confid) {
                    $query->where('cd.conference_id', $confid)
                    ->where('cd.position_id', '>=', 1)
                    ->where('cd.position_id', '<=', 6)
                    ->where('cd.is_active', 1);
                    })
                    ->orWhere(function ($query) use ($confid) {
                    $query->where('cd.conference_id', $confid)
                    ->where('cd.position_id', 25)
                    ->where('cd.is_active', 1);
                    })
                    ->orderBy('cd.first_name')
                    ->orderBy('cd.last_name')
                    ->get();

			echo "var element = document.createElement('select');";
			echo "element.id = \"Report\" + iCoordinatorCount;";
			echo "element.name = \"Report\" + iCoordinatorCount;";

			$rowcount = count($coordinator_options);

			for ($row=0; $row<$rowcount; $row++){
				echo "var opt = document.createElement('option');";
				echo "opt.innerHTML = \"" . $coordinator_options[$row]->cor_f_name . " " . $coordinator_options[$row]->cor_l_name . " (" . $coordinator_options[$row]->posi .")\"" . ";";
				echo "opt.value = " . $coordinator_options[$row]->cid . ";";
				echo "element.add(opt, null);";
			}

			echo "cell3.appendChild(element);";
			echo "element.value='".$conid."'";

		?>

		cell4.innerHTML = "<input type=\"hidden\" name=\"CoordinatorIDRow" + iCoordinatorCount + "\" id=\"CoordinatorIDRow" + iCoordinatorCount + "\" value=" + nCoordinatorID + "></td> \n";

		iCoordinatorCount++;
		document.getElementById('CoordinatorCount').value = iCoordinatorCount;
	}
	function AddChapter(){

		var table=document.getElementById("chapter-list");
		var newchapter = document.getElementById("SelectChapter");

		var row = table.insertRow(-1);
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);

		var strChapter = getSelectedText('SelectChapter');
		var nChapterID = getSelectedValue('SelectChapter');

		cell1.innerHTML = strChapter.substr(0,strChapter.indexOf(" - "));
		cell2.innerHTML = strChapter.substring(strChapter.indexOf(" - ")+3);

		<?php
			$conid = $coordinatorDetails[0]->id;
			$confid = $coordinatorDetails[0]->conference_id;

                $coordinator_options = DB::table('coordinators as cd')
                    ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as posi')
                    ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                    ->where(function ($query) use ($confid) {
                        $query->where('cd.conference_id', $confid)
                            ->where('cd.position_id', '>=', 1)
                            ->where('cd.position_id', '<=', 6)
                            ->where('cd.is_active', 1);
                            })
                            ->orWhere(function ($query) use ($confid) {
                                $query->where('cd.conference_id', $confid)
                                    ->where('cd.position_id', 25)
                                    ->where('cd.is_active', 1);
                            })
                            ->orderBy('cd.first_name')
                            ->orderBy('cd.last_name')
                            ->get();

			echo "var element = document.createElement('select');";
			echo "element.id = \"PCID\" + iChapterCount;";
			echo "element.name = \"PCID\" + iChapterCount;";

			$rowcount = count($coordinator_options);

			for ($row=0; $row<$rowcount; $row++){
				echo "var opt = document.createElement('option');";
				echo "opt.innerHTML = \"" . $coordinator_options[$row]->cor_f_name . " " . $coordinator_options[$row]->cor_l_name . " (" . $coordinator_options[$row]->posi .")\"" . ";";
				echo "opt.value = " . $coordinator_options[$row]->cid . ";";
				echo "element.add(opt, null);";
			}

			echo "cell3.appendChild(element);";
			echo "element.value=" . $conid . ";";

		?>
		cell4.innerHTML = "<input type=\"hidden\" name=\"ChapterIDRow" + iChapterCount + "\" id=\"ChapterIDRow" + iChapterCount + "\" value=" + nChapterID + "></td> \n";
		iChapterCount++;
		document.getElementById('ChapterCount').value = iChapterCount;
	}

	function getSelectedText(elementId) {
				var elt = document.getElementById(elementId);

				if (elt==null || elt.selectedIndex == -1)
					return null;

				return elt.options[elt.selectedIndex].text;
			}
	function getSelectedValue(elementId) {
				var elt = document.getElementById(elementId);

				if (elt==null || elt.selectedIndex == -1)
					return null;

				return elt.options[elt.selectedIndex].value;
			}
	function ActivateCoordinatorButton(element){
		var coordinatorbutton = document.getElementById("AssignCoordinator");

		if(element.value > 0){
			coordinatorbutton.disabled=false;
		}
		else{
			coordinatorbutton.disabled=true;
		}
	}
	function ActivateChapterButton(element){

				var chapterbutton = document.getElementById("AssignChapter");

				if(element.value > 0){
					chapterbutton.disabled=false;
				}
				else{
					chapterbutton.disabled=true;
				}
			}

	// function CheckPromotion(element){
	// 	var promotionDate = prompt("If this position change is a promotion, please enter the promotion date in the format YYYY-MM-DD.  If this position change is not a promotion, press cancel and the promotion date will not be updated.","<?php echo date("Y-m-d"); ?>");

	// 	if (promotionDate == null) {
	// 		return true;
	// 	}
	// 	else{

	// 		var allowBlank = false;
	// 		var minYear = 1980;
	// 		var maxYear = (new Date()).getFullYear();

	// 		var errorMsg = "";

	// 		// regular expression to match required date format
	// 		re = /^(\d{4})-(\d{1,2})-(\d{1,2})/;

	// 		if(promotionDate != '') {
	// 		  if(regs = promotionDate.match(re)) {
	// 			if(regs[13] < 1 || regs[3] > 31) {
	// 			  errorMsg = "Invalid value for day: " + regs[1];
	// 			} else if(regs[2] < 1 || regs[2] > 12) {
	// 			  errorMsg = "Invalid value for month: " + regs[2];
	// 			} else if(regs[1] < minYear || regs[1] > maxYear) {
	// 			  errorMsg = "Invalid value for year: " + regs[1] + " - must be between " + minYear + " and " + maxYear;
	// 			}
	// 		  } else {
	// 			errorMsg = "Invalid date format: " + promotionDate;
	// 		  }
	// 		} else if(!allowBlank) {
	// 		  errorMsg = "Empty date not allowed!";
	// 		}

	// 		var defaultReset = false;

	// 		if(errorMsg != "") {
	// 			alert(errorMsg);

	// 			for (var i = 0 ; i<element.length ; i++)
	// 			{
	// 				if (element[i].defaultSelected){
	// 					element.value = element[i].value;
	// 					defaultReset = true;
	// 				}
	// 			}

	// 			if (!defaultReset)
	// 				element.value = 0;

	// 			element.focus();
	// 			return false;
	// 		}

	// 		var lastPromotionHidden=document.getElementById("CoordinatorPromoteDateNew");
	// 		lastPromotionHidden.value = promotionDate;

	// 		var lastPromotion=document.getElementById("CoordinatorPromoteDate");
	// 		lastPromotion.value = promotionDate;

	// 		return true;
	// 	}
	// }

    function CheckPromotion(element) {
        const minYear = 1980;
        const maxYear = (new Date()).getFullYear();
        const allowBlank = false;

        Swal.fire({
            title: 'Position Change',
                icon: 'question',
                html: '</p>If this position change is a promotion, please enter the promotion date in the format YYYY-MM-DD. If this position change is not a promotion, press Cancel and the promotion date will not be updated.</p>',
                input: 'text',
                inputLabel: 'Promoation Date',
                inputValue: "<?php echo date('Y-m-d'); ?>",
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value && !allowBlank) {
                    return 'Empty date not allowed!';
                }
                if (value) {
                    // Regular expression to match required date format
                    const re = /^(\d{4})-(\d{2})-(\d{2})$/;
                    const match = value.match(re);

                    if (!match) {
                        return 'Invalid date format! Use YYYY-MM-DD.';
                    }

                    const year = parseInt(match[1], 10);
                    const month = parseInt(match[2], 10);
                    const day = parseInt(match[3], 10);

                    if (month < 1 || month > 12) {
                        return 'Invalid month: ' + month;
                    }

                    if (day < 1 || day > 31) {
                        return 'Invalid day: ' + day;
                    }

                    if (year < minYear || year > maxYear) {
                        return 'Invalid year: ' + year + '. Must be between ' + minYear + ' and ' + maxYear;
                    }
                }
                return null;
            },
            customClass: {
                container: 'swal-container',
                popup: 'swal-popup',
                title: 'swal-title',
                input: 'swal-input',
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const promotionDate = result.value;

                // Set the hidden input values
                const lastPromotionHidden = document.getElementById("CoordinatorPromoteDateNew");
                const lastPromotion = document.getElementById("CoordinatorPromoteDate");

                if (lastPromotionHidden && lastPromotion) {
                    lastPromotionHidden.value = promotionDate;
                    lastPromotion.value = promotionDate;
                }
                console.log('Promotion Date:', promotionDate);
            } else {
                console.log('Promotion date update was canceled.');
            }
        });
    }

    function checkCoordinators() {
        var table = document.getElementById("coordinator-list");
        var tablerowcountCord = table.rows.length;
        var i, value, selectid;

        for (i = 0; i < tablerowcountCord; i++) {
            selectid = "Report" + i;
            value = getSelectedValue(selectid);
            if (value == <?php echo $coordinatorDetails[0]->id; ?>) {
                Swal.fire({
                    title: 'Oops!',
                    text: 'All direct reports must be assigned to a new supervising coordinator before continuing.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                    },
                    buttonsStyling: false
                });
                return false;
            }
        }
        return true;
    }

    function checkChapters() {
        var table = document.getElementById("chapter-list");
        var tablerowcountChap = table.rows.length;
        var i, value, selectid;

        for (i = 0; i < tablerowcountChap; i++) {
            selectid = "PCID" + i;
            value = getSelectedValue(selectid);
            if (value == <?php echo $coordinatorDetails[0]->id; ?>) {
                Swal.fire({
                    title: 'Oops!',
                    text: 'All assigned chapters must be assigned to a new supervising coordinator before continuing.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn-sm btn-success',
                    },
                    buttonsStyling: false
                });
                return false;
            }
        }
        return true;
    }

    function ConfirmLeave() {
        if (checkCoordinators() && checkChapters()) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will put the coordinator on a leave of absence.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#submit_type").val('Leave');
                    $("#role").submit();
                }
            });
        }
    }

    function ConfirmRetire() {
        if (checkCoordinators() && checkChapters()) {
            Swal.fire({
                title: 'Retire Coordinator',
                text: "Retiring a volunteer will remove their login to MIMI. Please enter their reason for retiring:",
                input: 'text',
                inputPlaceholder: 'Enter reason...',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn-sm btn-success',
                    cancelButton: 'btn-sm btn-danger',
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $("#submit_type").val('Retire');
                    $("#RetireReason").val(result.value);
                    $("#role").submit();
                }
            });
        }
    }

    // function PreRetireValidate(LoA=false){
	// 	//Ensure all their chapters and coordinators have been reassigned before we allow them to be retired.
	// 	//First, check the coordinators
	// 	var table=document.getElementById("coordinator-list");
	// 	var tablerowcountCord = table.rows.length;
	// 	var i;
	// 	var selectbox;
	// 	var value;

	// 	for(i=0; i<tablerowcountCord; i++){
	// 		selectid = "Report" + i;

	// 		value = getSelectedValue(selectid);
	// 		if(value == <?php echo $coordinatorDetails[0]->id; ?>){
	// 			if (LoA)
	// 				alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be put on a leave of absence.");
	// 			else
	// 				alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be retired.");
	// 			return false;
	// 		}

	// 	}

	// 	table=document.getElementById("chapter-list");
	// 	tablerowcountChap = table.rows.length;

	// 	for(i=0; i<tablerowcountChap; i++){
	// 		selectid = "PCID" + i;

	// 		value = getSelectedValue(selectid);
	// 		if(value == <?php echo $coordinatorDetails[0]->id; ?>){
	// 			if (LoA)
	// 				alert ("All assigned chapters must be assigned to a new supervising coordinator before this coordinator can be put on a leave of absence.");
	// 			else
	// 				alert ("All assigned chapters must be assigned to a new supervising coordinator before this coordinator can be retired.");
	// 			return false;
	// 		}
	// 	}

	// 	if(tablerowcountChap <= 1 && tablerowcountCord <= 1){
	// 		if (LoA){
	// 			$("#submit_type").val('Leave');
	// 			return true;
	// 		}else{
	// 			var reason = prompt("Retiring a volunteer will remove their login to MIMI.  If you wish to continue, please enter their reason for retiring and press OK.", "");

	// 			if (reason != null) {
	// 					$("#submit_type").val('Retire');
	// 					document.getElementById("RetireReason").value = reason;
	// 					return true;
	// 			}
	// 			else
	// 				return false;
	// 		}

	// 	}else{
	// 		if (LoA)
	// 				alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be put on a leave of absence.");
	// 			else
	// 				alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be retired.");
	// 			return false;
	// 	}

	// }
</script>
@endsection

