@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Re-Registrations</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Re-Registrations</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>{{ $message }}</p>
        </div>
        @endif
        @if ($message = Session::get('fail'))
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>{{ $message }}</p>
        </div>
        @endif

        @if ($message = Session::get('info'))
        <div class="alert alert-warning">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p>{{ $message }}</p>
        </div>
    @endif

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="card">
                <div class="card-header">
                <h3 class="card-title">List of Chapter Re-Registrations</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
              {{-- <table id="chapterlist_reReg" class="table table-sm table-hover"> --}}
                <table id="chapterlist" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Payment</th>
                            <th>Notes</th>
                            <th>Email</th>
                            <th>Chapter State</th>
                            <th>Chapter Name</th>
                            <th>Re-Registration Notes</th>
                            <th>Due</th>
                            <th>Last Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reChapterList as $list)
                        @php
                            $emailDetails = app('App\Http\Controllers\ChapterController')->getEmailDetails($list->id);
                            $emailListCord = $emailDetails['emailListCord'];
                            $cc_string = $emailDetails['cc_string'];
                            $reregistration_url = "https://momsclub.org/resources/re-registration-payment/";

                            $mail_message = "**Insert Personal Message Here**
                                <br>
                                MCL,<br>
                                International MOMS Club
                                <br>
                                <hr>
                                <b><p>Calculate your payment:</p></b>
                                <ul>
                                    <li>Determine how many people paid dues to your chapter since your last re-registration payment through today.</li>
                                    <li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
                                    <li>If this total amount of members is less than 10, make your check for the amount of $50</li>
                                    <li>If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due</li>
                                    <li>Payments received after the last day of your renewal month should include a late fee of $10</li>
                                </ul>
                                <br>
                                <b><p>Make your payment:</p></b>
                                <ul>
                                    <li><a href='$reregistration_url'>Pay Online</a> (Password:  daytime support)</li>
                                    <li>Pay via Mail to: Chapter Re-Registration, 208 Hewitt Dr. Ste 103 #328, Waco, TX 76712</li>
                                </ul>";
                        @endphp
                        <tr>
                            <td>
                                @if (Session::get('positionid') == 6)
                                    <center><a href="{{ url("/chapter/re-registration/payment/{$list->id}") }}"><i class="far fa-credit-card"></i></a></center>
                                @endif
                            </td>
                            <td>
                                @if (Session::get('positionid') == 6)
                                    <center><a href="{{ url("/chapter/re-registration/notes/{$list->id}") }}"><i class="fas fa-pencil-alt"></i></a></center>
                                @endif
                            </td>
                            <td><center><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=Re-Registration Reminder - MOMS Club of {{ $list->name }}, {{ $list->state_short_name }}&body={{ urlencode($mail_message) }}"><i class="far fa-envelope"></i></a></center></td>
                            <td>{{ $list->state_short_name }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->reg_notes }}</td>
                            <td style="
                                    @php
                                        $due = $list->month_short_name . ' ' . $list->next_renewal_year;
                                        $overdue = (date('Y') * 12 + date('m')) - ($list->next_renewal_year * 12 + $list->start_month_id);
                                        if ($overdue > 1) {
                                            echo 'background-color: #dc3545; color: #ffffff;';
                                        } elseif ($overdue == 1) {
                                            echo 'background-color: #ffc107;';
                                        }
                                    @endphp
                                " data-sort="{{ $list->next_renewal_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                                {{ $due }}
                            </td>
                            <td><span class="date-mask">{{ $list->dues_last_paid }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

              </div>
              <!-- /.card-body -->

                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Show All Chapters</label>
                    </div>
                </div>
                <div class="card-body text-center">
                <?php if(Session::get('positionid') ==6 || Session::get('positionid') == 10 || Session::get('secpositionid') ==10){ ?>
                    {{-- <p>**Known issue - will not send more than 10 messages.**</p> --}}
                <a title="Re-registration reminders will be sent to all unpaid chapters in your conference with renewal dates this month." href="{{ route('chapter.reminder') }}"><button class="btn bg-gradient-primary"   <?php if($checkBoxStatus) echo "disabled";?>><i class="fas fa-envelope" ></i>&nbsp; Send Current Month Reminders</button></a>
                <?php }?>
                <?php if(Session::get('positionid') ==6 || Session::get('positionid') == 10 || Session::get('secpositionid') ==10){ ?>
                    <a href="{{route('chapter.latereminder')}}" class="btn bg-gradient-primary" <?php if($checkBoxStatus) echo "disabled";?>><i class="fas fa-envelope" ></i>&nbsp; Send One Month Late Notices</a>
                <?php }?>
					<a href="{{ route('export.rereg')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp; Export Overdue Chapter List</button></a>
            </div>
        </div>
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
<!-- /.content-wrapper -->

@section('customscript')
<script>

  function showPrimary() {
    var base_url = '{{ url("/chapter/re-registration") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

	function ConfirmSend(){
		var result=confirm("Re-registration reminders will be sent to all unpaid chapters in your conference with renewal dates this month.  Do you wish to continue?");

        if(result){
			return true;
		}

		else{
			return false;
		}

	}


</script>
@endsection
