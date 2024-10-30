@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>End of Year Reports</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">EOY Status Report</li>
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
            <div class="card card-outline card-primary">
                <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        EOY Status Report
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
              <thead>
			    <tr>
				<th>Edit</th>
                <th>Email</th>
				<th>State</th>
                <th>Name</th>
                <th>Extension Given<br>Do Not Send Late Notice</th>
                <th>Board Report Received</th>
                <th>Board Report Activated</th>
				<th>Financial Report Received</th>
				<th>Financial Review Completed</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr>
                            {{-- <tr id="chapter-{{ $list->id }}"> --}}
                                <td class="text-center align-middle">
                                    @if($regionalCoordinatorCondition)
                                        @if($list->new_board_active != '1')
                                            <a href="{{ url("/eoy/statusview/{$list->id}") }}"><i class="fas fa-edit"></i></a>
                                        @endif
                                    @endif
                                </td>
                            <td class="text-center align-middle">
                                    @if ($list->new_board_submitted == null || $list->financial_report_received == null || $list->new_board_submitted == 0 || $list->financial_report_received == 0)
                                        <a href="#" class="email-link" data-chapter="{{ $list->id }}"><i class="far fa-envelope"></i></a>
                                    @endif
                            </td>
                            <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            <td @if($list->report_extension == '1') style="background-color: #ffc107;" @else style="background-color: transparent;" @endif>
                                @if($list->report_extension == '1') YES @else @endif
                            </td>
                            <td @if($list->new_board_submitted == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->new_board_submitted == '1') YES @else NO @endif
                            </td>
                            <td @if($list->new_board_active == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->new_board_active == '1') YES @else NO @endif
                            </td>
                            <td @if($list->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->financial_report_received == '1') YES @else NO @endif
                            </td>
                            <td @if($list->financial_report_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->financial_report_complete == '1') YES @else NO @endif
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
                <div class="card-body text-center">
                {{-- <p>**Known issue - may not send more than 10 messages before returning 500 error.**</p> --}}
                <a href="{{ route('eoyreports.eoystatusreminder') }}" onclick="return confirmSendReminder();"> <button class="btn bg-gradient-primary"><i class="fas fa-envelope"></i>&nbsp;&nbsp;&nbsp;Send EOY Late Notices </button> </a>
                <a href="{{ route('eoyreports.eoystatus')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export EOY Status List</button></a>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
</div>
    </section>
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
                let mailMessage = "At this time, we have not received one or more of your chapter's End of Year Reports. They are now considered PAST DUE.<br>" +
                                  "The following items are missing:<ul>";

                // Conditional checks for missing items
                const boardSubmitted = data.board_submitted;
                const reportReceived = data.report_received;
                const einLetter = data.ein_letter;

                if (boardSubmitted == null || boardSubmitted == 0) {
                    mailMessage += "<li>Board Election Report</li>";
                }
                if (reportReceived == null || reportReceived == 0) {
                    mailMessage += "<li>Financial Report</li>";
                }
                if (einLetter == null || einLetter == 0) {
                    mailMessage += "<li>Copy of EIN Letter</li>";
                }

                // Close the list
                mailMessage += "</ul>";

                // Add closing remarks
                mailMessage += `Please submit these reports as soon as possible to ensure compliance and access to resources. The reports can be accessed by logging into your MIMI account: ${mimiUrl}.`;

                // Create the mailto link with the message
                const subject = 'End of Year Report Reminder | ' + name + ', ' + state;
                emailLink.setAttribute('href', 'mailto:' +  emailListChap + '?cc=' + emailListCoord + '&subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(mailMessage));
            })
            .catch(error => {
                console.error('Error fetching email details:', error);
            });
    });
});


function showPrimary() {
    var base_url = '{{ url("/eoy/status") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
        return confirm('This action will send a Late Notice to all chapters who have not submitted their Board Election Report OR their Financial Report, excluding those with an extension or an assigned reviewer. \n\nAre you sure you want to send the EOY Late Notices?');
    }

</script>
@endsection
