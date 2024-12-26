@extends('layouts.coordinator_theme')

@section('page_title', 'End of Year Reports')
@section('breadcrumb', 'Financial Reports')

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
                        Financial Reports
                    </h3>
                    <span class="ml-2">Chapters that were added after June 30, <?php echo date('Y');?> will not be listed</span>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('eoyreports.eoystatus') }}">EOY Status Report</a>
                        <a class="dropdown-item" href="{{ route('eoyreports.eoyboardreport') }}">Board Election Reports</a>
                        <a class="dropdown-item" href="{{ route('eoyreports.eoyfinancialreport') }}">Financial Reports</a>
                        <a class="dropdown-item" href="{{ route('eoyreports.eoyattachments') }}">Financial Report Attachments</a>
                        <a class="dropdown-item" href="{{ route('eoyreports.eoyboundaries') }}">Boundary Issues Report</a>
                        <a class="dropdown-item" href="{{ route('eoyreports.eoyawards') }}">Chapter Awards Report</a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_review" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
					<th>Review</th>
                    <th>PDF</th>
					<th>Email</th>
                    <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Primary Coordinator</th>
                 <th>Assigned Reviewer</th>
                 <th>Extension Given</th>
                 <th>Report Received</th>
                 <th>Date Received</th>
                 <th>Review Completed</th>
                 <th>Date Completed</th>
                 {{-- <th>Ending Balance</th> --}}
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    @php
                        $emailDetails = app('App\Http\Controllers\UserController')->loadEmailDetails($list->id);
                        $emailListChap = $emailDetails['emailListChapString'];
                        $emailListCoord = $emailDetails['emailListCoordString'];

                        // Define the message body with a link
                        $mimiUrl = 'https://example.com/mimi';
                        $mailMessage = "Don't forget to complete the Financial Report for your chapter! This report is available now and is due no later than July 10th at 11:59pm.\n\n";
                        $mailMessage .= "After receiving your completed reports, your Coordinator Team will review the report and reach out if they have any questions.\n\n";
                        $mailMessage .= "The Financial Report (as well as the Board Election Report) can be accessed by logging into your MIMI account: $mimiUrl and selecting the buttons at the top of your screen.";
                    @endphp
                    <tr>
                        <td class="text-center align-middle">
                            <a href="{{ url("/eoy/financialreportreview/{$list->id}") }}"><i class="fas fa-edit"></i></a>
                        </td>
                        <td class="text-center align-middle">
                            @if($list->documents->financial_report_received == '1' && $list->documents->financial_pdf_path != null)
                                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $list->documents->financial_pdf_path }}"><i class="far fa-file-pdf"></i></a>
                            @endif
                        </td>
                        <!-- Email link to be dynamically populated via AJAX -->
                        <td class="text-center align-middle">
                            @if($list->documents->financial_report_received == null || $list->documents->financial_report_received == 0)
                            <a href="mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('Financial Report Reminder | MOMS Club of ' . $list->name . ', ' . $list->state) }}&body={{ rawurlencode($mailMessage) }}"><i class="far fa-envelope"></i></a>
                            @endif
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
                        <td>{{ $list->fname }} {{ $list->lname }}</td>
                        <td>{{ $list->pcfname }} {{ $list->pclname }}</td>
                        <td @if($list->documents->report_extension == '1') style="background-color: #ffc107;" @else style="background-color: transparent;" @endif>
                            @if($list->documents->report_extension == '1') YES @else @endif
                        </td>
                        <td @if($list->documents->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->financial_report_received == '1') YES @else NO @endif
                        </td>
                        <td @if($list->documents->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->financial_report_received != null)<span class="date-mask">{{ $list->documents->report_received }}</span>@endif
                        </td>
                        <td @if($list->documents->financial_report_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->financial_report_complete == '1') YES @else NO @endif
                        </td>
                        <td @if($list->documents->financial_report_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->review_complete != null)<span class="date-mask">{{ $list->documents->review_complete }}</span>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBox2Status}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am Assigned Reviewer for</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="show2Primary" id="show2Primary" class="custom-control-input" {{$checkBoxStatus}} onchange="show2Primary()" />
                        <label class="custom-control-label" for="show2Primary">Only show chapters I am primary for</label>
                    </div>
                </div>
              <div class="card-body text-center">
                {{-- <p>**Known issue - may not send more than 10 messages before returning 500 error.**</p> --}}
              <a href="{{ route('eoyreports.eoyfinancialreportreminder') }}" onclick="return confirmSendReminder();"><button class="btn bg-gradient-primary"><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Send Financial Report Reminders</button></a>
           </div>
        </div>

          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- Main content -->
    <!-- /.content -->
@endsection
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
    var base_url = '{{ url("/eoy/financialreport") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function show2Primary() {
    var base_url = '{{ url("/eoy/financialreport") }}';

    if ($("#show2Primary").prop("checked") == true) {
        window.location.href = base_url + '?check2=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Financial Report, excluding those with an extension or wtih an assigned reviewer. \n\nAre you sure you want to send the Financial Report Reminders?');
    }

</script>
@endsection
