@extends('layouts.chapter_theme')

@section('content')
<div class="container">
<div>
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
</div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-user">
                <div class="card-image color_header">

                </div>
                <div class="card-body">
                    <div class="author">
                        <a href="#">
							<div class="border-gray avatar">
								<img src="{{ asset('chapter_theme/img/logo.png') }}" alt="...">
							</div>
                            <h2 class="moms-c">MOMS Club of {{$chapterDetails->name}}, {{$chapterState}}</h2>
                        </a>
                        <h4 class="ein">
                            EIN: {{$chapterDetails->ein}}
                        </h4>
                        <p class="description">
                            Boundaries: {{$chapterDetails->territory}}
                        </p>
                    </div>
                    <p class="description text-center">
                       Welcome, <b>{{$borDetails->first_name}} {{$borDetails->last_name}}</b>, to the MOMS Club MOMS information Management Interface (MIMI)!
                       </br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                    </p>
                    <p class="description text-center" style="color:red;">All Board Member Information is READ ONLY at this time.
                    <br>In order to add new board members to MIMI, please complete the Board Election Report.
                    <br>If you need to make updates to your current year officers please contact your Primary Coordinator.
                    </p>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12 text-center">
                        <div class="col-md-4 float-left">
                       @if($chapterDetails->ein_letter=='1')
                      <a class="btn btn-info btn-fill" href="{{ $chapterDetails->ein_letter_path }}" target="blank">View/Download EIN Letter</a>
                      	@else
                       <a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>>No EIN Letter on File</a>
                       	@endif
                      </div>

                    <div class="col-md-4 float-left">
					 @if($chapterDetails->new_board_active=='1')
								<a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report</a>
							@else
							<!--LIVE BUTTON-->
						            <!--<a class="btn btn-info btn-fill" href="<?php echo url("/boardinfo") ?>"><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report</a>-->
							<!--DISABLED BUTTON-->
									<a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Election Report</a>

							@endif


					</div>

                    <div class="col-md-4 float-left">
                            <!--DISABLED BUTTON-->
					            <a class="btn btn-info btn-fill" href="#" disabled><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</a>
					        <!--LIVE BUTTON-->
					            <!--<a class="btn btn-info btn-fill" href="<?php echo url("/board/financial/{$chapterDetails->id}") ?>"><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</a>-->


                    </div>


                    </div>
                </div>

            <form method="POST" action='{{ route("member.update",$borDetails->chapter_id) }}'>
				@csrf

				@if($borPositionId == '2')
                 <div class="card-header">
                    <h4 class="card-title">AVP</h4>
                </div>
                @endif

				@if($borPositionId == '3')
                <div class="card-header">
                    <h4 class="card-title">MVP</h4>
                </div>
                @endif

				@if($borPositionId == '4')
                <div class="card-header">
                    <h4 class="card-title">TREASURER</h4>
                </div>
                @endif

				@if($borPositionId == '5')
                <div class="card-header">
                    <h4 class="card-title">SECRETARY</h4>
                </div>
        		@endif
                <div class="card-body">

                        <div class="row" id="checkRadios">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label><span class="field-required">*</span>
                                    <input  type="text" class="form-control" name="bor_fname" id="bor_fname" value="{{$borDetails->first_name}}" maxlength="50" required onkeypress="return isAlphanumeric(event"  >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label><span class="field-required">*</span>
                                    <input  type="text" class="form-control" name="bor_lname" id="bor_lname" value="{{$borDetails->last_name}}" maxlength="50" required onkeypress="return isAlphanumeric(event)"  >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label><span class="field-required">*</span>
                                    <input  type="text" class="form-control" name="bor_addr" id="bor_addr" value="{{$borDetails->street_address}}" maxlength="250" required  >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>City</label><span class="field-required">*</span>
                                    <input  type="text" class="form-control" name="bor_city" id="bor_city" value="{{$borDetails->city}}"  maxlength="50" required onkeypress="return isAlphanumeric(event)"  >
                                </div>
                            </div>
                            <div class="col-md-4 pr-1">
                                <div class="form-group">
                                    <label>State</label><span class="field-required">*</span>
                                    <select  name="bor_state" id="bor_state" class="form-control select2" style="width: 100%;" required  >
                                        <option value="">Select State</option>
                                            @foreach($stateArr as $state)
                                            <option value="{{$state->state_short_name}}" {{$borDetails->state == $state->state_short_name  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                                            @endforeach
                                        </select>
                                 </div>
                            </div>
                            <div class="col-md-4 pl-1">
                                <div class="form-group">
                                    <label>Zip Code</label><span class="field-required">*</span>
                                    <input  type="text" class="form-control" name="bor_zip" id="bor_zip" maxlength="10" value="{{$borDetails->zip}}" required onkeypress="return isNumber(event)"  >
                                </div>
                            </div>
                        </div>
                        <div class="row radio-chk">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email ID</label><span class="field-required">*</span>
                                    <input  type="email" class="form-control" name="bor_email" id="bor_email"  value="{{$borDetails->email}}" maxlength="50" required  >
									<input  type="hidden" id="bor_email_chk" value="{{ $borDetails->email }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label><span class="field-required">*</span>
                                    <input  type="text" class="form-control" name="bor_phone" id="bor_phone" maxlength="12" value="{{$borDetails->phone}}" required  >
                                </div>
                            </div>

                        </div>
						<div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="bor_pswd" id="bor_pswd" value="" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="bor_pswd_cnf" id="bor_pswd_cnf" value="" maxlength="30">
                                </div>
                            </div>
                        </div>
						<input  type="hidden" name="bor_pswd_cng" id="bor_pswd_cng" value="0">
                    <input  type="hidden" class="form-control" name="bor_positionid" value="{{$borPositionId}}">
                </div>

                <div class="card-header">
                    <h4 class="card-title">CHAPTER INFORMATION</h4>
                </div>
                <div class="card-body">
                    <form>
						<div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Chapter Website</label>
                                    <input  type="text" name="ch_website" class="form-control rowheight" maxlength="150" id="ch_website" placeholder="http://www.momsclubofchaptername.com"  value="{{$chapterDetails->website_url}}" onchange="is_url()" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>ONLINE CHAPTER DISCUSSION GROUP (MEETUP, GOOGLE GROUPS, ETC)</label>
                                    <input  type="text" class="form-control" maxlength="30"  value="{{ $chapterDetails->egroup}}"readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>FACEBOOK</label>
                                    <input  type="text" class="form-control" maxlength="30"  value="{{ $chapterDetails->social1}}"readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>TWITTER</label>
                                    <input  type="text" class="form-control" maxlength="30"  value="{{ $chapterDetails->social2}}"readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>INSTAGRAM</label>
                                    <input  type="text" class="form-control" maxlength="30"  value="{{ $chapterDetails->social3}}"readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>CHAPTER E-MAIL ADDRESS</label>
                                    <input  type="email" class="form-control" maxlength="50"  value="{{ $chapterDetails->email}}"readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>E-MAIL ADDRESS TO GIVE TO MOMS INTERESTED IN JOINING YOUR CHAPTER</label>
                                    <input  type="email" class="form-control" maxlength="50" value="{{ $chapterDetails->inquiries_contact}}"  readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>PO BOX</label>
                                    <input  type="text" class="form-control" value="{{ $chapterDetails->po_box}}" maxlength="30" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-header">
                    <h4 class="card-title">READ ONLY - PLEASE CONTACT PC IF INCORRECT</h4>
                </div>
                <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED MONTH</label>
                                    <select name="ch_founddate" class="form-control select2" style="width: 100%;" required disabled>
                                        <option value="">Select Date</option>
                                        @foreach($foundedMonth as $key=>$val)
                                        <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED Year</label>
                                    <input  type="text" class="form-control" value="{{ $chapterDetails->start_year}}"  readonly >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RE-REGISTRATION DUES LAST PAID</label>
                                    <input  type="date" class="form-control" value="{{ $chapterDetails->dues_last_paid}}" readonly  >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>LAST NUMBER OF MEMBERS REGISTERED</label>
                                    <input  type="text" class="form-control" value="{{ $chapterDetails->members_paid_for}}" readonly >
                                </div>
                            </div>
                        </div>
						 <div class="row radio-chk">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo $a = date('Y'); echo "-"; echo $a+1;?> Board Info Received</label>
                                    <label style="display: block;"><input  type="checkbox" class="ios-switch green bigswitch" disabled {{$chapterDetails->new_board_submitted == '1'  ? 'checked' : ''}}/><div><div></div></div></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report Received</label>
                                    <label style="display: block;"><input  type="checkbox" class="ios-switch green bigswitch" disabled {{$chapterDetails->financial_report_received == '1'  ? 'checked' : ''}}/><div><div></div></div></label>

                                </div>
                            </div>
                            </div>

                </div>
                <div class="card-header">
                    <h4 class="card-title">INTERNATIONAL MOMS CLUB COORDINATORS</h4>
                </div>
                <div class="card-body">
                    <div class="col-md-6 float-left">
						<input  type="hidden" id="pcid" value="{{ $chapterDetails->primary_coordinator_id}}">
						<div id="display_corlist">
						</div>

                    </div>
                </div>

				 <div class="card-body card-b"><hr></div>
                   <div class="box-body text-center">

                        <button type="submit" class="btn btn-info btn-fill" onclick="return PreSaveValidate()">Save</button></div>
                        </div>
                           <div class="box-body text-center">

                    <button type="button" class="btn btn-info btn-fill" onclick="window.open('https://groups.google.com/u/1/a/momsclub.org/g/2021-22boardgroup')">Board Group/Forum</button>
                    <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-info btn-fill">eLearning Library</button></div>

                    </div>
                </div>
			</form>
            </div>
		</div>

    </div>
</div>
@endsection
@section('customscript')
<script>

//Disable Fields for EOY Editing
    $("#bor_fname").prop("readonly",true);
    $("#bor_lname").prop("readonly",true);
    $("#bor_addr").prop("readonly",true);
    $("#bor_city").prop("readonly",true);
    $("#bor_state").prop("disabled",true);
    $("#bor_zip").prop("readonly",true);
    $("#bor_email").prop("readonly",true);
    $("#bor_phone").prop("readonly",true);


function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function isAlphanumeric(e){
	var k;
	document.all ? k = e.keyCode : k = e.which;
	return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}

function isPhone() {
	if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8) {
		event.keyCode = 0;
		alert("Please Enter Number Only");
		return false;
	}
}
$( document ).ready(function() {
	var phoneListArr = ["bor_phone"];
    for (var i = phoneListArr.length - 1; i >= 0; i--) {
        var inputValue = $("#"+phoneListArr[i]).val();
        if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
        var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
        $("#"+phoneListArr[i]).val(reInputValue);
    }
	$("#bor_phone").keyup(function() {
        this.value = this.value.replace(/(\d{3})(\d{3})/, "$1-$2")
    });
	$('.cls-pswd').on('keypress', function(e) {
		if (e.which == 32)
			return false;
	});

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
});

//submit validation function
  function PreSaveValidate(){
          var NewPassword=document.getElementById("bor_pswd").value;

				//They changed their password
				if(document.getElementById("bor_pswd").value != document.getElementById("bor_pswd").getAttribute("value")){
					if(document.getElementById("bor_pswd").value != document.getElementById("bor_pswd_cnf").value){  //Make sure the password and confirmation match
						alert ("The provided passwords do not match, please re-enter your password.");
						document.getElementById("bor_pswd_cnf").focus();
						return false;
					}
					// Make sure the password is the right length
					else if(NewPassword.length < 7){
						alert("Password must be at least 7 characters.");
						document.getElementById("bor_pswd").focus();
						return false;
					}
					else{
						document.getElementById("bor_pswd_chg").value="1";
					}

				}
        //}

		//Okay, all validation passed, save the records to the database
		return true;
	}


</script>
@endsection
