@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
      <h1>
        M2M Donation
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">M2M Donation</li>
      </ol>
    </section>
    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.createdonation",$chapterList[0]->id) }}'>

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
                
                <input type="hidden" name="ch_pre_email" value="{{ $chapterList[0]->bor_email }}">
				<input type="hidden" name="ch_pc_fname" value="{{ $chapterList[0]->cor_fname }}">              
				<input type="hidden" name="ch_pc_lname" value="{{ $chapterList[0]->cor_lname }}">              
				<input type="hidden" name="ch_pc_email" value="{{ $chapterList[0]->cor_email }}">              
				<input type="hidden" name="ch_pc_confid" value="{{ $chapterList[0]->cor_confid }}">              
				<input type="hidden" name="ch_name" value="{{ $chapterList[0]->name }}">              
				<input type="hidden" name="ch_state" value="{{ $chapterList[0]->statename }}">         
                
                
              <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
              <div class="form-group">
                <label>Chapter Name</label>
                <input type="text" name="ch_name" class="form-control my-colorpicker1" maxlength="200" required value="{{ $chapterList[0]->statename }} - {{ $chapterList[0]->name }}"  readonly>
              </div>
              </div>
                 <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>M2M Donation Date</label>
                <input type="date" name="ch_m2m_date" id="ch_m2m_date" class="form-control my-colorpicker1" value="{{ $chapterList[0]->m2m_date }}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>M2M Donation Amount</label>
                <input type="text" name="ch_m2m_payment" id="ch_m2m_payment" class="form-control my-colorpicker1" value="{{ $chapterList[0]->m2m_payment }}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Sustaining Chapter Donation Date</label>
                <input type="date" name="ch_sustaining_date" id="ch_sustaining_date" class="form-control my-colorpicker1" value="{{ $chapterList[0]->sustaining_date }}" >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 col-xs-12">
              <div class="form-group">
                <label>Sustaining Chapter Donation Amount</label>
                <input type="text" name="ch_sustaining_donation" id="ch_sustaining_donation" class="form-control my-colorpicker1" value="{{ $chapterList[0]->sustaining_donation }}" >
              </div>
              </div>
            <!-- /.form group -->
              <div class="col-sm-12 col-xs-12">
			   <div class="radio-chk">
				<div class="col-sm-12 col-xs-12">
					<div class="form-group">
                        <label>Send M2M Donation Thank You to Chapter </label>
                        <label ><input type="checkbox" name="ch_thanks" id="ch_thanks" class="ios-switch green bigswitch"/><div><div></div></div>
						</label>
                    </div>
				</div>
              </div>
                    </div>    
                    <div class="col-sm-12 col-xs-12">
			   <div class="radio-chk">
				<div class="col-sm-12 col-xs-12">
					<div class="form-group">
                        <label>Send Sustaining Chapter Donation Thank You to Chapter </label>
                        <label ><input type="checkbox" name="ch_sustaining" id="ch_sustaining" class="ios-switch green bigswitch"/><div><div></div></div>
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
              <a href="{{ route('report.m2mdonation') }}" class="btn btn-themeBlue margin">Back</a>
        
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
