@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
      <h1>
        Report Status Update
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Report Status Update</li>
      </ol>
    </section>
    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.updatestatus",$chapterList[0]->id) }}'>
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
              <div class="col-sm-12 col-xs-12"><br> </div>
              <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Board Report</h3>
              </div>
                <div class="box-body">
              			    <div class="radio-chk">
                <div class="col-sm-4 col-xs-12">
                  <div class="form-group">
                    <label>New Board Submitted</label>
                    <label style="display: block;"><input type="checkbox" name="ch_board_submitted" id="" class="ios-switch green bigswitch" {{$chapterList[0]->new_board_submitted == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                </div>
                  <div class="radio-chk">
                <div class="col-sm-4 col-xs-12">
                  <div class="form-group">
                    <label>New Board Active</label>
                    <label style="display: block;"><input type="checkbox" name="ch_board_active" id="" class="ios-switch green bigswitch" {{$chapterList[0]->new_board_active == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                </div>

            </div>
                <div class="col-sm-12 col-xs-12">
                    <p><strong>Board Report Note:</strong>   Activating Board Report HERE will only update the STATUS and will NOT trigger the activation sequence.  To trigger the board activation sequence, activate individually from the Board Report or as a group from the Board Report List</p>
                </div>
            <div class="col-sm-12 col-xs-12"><br> </div>
            <div class="box-header with-border mrg-t-10">
              <h3 class="box-title">Financial Report</h3>
            </div>
              <div class="box-body">
                    <div class="radio-chk">
                <div class="col-sm-4 col-xs-12">
                  <div class="form-group">
                    <label>Financial Report Submitted</label>
                    <label style="display: block;"><input type="checkbox" name="ch_financial_received" id="" class="ios-switch green bigswitch" {{$chapterList[0]->financial_report_received == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                </div>
                  <div class="radio-chk">
                <div class="col-sm-4 col-xs-12">
                  <div class="form-group">
                    <label>Financial Report Complete</label>
                    <label style="display: block;"><input type="checkbox" name="ch_financial_complete" id="" class="ios-switch green bigswitch" {{$chapterList[0]->financial_report_complete == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                </label>
                  </div>
                  </div>
                </div>

                  <div class="radio-chk">
                    <div class="col-sm-4 col-xs-12">
                      <div class="form-group">
                        <label>Report Extension Given</label>
                        <label style="display: block;"><input type="checkbox" name="ch_report_extension" id="" class="ios-switch green bigswitch" {{$chapterList[0]->report_extension == '1'  ? 'checked' : ''}} /><div><div></div></div>
                                    </label>
                      </div>
                      </div>
                    </div>

                      <div class="col-sm-6 col-xs-12">
                        <div class="form-group">
                          <label>Extension Notes</label>
                          <input type="text" name="ch_extension_notes" id="ch_extension_notes" class="form-control my-colorpicker1" maxlength="50" value="{{$chapterList[0]->extension_notes}}">
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <p><strong>Financial Report Note:</strong>  Submitting Board Report HERE will only update the STATUS and will NOT assign a report reviewer.  A report reviewer will need to be aassigned before the review process can begin to avoid receiving an error code.</p>
                        <p><strong>Report Extension Note:</strong>  Checking this box HERE will exempt chapter from receiving EOY Late Notice if batch notices are sent.</p>
                    </div>
                <div class="col-sm-12 col-xs-12"><br> </div>
              <div class="box-body text-center">
                          <button type="submit" class="btn btn-themeBlue margin"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>

              <a href="{{ route('report.eoystatus') }}" class="btn btn-themeBlue margin"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>

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
            url: '{{ url("/checkreportid/") }}' + '/' + pcid,
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

