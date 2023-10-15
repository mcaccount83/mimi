@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Chapter Website Details
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Website Details</li>
      </ol>
    </section>
   
    <!-- Main content -->
    <form method="POST" name="chapter-website-list" action='{{ route("chapter.updateweb",$chapterList[0]->id) }}'">
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
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" id="ch_name" class="form-control my-colorpicker1"  value="{{ $chapterList[0]->name }}" readonly>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select name="ch_state" id="ch_state" class="form-control select2" style="width: 100%;" disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
              </div>
              </div>

            
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Status</label>
                <select id="ch_status" name="ch_status" class="form-control select2" style="width: 100%;" disabled>
                  <option value="">Select Status</option>
                  <option value="1" {{$chapterList[0]->status == 1  ? 'selected' : ''}}>Operating OK</option>
                  <option value="4" {{$chapterList[0]->status == 4  ? 'selected' : ''}}>On Hold Do not Refer</option>
                  <option value="5" {{$chapterList[0]->status == 5  ? 'selected' : ''}}>Probation</option>
                  <option value="6" {{$chapterList[0]->status == 6  ? 'selected' : ''}}>Probation Do Not Link</option>
                </select>
              </div>
              </div>
             
              </div>
            
             
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Information</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              
               <!-- /.form group -->
               <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Chapter Website</label>
				        <input type="hidden" name="CurrentWebsite" id="CurrentWebsite" value="{{$chapterList[0]->website_url}}" />
                <input type="url" name="Website" id="Website" class="form-control my-colorpicker1" onchange="ConfirmWebsiteAddressChange()" value="{{$chapterList[0]->website_url}}" pattern="https?://.+">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
                <label>Link Status</label>
              </div>
			  <input type="hidden" name="CurrentLinkStatus" id="CurrentLinkStatus" value="{{$chapterList[0]->website_link_status}}" />
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="WebStatus" id="WebStatus1" class="" value="1" onchange="ConfirmWebStatusChange()" {{$chapterList[0]->website_link_status == '1'  ? 'checked' : ''}}>
                  <span>Linked</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="WebStatus" id="WebStatus2" class="" value="2" onchange="ConfirmWebStatusChange()" {{$chapterList[0]->website_link_status == '2'  ? 'checked' : ''}}>
                  <span>Add Link Requested</span>
                </label>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label class="span-t mrg-10">
                  <input type="radio" name="WebStatus" id="WebStatus3" class="" value="3" onchange="ConfirmWebStatusChange()" {{$chapterList[0]->website_link_status == '3'  ? 'checked' : ''}}>
                 <span> Do not Link</span>
                </label>
              </div>
              </div>
                          
              <!-- /.form group -->
             <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Web Reviewer Notes (not visible to board members)</label>
                <input type="text" name="ch_notes" class="form-control my-colorpicker1" value="{{ $chapterList[0]->website_notes}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Online Discussion Group (Meetup, Google Groups, Etc)</label>
                <input type="text" name="ch_onlinediss" class="form-control my-colorpicker1" value="{{ $chapterList[0]->egroup}}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Facebook</label>
                <input type="text" name="ch_social1" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social1}}">
              </div>
              </div>
             <!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Twitter</label>
                <input type="text" name="ch_social2" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social2}}">
              </div>
              </div><!-- /.form group -->
              <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="ch_social3" class="form-control my-colorpicker1" value="{{ $chapterList[0]->social3}}">
              </div>
              </div>
              
              </div>
             <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">President</h3>
              </div>
              <div class="box-body">
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>First Name</label>
                <input type="text" name="ch_pre_fname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->first_name }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="ch_pre_lname" class="form-control my-colorpicker1" value="{{ $chapterList[0]->last_name }}" disabled>
              </div>
              </div>
             
             
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control my-colorpicker1" value="{{ $chapterList[0]->bd_email }}" disabled>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Phone</label>
                <input type="text" name="ch_pre_phone" class="form-control my-colorpicker1" value="{{ $chapterList[0]->phone }}" disabled>
              </div>
              </div>
              
              </div>
             
          <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">International Moms Clubs Coordinators</h3>
              </div>
              <div class="box-body">
               
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <select name="ch_primarycor" id="ch_primarycor" class="form-control select2" style="width: 100%; display:none" onchange="checkReportId(this.value)" required>
                <option value="">Select Primary Coordinator</option>
                @foreach($primaryCoordinatorList as $pcl)
                      <option value="{{$pcl->cid}}" {{$chapterList[0]->primary_coordinator_id == $pcl->cid  ? 'selected' : ''}}>{{$pcl->cor_f_name}} {{$pcl->cor_l_name}} ({{$pcl->pos}})</option>
                    @endforeach
                </select>
              </div>
              <div id="display_corlist"> </div>
              </div>
              </div>

			  </div>
			  
              </div>
      </div>
            
            <!-- /.box-body -->
            <div class="box-body text-center">
              <button type="submit" class="btn btn-themeBlue margin" onclick="return PreSaveValidate()">Save</button>
              
              <a href="{{ route('chapter.website') }}" class="btn btn-themeBlue margin">Back</a>
              </div>
			<input type="hidden" name="WebsiteReset" id="WebsiteReset" value="false">
			 
            <!-- /.box-body -->
            
          </div>
          
          <!-- /.box -->
        </div>
      </div>



    </section>
    </form>
    @endsection

  @section('customscript')
  <script>
  $( document ).ready(function() {
	  
    var selectedCorId = $("select#ch_primarycor option").filter(":selected").val();
    if(selectedCorId !=""){
      $.ajax({
            url: '/mimi/checkreportid/'+selectedCorId,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
    }
     
  });

	
  function checkReportId(val){
          $.ajax({
            url: '/mimi/checkreportid/'+val,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });
        
      }
  
	function ConfirmWebStatusChange(){
	
			var confirmlinkremoval = false;

			// First check and see if they changed the status and it needs to be delinked
			if(document.getElementsByName("WebStatus")[2].checked && document.getElementById("CurrentLinkStatus").value==1){ //They want the site removed and it was previously linked
				confirmlinkremoval = confirm("This chapter's website is current linked to the International MOMS Club website, selecting 'Do NOT Linked' will remove this link.  Do you wish to have the chapter's link removed?")

				if (confirmlinkremoval)
					document.getElementById("WebsiteReset").value="true";	
				else
					document.getElementById("WebStatus1").checked=true; // Set webstatus value back to 'linked'
			}	
			else if(document.getElementsByName("WebStatus")[1].checked && document.getElementById("CurrentLinkStatus").value==1){ //They want the site removed and it was previously linked
				confirmlinkremoval = confirm("This chapter's website is current linked to the International MOMS Club website, selecting 'Add Link Requested' will remove the existing link.  Do you wish to have the chapter's link removed?")
				if (confirmlinkremoval)
					document.getElementById("WebsiteReset").value="true";	
				else
					document.getElementById("WebStatus1").checked=true; // Set webstatus value back to 'linked'				
			
			}
			else if(document.getElementsByName("WebStatus")[0].checked && document.getElementById("CurrentLinkStatus").value!=1){ //They want the site removed and it was previously linked
				document.getElementById("WebsiteReset").value="true";	

			}
		}
	function ConfirmWebsiteAddressChange(){
			// They want the site linked so see if it changed
			if(document.getElementById("Website").value != document.getElementById("Website").getAttribute("value")){				

				if(document.getElementById("Website").value==""){			
					alert("You have removed the chapter's website, the link status will also be removed.");
					document.getElementById("WebsiteReset").value="true";
					
					$(this).removeAttr('checked');
					
					document.getElementById("WebStatus1").checked=false; // Set webstatus value to 'review'
					document.getElementById("WebStatus2").checked=false; // Set webstatus value to 'review'
					document.getElementById("WebStatus3").checked=false;// Set webstatus value to 'review'
					return;
				}
				
				//okay, website was changed - do they want it linked?
				if(document.getElementsByName("WebStatus")[0].checked){ //was linked and still want it linked
					confirmlinkremoval = alert("You have changed this chapter's website that is currently linked to the International MOMS Club website.  If the site has not yet been reviewed, please change the link status to Add Link Requested.")
				}
			}
	
		}

    $("form[name=chapter-website-list]").submit(function(){
      var chapterWebsite = $("input[name=Website]").val();
      var WebStatus = document.getElementsByName("WebStatus");
      if(chapterWebsite != ""){
        if(WebStatus[0].checked == false && WebStatus[1].checked == false && WebStatus[2].checked == false){
          alert("Link status must be checked."); return false;
        }
      }
    });
</script>
@endsection



