@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Re-Registration Date')

@section('content')
    <!-- Main content -->
<section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
            <form method="POST" name="admin-rereg-date" action='{{ route("adminreports.updatereregdate",$chDetails->id) }}'>
                @csrf

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
              <p class="text-center">{{ $chDetails->confname }} Conference, {{ $chDetails->regname }} Region
              <br>

              <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Founded Month:</b> <span class="float-right">{{$chDetails->startMonth->month_long_name}}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Founded Year:</b> <span class="float-right">{{ $chDetails->start_year}}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Dues Last Paid:</b> <span class="float-right">
                            @if($chPayments->rereg_date)
                                {{$chPayments->rereg_date }}</span>
                            @else
                                No Payment Recorded</span>
                            @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Number of Members:</b> <span class="float-right">
                            @if ($chPayments->rereg_members)
                                {{ $chPayments->rereg_members }}</span>
                            @else
                                N/A</span>
                            @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Re-Registration Notes:</b> <span class="float-right">{{ $chPayments->rereg_notes}}</span>
                    </div>
                </li>
                <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                <input type="hidden" id="ch_id" value="{{ $chDetails->id }}">
                <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
            </ul>

            <div class="text-center">
                      @if ($chDetails->active_status == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @elseif ($chDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Chapter is PENDING</span></b>
                      @elseif ($chDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b><br>
                          Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @elseif ($chDetails->active_status == 0)
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @endif
                  </div>
          </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
</div>
<!-- /.col -->

<div class="col-md-8">
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
        <h3 class="profile-username">Re-Registration Information</h3>
            <!-- /.card-header -->
            <div class="row">
                <div class="col-md-12">
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Founded/Renewal Month:</label>
                    <div class="col-sm-3 mb-1">
                        <select name="ch_founddate" class="form-control" style="width: 100%;">
                            <option value="">Select Month</option>
                            @foreach($allMonths as $month)
                                <option value="{{$month->id }}"
                                    @if($chDetails->start_month_id == $month->id) selected @endif>
                                    {{$month->month_long_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Next Renwal Year:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="text" name="ch_renewyear" class="form-control" value="{{ $chDetails->next_renewal_year}}">
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Dues Last Paid:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="date" name="ch_duespaid" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chPayments->rereg_date }}">
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Number of Members:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="text" name="ch_members" class="form-control" value="{{ $chPayments->rereg_members }}">
                    </div>
                </div>


            </div>
        </div>
            </div>
            <!-- /.card-body -->
                      </div>
          <!-- /.card -->
                    </div>
        <!-- /.col -->

            </div>
            <!-- /.box-body -->
            <div class="card-body text-center">
                <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp; Save</button>

              <a href="{{ route('adminreports.reregdate') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
              </div>

            <!-- /.box-body -->

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

$(document).ready(function() {
    function loadCoordinatorList(corId) {
        if (corId != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + corId,
                type: "GET",
                success: function(result) {
                    $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                    console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    var selectedCorId = $("#ch_primarycor").val();
    loadCoordinatorList(selectedCorId);

    $("#ch_primarycor").change(function() {
        var selectedValue = $(this).val();
        loadCoordinatorList(selectedValue);
    });
});

</script>
@endsection



