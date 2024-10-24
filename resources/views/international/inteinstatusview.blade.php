@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>EIN Information&nbsp;<small>(Notes)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter EIN Information</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("international.updateinteinstatus",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">EIN/IRS Notes & Information</h3>
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

                <label>Chapter name</label>
                <input type="text" name="ch_fullname" class="form-control" value="{{ $chapterList[0]->statename }} - {{ $chapterList[0]->name }}" readonly>

              </div>
			  </div>
			  <div class="col-sm-12">
                <div class="form-group">
                    <label>EIN</label>
                    <input type="text" id="ch_ein" name="ch_ein" class="form-control" value="{{ $chapterList[0]->ein }}" readonly>
                </div>
			  </div>
			                <!-- /.form group -->
              <div class="col-sm-12">
              <div class="form-group">
                <label>EIN/IRS Notes (not visible to board members)</label>
                <input type="textera" name="ch_einnotes" id="ch_einnotes" rows="3" class="form-control" maxlength="255" value="{{ $chapterList[0]->ein_notes}}" >
              </div>
              </div>

            </div>
            <!-- /.box-body -->
            <div class="card-body text-center">
              <button type="submit" class="btn bg-gradient-primary" onclick="return PreSaveValidate()"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
              <a href="{{ route('international.inteinstatus') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</a>
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

</script>
@endsection
