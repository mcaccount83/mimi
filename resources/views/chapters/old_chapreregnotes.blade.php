@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Re-Registration&nbsp;<small>(Notes)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Re-Registration</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("chapters.updatechapreregnotes",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Re-Registratin Notes</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="ch_nxt_renewalyear" value="{{ $chapterList[0]->next_renewal_year }}">

				<input type="hidden" name="ch_pre_email" value="{{ $chapterList[0]->bor_email }}">
				<input type="hidden" name="ch_pc_fname" value="{{ $chapterList[0]->cor_fname }}">
				<input type="hidden" name="ch_pc_lname" value="{{ $chapterList[0]->cor_lname }}">
				<input type="hidden" name="ch_pc_email" value="{{ $chapterList[0]->cor_email }}">
				<input type="hidden" name="ch_pc_confid" value="{{ $chapterList[0]->cor_confid }}">
				<input type="hidden" name="ch_name" value="{{ $chapterList[0]->name }}">
				<input type="hidden" name="ch_state" value="{{ $chapterList[0]->statename }}">

              <!-- /.form group -->
              <div class="col-sm-4 ">
                <div class="form-group">
                    <label>MOMS Club of</label> <span class="field-required">*</span>
                    <input type="text" name="name" class="form-control"  required value="{{ $chapterList[0]->name }}" >
                </div>
                </div>
                <!-- /.form group -->
                    <div class="col-sm-4 ">
                <div class="form-group">
                    <label>State</label> <span class="field-required">*</span>
                    <select id="state" name="state" class="form-control select2-bs4" style="width: 100%;" required >
                    <option value="">Select State</option>
                        @foreach($stateArr as $state)
                        <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                        @endforeach
                    </select>
                </div>
                </div>
                <!-- /.form group -->
                    <div class="col-sm-4 ">
                <div class="form-group">
                    <label>Country</label> <span class="field-required">*</span>
                    <select id="country" name="country" class="form-control select2-bs4" style="width: 100%;" required >
                    <option value="">Select Country</option>
                        @foreach($countryArr as $con)
                        <option value="{{$con->short_name}}" {{$chapterList[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
                        @endforeach
                    </select>
                </div>
                </div>
                <!-- /.form group -->
                    <div class="col-sm-4 ">
                <div class="form-group">
                    <label>Conference</label> <span class="field-required">*</span>
                    <select id="conference" name="conference" class="form-control select2-bs4" style="width: 100%;" required >
                    <option value="">Select Conference</option>
                                @foreach($confList as $con)
                        <option value="{{$con->id}}" {{$chapterList[0]->conference == $con->id  ? 'selected' : ''}} >{{$con->conference_name}} </option>
                        @endforeach
                            </select>
                            </div>
                        </div>
                <!-- /.form group -->
                    <div class="col-sm-4 ">
                <div class="form-group">
                    <label>Region</label> <span class="field-required">*</span>
                    <select id="region" name="region" class="form-control select2-bs4-bs4" style="width: 100%;" required >
                    <option value="">Select Region</option>
                        @foreach($regionList as $rl)
                        <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                        @endforeach
                    </select>
                </div>
                </div>

                <!-- /.form group -->
                    <div class="col-sm-4 ">
                <div class="form-group">
                    <label>Status</label> <span class="field-required">*</span>
                    <select id="status" name="status" class="form-control select2-bs4" style="width: 100%;" required >
                    <option value="">Select Status</option>
                    <option value="1" {{$chapterList[0]->status == 1  ? 'selected' : ''}}>Operating OK</option>
                    <option value="4" {{$chapterList[0]->status == 4  ? 'selected' : ''}}>On Hold Do not Refer</option>
                    <option value="5" {{$chapterList[0]->status == 5  ? 'selected' : ''}}>Probation</option>
                    <option value="6" {{$chapterList[0]->status == 6  ? 'selected' : ''}}>Probation Do Not Refer</option>
                    </select>
                </div>
                </div>

             <!-- /.form group -->
             <div class="col-sm-6">
                <div class="form-group">
                    <label>Last Payment</label>
                    <input type="date" name="ch_lastpay" class="form-control"  data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->dues_last_paid }}" readonly>
                </div>
            </div>
                <!-- /.form group -->
                <div class="col-sm-6">
                <div class="form-group">
                  <label>Members with last Payment</label>
                  <input type="text" name="ch_memberpaid" class="form-control" value="{{ $chapterList[0]->members_paid_for }}" >
                </div>
                </div>

			                <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Re-Registration Notes (not visible to board members)</label>
                <input type="text" name="ch_regnotes" id="ch_regnotes" class="form-control"  value="{{ $chapterList[0]->reg_notes}}" >
              </div>
              </div>

            </div>
            <!-- /.box-body -->
            <div class="card-body text-center">
              <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
              <a href="{{ route('chapters.chapreregistration') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</a>
              </div>

            </div>
            <!-- /.box -->
          </div>
        </div>
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


