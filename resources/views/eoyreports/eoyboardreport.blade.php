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
                        Board Election Reports
                    </h3>
                    <span class="ml-2">Chapters that were added after June 30, <?php echo date('Y');?> will not be listed</span>
                    @include('layouts.dropdown_menus.menu_eoy')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				<th>Details</th>
                <th>Email</th>
                <th>Conf/Reg</th>
				<th>State</th>
                <th>Name</th>
                <th>Primary Coordinator</th>
                <th>Received</th>
				<th>Activated</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    @php
                        $mailData = [
                            'chapterName' => $list->name,
                            'chapterState' => $list->state,
                        ];

                        $renderedHtml = View::make('emails.endofyear.electionreportreminder', ['mailData' => $mailData])->render();
                        $renderedPlainText = strip_tags($renderedHtml);
                    @endphp
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle">
                            @if ($assistConferenceCoordinatorCondition)
                               <a href="{{ url("/eoy/editboardreport/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                           @endif
                        </td>
                            <td class="text-center align-middle">
                                @if ($list->documents->new_board_submitted == null || $list->documents->new_board_submitted == 0)
                                    <a href="#" class="email-link" data-chapter-name="{{ $list->name }}" data-chapter-id="{{ $list->id }}" data-user-name="{{ $userName }}"
                                    data-user-position="{{ $userPosition }}" data-user-conf-name="{{ $userConfName }}" data-user-conf-desc="{{ $userConfDesc }}"
                                    data-predefined-subject="Board Election Report Reminder" data-message-id="msg-{{ $list->id }}"> <i class="far fa-envelope text-primary"></i></a>
                                    <textarea id="msg-{{ $list->id }}" class="d-none">{{ $renderedHtml = View::make('emails.endofyear.electionreportreminder',
                                        ['mailData' => $mailData, 'minimal' => true, ])->render(); }}</textarea>
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
                            <td @if($list->documents->new_board_submitted == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documents->new_board_submitted == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documents->new_board_active == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documents->new_board_active == '1') YES @else NO @endif
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
                                        <div class="d-flex justify-content-center align-items-start flex-wrap">

				@if ($regionalCoordinatorCondition)
                    <a href="{{ route('eoyreports.eoyboardreportreminder') }}" onclick="return confirmSendReminder();">><button class="btn bg-gradient-primary"><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Send Board Election Reminders</button></a>
				@endif
                @if ($assistConferenceCoordinatorCondition)
                    <form id="activateAllBoardsForm" action="{{ route('eoyreports.eoyboardreport') }}" method="GET">
    <input type="hidden" name="board" value="active">
    <button type="button" class="btn bg-gradient-primary ml-1" onclick="confirmActivateAllBoards()">
        <i class="fas fa-play mr-2"></i>Make Received Boards Active
    </button>
</form>
                @endif
                </div>
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

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.email-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const messageId = this.dataset.messageId;
            const fullMessage = document.getElementById(messageId).value;

            showChapterEmailModal(
                this.dataset.chapterName,
                this.dataset.chapterId,
                this.dataset.userName,
                this.dataset.userPosition,
                this.dataset.userConfName,
                this.dataset.userConfDesc,
                this.dataset.predefinedSubject,
                fullMessage
            );
        });
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

function showReviewer() {
    var base_url = '{{ url("/eoy/boardreport") }}';

    if ($("#showReviewer").prop("checked") == true) {
        window.location.href = base_url + '?check2=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
        return confirm('This action will send reminders to all chapters who have not submitted their Board Election Report. \n\nAre you sure you want to send the Board Elecion Report Reminders?');
    }

    function confirmActivateAllBoards() {
    Swal.fire({
        title: 'Activate All Boards?',
        html: 'This action will activate all received boards.<br><br>Are you sure you want to activate all received boards?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Activate All!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('activateAllBoardsForm').submit();
        }
    });
}


</script>
@endsection
