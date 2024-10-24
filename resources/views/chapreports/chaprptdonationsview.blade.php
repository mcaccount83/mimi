@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>M2M & Sustaining Chapter Donation&nbsp;<small>(Payment)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">M2M & Sustaining Chapter Donation</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("chapreports.updatechaprptdonations",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">M2M & Sustaining Chapter Donation</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">

                <input type="hidden" name="ch_pre_email" value="{{ $chapterList[0]->bor_email }}">
				<input type="hidden" name="ch_pc_fname" value="{{ $chapterList[0]->cor_fname }}">
				<input type="hidden" name="ch_pc_lname" value="{{ $chapterList[0]->cor_lname }}">
				<input type="hidden" name="ch_pc_email" value="{{ $chapterList[0]->cor_email }}">
				<input type="hidden" name="ch_pc_confid" value="{{ $chapterList[0]->cor_confid }}">
				<input type="hidden" name="ch_name" value="{{ $chapterList[0]->name }}">
				<input type="hidden" name="ch_state" value="{{ $chapterList[0]->statename }}">

                <label>Chapter Name</label>
                <input type="text" name="ch_long_name" class="form-control"  required value="{{ $chapterList[0]->statename }} - {{ $chapterList[0]->name }}"  readonly>
              </div>
              </div>
                <!-- /.form group -->
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>M2M Donation Date</label>
                        <input type="date" name="ch_m2m_date" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->m2m_date }}" >
                    </div>
                </div>

              <div class="col-sm-6">
              <div class="form-group">
                <label>M2M Donation Amount</label>
                <input type="text" name="ch_m2m_payment" id="ch_m2m_payment" class="form-control" value="{{ $chapterList[0]->m2m_payment }}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Sustaining Chapter Donation Date</label>
                    <input type="date" name="ch_sustaining_date" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->sustaining_date }}" >
                </div>
            </div>

              <div class="col-sm-6">
              <div class="form-group">
                <label>Sustaining Chapter Donation Amount</label>
                <input type="text" name="ch_sustaining_donation" id="ch_sustaining_donation" class="form-control" value="{{ $chapterList[0]->sustaining_donation }}" >
              </div>
              </div>

              <div class="col-sm-12">&nbsp;</div>
            <!-- /.form group -->
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="ch_thanks" id="ch_thanks" class="custom-control-input" />
                    <label class="custom-control-label" for="ch_thanks">Send M2M Donation Thank You to Chapter </label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="ch_sustaining" id="ch_sustaining" class="custom-control-input" />
                    <label class="custom-control-label" for="ch_sustaining">Send Sustaining Chapter Donation Thank You to Chapter </label>
                </div>
            </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="card-body text-center">
              <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
              <a href="{{ route('chapreports.chaprptdonations') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</a>
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
