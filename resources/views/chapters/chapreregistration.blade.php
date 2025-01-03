@extends('layouts.coordinator_theme')

@section('page_title', 'Payments/Donations')
@section('breadcrumb', 'Re-Registration Payments')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Re-Registration Payments
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($coordinatorCondition)
                            <a class="dropdown-item" href="{{ route('chapters.chapreregistration') }}">Re-Registration Payments</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptdonations') }}">M2M & Sustaining Donations</a>
                        @endif
                        @if ($m2mCondition || $adminReportCondition)
                            <a class="dropdown-item" href="{{ route('international.intdonation') }}">International M2M & Sustaining Donations</a>
                        @endif
                    </div>
                </div>
            </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Payment</th>
                            <th>Email</th>
                            <th>Conf/Reg</th>
                            <th>State</th>
                            <th>Name</th>
                            <th>Re-Registration Notes</th>
                            <th>Due</th>
                            <th>Last Paid</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reChapterList as $list)
                        @php
                            $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($list->id);
                            $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                            $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string

                            // Define the message body with a link
                            $mimiUrl = 'https://example.com/mimi';
                            $mailMessage = "Your chapter's re-registration payment is due at this time and has not yet been received.\n\n";
                            $mailMessage .= "Calculate your payment:\n";
                            $mailMessage .= "- Determine how many people paid dues to your chapter since your last re-registration payment through today.\n";
                            $mailMessage .= "- Add in any people who paid reduced dues or had their dues waived due to financial hardship.\n";
                            $mailMessage .= "- If this total amount of members is less than 10, make your check for the amount of $50.\n";
                            $mailMessage .= "- If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due.\n";
                            $mailMessage .= "- Payments received after the last day of your renewal month should include a late fee of $10.\n\n";
                            $mailMessage .= "Make your payment:\n";
                            $mailMessage .= "- Pay Online: $mimiUrl\n";
                            $mailMessage .= "- Pay via Mail to: Chapter Re-Registration, 208 Hewitt Dr. Ste 103 #328, Waco, TX 76712\n";
                        @endphp
                        <tr>
                            <td class="text-center align-middle">
                                @if ($conferenceCoordinatorCondition)
                                    <a href="{{ url("/chapterpaymentedit/{$list->id}") }}"><i class="far fa-credit-card"></i></a>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                <a href="mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('Re-Registration Payment Reminder | MOMS Club of ' . $list->name . ', ' . $list->state_short_name) }}&body={{ rawurlencode($mailMessage) }}"><i class="far fa-envelope"></i></a>
                            </td>
                            <td>
                                @if ($list->region->short_name != "None")
                                    {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                                @else
                                    {{ $list->conference->short_name }}
                                @endif
                            </td>
                            <td>{{ $list->state->state_short_name }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->reg_notes }}</td>
                            <td style="
                                @php
                                    $due = $list->startMonth->month_short_name . ' ' . $list->next_renewal_year;
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
                            <td>{{ $list->members_paid_for }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
              </div>
          <!-- /.card-body -->
          <div class="col-sm-12">
            <div class="custom-control custom-switch">
                <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
            </div>
        </div>
          <div class="col-sm-12">
            <div class="custom-control custom-switch">
                        <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox3Status}} onchange="showAll()" />
                        <label class="custom-control-label" for="showAll">Show All Chapters</label>
                    </div>
                </div>
                <div class="card-body text-center">
                    @if($conferenceCoordinatorCondition)
                    @if($checkBoxStatus == null && $checkBox3Status == null)
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chapreregreminder') }}"><i class="fas fa-envelope mr-2" ></i>Send Current Month Reminders</a>
                        <a class="btn bg-gradient-primary" href="{{ route('chapters.chaprereglatereminder') }}"><i class="fas fa-envelope mr-2" ></i>Send One Month Late Notices</a>
                    @else
                        <button class="btn bg-gradient-primary" disabled><i class="fas fa-envelope mr-2" ></i>Send Current Month Reminders</button>
                        <button class="btn bg-gradient-primary" disabled><i class="fas fa-envelope mr-2" ></i>Send One Month Late Notices</button>
                    @endif
                        <a href="{{ route('export.rereg')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download mr-2" ></i>Export Overdue Chapter List</button></a>
                    @endif
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

document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

function showPrimary() {
var base_url = '{{ url("/chapter/reregistration") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}


function showAll() {
    var base_url = '{{ url("/chapter/reregistration") }}';
    if ($("#showAll").prop("checked") == true) {
        window.location.href = base_url + '?check3=yes';
    } else {
        window.location.href = base_url;
    }
}

</script>
@endsection
