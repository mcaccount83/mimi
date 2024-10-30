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
            <li class="breadcrumb-item active">Board Election Reports</li>
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
                        Board Election Reports
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
				<th>Review</th>
                <th>Email</th>
				<th>State</th>
                <th>Name</th>
                <th>Primary Coordinator</th>
                <th>Received</th>
				<th>Activated</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle">
                                @if($regionalCoordinatorCondition)
                                    @if($list->new_board_active == '1')
                                        <a href="#" disabled></a>
                                    @else
                                        <a href="{{ url("/eoy/boardreportview/{$list->id}") }}"><i class="fas fa-edit"></i></a>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($list->new_board_submitted == null || $list->new_board_submitted == 0)
                                    <a href="mailto:" class="email-link" data-chapter="{{ $list->id }}">
                                        <i class="far fa-envelope"></i>
                                    </a>
                                @endif
                            </td>
                            <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            <td>{{ $list->cor_fname }} {{ $list->cor_lname }}</td>
                            <td @if($list->new_board_submitted == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->new_board_submitted == '1') YES @else NO @endif
                            </td>
                            <td @if($list->new_board_active == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->new_board_active == '1') YES @else NO @endif
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
				@if ($regionalCoordinatorCondition)
                    <a href="{{ route('eoyreports.eoyboardreportreminder') }}" onclick="return confirmSendReminder();">><button class="btn bg-gradient-primary"><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Send Board Election Reminders</button></a>
				    <button type="button" id="board-active" class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-play" ></i>&nbsp;&nbsp;&nbsp;Make Received Boards Active</button>
				    <a href="{{ route('export.boardelection')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export UN-Activated Board List</button></a>
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

$(document).ready(function(){
    var base_url = '{{ url("/eoy/boardreport") }}';

    $("#board-active").click(function() {
        window.location.href = base_url + "?board=active";
    });
});

document.addEventListener('DOMContentLoaded', function() {
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
                let mailMessage = "Don't forget to complete the Board Election Report for your chapter! This report is available now and should be filled out as soon as your chapter has held its election but is due no later than June 30th at 11:59pm.<br>" +
                                  "Please submit your report as soon as possible to ensure that your incoming board members have access to all the tools they need to be successful. The information from the report is used for:<ul>" +
                                  "<li>Chapter Contacts for your Coordinator Team</li>" +
                                  "<li>Access to MIMI</li>" +
                                  "<li>Inclusion in the Board Discussion Group</li>" +
                                  "<li>Receipt of Conference Newsletter</li>" + // Fixed closing tag here
                                  "<li>Automated Messages from MIMI, including Re-Registration payment reminders.</li>" +
                                  "</ul>" +
                                  "The Board Election Report can be accessed by logging into your MIMI account: " + mimiUrl + " and selecting the buttons at the top of your screen.<br>" +
                                  "<br>" ;

                // Create the mailto link with the message
                const subject = 'Board Election Reminder | ' + name + ', ' + state;
                emailLink.setAttribute('href', 'mailto:' + emailListChap + '?cc=' + emailListCoord + '&subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(mailMessage));
            })
            .catch(error => {
                console.error('Error fetching email details:', error);
            });
    });
});


function showPrimary() {
    var base_url = '{{ url("/eoy/boardreport") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Board Election Report. \n\nAre you sure you want to send the Board Elecion Report Reminders?');
    }


</script>
@endsection
