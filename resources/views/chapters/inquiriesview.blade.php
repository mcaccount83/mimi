@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
      <h1>
        Inquiry Details
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Inquiry Details</li>
      </ol>
    </section>
    <!-- Main content -->
      <form method="POST" action="">
    @csrf
   <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box card">
            <div class="box-header with-border">
              <h3 class="box-title">Chapter</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()">
              </div>
              </div>
              <!-- /.form group -->
            <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2" style="width: 100%;" required >
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Region</label> 
                <select id="ch_region" name="ch_region" class="form-control select2" style="width: 100%;" required >
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
             <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Boundaires</label>
                <input type="text" name="ch_territory" class="form-control my-colorpicker1" value="{{ $chapterList[0]->territory }}" maxlength="50" required onkeypress="return isAlphanumeric(event)" >
			</div>
			</div>
             <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Status</label>
                <select id="ch_status" name="ch_status" class="form-control select2" style="width: 100%;" required >
                  <option value="">Select Status</option>
                  <option value="1" {{$chapterList[0]->status == 1  ? 'selected' : ''}}>Operating OK</option>
                  <option value="4" {{$chapterList[0]->status == 4  ? 'selected' : ''}}>On Hold Do not Refer</option>
                  <option value="5" {{$chapterList[0]->status == 5  ? 'selected' : ''}}>Probation</option>
                  <option value="6" {{$chapterList[0]->status == 6  ? 'selected' : ''}}>Probation Do Not Refer</option>
                </select>
                <input type="hidden" name="ch_hid_status" value="{{ $chapterList[0]->status }}">
              </div>
              </div>
          <div class="col-sm-8 col-xs-12">
              <div class="form-group">
                <label>Status Notes (not visible to board members)</label>
                <input type="text" name="ch_notes" class="form-control my-colorpicker1" maxlength="50" value="{{ $chapterList[0]->notes}}" >
              </div>
              </div>

			  <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Inquiries Email Address</label>
                <input type="email" name="ch_inqemailcontact" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_contact}}" maxlength="50" required >
              </div>
              </div>
              <div class="col-sm-8 col-xs-12">
              <div class="form-group">
                <label>Inquiries Notes (not visible to board members)</label>
                <input type="text" name="ch_inqnote" class="form-control my-colorpicker1" value="{{ $chapterList[0]->inquiries_note}}" maxlength="50" >
              </div>
              </div>
  
            
              
 <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">President</h3>
              </div>
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_pre_fname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->first_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
         <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label> 
                <input type="text" name="ch_pre_lname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->last_name }}" maxlength="50" required onkeypress="return isAlphanumeric(event)">
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control my-colorpicker1" onblur="checkDuplicateEmail(this.value,this.id)" value="{{ $chapterList[0]->bd_email }}" maxlength="50" required >
                <input type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label> 
                <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control my-colorpicker1" value="{{ $chapterList[0]->phone }}" maxlength="12" required onkeypress="return isPhone(event)">
              </div>
              </div>
                                   
          <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">International MOMS Club Coordinators</h3>
              </div>
                            <div class="box-body">
               
              <!-- /.form group -->

                    <div class="col-md-6 float-left">
						<input  type="hidden" id="pcid" value="{{ $chapterList[0]->primary_coordinator_id}}">
						<div id="display_corlist">
						</div>
                        
                    </div>    
                </div>
              </div>
              </div>
              

              <div class="box-body text-center">
                  

              <a href="{{ route('chapter.inquiries') }}" class="btn btn-themeBlue margin">Back</a>
        
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
    <script>
    function test() {
alert('test');
return false;
}
function CopyEmail(elementId){
		
		// Create a "hidden" input
		var aux = document.createElement("input");
		
		var elementName = "email" + elementId;
		
		// Assign it the value of the specified element
		aux.setAttribute("value", document.getElementById(elementName).innerHTML);
		
		// Append it to the body
		document.body.appendChild(aux);
		
		// Highlight its content
		aux.select();
		
		// Copy the highlighted text
		document.execCommand("copy");
		
		// Remove it from the body
		document.body.removeChild(aux);

		return false;

	}
function CopyNoChapter(){
		
		  var copyTextarea = document.querySelector('.js-copytextarea');
		  copyTextarea.select();
		
		  try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Copying text command was ' + msg);
		  } catch (err) {
			console.log('Oops, unable to copy');
		  }

		  clearSelection();		

		return false;

	}
function clearSelection() {
		var sel;
		if ( (sel = document.selection) && sel.empty ) {
			sel.empty();
		} else {
			if (window.getSelection) {
				window.getSelection().removeAllRanges();
			}
			var activeEl = document.activeElement;
			if (activeEl) {
				var tagName = activeEl.nodeName.toLowerCase();
				if ( tagName == "textarea" || (tagName == "input" && activeEl.type == "text") ) {
					// Collapse the selection to the end
					activeEl.selectionStart = activeEl.selectionEnd;
				}
			}
		}
	}	
	function CopyInquiryResp(elementId){

		// Create a "hidden" input
		var aux = document.createElement("input");
		
		var elementName = "response" + elementId;
		
		// Assign it the value of the specified element
		aux.setAttribute("value", document.getElementById(elementName).innerHTML);
		
		// Append it to the body
		document.body.appendChild(aux);
		
		// Highlight its content
		aux.select();
		
		// Copy the highlighted text
		document.execCommand("copy");
		
		// Remove it from the body
		document.body.removeChild(aux);

		return false;

	}
    
	var pcid = $("#pcid").val();
	if(pcid !=""){
		$.ajax({
            url: '/mimi/checkreportid/'+pcid,
            type: "GET",
            success: function(result) {
				$("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }
        </script>
        
        @endsection
