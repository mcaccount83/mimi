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
                <th>Extension Given<br>Do Not Send Late Notice</th>
                <th>Board Report Received</th>
                <th>Board Report Activated</th>
				<th>Financial Report Received</th>
				<th>Financial Review Completed</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        @php
                            $emailData = app('App\Http\Controllers\UserController')->loadEmailDetails($list->id);
                            $emailListChap = implode(',', $emailData['emailListChap']); // Convert array to comma-separated string
                            $emailListCoord = implode(',', $emailData['emailListCoord']); // Convert array to comma-separated string

                            $boardSubmitted = $emailData['boardSubmitted'] ?? null;
                            $reportReceived = $emailData['reportReceived'] ?? null;
                            $einLetter = $emailData['einLetter'] ?? null;

                            $mimiUrl = 'https://momsclub.org/mimi';
                            $mailMessage = "At this time, we have not received one or more of your chapter's End of Year Reports. They are now considered PAST DUE.\n\n";
                            $mailMessage .= "The following items are missing:\n";
                                if (is_null($list->documents->new_board_submitted) || $list->documents->new_board_submitted == 0) {
                                    $mailMessage .= "- Board Election Report\n";
                                }
                                if (is_null($list->documents->financial_report_received) || $list->documents->financial_report_received == 0) {
                                    $mailMessage .= "- Financial Report\n";
                                }
                                if (is_null($einLetter) || $einLetter == 0) { // `einLetter` is still used if its value directly reflects the EIN letter status
                                    $mailMessage .= "- Copy of EIN Letter\n";
                                }
                            $mailMessage .= "\nPlease submit these reports as soon as possible to ensure compliance and access to resources. The reports can be accessed by logging into your MIMI account: $mimiUrl.\n";
                        @endphp

                        <tr>
                            <td class="text-center align-middle">
                                @if($regionalCoordinatorCondition)
                                    <a href="{{ url("/eoy/editstatus/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if ($list->documents->new_board_submitted == null || $list->documents->financial_report_received == null || $list->documents->new_board_submitted == 0 || $list->documents->financial_report_received == 0)
                                    <a href="mailto:{{ rawurlencode($emailListChap) }}?cc={{ rawurlencode($emailListCoord) }}&subject={{ rawurlencode('EOY Status Report | MOMS Club of ' . $list->name . ', ' . $list->state->state_short_name) }}&body={{ rawurlencode($mailMessage) }}"><i class="far fa-envelope"></i></a>
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
