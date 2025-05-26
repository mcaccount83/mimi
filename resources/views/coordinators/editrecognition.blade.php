@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Appreciation & Recognition')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updaterecognition",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                  <h3 class="profile-username text-center">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                  <p class="text-center">{{ $conferenceDescription }} Conference
                      @if ($regionLongName != "None")
                          , {{ $regionLongName }} Region
                      @else
                      @endif
                  </p>
                  <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                          <b>Supervising Coordinator:</b> <span class="float-right">{{ $ReportTo }}</span>
                          <br>
                          <b>Display Position:</b> <span class="float-right">{{ $displayPosition->long_title }}</span>
                        <br>
                        <b>MIMI Position:</b> <span class="float-right">{{ $mimiPosition?->long_title }}</span>
                          <br>
                          <div style="display: flex; justify-content: space-between;">
                            <b>Secondary Positions:</b>
                            <span style="text-align: right;">
                                @forelse($cdDetails->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                    None
                                @endforelse
                            </span>
                        </div>
                        @if ($userAdmin)
                        <b>MIMI Admin:</b> <span class="float-right">{{ $cdAdminRole->admin_role }}</span>
                        @endif

                      </li>
                      <li class="list-group-item">
                          <b>Start Date:</b> <span class="float-right date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                          <br>
                          <b>Last Promotion Date:</b> <span class="float-right date-mask">{{ $cdDetails->last_promoted }}</span>
                          <br>
                          <b>Home Chapter:</b><span class="float-right">{{ $cdDetails->home_chapter }}</span>
                          <br>
                      </li>
                  </ul>
                 <div class="text-center">
                     @if ($cdDetails->active_status == 1 && $cdDetails->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span><br>
                    @else
                        @if ($cdDetails->active_status == 1 && $cdDetails->on_leave != 1)
                            <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                        @elseif ($cdDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Coordinator is PENDING</span></b>
                        @elseif ($cdDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Coordinator was NOT APPROVED</span></b><br>
                            Rejected Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @elseif ($cdDetails->active_status == 0)
                            <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b><br>
                            Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @endif
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
                <h3 class="profile-username">Appreciation & Recognition Information</h3>
                    <!-- /.card-header -->
                    <div class="form-group row" style="margin-bottom: 0;">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label"></label>
                            <label class="col-sm-3 col-form-label">Recognition Gift</label>
                            <label class="col-sm-2 mr-5 col-form-label">Year Given</label>
                            <label class="col-sm-3 col-form-label">Recognition Gift</label>
                            <label class="col-sm-2 col-form-label">Year Given</label>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">&lt; 1 Year:</label>
                            <div class="col-sm-3">
                                <select name="recognition0" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition0 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year0"class="form-control" value="{{ $cdDetails->recognition->year0 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">1 Year:</label>
                            <div class="col-sm-3">
                                <select name="recognition1" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition1 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year1"class="form-control" value="{{ $cdDetails->recognition->year1 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">2 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition2" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition2 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year2"class="form-control" value="{{ $cdDetails->recognition->year2 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">3 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition3" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition3 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year3"class="form-control" value="{{ $cdDetails->recognition->year3 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">4 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition4" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition4 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year4"class="form-control" value="{{ $cdDetails->recognition->year4 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">5 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition5" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition5 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year5"class="form-control" value="{{ $cdDetails->recognition->year5 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">6 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition6" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition6 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year6"class="form-control" value="{{ $cdDetails->recognition->year6 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">7 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition7" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition7 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year7"class="form-control" value="{{ $cdDetails->recognition->year7 }}" >
                            </div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-1 col-form-label">8 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition8" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition8 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year8"class="form-control" value="{{ $cdDetails->recognition->year8 }}" >
                            </div>

                            <label class="col-sm-1 col-form-label">9 Years:</label>
                            <div class="col-sm-3">
                                <select name="recognition9" class="form-control" style="width: 100%;" >
                                    <option value="">Select Recognition</option>
                                        @foreach($allRecognitionGifts as $recognition)
                                        <option value="{{$recognition->id}}"
                                            @if($cdDetails->recognition->recognition9 == $recognition->id) selected @endif>
                                            {{$recognition->recognition_gift}}
                                        </option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="col-sm-1 mr-5">
                                <input type="text" name="year9"class="form-control" value="{{ $cdDetails->recognition->year9 }}" >
                            </div>

                        </div>
                    </div>

                    {{-- <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">10 Years+ or Top Tier:</label>
                            <div class="col-sm-9">
                                <input type="text" name="recognition_toptier"class="form-control" value="{{ $cdDetails->recognition->recognition_toptier }}" >
                            </div>
                        </div>
                    </div> --}}

                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="col-sm-2 col-form-label">10 Years+ or Top Tier:</label>
                            <div class="col-sm-9">
                                <textarea name="recognition_toptier" class="form-control" rows="4" >{{ $cdDetails->recognition->recognition_toptier }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12 d-flex align-items-center">
                        <label class="ml-2 col-form-label mr-2">MC Gold Pin:</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="recognition_pin" id="recognition_pin" class="custom-control-input"
                                {{$cdDetails->recognition->recognition_pin == 1 ? 'checked' : ''}}>
                                <label class="custom-control-label" for="recognition_pin"></label>
                            </div>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-md-12 d-flex align-items-center">
                            <label class="ml-2 col-form-label mr-2">MC Necklace:</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="recognition_necklace" id="recognition_necklace" class="custom-control-input"
                                    {{$cdDetails->recognition->recognition_necklace == 1 ? 'checked' : ''}}>
                                    <label class="custom-control-label" for="recognition_necklace"></label>
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
          <div class="col-md-12">
            <div class="card-body text-center">
                <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2" ></i>Save</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordreports.coordrptappreciation') }}'"><i class="fas fa-reply mr-2"></i>Back to Appreciation Report</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.view', ['id' => $cdDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Coordinator Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>

function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    alert('This Email already used in the system. Please try with new one.');
                    $("#" + id).val('');
                    $("#" + id).focus();
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
    }


</script>
@endsection
