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
                    @php
                        $thisDate = \Carbon\Carbon::now();
                    @endphp
                        <div class="author">
                                <div class="border-gray avatar">
                                    <img src="{{ asset('chapter_theme/img/logo.png') }}" alt="...">
                                </div>
                               <h2 class="moms-c"> MOMS Club of {{$chapterDetails->name}}, {{$chapterState}} </h2>
                            </a>
                            <h4 class="ein">
                                EIN: {{$chapterDetails->ein}}
                            </h4>
                            <p class="description">
                                Boundaries: {{$chapterDetails->territory}}
                            </p>
                        </div>
                            <p class="description text-center">
                                <b>{{$borDetails->first_name}} {{$borDetails->last_name}}, {{$boardPositionAbbreviation}}</b>
                            </p>
                        <p class="description text-center">
                               Welcome to the MOMS Club's "MOMS information Management Interface" -- MIMI!
                               </br>Here you can view your chapter's information, update your profile, complete End of Year Reports, etc.
                            </p>
                            @if($thisDate->month >= 5 && $thisDate->month <= 8)
                             <div id="readOnlyText" class="description text-center">
                                <p><span style="color: red;"><strong>All Board Member Information is READ ONLY at this time.<br>
                                    In order to add new board members to MIMI, please complete the Board Election Report.<br>
                                    If you need to make updates to your current year officers, please contact your Primary Coordinator.</strong></span></p>
                            </div>
                            @endif
                    </div>

                </div>
            </div>

            @php
            $admin = DB::table('admin')
                ->select('admin.*',
                    DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'),)
                ->leftJoin('coordinator_details as cd', 'admin.updated_id', '=', 'cd.coordinator_id')
                ->orderBy('admin.id', 'desc') // Assuming 'id' represents the order of insertion
                ->first();

            $eoy_boardreport = $admin->eoy_boardreport;
            $eoy_financialreport = $admin->eoy_financialreport;

            $boardreport_yes = ($eoy_boardreport == 1);
            $financialreport_yes = ($eoy_financialreport == 1);
        @endphp


            <div class="col-md-12">
                <div class="card">
                   <div class="card-body">

                    @if ($thisDate->gte($due_date))
                        @if ($due_date->month === $thisDate->month)
                            <div class="col-md-12" style="color: green;"><center>Your chapter's anniversary month is <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is due now.</center></div>
                        @else
                            <div class="col-md-12" style="color: red;"><center>Your chapter's anniversary month was <strong>{{ $startMonth }}</strong>.&nbsp; Your Re-registration payment is now considered overdue.</center></div>
                        @endif
                        <div class="col-md-12"><br></div>
                        <div class="col-md-12 text-center">
                            <a href="{{ route('board.showreregpayment') }}" class="btn btn-info btn-fill"><i class="fa fa-money fa-fw" aria-hidden="true" ></i>&nbsp; PAY HERE</a>
                        </div>
                        <hr>
                    <div class="col-md-12"><br></div>
                    @endif

                    <div class="col-md-12"><br></div>

                    <div class="col-md-12 text-center">
                    <div class="col-md-4 float-left">
                        @if($chapterDetails->ein_letter=='1')
                      <a class="btn btn-info btn-fill" href="{{ $chapterDetails->ein_letter_path }}" target="blank"><i class="fa fa-bank fa-fw" aria-hidden="true" ></i>&nbsp; View/Download EIN Letter</a>
                      	@else
                       <a class="btn btn-info btn-fill" href="#" <?php echo "disabled";?>><i class="fa fa-bank fa-fw" aria-hidden="true" ></i>&nbsp; No EIN Letter on File</a>
                       	@endif
                    </div>

                @if($thisDate->month >= 1 && $thisDate->month <= 4)
                    <div id="reportStatusText" class="description text-center">
                        <p><strong><?php echo date('Y')-1 .'-'.date('Y');?> EOY Reports are not available at this time.</strong></p>
                    </div>
                @endif

                @if($thisDate->month >= 5 && $thisDate->month <= 12 && $boardreport_yes)
                @if($chapterDetails->new_board_active != '1')
                    <div class="col-md-4 float-left">
                        <button id="BoardReport" type="button" class="btn btn-info btn-fill" onclick="window.location.href='{{ route('boardinfo.showboardinfo', ['id' => $chapterDetails->id]) }}'">
                            <i class="fa fa-user-plus fa-fw" aria-hidden="true" ></i>&nbsp; {{ date('Y') . '-' . (date('Y') + 1) }} Board Election Report
                        </button>
                    </div>
                    @endif
                    @endif
                    @if($thisDate->month >= 6 && $thisDate->month <= 12 && $financialreport_yes)
                    <div class="col-md-4 float-left">
                        <button id="FinancialReport" type="button" class="btn btn-info btn-fill" onclick="window.location.href='{{ route('board.showfinancial', ['id' => $chapterDetails->id]) }}'">
                            <i class="fa fa-usd fa-fw" aria-hidden="true" ></i>&nbsp; {{ date('Y')-1 .'-'.date('Y') }} Financial Report
                        </button>
					</div>
                @endif

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
                                    <input  type="email" class="form-control" name="bor_email" id="bor_email" onblur="checkDuplicateEmail(this.value,this.id)" value="{{$borDetails->email}}" maxlength="50" required  >
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
                                    <label>Update Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="bor_pswd" id="bor_pswd" value="" maxlength="30">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Confirm Updated Password</label>
                                    <input  type="password" class="form-control cls-pswd" placeholder="***********" name="bor_pswd_cnf" id="bor_pswd_cnf" value="" maxlength="30">
                                </div>
                            </div>
                        </div>
						<input  type="hidden" name="bor_pswd_cng" id="bor_pswd_cng" value="0">
                    <input  type="hidden" class="form-control" name="bor_positionid" value="{{$borPositionId}}">
                </div>

                <div class="card-header">
                    <h4 class="card-title">CHAPTER INFORMATION - CONTACT PRESIDENT TO UPDATE</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Chapter Website</label>
                                <p>{{ $chapterDetails->website_url ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>ONLINE CHAPTER DISCUSSION GROUP (MEETUP, GOOGLE GROUPS, ETC)</label>
                                <p>{{ $chapterDetails->egroup ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>FACEBOOK</label>
                                <p>{{ $chapterDetails->social1 ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>TWITTER</label>
                                <p>{{ $chapterDetails->social2 ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>INSTAGRAM</label>
                                <p>{{ $chapterDetails->social3 ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>CHAPTER E-MAIL ADDRESS</label>
                                    <p>{{ $chapterDetails->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>E-MAIL ADDRESS TO GIVE TO MOMS INTERESTED IN JOINING YOUR CHAPTER</label>
                                    <p>{{ $chapterDetails->inquiries_contact ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>PO BOX</label>
                                    <p>{{ $chapterDetails->po_box ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                </div>

              <div class="card-header">
                    <h4 class="card-title">READ ONLY - PLEASE CONTACT PC IF INCORRECT</h4>
                </div>
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED MONTH</label>
                                    <p>{{$currentMonthAbbreviation}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FOUNDED YEAR</label>
                                    <p>{{$chapterDetails->start_year}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>RE-REGISTRATION DUES LAST PAID</label>
                                    <p>{{\Carbon\Carbon::parse($chapterDetails->dues_last_paid)->format('m-d-Y')}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>LAST NUMBER OF MEMBERS REGISTERED</label>
                                    <p>{{$chapterDetails->members_paid_for}}</p>
                                </div>
                            </div>
                        </div>

                            <div class="row">
                                <div class="col-md-6 BoardInfoStatus">
                                    <div class="form-group">
                                        <label><?php echo $a = date('Y'); echo "-"; echo $a + 1;?> Board Info Received</label>
                                        <p>{{$chapterDetails->new_board_submitted == '1' ? 'Received' : 'Not Received'}}</p>
                                    </div>
                                </div>
                                <div class="col-md-6 FinancialReportStatus">
                                    <div class="form-group">
                                        <label><?php echo date('Y') - 1 . '-' . date('Y');?> Financial Report Received</label>
                                        <p>{{$chapterDetails->financial_report_received == '1' ? 'Received' : 'Not Received'}}</p>
                                    </div>
                                </div>
                    </div>
                </div>
                <div class="card-header">
                    <h4 class="card-title">INTERNATIONAL MOMS CLUB COORDINATORS</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                    <div class="col-md-6">
						<input  type="hidden" id="pcid" value="{{ $chapterDetails->primary_coordinator_id}}">
						<div id="display_corlist">
						</div>
                    </div>
                </div>

			<div class="card-body card-b"><hr></div>
                   <div class="box-body text-center">
                        <button type="submit" id="Save" class="btn btn-info btn-fill" onclick="return PreSaveValidate()"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button></div><br>
                    <div class="box-body text-center">
                    {{-- <button type="button" class="btn btn-info btn-fill" onclick="window.open('https://groups.google.com/a/momsclub.org/g/2023-24boardlist"><i class="fa fa-list fa-fw" aria-hidden="true" ></i>&nbsp; BoardList Forum</button> --}}
                    <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-info btn-fill"><i class="fa fa-graduation-cap fa-fw" aria-hidden="true" ></i>&nbsp; eLearning Library</button>
                    <a href="{{ route('board.resources') }}" class="btn btn-info btn-fill"><i class="fa fa-briefcase fa-fw" aria-hidden="true" ></i>&nbsp; Chapter Resources</a>
                </div>
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
/* Disable fields and buttons  */
$(document).ready(function () {
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth() + 1; // JavaScript months are 0-based

    if (currentMonth >= 5 && currentMonth <= 8) {
        // Disable all input fields, select elements, textareas and Save button
        $('input, select, textarea').prop('disabled', true);
        $('#Save').prop('disabled', true);
    } else {
        // If the condition is not met, keep the fields active
        $('input, select, textarea').prop('disabled', false);
        $('#Save').prop('disabled', false);
    }

        // //Update to show/hide for true/false
        // $('#reportStatusText').show();  /*report status text (.show/.hide to change visibility)*/
        // $('#readOnlyText').hide();  /*read only text (.show/.hide to change visibility)*/
        // $('input, select, textarea').prop('disabled', false);  /*fields on page (true disables fields for editing)*/
        // $('#BoardReport').prop('disabled', true);  /*board report button (true grays out button)*/
        // $('#FinancialReport').prop('disabled', true);  /*financial report button (true grays out button)*/
        // $('#Save').prop('disabled', false);  /*save button (true grays out button)*/
        // $('.BoardInfoStatus').hide();  /*board info status (.show/.hide to change visibility)*/
        // $('.FinancialReportStatus').hide();  /*financial report status (.show/.hide to change visibility)*/

        // //ALWAYS leave thise fiels set to "true" it works on conditional logic for submtited Election Report
        // $('#BoardReportAlwaysDisabled').prop('disabled', true);

    //Check the disabled status of EOY Buttons and show the "fields are locked" description if necessary
    if ($('input, select, textarea').prop('disabled')) {
            $('.description').show();
        }
 });

 $( document ).ready(function() {
	var phoneListArr = ["bor_phone"];
    for (var i = phoneListArr.length - 1; i >= 0; i--) {
        var inputValue = $("#"+phoneListArr[i]).val();
        if(inputValue.length > 10) inputValue = inputValue.substring(0,12);
        var reInputValue = inputValue.replace(/(\d{3})(\d{3})/, "$1-$2-");
        $("#"+phoneListArr[i]).val(reInputValue);
    }
	$("bor_phone").keyup(function() {
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
                console.error("AJAX Error:", jqXHR, exception);
            }
        });
    }

  });

  function isPhone() {
    if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode != 8) {
        event.keyCode = 0;
        alert("Please Enter Number Only");
        return false;
    }
}

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

  function PreSaveValidate(){
    var phoneListArr = ["bor_phone"];
        for (var i = 0; i < phoneListArr.length; i++) {
            var inputField = document.getElementById(phoneListArr[i]);
            var inputValue = inputField.value;
            inputValue = inputValue.replace(/-/g, ''); // Remove hyphens
            inputValue = inputValue.replace(/\D/g, '').substring(0, 10); // Remove non-digits and limit to 10 digits
            inputField.value = inputValue; // Update the input field with the cleaned value
        }

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

		//Okay, all validation passed, save the records to the database
		return true;
	}

</script>
@endsection
