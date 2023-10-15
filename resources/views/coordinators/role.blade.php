@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
      Coordinator List
       <small>Role</small>
       </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Coordinator List</li>
      </ol>
    </section>
	@if ($message = Session::get('success'))
      <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>	
         <p>{{ $message }}</p>
      </div>
    @endif
	 @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>	
         <p>{{ $message }}</p>
      </div>
    @endif

    <!-- Main content -->
    <form id="role" method="POST" action='{{ route("coordinator.updaterole",$coordinatorDetails[0]->coordinator_id) }}'>
    @csrf
    <section class="content">
		<div class="row">
		<div class="col-md-12">
			<div class="box card">
				<div class="box-header with-border mrg-t-10">
					<h3 class="box-title">Coordinator</h3>
				</div>
				<div class="box-body">
				    <div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>First Name</label><span class="field-required">*</span>
						<input type="text" name="cord_fname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" readonly>
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" readonly>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Primary Position</label><span class="field-required">*</span>
						<select name="cord_pri_pos" id="cord_pos" class="form-control select2" style="width: 100%;" onChange="CheckPromotion(this)" required>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
						<input type="hidden" name="OldPrimaryPosition" value="{{$coordinatorDetails[0]->position_id}}">
						<input type="hidden" name="submit_type" id="submit_type" value="" />
						<input type="hidden" name="userid" id="userid" value="{{$coordinatorDetails[0]->user_id}}" />
						<input type="hidden" name="coordName" value="{{$coordinatorDetails[0]->first_name }} {{$coordinatorDetails[0]->last_name}}" />
						<input type="hidden" name="coordConf" value="{{$coordinatorDetails[0]->conference_id}}" />
						<input type="hidden" name="email" value="{{$coordinatorDetails[0]->email}}" />
						<input type="hidden" name="RetireReason" id="RetireReason" value="" />
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Secondary Position</label>
						<select name="cord_sec_pos" class="form-control select2" style="width: 100%;" onChange="CheckPromotion(this)">
							<option value=""></option>
							@foreach($positionList as $pos)
							  <option value="{{$pos->id}}" {{$coordinatorDetails[0]->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
							@endforeach
						</select>
						</div>
						<input type="hidden" name="OldSecPosition" value="{{$coordinatorDetails[0]->sec_position_id}}">
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Conference</label><span class="field-required">*</span>
						<select name="cord_conf" id= "cord_conf" class="form-control select2" style="width: 100%;" required>
						 
						  @foreach($confList as $con)
							  <option value="{{$con->id}}" {{$coordinatorDetails[0]->conference_id == $con->id  ? 'selected' : ''}}>{{$con->conference_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12">
						<div class="form-group">
						<label>Region</label><span class="field-required">*</span>
						<select name="cord_region" id="cord_region" class="form-control select2" style="width: 100%;" required>
							<option value="0" {{$coordinatorDetails[0]->region_id == 0  ? 'selected' : ''}}>None</option>
							@foreach($regionList as $reg)
							  <option value="{{$reg->id}}" {{$coordinatorDetails[0]->region_id == $reg->id  ? 'selected' : ''}}>{{$reg->long_name}}</option>
							@endforeach
						</select>
						</div>
					</div>
					
					<div class="col-sm-12 col-xs-12">
					  <div class="form-group">
						<label>Home Chapter</label><span class="field-required">*</span>
						<input type="text" name="cord_chapter" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->home_chapter }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" autocomplete="nope" >
					  </div>
					</div>
					
					<div class="col-sm-12 col-xs-12">
						<div class="form-group">
						<label>Reports To</label><span class="field-required">*</span>
						<select name="cord_report" id="cord_report" class="form-control select2" style="width: 100%;" required>
						  
						   @foreach($primaryCoordinatorList as $pcl)
							  <option value="{{$pcl->cid}}" {{$coordinatorDetails[0]->report_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
							@endforeach
						</select>
						</div>
					</div>
				</div>	
					<div class="box-header with-border mrg-t-10"></div>
					<div class="box-body">
						<div class="col-sm-12 col-xs-12">
							<div class="form-group mrg-b-30">
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
									<?php
									$coordinator_list = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos 
									                                FROM coordinator_details as cd 
									                                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id 
									                                INNER JOIN region ON cd.region_id = region.id 
									                                WHERE cd.report_id = {$coordinatorDetails[0]->coordinator_id} AND cd.is_active=1 ") );
									$row_count=count($coordinator_list);
									
									$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as pos 
									                                FROM coordinator_details as cd 
									                                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id 
									                                WHERE (cd.conference_id = {$coordinatorDetails[0]->conference_id} AND cd.position_id >= 1 AND cd.position_id <= 6 AND cd.is_active=1 )
									                                OR (cd.conference_id = {$coordinatorDetails[0]->conference_id} AND cd.position_id = 25 AND cd.is_active=1 )
									                                ORDER BY cd.first_name, cd.last_name") );
									$row_countCO=count($coordinator_options);
									
									for ($row = 0; $row < $row_count; $row++){
										echo "<tr>";
											echo "<td>".$coordinator_list[$row]->cor_f_name."</td>";
											echo "<td>".$coordinator_list[$row]->cor_l_name."</td>";
											echo "<td><select name=\"Report" . $row . "\" id=\"Report" . $row . "\" required>";
											for ($row1 = 0; $row1 < $row_countCO; $row1++){
												$sel ='';
												if($coordinator_options[$row1]->cid == $coordinatorDetails[0]->coordinator_id)
													$sel ='selected';
												echo "<option value='".$coordinator_options[$row1]->cid."' $sel >".$coordinator_options[$row1]->cor_f_name.' '.$coordinator_options[$row1]->cor_l_name.' ('.$coordinator_options[$row1]->pos.')'."</option>";
											}	
											echo "</select></td>";
											echo " <td style=\"display:none;\"> <input type=\"hidden\" name=\"CoordinatorIDRow" . $row . "\" id=\"CoordinatorIDRow" . $row . "\" value=" . $coordinator_list[$row]->cid . "></td> \n";	
											
										echo "</tr>";
									}
									?>
								</tbody>
							</table>
						 
							</div>
							<input type="hidden" name="CoordinatorCount" id="CoordinatorCount"  value="<?php echo $row_count;?>" />		
						</div>
						<div class="clearfix"></div>
						<div class="col-sm-8 col-xs-12">
						  <div class="form-group">
							<label>Select Direct Report To</label>
							<select name="SelectCoordinator" id="SelectCoordinator" class="form-control select2" style="width: 100%;" onChange="ActivateCoordinatorButton(this)">
						  <option value="" ></option>
						   @foreach($directReportTo as $pcl)
							  <option value="{{$pcl->cid}}" >{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
							@endforeach
						</select>
						  </div>
						</div>
						<div class="col-sm-4 col-xs-12">
						  <div class="form-group">
						  <button type="button" class="btn btn-themeBlue margin" id="AssignCoordinator" disabled onclick="AddCoordinator()">Assign Coordinator</button>
						</div>
						</div>
					</div>
					<div class="box-header with-border mrg-t-10"></div>
					<div class="box-body">
						<div class="col-sm-12 col-xs-12">
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
									//echo $coordinatorDetails[0]->coordinator_id;
									//echo $coordinatorDetails[0]->conference_id;
									//$chapter_list = null;
									//$chip->GetChapterListIDIsPrimaryFor($coordinator->ID, $chapter_list);
									$chapter_list = DB::select(DB::raw("SELECT chapters.id, state.state_short_name as state, chapters.name as name 
									                    FROM chapters 
									                    INNER JOIN state ON chapters.state=state.id	
									                    WHERE primary_coordinator_id = {$coordinatorDetails[0]->coordinator_id} AND chapters.is_active=1 
									                    ORDER BY state.state_short_name, chapters.name") );
									if($coordinatorDetails[0]->region_id ==0){
										$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid, cd.first_name as cor_f_name, cd.last_name as cor_l_name, cp.short_title as pos 
										                FROM coordinator_details as cd 
										                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id 
										                WHERE (cd.conference_id = {$coordinatorDetails[0]->conference_id} AND cd.position_id >= 1 AND cd.position_id <= 6 AND cd.is_active=1) 
										                OR (cd.conference_id = {$coordinatorDetails[0]->conference_id} AND cd.position_id = 25 AND cd.is_active=1 )
										                ORDER BY cd.first_name, cd.last_name") );
									}else{
										$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid, cd.first_name as cor_f_name, cd.last_name as cor_l_name, cp.short_title as pos 
										                FROM coordinator_details as cd 
										                INNER JOIN coordinator_position as cp ON cd.position_id=cp.id 
										                WHERE (cd.region_id = {$coordinatorDetails[0]->region_id} AND (cd.position_id >= 1 AND cd.position_id <= 5 AND cd.is_active=1) )
										                OR (cd.position_id = 6 AND cd.conference_id = {$coordinatorDetails[0]->conference_id} AND cd.is_active=1)
										                OR (cd.position_id = 25 AND cd.conference_id = {$coordinatorDetails[0]->conference_id} AND cd.is_active=1)
										                ORDER BY cd.first_name, cd.last_name") );
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
												if($coordinator_options[$row1]->cid == $coordinatorDetails[0]->coordinator_id)
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
						<div class="col-sm-8 col-xs-12">
						  <div class="form-group">
							<label>Select Chapter</label>
							<select name="SelectChapter" class="form-control select2" style="width: 100%;" id="SelectChapter" onChange="ActivateChapterButton(this)">
						  <option value="" ></option>
						   @foreach($primaryChapterList as $pcl)
							  <option value="{{$pcl->id}}">{{$pcl->state}} - {{$pcl->chapter_name}}</option>
							@endforeach
						</select>
						  </div>
						</div>
						<div class="col-sm-4 col-xs-12">
						  <div class="form-group">
						  <button type="button" class="btn btn-themeBlue margin" id="AssignChapter" name="AssignChapter" disabled onclick="AddChapter()">Assign Chapter</button>
						</div>
						</div>
					</div>
					<div class="box-header with-border mrg-t-10">
					</div>
					<div class="box-body">
					 
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Coordinator Start Date</label>
						<input type="text" name="cord_phone" class="form-control my-colorpicker1" value="{{ $coordinatorDetails[0]->coordinator_start_date }}" disabled>
					  </div>
					</div>
					<div class="col-sm-6 col-xs-12">
					  <div class="form-group">
						<label>Last Promotion Date</label>
						<input type="text" name="CoordinatorPromoteDate" id="CoordinatorPromoteDate" class="form-control my-colorpicker1" value="{{ $lastPromoted }}" readonly>
					  </div>
					  <input type="hidden" name="CoordinatorPromoteDateNew" id="CoordinatorPromoteDateNew"  value="{{$lastPromoted}}"/>
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
			<button type="submit" class="btn btn-themeBlue margin">Save</button>
			<button type="button" class="btn btn-themeBlue margin" onclick="ConfirmCancel(this);">Reset</button>
			
			<a href='{{ route("coordinator.edit",$coordinatorDetails[0]->coordinator_id) }}' class="btn btn-themeBlue margin">Back</a>
			
		</div>
		<div class="box-body text-center">
			<?php if ($coordinatorDetails[0]->on_leave) {?>
			<button type="button" class="btn btn-themeBlue margin" id="remove-leave">Remove Volunteer on Leave</button> <?php }
			else { ?>
			<button type="submit" class="btn btn-themeBlue margin" onclick="return PreRetireValidate(true)">Put Volunteer on Leave</button>
			<?php } ?>
			<button type="submit" class="btn btn-themeBlue margin" onclick="return PreRetireValidate()">Retire Volunteer</button>
		</div> 
        <!-- /.box-body -->
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
                    url: '/mimi/getregion/'+confID,
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
                    url: '/mimi/getreporting',
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
                    url: '/mimi/getdirectreport',
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
            //var regID = $(this).val();
            var regID = $('#cord_region').val();
			var confID = $('#cord_conf').val();
			var posID = $('#cord_pos').val();
            if(confID) {
               	$.ajax({
                    url: '/mimi/getdirectreport',
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
            //var regID = $(this).val();
            var regID = $('#cord_region').val();
			var confID = $('#cord_conf').val();
			var posID = $('#cord_pos').val();
            if(confID) {
               	$.ajax({
                    url: '/mimi/getchapterprimary',
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
});

function ConfirmCancel(element){
		var result=confirm("Any unsaved changes will be lost. Do you want to continue?");
		if(result)
			location.reload()
		else
			return false;
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
		$conid = $coordinatorDetails[0]->coordinator_id;
		//echo $a = "confid = $('#cord_conf').val()";
		$confid = $coordinatorDetails[0]->conference_id;
			//$rowcount = count($coordinator_options);

			//$coordinator_options = null;

			/*$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as posi FROM coordinator_details as cd INNER JOIN coordinator_position as cp ON cd.position_id=cp.id WHERE (cd.position_id > 1 AND cd.conference_id = $confid AND cd.is_active=1) OR (cd.position_id = 7)  ORDER BY cd.position_id,cd.first_name, cd.last_name") );*/
			//$chip->load_coordinator_list_for_conference($coordinator_options, $coordinator->ConferenceID);
			
			$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as posi 
			                        FROM coordinator_details as cd 
			                        INNER JOIN coordinator_position as cp ON cd.position_id=cp.id 
			                        WHERE (cd.conference_id = $confid AND cd.position_id >= 1 AND cd.position_id <= 6 AND cd.is_active=1 )
			                        OR (cd.conference_id = $confid AND cd.position_id = 25 AND cd.is_active=1 )
			                        ORDER BY cd.first_name, cd.last_name") );

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
			$conid = $coordinatorDetails[0]->coordinator_id;
			$confid = $coordinatorDetails[0]->conference_id;
				//$rowcount = count($coordinator_options);

				//$coordinator_options = null;

			/*	$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as posi FROM coordinator_details as cd INNER JOIN coordinator_position as cp ON cd.position_id=cp.id WHERE (cd.position_id > 1 AND cd.conference_id = $confid AND cd.is_active=1) OR (cd.position_id = 7)  ORDER BY cd.position_id,cd.first_name, cd.last_name") );*/
				//$chip->load_coordinator_list_for_conference($coordinator_options, $coordinator->ConferenceID);

			$coordinator_options = DB::select(DB::raw("SELECT cd.coordinator_id as cid,cd.first_name as cor_f_name,cd.last_name as cor_l_name,cp.short_title as posi 
			                        FROM coordinator_details as cd 
			                        INNER JOIN coordinator_position as cp ON cd.position_id=cp.id 
			                        WHERE (cd.conference_id = $confid AND cd.position_id >= 1 AND cd.position_id <= 6 AND cd.is_active=1 )
			                        OR (cd.conference_id = $confid AND cd.position_id = 25 AND cd.is_active=1 )
			                        ORDER BY cd.first_name, cd.last_name") );

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
			
	function CheckPromotion(element){
		var promotionDate = prompt("If this position change is a promotion, please enter the promotion date in the format YYYY-MM-DD.  If this position change is not a promotion, press cancel and the promotion date will not be updated.","<?php echo date("Y-m-d"); ?>");

		if (promotionDate == null) {
			return true;
		}		
		else{
			// Original JavaScript code by Chirp Internet: www.chirp.com.au
			// Please acknowledge use of this code by including this header.
		
			var allowBlank = false;
			var minYear = 1980;
			var maxYear = (new Date()).getFullYear();
		
			var errorMsg = "";
		
			// regular expression to match required date format
			re = /^(\d{4})-(\d{1,2})-(\d{1,2})/;
		
			if(promotionDate != '') {
			  if(regs = promotionDate.match(re)) {
				if(regs[13] < 1 || regs[3] > 31) {
				  errorMsg = "Invalid value for day: " + regs[1];
				} else if(regs[2] < 1 || regs[2] > 12) {
				  errorMsg = "Invalid value for month: " + regs[2];
				} else if(regs[1] < minYear || regs[1] > maxYear) {
				  errorMsg = "Invalid value for year: " + regs[1] + " - must be between " + minYear + " and " + maxYear;
				}
			  } else {
				errorMsg = "Invalid date format: " + promotionDate;
			  }
			} else if(!allowBlank) {
			  errorMsg = "Empty date not allowed!";
			}
			
			var defaultReset = false;
		
			if(errorMsg != "") {
				alert(errorMsg);

				for (var i = 0 ; i<element.length ; i++)
				{
					if (element[i].defaultSelected){
						element.value = element[i].value;
						defaultReset = true;
					}
				}
			
				if (!defaultReset)
					element.value = 0;
			
				element.focus();					  
				return false;
			}
		
			var lastPromotionHidden=document.getElementById("CoordinatorPromoteDateNew");
			lastPromotionHidden.value = promotionDate;

			var lastPromotion=document.getElementById("CoordinatorPromoteDate");
			lastPromotion.value = promotionDate;
		
			return true;
		}
	}		
    function PreRetireValidate(LoA=false){
		//Ensure all their chapters and coordinators have been reassigned before we allow them to be retired.
		//First, check the coordinators
		var table=document.getElementById("coordinator-list");
		var tablerowcountCord = table.rows.length;
		var i;
		var selectbox;
		var value;
		
		for(i=0; i<tablerowcountCord; i++){
			selectid = "Report" + i;			

			value = getSelectedValue(selectid);
			if(value == <?php echo $coordinatorDetails[0]->coordinator_id; ?>){
				if (LoA)
					alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be put on a leave of absence.");
				else
					alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be retired.");
				return false;
			}
			
		}
		
		table=document.getElementById("chapter-list");
		tablerowcountChap = table.rows.length;
		
		for(i=0; i<tablerowcountChap; i++){
			selectid = "PCID" + i;			

			value = getSelectedValue(selectid);
			if(value == <?php echo $coordinatorDetails[0]->coordinator_id; ?>){
				if (LoA)
					alert ("All assigned chapters must be assigned to a new supervising coordinator before this coordinator can be put on a leave of absence.");
				else
					alert ("All assigned chapters must be assigned to a new supervising coordinator before this coordinator can be retired.");
				return false;
			}
		}
		//alert(tablerowcountChap);
		//alert(tablerowcountCord);
		if(tablerowcountChap <= 1 && tablerowcountCord <= 1){
			if (LoA){
				$("#submit_type").val('Leave');
				return true;
			}else{
				var reason = prompt("Retiring a volunteer will remove their login to MIMI.  If you wish to continue, please enter their reason for retiring and press OK.", "");
					
				if (reason != null) {
						$("#submit_type").val('Retire');
						document.getElementById("RetireReason").value = reason;
						return true;
				}		
				else
					return false;	
			}
			
		}else{
			if (LoA)
					alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be put on a leave of absence.");
				else
					alert ("All direct reports must be assigned to a new supervising coordinator before this coordinator can be retired.");
				return false;
		}
			
	}
</script>
@endsection

