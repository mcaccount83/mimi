@extends('layouts.coordinator_theme')

@section('page_title', $title)
@section('breadcrumb', $breadcrumb)

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
                    @include('layouts.dropdown_menus.menu_eoy')
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
                        $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($list->id);
                        $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                        $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string

                        // Define the message body with a link
                        $mimiUrl = 'https://momsclub.org/mimi';
                        $mailMessage = "Don't forget to complete the Financial Report for your chapter! This report is available now and is due no later than July 15th at 11:59pm.\n\n";
                        $mailMessage .= "After receiving your completed reports, your Coordinator Team will review the report and reach out if they have any questions.\n\n";
                        $mailMessage .= "The Financial Report (as well as the Board Election Report) can be accessed by logging into your MIMI account: $mimiUrl and selecting the buttons at the top of your screen.";
                    @endphp
                    <tr>
                        <td class="text-center align-middle">
                            <a href="{{ url("/eoy/reviewfinancialreport/{$list->id}") }}"><i class="fas fa-edit"></i></a>
                        </td>
                        <td class="text-center align-middle">
                            @if($list->documents->financial_report_received == '1' && $list->documents->financial_pdf_path != null)
                                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $list->documents->financial_pdf_path }}"><i class="far fa-file-pdf"></i></a>
                            @endif
                        </td>
                        <!-- Email link to be dynamically populated via AJAX -->
                        <td class="text-center align-middle">
                            @if($list->documents->financial_report_received == null || $list->documents->financial_report_received == 0)
                            <a href="mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('Financial Report Reminder | MOMS Club of ' . $list->name . ', ' . $list->state->state_short_name) }}&body={{ rawurlencode($mailMessage) }}"><i class="far fa-envelope"></i></a>
                            @endif
                        </td>
                        <td>
                            @if ($list->region->short_name != "None")
                                {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->primaryCoordinator->first_name }} {{ $list->primaryCoordinator->last_name }}</td>
                        <td>{{ $list->reportReviewer->first_name?? null }} {{ $list->reportReviewer->last_name?? null }}</td>
                        <td @if($list->documents->report_extension == '1') style="background-color: #ffc107;" @else style="background-color: transparent;" @endif>
                            @if($list->documents->report_extension == '1') YES @else @endif
                        </td>
                        <td @if($list->documents->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->financial_report_received == '1') YES @else NO @endif
                        </td>
                        <td @if($list->documents->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->financial_report_received != null)<span class="date-mask">{{ $list->documents->report_received }}</span>@endif
                        </td>
                        <td @if($list->documents->financial_review_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->financial_review_complete == '1') YES @else NO @endif
                        </td>
                        <td @if($list->documents->financial_review_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
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
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showReviewer" id="showReviewer" class="custom-control-input" {{$checkBox2Status}} onchange="showReviewer()" />
                        <label class="custom-control-label" for="showReviewer">Only show chapters I am Assigned Reviewer for</label>
                    </div>
                </div>
              <div class="card-body text-center">
                @if ($regionalCoordinatorCondition)
                    <a href="{{ route('eoyreports.eoyfinancialreportreminder') }}" onclick="return confirmSendReminder();"><button class="btn bg-gradient-primary"><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Send Financial Report Reminders</button></a>
                @endif
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

function showReviewer() {
    var base_url = '{{ url("/eoy/financialreport") }}';

    if ($("#showReviewer").prop("checked") == true) {
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
