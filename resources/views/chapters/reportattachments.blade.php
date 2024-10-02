@extends('layouts.coordinator_theme')

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Report Attachment Update&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Report Attachment Update</li>
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
                        <h3 class="card-title">Chapter</h3>
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
                        <h3 class="card-title">Report Attachments</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-md-12">

                <div class="col-md-12">
                    @if (!empty($chapterList[0]->roster_path))
                        <div class="col-xs-12">
                            <label class="control-label" for="RosterLink">Chapter Roster File:</label>
                            <a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->roster_path }}">Chapter Roster</a>
                        </div>
                    @else
                        <div class="col-xs-12">
                            <label class="control-label" for="RosterLink">Chapter Roster File:</label>
                            No file attached
                        </div>
                    @endif
                </div>

                <div class="col-md-12">
                    @if (!empty($chapterList[0]->bank_statement_included_path))
                        <div class="col-xs-12">
                            <label class="control-label" for="Statement1Link">Primary Bank Statement:</label>
                            <a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->bank_statement_included_path }}">Statement 1</a>
                        </div>
                    @else
                        <div class="col-xs-12">
                            <label class="control-label" for="Statement1Link">Primary Bank Statement:</label>
                            No file attached
                        </div>
                    @endif
                </div>

                <div class="col-md-12">
                    @if (!empty($chapterList[0]->bank_statement_2_included_path))
                        <div class="col-xs-12">
                            <label class="control-label" for="Statement2Link">Additional Bank Statement:</label>
                            <a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->bank_statement_2_included_path }}">Statement 2</a>
                        </div>
                    @else
                        <div class="col-xs-12">
                            <label class="control-label" for="Statement2Link">Additional Bank Statement:</label>
                            No file attached
                        </div>
                    @endif
                </div>

                <div class="col-md-12">
                    @if (!empty($chapterList[0]->file_irs_path))
                        <div class="col-xs-12">
                            <label class="control-label" for="990NLink">990N Filing:</label>
                            <a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->file_irs_path }}">990N Confirmation</a>
                        </div>
                    @else
                        <div class="col-xs-12">
                            <label class="control-label" for="990NLink">990N Filing:</label>
                            No file attached
                        </div>
                    @endif
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

