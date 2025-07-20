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
                        EOY Status Report
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
                <th>Extension Given or<br>Returned to Chapter</th>
                <th>Board Report Received</th>
                <th>Board Report Activated</th>
				<th>Financial Report Received</th>
				<th>Financial Review Completed</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    @php
                        $mailData = [
                            'chapterName' => $list->name,
                            'chapterState' => $list->state,
                            'boardElectionReportReceived' => $list->documents->new_board_submitted,
                            'financialReportReceived' => $list->documents->financial_report_received,
                            '990NSubmissionReceived' => $list->documents->irs_path,
                            'einLetterCopyReceived' => $list->documents->ein_letter,
                        ];

                        $renderedHtml = View::make('emails.endofyear.latereportreminder', ['mailData' => $mailData])->render();
                        $renderedPlainText = strip_tags($renderedHtml);
                    @endphp

                        <tr>
                            <td class="text-center align-middle">
                                @if($regionalCoordinatorCondition)
                                    <a href="{{ url("/eoy/editstatus/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($list->documents->new_board_submitted == null || $list->documents->financial_report_received == null || $list->documents->new_board_submitted == 0 || $list->documents->financial_report_received == 0)
                                   <a href="#" class="email-link" data-chapter-name="{{ $list->name }}" data-chapter-id="{{ $list->id }}" data-user-name="{{ $userName }}"
                                    data-user-position="{{ $userPosition }}" data-user-conf-name="{{ $userConfName }}" data-user-conf-desc="{{ $userConfDesc }}"
                                    data-predefined-subject="End of Year Reports Late Notice" data-message-id="msg-{{ $list->id }}"> <i class="far fa-envelope text-primary"></i></a>
                                    <textarea id="msg-{{ $list->id }}" class="d-none">{{ $renderedHtml = View::make('emails.endofyear.latereportreminder',
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
                            <td @if($list->documents->report_extension == '1') style="background-color: #ffc107;" @else style="background-color: transparent;" @endif>
                                @if($list->documents->report_extension == '1') YES @else @endif
                            </td>
                            <td @if($list->documents->new_board_submitted == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documents->new_board_submitted == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documents->new_board_active == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documents->new_board_active == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documents->financial_report_received == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documents->financial_report_received == '1') YES @else NO @endif
                            </td>
                            <td @if($list->documents->financial_review_complete == '1') style="background-color: transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                                @if($list->documents->financial_review_complete == '1') YES @else NO @endif
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
                    <a href="{{ route('eoyreports.eoystatusreminder') }}" onclick="return confirmSendReminder();"> <button class="btn bg-gradient-primary"><i class="fas fa-envelope"></i>&nbsp;&nbsp;&nbsp;Send EOY Late Notices </button> </a>
                @endif
                @if ($assistConferenceCoordinatorCondition)
                    <button class="btn bg-gradient-primary" onclick="startExport('eoystatus', 'EOY Status List')"><i class="fas fa-download mr-2" ></i>Export EOY Status List</button>
                @endif
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

function showPrimary() {
    var base_url = '{{ url("/eoy/status") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function showReviewer() {
    var base_url = '{{ url("/eoy/status") }}';

    if ($("#showReviewer").prop("checked") == true) {
        window.location.href = base_url + '?check2=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
        return confirm('This action will send a Late Notice to all chapters who have not submitted their Board Election Report OR their Financial Report, excluding those with an extension or an assigned reviewer. \n\nAre you sure you want to send the EOY Late Notices?');
    }

</script>
@endsection
