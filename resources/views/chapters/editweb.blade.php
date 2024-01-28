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
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                  <label>Chapter Website</label>
                  <input type="text" name="ch_website" class="form-control my-colorpicker1" placeholder="http://www.momsclubofchaptername.com" value="{{$chapterList[0]->website_url}}" maxlength="50" id="validate_url" onchange="is_url(); updateWebsiteStatus();">
                </div>
                </div>
                  <!-- /.form group -->
                  <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        <label>Website Link Status</label> <span class="field-required">*</span>
                        <select id="ch_webstatus" name="ch_webstatus" class="form-control select2" style="width: 100%;" required>
                            <option value="0" id="option0" {{$chapterList[0]->website_status == 0 ? 'selected' : ''}} disabled>Website Not Linked</option>
                            <option value="1" id="option1" {{$chapterList[0]->website_status == 1 ? 'selected' : ''}}>Website Linked</option>
                            <option value="2" id="option2" {{$chapterList[0]->website_status == 2 ? 'selected' : ''}}>Add Link Requested</option>
                            <option value="3" id="option3" {{$chapterList[0]->website_status == 3 ? 'selected' : ''}}>Do Not Link</option>
                        </select>

                        <input type="hidden" name="ch_hid_webstatus" value="{{ $chapterList[0]->website_status }}">
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
              <button type="submit" class="btn btn-themeBlue margin"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>

              <a href="{{ route('chapter.website') }}" class="btn btn-themeBlue margin"><i class="fa fa-backward fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>
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

    // Disable Web Link Status option 0
    document.getElementById('option0').disabled = true;


  $( document ).ready(function() {

    var selectedCorId = $("select#ch_primarycor option").filter(":selected").val();
    if(selectedCorId !=""){
      $.ajax({
        url: '{{ url("/checkreportid/") }}' + '/' + selectedCorId,
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
            url: '{{ url("/checkreportid/") }}' + '/' + val,
            type: "GET",
            success: function(result) {
               $("#display_corlist").html(result);
            },
            error: function (jqXHR, exception) {

            }
        });

      }

      function is_url() {
        var str = $("#validate_url").val().trim(); // Trim leading and trailing whitespace
        var chWebStatusSelect = document.querySelector('select[name="ch_webstatus"]');

        if (str === "") {
            chWebStatusSelect.value = '0'; // Set to 0 if the input is blank
            chWebStatusSelect.disabled = true; // Disable the select field
            return true; // Field is empty, so no validation needed
        }

        var regexp = /^(https?:\/\/)([a-z0-9-]+\.(com|org))$/;

        if (regexp.test(str)) {
            chWebStatusSelect.disabled = false; // Enable the select field if a valid URL is entered
            return true;
        } else {
            alert("Please Enter URL, Should be http://xxxxxxxx.xxx format");
            chWebStatusSelect.value = '0'; // Set to 0 if an invalid URL is entered
            chWebStatusSelect.disabled = true; // Disable the select field
            return false;
        }
    }

        function updateWebsiteStatus() {
            const chWebsiteInput = document.querySelector('input[name="ch_website"]');
            const chWebStatusSelect = document.querySelector('select[name="ch_webstatus"]');

            if (chWebsiteInput.value === '') {
                chWebStatusSelect.value = '0'; // Set to 0 if the input is blank
            } else if (chWebsiteInput.value !== 'http://www.momsclubofchaptername.com') {
                // Set to 2 or 3 based on some condition, you can customize this part.
                // For now, I'm setting it to 2.
                chWebStatusSelect.value = '2';
            }
        }

</script>
@endsection



