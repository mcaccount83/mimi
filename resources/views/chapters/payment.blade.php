@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Chapter Re-Registrations
       <small>Payment</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Re-Registrations</li>
      </ol>
    </section>
   
    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.makepayment",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box card">
            <div class="box-header with-border">
              <h3 class="box-title">Re-Registration Payment</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
			  
				<input type="hidden" name="ch_nxt_renewalyear" value="{{ $chapterList[0]->next_renewal_year }}">
			  
				<input type="hidden" name="ch_pre_email" value="{{ $chapterList[0]->bor_email }}">
				<input type="hidden" name="ch_pc_fname" value="{{ $chapterList[0]->cor_fname }}">              
				<input type="hidden" name="ch_pc_lname" value="{{ $chapterList[0]->cor_lname }}">              
				<input type="hidden" name="ch_pc_email" value="{{ $chapterList[0]->cor_email }}">              
				<input type="hidden" name="ch_pc_confid" value="{{ $chapterList[0]->cor_confid }}">              
				<input type="hidden" name="ch_name" value="{{ $chapterList[0]->name }}">              
				<input type="hidden" name="ch_state" value="{{ $chapterList[0]->statename }}">              
			  
					<label>Chapter name</label>
               		<input type="text" name="ch_fullname" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->statename }} - {{ $chapterList[0]->name }}" readonly>
				
              </div>
			  </div>
			  <div class="col-sm-12 col-xs-12">
              <div class="form-group">
			  	<label>Last Payment</label>
               		<input type="text" name="ch_lastpay" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->dues_last_paid }}" readonly>
				
              </div>
			  </div>
			                <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Re-Registration Notes (not visible to board members)</label>
                <input type="text" name="ch_regnotes" id="ch_regnotes" class="form-control my-colorpicker1" maxlength="50" value="{{ $chapterList[0]->reg_notes}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Postmark Date of Payment</label> <span class="field-required">*</span>
                <!-- max="<?php echo $maxDateLimit;?>" -->
                <input type="date" class="form-control my-colorpicker1" name="PaymentDate" id="PaymentDate" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" value="" min="<?php echo $minDateLimit;?>"  required />
				
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Members Paid For</label> <span class="field-required">*</span>
               <input type="number" name="MembersPaidFor" id="MembersPaidFor" onKeyPress="if(this.value.length==9) return false;" class="form-control my-colorpicker1 txt-num" onkeydown="return event.keyCode !== 69" min=0 required />
              </div>
              </div>
             
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
             
			  
			   <div class="radio-chk">
				<div class="col-sm-12 col-xs-12">
					<div class="form-group">
                        <label>Send Payment Received Notification to Chapter</label>
                        <label ><input type="checkbox" name="ch_notify" id="ch_notify" class="ios-switch green bigswitch"/><div><div></div></div>
						</label>
                    </div>
				</div>
              </div>
			  
              </div>
            </div>
           </div>
      </div>
            
            <!-- /.box-body -->
            <div class="box-body text-center">
              <button type="submit" class="btn btn-themeBlue margin" onclick="return PreSaveValidate()">Save</button>
              <a href="{{ route('chapter.registration') }}" class="btn btn-themeBlue margin">Back</a>
              </div>
			 
            <!-- /.box-body -->
            
          </div>
          <!-- /.box -->
       
    </section>
    </form>
@endsection
@section('customscript')
<script>
$(document).ready(function(){
	$(".txt-num").keypress(function (e) {
        var key = e.charCode || e.keyCode || 0;
		// only numbers
		if (key < 48 || key > 58) {
			return false;
		}
	});
});
    
</script>
@endsection	
  


