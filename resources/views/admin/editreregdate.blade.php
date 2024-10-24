@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
 <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Re-Registration Date&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Re-Registration Date</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" name="admin-rereg-date" action='{{ route("admin.updatereregdate",$chapterList[0]->id) }}'">
    @csrf

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Chapter Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->

                   <div class="col-12">
                    <h4>
                    MOMS Club of&nbsp;{{ $chapterList[0]->name }},&nbsp;
                                            @foreach($stateArr as $state)
                            @if($chapterList[0]->state == $state->id)
                                {{$state->state_long_name}}
                            @endif
                        @endforeach
                    </h4>
                </div>
            <p>
                <div class="col-12 ">
                    Founded Month:&nbsp;{{$currentMonth}}
                </div>
                <div class="col-12">
                    Founded Year:&nbsp;{{ $chapterList[0]->start_year}}
                </div>
                <div class="col-12"><br></div>
                <div class="col-12">
                    Re-Registration Dues Paid:&nbsp;{{$chapterList[0]->dues_last_paid }}
                </div>
                <div class="col-12 ">
                    Number of Members:&nbsp;{{ $chapterList[0]->members_paid_for }}
                </div>
                <div class="col-12">
                    Re-Registration Notes (not visible to board members):&nbsp;{{ $chapterList[0]->reg_notes}}
                </div>
            </p>
            </div>
        </div>
<br>
                    <div class="card-header">
                    <h3 class="card-title">Update Re-Registration Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                    <div class="col-4 ">
                        <div class="form-group">
                            <label>Founded Month (used for renewal month)</label>
                            <select name="ch_founddate" class="form-control" style="width: 100%;">
                            <option value="">Select Month</option>
                            @foreach($foundedMonth as $key=>$val)
                            <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
              <div class="col-4 ">
                    <div class="form-group">
                        <label>Next Renwal Year</label>
                        <input type="text" name="ch_renewyear" class="form-control" value="{{ $chapterList[0]->next_renewal_year}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label>Dues Last Paid</label>
                        <input type="date" name="ch_duespaid" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $chapterList[0]->dues_last_paid}}">

                        {{-- <input type="text" name="ch_duespaid" class="form-control my-colorpicker1" value="{{ $chapterList[0]->dues_last_paid}}"> --}}
                    </div>
                </div>
                </div>

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



</script>
@endsection



