@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Chapter Re-Reg Date
       <small>Edit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Re-Reg Date</li>
      </ol>
    </section>

    <!-- Main content -->
    <form method="POST" name="admin-rereg-date" action='{{ route("admin.updatereregdate",$chapterList[0]->id) }}'">
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
                <div class="col-sm-12 col-xs-12">
                    <h3>
                    MOMS Club of&nbsp;{{ $chapterList[0]->name }},&nbsp;
                    @foreach($stateArr as $state)
    @if($chapterList[0]->state == $state->id)
        {{$state->state_long_name}}
    @endif
@endforeach
                    </h3>
                </div>
            <h4>
                <div class="col-sm-4 col-xs-12">
                    Founded Month:&nbsp;{{$currentMonth}}
                </div>
                <div class="col-sm-4 col-xs-12">
                    Founded Year:&nbsp;{{ $chapterList[0]->start_year}}
                </div>
                <div class="col-sm-4 col-xs-12">&nbsp;</div>
                <div class="col-sm-4 col-xs-12">
                    Re-Registration Dues Paid:&nbsp;{{$chapterList[0]->dues_last_paid }}
                </div>
                <div class="col-sm-4 col-xs-12">
                    Number of Members:&nbsp;{{ $chapterList[0]->members_paid_for }}
                </div>
                <div class="col-sm-12 col-xs-12">
                    Re-Registration Notes (not visible to board members):&nbsp;{{ $chapterList[0]->reg_notes}}
                </div>
            </h4>
            </div>

<br>
            <div class="box-header with-border">
              <h3 class="box-title">Update Re-Reg Date</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                    <div class="col-sm-4 col-xs-12">
                        <div class="form-group">
                            <label>Founded Month (used for renewal month)</label>
                            <select name="ch_founddate" class="form-control select2" style="width: 100%;">
                            <option value="">Select Month</option>
                            @foreach($foundedMonth as $key=>$val)
                            <option value="{{$key}}" {{$currentMonth == $key  ? 'selected' : ''}}>{{$val}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
              <div class="col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>Next Renwal Year</label>
                        <input type="text" name="ch_renewyear" class="form-control my-colorpicker1" value="{{ $chapterList[0]->next_renewal_year}}">
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="form-group">
                        <label>Dues Last Paid</label>
                        <input type="text" name="ch_duespaid" class="form-control my-colorpicker1" value="{{ $chapterList[0]->dues_last_paid}}">
                    </div>
                </div>
                </div>


            <!-- /.box-body -->
            <div class="box-body text-center">
              <button type="submit" class="btn btn-themeBlue margin"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>

              <a href="{{ route('admin.reregdate') }}" class="btn btn-themeBlue margin"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>
              </div>

            <!-- /.box-body -->

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



