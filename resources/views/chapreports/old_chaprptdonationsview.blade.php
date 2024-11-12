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
            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
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
                        <input type="text" name="name" class="form-control disable-field "  required value="{{ $chapterList[0]->name }}" >
                    </div>
                    </div>
                    <!-- /.form group -->
                        <div class="col-sm-4 ">
                    <div class="form-group">
                        <label>State</label> <span class="field-required">*</span>
                        <select id="state" name="state" class="form-control disable-field select2-bs4" style="width: 100%;" required >
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
                        <select id="country" name="country" class="form-control disable-field select2-bs4" style="width: 100%;" required >
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
                        <select id="conference" name="conference" class="form-control disable-field select2-bs4" style="width: 100%;" required disabled>
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
                        <select id="region" name="region" class="form-control disable-field select2-bs4-bs4" style="width: 100%;" required >
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
                        <select id="status" name="status" class="form-control disable-field select2-bs4" style="width: 100%;" required >
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
                        <label>Last M2M Donation</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="date" name="ch_m2m_date" class="form-control disable-field" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->m2m_date }}">
                            </div>
                            <div class="col-sm-6">
                                <input type="text" name="ch_m2m_payment" id="ch_m2m_payment" class="form-control disable-field" value="${{ $chapterList[0]->m2m_payment }}">
                            </div>
                        </div>
                    </div>
                </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Last Sustaining Chapter Donation</label>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="date" name="ch_sustaining_date" class="form-control disable-field" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->sustaining_date }}">
                        </div>
                        <div class="col-sm-6">
                            <input type="text" name="ch_sustaining_donation" id="ch_sustaining_donation" class="form-control disable-field" value="${{ $chapterList[0]->sustaining_donation }}">
                        </div>
                    </div>
                </div>
            </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>M2M Donation Received</label>
                    <input type="date" name="M2MPaymentDate" id="M2MPaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask />
                </div>
            </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>M2M Donation Amount</label>
               <input type="text" name="M2MPayment" id="M2MPayment" class="form-control"/>
              </div>
              </div>
              <!-- /.form group -->
              <div class="col-sm-6">
                <div class="form-group">
                    <label>Sustaining Chapter Donation Received</label>
                    <input type="date" name="SustainingPaymentDate" id="SustainingPaymentDate"class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask />
                </div>
            </div>
              <!-- /.form group -->
              <div class="col-sm-6">
              <div class="form-group">
                <label>Sustaining Chapter Donation Amount</label>
               <input type="text" name="SustainingPayment" id="SustainingPayment" class="form-control" />
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
// Disable fields and buttons --- then re-enable the ones we ant to save
$(document).ready(function () {
    $(document).ready(function () {
   // Disable fiels for all users with class
   $('.disable-field').prop('disabled', true);
});
});

// document.querySelector('form').addEventListener('submit', function(event) {
//     var m2mdateField = document.querySelector('input[name="M2MPaymentDate"]');
//     var m2mdateValue = m2mdateField.value;
//     var susdateField = document.querySelector('input[name="SustainingPaymentDate"]');
//     var susdateValue = susdateField.value;

//     if (m2mdateValue) {
//         // Convert mm/dd/yyyy to yyyy-mm-dd
//         var parts = m2mdateValue.split('/');
//         var formattedDate = parts[2] + '-' + parts[0] + '-' + parts[1];
//         m2mdateField.value = m2mformattedDate;
//     }

//     if (susdateValue) {
//         // Convert mm/dd/yyyy to yyyy-mm-dd
//         var parts = susdateValue.split('/');
//         var formattedDate = parts[2] + '-' + parts[0] + '-' + parts[1];
//         susdateField.value = susformattedDate;
//     }
// });

</script>
@endsection
