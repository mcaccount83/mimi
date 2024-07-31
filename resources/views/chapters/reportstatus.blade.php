@extends('layouts.coordinator_theme')

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Report Status Update&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Report Status Update</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.updatestatus",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Re-Registration Payment</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()" disabled>
              </div>
              </div>
              <!-- /.form group -->
            <div class="col-sm-4">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
              </div>
            </div>

             <div class="card-header">
                        <h3 class="card-title">Board Report</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="ch_board_submitted" id="ch_board_submitted" class="custom-control-input" {{ $chapterList[0]->new_board_submitted ? 'checked' : '' }} />
                    <label class="custom-control-label" for="ch_board_submitted">New Board Submitted</label>
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="ch_board_active" id="ch_board_active" class="custom-control-input" {{ $chapterList[0]->new_board_active ? 'checked' : '' }} />
                        <label class="custom-control-label" for="ch_board_active">New Board Active</label>
                    </div>
                </div>
                <div class="col-sm-12">&nbsp;</div>
                <div class="col-sm-12">
                    <p><strong>Board Report Note:</strong>   Activating Board Report HERE will only update the STATUS and will NOT trigger the activation sequence.  To trigger the board activation sequence, activate individually from the Board Report or as a group from the Board Report List</p>
                </div>
            </div>
        </div>

            <div class="card-header">
                <h3 class="card-title">Financial Report</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
      <!-- /.form group -->
            <div class="col-sm-3">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="ch_financial_received" id="ch_financial_received" class="custom-control-input" {{ $chapterList[0]->financial_report_received ? 'checked' : '' }} />
                    <label class="custom-control-label" for="ch_financial_received">Financial Report Submitted</label>
                </div>
                </div>
                <div class="col-sm-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="ch_financial_complete" id="ch_financial_complete" class="custom-control-input" {{ $chapterList[0]->financial_report_complete ? 'checked' : '' }} />
                        <label class="custom-control-label" for="ch_financial_complete">Financial Report Complete</label>
                    </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="ch_report_extension" id="ch_report_extension" class="custom-control-input" {{ $chapterList[0]->report_extension ? 'checked' : '' }} />
                            <label class="custom-control-label" for="ch_report_extension">Report Extension Given</label>
                        </div>
                        </div>
                    <div class="col-sm-12">&nbsp;</div>
                      <div class="col-sm-6">
                        <div class="form-group">
                          <label>Extension Notes</label>
                          <input type="text" name="ch_extension_notes" id="ch_extension_notes" class="form-control" maxlength="50" value="{{$chapterList[0]->extension_notes}}">
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <p><strong>Financial Report Note:</strong>  Submitting Board Report HERE will only update the STATUS and will NOT assign a report reviewer.  A report reviewer will need to be aassigned before the review process can begin to avoid receiving an error code.</p>
                        <p><strong>Report Extension Note:</strong>  Checking this box HERE will exempt chapter from receiving EOY Late Notice if batch notices are sent.</p>
                    </div>
                </div>

                <div class="card-body text-center">
                        <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>
                    <a href="{{ route('report.eoystatus') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
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

