@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Financial Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Financial Report</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
                <h3 class="card-title">Report of Chapter Financials&nbsp;<small>(Chapters that were added after June 30, <?php echo date('Y');?> will not be listed)</small></h3>
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
                    <tr>
                        <td class="text-center align-middle">
                            <a href="{{ url("/eoy/financialreportview/{$list->chap_id}") }}"><i class="fas fa-edit"></i></a>
                        </td>
                        <td class="text-center align-middle">
                            @if($list->financial_report_received == '1' && $list->financial_pdf_path != null)
                                <a id="downloadPdfLink" href="https://drive.google.com/uc?export=download&id={{ $list->financial_pdf_path }}"><i class="far fa-file-pdf"></i></a>
                            @endif
                        </td>
                        <!-- Email link to be dynamically populated via AJAX -->
                        <td class="text-center align-middle">
                            @if($list->financial_report_received == null || $list->financial_report_received == 0)
                                <a href="#" class="email-link" data-chapter="{{ $list->chap_id }}"><i class="far fa-envelope"></i></a>
                            @endif
                        </td>
                        <td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->fname }} {{ $list->lname }}</td>
                        <td>{{ $list->pcfname }} {{ $list->pclname }}</td>
                        <td style="{{ $list->report_extension == '1' ? 'background-color: #ffc107;' : 'background-color: transparent;' }}">
                            {{ $list->report_extension == '1' ? 'YES' : '' }}
                        </td>
                        <td style="{{ $list->financial_report_received == '1' ? 'background-color: transparent;' : 'background-color:#dc3545; color: #ffffff;' }}">
                            {{ $list->financial_report_received == '1' ? 'YES' : 'NO' }}
                        </td>
                        <td style="{{ $list->financial_report_received == '1' ? 'background-color: transparent;' : 'background-color:#dc3545; color: #ffffff;' }}">
                            @if($list->financial_report_received != null)
                                <span class="date-mask">{{ $list->report_received }}</span>
                            @endif
                        </td>
                        <td style="{{ $list->report_complete == '1' ? 'background-color: transparent;' : 'background-color:#dc3545; color: #ffffff;' }}">
                            {{ $list->report_complete == '1' ? 'YES' : 'NO' }}
                        </td>
                        <td style="{{ $list->report_complete == '1' ? 'background-color: transparent;' : 'background-color:#dc3545; color: #ffffff;' }}">
                            @if($list->review_complete != null)
                                <span class="date-mask">{{ $list->review_complete }}</span>
                            @endif
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
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am Assigned Reviewer for</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="show2Primary" id="show2Primary" class="custom-control-input" {{$checkBox2Status}} onchange="show2Primary()" />
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

document.addEventListener('DOMContentLoaded', function() {
    // Iterate through each email link
    document.querySelectorAll('.email-link').forEach(function(emailLink) {
        const chapterId = emailLink.getAttribute('data-chapter');

        // Make an AJAX call to fetch the email details for the chapter
        fetch('/load-email-details/' + chapterId)
            .then(response => response.json())
            .then(data => {
                // Email details from the response
                const emailListCoord = data.emailListCoord;
                const emailListChap = data.emailListChap;
                const name = data.name;
                const state = data.state;
                const mimiUrl = "https://momsclub.org/mimi";

                // Construct the mail message
                let mailMessage = `Don't forget to complete the Financial Report for your chapter! This report is available now and is due no later than July 10th at 11:59pm.

                After receiving your completed reports, your Coordinator Team will review the report and reach out if they have any questions.

                The Financial Report (as well as the Board Election Report) can be accessed by logging into your MIMI account: ${mimiUrl} and selecting the buttons at the top of your screen.`;

                // Create the mailto link with the message
                const subject = 'Financial Report Reminder | ' + name + ', ' + state;
                emailLink.setAttribute('href', 'mailto:' + emailListChap + '?cc=' + emailListCoord + '&subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(mailMessage));
            })
            .catch(error => {
                console.error('Error fetching email details:', error);
            });
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
