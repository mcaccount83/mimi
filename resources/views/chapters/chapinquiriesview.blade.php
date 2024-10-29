@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Inquiry Details&nbsp;<small>(View)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Inquiry Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
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
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>MOMS Club of</label> <span class="field-required">*</span>
                        <input type="text" name="ch_name" class="form-control"  required value="{{ $chapterList[0]->name }}" >
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>State</label> <span class="field-required">*</span>
                        <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required >
                        <option value="">Select State</option>
                            @foreach($stateArr as $state)
                            <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Country</label> <span class="field-required">*</span>
                        <select id="ch_country" name="ch_country" class="form-control select2-bs4" style="width: 100%;" required >
                        <option value="">Select Country</option>
                            @foreach($countryArr as $con)
                            <option value="{{$con->short_name}}" {{$chapterList[0]->country == $con->short_name  ? 'selected' : ''}}>{{$con->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ch_hid_country" value="{{ $chapterList[0]->country }}">
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Conference</label> <span class="field-required">*</span>
                        <select id="ch_conference" name="ch_conference" class="form-control select2-bs4" style="width: 100%;" required disabled>
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
                        <select id="ch_region" name="ch_region" class="form-control select2-bs4-bs4" style="width: 100%;" required >
                        <option value="">Select Region</option>
                            @foreach($regionList as $rl)
                            <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
                    </div>
                    </div>
                     <!-- /.form group -->
                     <div class="col-sm-4 ">
                        <div class="form-group">
                            <br>
                                    </div>
                                </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>Status</label> <span class="field-required">*</span>
                        <select id="ch_status" name="ch_status" class="form-control select2-bs4" style="width: 100%;" required >
                        <option value="">Select Status</option>
                        <option value="1" {{$chapterList[0]->status == 1  ? 'selected' : ''}}>Operating OK</option>
                        <option value="4" {{$chapterList[0]->status == 4  ? 'selected' : ''}}>On Hold Do not Refer</option>
                        <option value="5" {{$chapterList[0]->status == 5  ? 'selected' : ''}}>Probation</option>
                        <option value="6" {{$chapterList[0]->status == 6  ? 'selected' : ''}}>Probation Do Not Refer</option>
                        </select>
                        <input type="hidden" name="ch_hid_status" value="{{ $chapterList[0]->status }}">
                    </div>
                    </div>
                                    <!-- /.form group -->
                        <div class="col-sm-8 ">
                    <div class="form-group">
                        <label>Status Notes (not visible to board members)</label>
                        <input type="text" name="ch_notes" class="form-control"  value="{{ $chapterList[0]->notes}}" >
                    </div>
                    </div>
                    <!-- /.form group -->
                    <div class="col-sm-12">
                    <div class="form-group">
                        <label>Boundaries</label> <span class="field-required">*</span>
                        <input type="text" name="ch_boundariesterry" class="form-control" rows="2" value="{{ $chapterList[0]->territory }}" required >
                    </div>
                    </div>

                       <!-- /.form group -->
                       <div class="col-md-6">
                        <div class="form-group">
                          <label>Inquiries Email Address</label> <span class="field-required">*</span>
                          <input type="email" name="ch_inqemailcontact" class="form-control" value="{{ $chapterList[0]->inquiries_contact }}" required>
                        </div>
                      </div>
                      <!-- /.form group -->
                      <div class="col-md-6">
                        <div class="form-group">
                          <label>Inquiries Notes (not visible to board members)</label>
                          <input type="text" name="ch_inqnote" class="form-control" value="{{ $chapterList[0]->inquiries_note }}">
                        </div>
                      </div>
                  <!-- /.form group -->
                  <div class="col-sm-12">
                  <div class="form-group">
                    <label>Additional Information (not visible to board members)</label>
                    <textarea name="ch_addinfo" class="form-control" rows="4" >{{ $chapterList[0]->additional_info }}</textarea>
                  </div>
                  </div>

                </div>
            </div>

            <div class="card-header">
                <h3 class="card-title">President</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>First Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_fname" class="form-control " value="{{ $chapterList[0]->first_name }}"  required >
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6 ">
              <div class="form-group">
                <label>Last Name</label> <span class="field-required">*</span>
                <input type="text" name="ch_pre_lname" class="form-control " value="{{ $chapterList[0]->last_name }}"  required >
              </div>
              </div>
                            <!-- /.form group -->
              <div class="col-sm-6 ">
              <div class="form-group">
                <label>Email</label> <span class="field-required">*</span>
                <input type="email" name="ch_pre_email" id="ch_pre_email" class="form-control "  value="{{ $chapterList[0]->bd_email }}"  required >
                <input type="hidden" id="ch_pre_email_chk" value="{{ $chapterList[0]->bd_email }}">
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone</label><span class="field-required">*</span>
                        <input type="text" name="ch_pre_phone" id="ch_pre_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $chapterList[0]->phone }}" required>
                </div>
            </div>
        </div>
    </div>


              <div class="card-body text-center">
              <a href="{{ route('chapters.chapinquiries') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp;&nbsp;&nbsp;Back</a>
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
        // Disable fields and buttons
$(document).ready(function () {
   $('input, select, textarea').prop('disabled', true);
});



</script>

@endsection
