@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Re-Registration Date')

@section('content')
    <!-- Main content -->
<section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
            <form method="POST" name="admin-rereg-date" action='{{ route("admin.updatereregdate",$chapterList[0]->id) }}'>
                @csrf

          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <h3 class="profile-username text-center">MOMS Club of {{ $chapterList[0]->name }}, {{$chapterList[0]->statename}}</h3>
              <p class="text-center">{{ $chapterList[0]->confname }} Conference, {{ $chapterList[0]->regname }} Region
              <br>

              <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Founded Month:</b> <span class="float-right">{{$chapterList[0]->startmonth}}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Founded Year:</b> <span class="float-right">{{ $chapterList[0]->start_year}}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Dues Last Paid:</b> <span class="float-right">
                            @if($chapterList[0]->dues_last_paid)
                                {{$chapterList[0]->dues_last_paid }}</span>
                            @else
                                No Payment Recorded</span>
                            @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Number of Members:</b> <span class="float-right">
                            @if ($chapterList[0]->members_paid_for)
                                {{ $chapterList[0]->members_paid_for }}</span>
                            @else
                                N/A</span>
                            @endif
                    </div>
                    <div class="d-flex align-items-center justify-content-between w-100 mb-1">
                        <b>Re-Registration Notes:</b> <span class="float-right">{{ $chapterList[0]->reg_notes}}</span>
                    </div>
                </li>
                <input type="hidden" id="ch_primarycor" value="{{ $chapterList[0]->primary_coordinator_id }}">
                <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
            </ul>

            <div class="text-center">
                @if ($chapterList[0]->is_active == 1 )
                    <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                @else
                    <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                    Disband Date: <span class="date-mask">{{ $chapterList[0]->zap_date }}</span><br>
                    {{ $chapterList[0]->disband_reason }}
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
                                @foreach($monthArr as $month)
                                    <option value="{{$month->id}}" {{$chapterList[0]->start_month_id == $month->id  ? 'selected' : ''}}>{{$month->month_long_name}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Next Renwal Year:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="text" name="ch_renewyear" class="form-control" value="{{ $chapterList[0]->next_renewal_year}}">
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Dues Last Paid:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="date" name="ch_duespaid" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->dues_last_paid}}">
                    </div>
                </div>
                <!-- /.form group -->
                <div class="form-group row">
                    <label class="col-sm-4 mb-1 col-form-label">Number of Members:</label>
                    <div class="col-sm-3 mb-1">
                        <input type="text" name="ch_members" class="form-control" value="{{ $chapterList[0]->members_paid_for}}">
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

              <a href="{{ route('admin.reregdate') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
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



