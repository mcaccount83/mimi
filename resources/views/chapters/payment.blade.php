@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Re-Registration&nbsp;<small>(Payment)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Re-Registration</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.makepayment",$chapterList[0]->id) }}'>
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
              <div class="col-sm-12">
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
               		<input type="text" name="ch_fullname" class="form-control" maxlength="200" value="{{ $chapterList[0]->statename }} - {{ $chapterList[0]->name }}" readonly>

              </div>
			  </div>
			  <div class="col-sm-12">
                <div class="form-group">
                    <label>Last Payment</label>
                    <input type="date" name="ch_lastpay" class="form-control" maxlength="200" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->dues_last_paid }}" readonly>
                </div>
            </div>
			                <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>Re-Registration Notes (not visible to board members)</label>
                <input type="text" name="ch_regnotes" id="ch_regnotes" class="form-control" maxlength="50" value="{{ $chapterList[0]->reg_notes}}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Date Payment Received</label> <span class="field-required">*</span>
                    <input type="date" name="PaymentDate" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask>
{{--
                    <div class="input-group date" name="PaymentDate" id="datepicker" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#datepicker" placeholder="mm/dd/yyyy" required/>
                        <div class="input-group-append" data-target="#datepicker" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div> --}}
                </div>
            </div>
            {{--<div class="col-sm-6">
                <div class="form-group">
                    <label>Date Payment Received</label> <span class="field-required">*</span>
                    <input type="text" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask name="PaymentDate" id="PaymentDate" placeholder="mm/dd/yyyy" required>
                    <input type="date" class="form-control" name="PaymentDate" id="PaymentDate"
                        pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))"
                        value="" min="<?php echo $minDateLimit; ?>" max="<?php echo $maxDateLimit; ?>" required />
                    </div>
                </div> --}}
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Members Paid For</label> <span class="field-required">*</span>
               <input type="number" name="MembersPaidFor" id="MembersPaidFor" onKeyPress="if(this.value.length==9) return false;" class="form-control txt-num" onkeydown="return event.keyCode !== 69" min=0 required />
              </div>
              </div>

              <!-- /.form group -->
              <div class="col-sm-12">


			   <div class="radio-chk">
				<div class="col-sm-12">
					<div class="form-group">
                        <label>Send Payment Received Notification to Chapter</label>
                        <label ><input type="checkbox" name="ch_notify" id="ch_notify" class="ios-switch green bigswitch"/><div><div></div></div>
						</label>
                    </div>
				</div>
              </div>

              </div>
            </div>
            <!-- /.box-body -->
            <div class="card-body text-center">
              <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
              <a href="{{ route('chapter.registration') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</a>
              </div>
            </div>

            </div>
            <!-- /.box -->
          </div>
        </div>
    </div>
    </section>

    </form>
@endsection
@section('customscript')
<script>
// $(document).ready(function(){
// 	$(".txt-num").keypress(function (e) {
//         var key = e.charCode || e.keyCode || 0;
// 		// only numbers
// 		if (key < 48 || key > 58) {
// 			return false;
// 		}
// 	});
// });

document.querySelector('form').addEventListener('submit', function(event) {
    var dateField = document.querySelector('input[name="PaymentDate"]');
    var dateValue = dateField.value;

    if (dateValue) {
        // Convert mm/dd/yyyy to yyyy-mm-dd
        var parts = dateValue.split('/');
        var formattedDate = parts[2] + '-' + parts[0] + '-' + parts[1];
        dateField.value = formattedDate;
    }
});


</script>
@endsection



