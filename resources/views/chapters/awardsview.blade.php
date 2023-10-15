@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
      <h1>
        Chapter Awards
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Awards</li>
      </ol>
    </section>
    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.updateawards",$chapterList[0]->id) }}'>
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
                <input type="text" name="ch_name" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()" disabled>
              </div>
              </div>
              <!-- /.form group -->
            <div class="col-sm-4 col-xs-12">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2" style="width: 100%;" required disabled>
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
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2" style="width: 100%;" required disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
  </div>
  </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Award 1</h3>
              </div>
              <div class="box-body">
              <div class="col-sm-12 col-xs-12">

                                <div class="form-group">
                                <label for="NominationType1">Award 1 Type:</label>
                                    <select class="form-control" id="NominationType1" name="NominationType1" >
                                       <option value="" selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_1_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                         </div>

                                             <div class="col-sm-12 col-xs-12">
 
                                 <div class="form-group">
                                <label for="AwardDesc1">Award 1 Description:</label>
                                    <textarea class="form-control" rows="5" id="AwardDesc1" name="AwardDesc1"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_1_outstanding_project_desc'];}?></textarea>
                                 </div>

                          
                        </div>
             
			              <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Award 1 Approved</label>
                    <label style="display: block;"><input type="checkbox" name="approved_1" id="" class="ios-switch green bigswitch" {{$financial_report_array->check_award_1_approved == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
  </div>
  </div>
    </div>

              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Award 2</h3>
              </div>
              <div class="box-body">
                  <div class="col-sm-12 col-xs-12">

                                <div class="form-group">
                                <label for="NominationType2">Award 2 Type:</label>
                                    <select class="form-control" id="NominationType2" name="NominationType2" onClick="ShowOutstandingCriteria(2)">
                                       <option value="" selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_2_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                         </div>
                                                                                                                        <div id="OutstandingCriteria2" style="display: none;">

                                             <div class="col-sm-12 col-xs-12">
 
                                 <div class="form-group">
                                <label for="AwardDesc2">Award 2 Description:</label>
                                    <textarea class="form-control" rows="5" id="AwardDesc2" name="AwardDesc2"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_2_outstanding_project_desc'];}?></textarea>
                                 </div>

                          
                        </div>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Award 2 Approved</label>
                    <label style="display: block;"><input type="checkbox" name="approved_2" id="" class="ios-switch green bigswitch" {{$financial_report_array->check_award_2_approved == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                  </div>
  </div>
    </div>

              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Award 3</h3>
              </div>
              <div class="box-body">
                  <div class="col-sm-12 col-xs-12">

                                <div class="form-group">
                                <label for="NominationType3">Award 3 Type:</label>
                                    <select class="form-control" id="NominationType3" name="NominationType3" onClick="ShowOutstandingCriteria(3)">
                                       <option value="" selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_3_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                         </div>
                                                                                                                        <div id="OutstandingCriteria3" style="display: none;">

                                             <div class="col-sm-12 col-xs-12">
 
                                 <div class="form-group">
                                <label for="AwardDesc3">Award 3 Description:</label>
                                    <textarea class="form-control" rows="5" id="AwardDesc3" name="AwardDesc3"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_3_outstanding_project_desc'];}?></textarea>
                                 </div>

                          
                        </div>
                              <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Award 3 Approved</label>
                    <label style="display: block;"><input type="checkbox" name="approved_3" id="" class="ios-switch green bigswitch" {{$financial_report_array->check_award_3_approved == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                  </div>
  </div>
    </div>

              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Award 4</h3>
              </div>
              <div class="box-body">
                  <div class="col-sm-12 col-xs-12">

                                <div class="form-group">
                                <label for="NominationType4">Award 4 Type:</label>
                                    <select class="form-control" id="NominationType4" name="NominationType4" onClick="ShowOutstandingCriteria(4)">
                                       <option value="" selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_4_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                         </div>
                                                                                                                        <div id="OutstandingCriteria4" style="display: none;">

                                             <div class="col-sm-12 col-xs-12">
 
                                 <div class="form-group">
                                <label for="AwardDesc4">Award 4 Description:</label>
                                    <textarea class="form-control" rows="5" id="AwardDesc4" name="AwardDesc4"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_4_outstanding_project_desc'];}?></textarea>
                                 </div>

                          
                        </div>
                              <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Award 4 Approved</label>
                    <label style="display: block;"><input type="checkbox" name="approved_4" id="" class="ios-switch green bigswitch" {{$financial_report_array->check_award_4_approved == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
  </div></div>
    </div>

              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Award 5</h3>
              </div>
              <div class="box-body">
                                   <div class="col-sm-12 col-xs-12">

                                <div class="form-group">
                                <label for="NominationType5">Award 5 Type:</label>
                                    <select class="form-control" id="NominationType5" name="NominationType5" onClick="ShowOutstandingCriteria(5)">
                                       <option value="" selected>Select an award type</option>
                                       <option value=1 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==1) echo "selected";} ?>>Outstanding Specific Service Project (one project only)</option>
                                            <option value=2 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==2) echo "selected";} ?>>Outstanding Overall Service Program (multiple projects considered together)</option>
                                            <option value=3 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==3) echo "selected";} ?>>Outstanding Children's Activity</option>
                                            <option value=4 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==4) echo "selected";} ?>>Outstanding Spirit (formation of sister chapters)</option>
                                            <option value=5 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==5) echo "selected";} ?>>Outstanding Chapter (for chapters started before July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=6 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==6) echo "selected";} ?>>Outstanding New Chapter (for chapters started after July 1, <?php echo date('Y')-1;?>)</option>
                                            <option value=7 <?php if (!empty($financial_report_array)) {if ($financial_report_array['award_5_nomination_type']==7) echo "selected";} ?>>Other Outstanding Award</option>
                                    </select>
                                </div>
                         </div>
                                                                                             <div id="OutstandingCriteria5" style="display: none;">

                                             <div class="col-sm-12 col-xs-12">
 
                                 <div class="form-group">
                                <label for="AwardDesc5">Award 5 Description:</label>
                                    <textarea class="form-control" rows="5" id="AwardDesc5" name="AwardDesc5"><?php if (!empty($financial_report_array)) {echo $financial_report_array['award_5_outstanding_project_desc'];}?></textarea>
                                 </div>

                          
                        </div>
                              <div class="radio-chk">

                <div class="col-sm-12 col-xs-12">
                  <div class="form-group">
                    <label>Award 5 Approved</label>
                    <label style="display: block;"><input type="checkbox" name="approved_5" id="" class="ios-switch green bigswitch" {{$financial_report_array->check_award_5_approved == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                    </div>
</div>

                </div>
              </div>
              </div>
              

              <div class="box-body text-center">
                          <button type="submit" class="btn btn-themeBlue margin">Save</button>

              <button type="button" class="btn btn-themeBlue margin" onclick="window.history.go(-1); return false;">Back</button>
              </div>
        
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
    
    function ShowOutstandingCriteria(AwardNumber){
        
        var NominationElementName="";
        var CriteriaElementName="";
        
        NominationElementName = "NominationType" + AwardNumber;
        CriteriaElementName = "OutstandingCriteria" + AwardNumber;
        
        if (document.getElementById(NominationElementName).value == 1 || document.getElementById(NominationElementName).value == 2 || document.getElementById(NominationElementName).value == 3 || document.getElementById(NominationElementName).value == 4 || document.getElementById(NominationElementName).value == 5 || document.getElementById(NominationElementName).value == 6 ||document.getElementById(NominationElementName).value == 7){
            document.getElementById(CriteriaElementName).style.display = 'block';
        }
        else{
            document.getElementById(CriteriaElementName).style.display = 'none';
        }
    }
    
    
  
        </script>
        
        @endsection

  