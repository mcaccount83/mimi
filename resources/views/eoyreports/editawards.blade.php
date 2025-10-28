@extends('layouts.coordinator_theme')

@section('page_title', $title)
@section('breadcrumb', $breadcrumb)

<style>

.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.align-bottom {
        display: flex;
        align-items: flex-end;
    }

    .align-middle {
        display: flex;
        align-items: center;
    }

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("eoyreports.updateawards", $chDetails->id) }}'>
        @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Submitted:</label>
                                <span class="float-right">{{ $chDetails->documents->new_board_submitted == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>New Board Activated:</label>
                                <span class="float-right">{{ $chDetails->documents->new_board_active == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Report Received</label>
                                <span class="float-right">{{ $chDetails->documents->financial_report_received == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Financial Review Complete:</label>
                                <span class="float-right">{{ $chDetails->documents->financial_review_complete == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Report Extension Given:</label>
                                <span class="float-right">{{ $chDetails->documents->report_extension == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>990N Verifed on irs.gov:</label>
                                <span class="float-right">{{ $chFinancialReport->check_current_990N_verified_IRS == 1 ? 'YES' : 'NO' }}</span>
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Assigned Reviewer:</label>
                                    @if($chFinancialReport->reviewer_id != null)
                                    <span class="float-right">{{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}</span>
                                    @else
                                        No Reviewer Assigned
                                    @endif
                            </div>
                        </div>
                </li>

                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>
                </ul>
               <div class="text-center">
                      @if ($chDetails->active_status == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @elseif ($chDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Chapter is PENDING</span></b>
                      @elseif ($chDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b><br>
                          Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @elseif ($chDetails->active_status == 0)
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @endif
                  </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                <h3 class="profile-username">{{ (date('Y') - 1) . '-' . date('Y') }} Chapter Awards</h3>
                    <!-- /.card-header -->

                    <!-- Awards Table -->
                    <table id="awards" width="100%" class="table table-bordered">
                        <thead>
                            <tr>
                                <td width="25%">Award Category</td>
                                <td width="60%">Description/Information</td>
                                <td width="15%">Approval</td>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $chapter_awards = null;
                            if (isset($chFinancialReport['chapter_awards'])) {
                                $blobData = base64_decode($chFinancialReport['chapter_awards']);
                                $chapter_awards = unserialize($blobData);
                                if ($chapter_awards == false) {
                                    $chapter_awards = [];
                                }
                            }
                            $ChapterAwardsRowCount = !empty($chapter_awards) ? count($chapter_awards) : 1;
                        @endphp

                        @for ($row = 0; $row < $ChapterAwardsRowCount; $row++)
                        <tr>
                            <td>
                                <div class="form-group">
                                    <select class="form-control" name="ChapterAwardsType{{ $row }}"
                                        id="ChapterAwardsType{{ $row }}">
                                        <option value="">Select an Award Type</option>
                                        @foreach($allAwards as $award)
                                            <option value="{{ $award->id }}"
                                                {{ isset($chapter_awards[$row]['awards_type']) &&
                                                $chapter_awards[$row]['awards_type'] == $award->id ? 'selected' : '' }}>
                                                {{ $award->award_type }} {{ $award->extra }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <textarea class="form-control" rows="2" name="ChapterAwardsDesc{{ $row }}"
                                            id="ChapterAwardsDesc{{ $row }}">{{ $chapter_awards[$row]['awards_desc'] ?? '' }}</textarea>
                                </div>
                            </td>
                            <td>
                                <div class="form-group row">
                                    <div class="col-12 row">
                                        <div class="form-check" style="margin-right: 20px;">
                                            <input class="form-check-input" type="radio"
                                                   name="ChapterAwardsApproved{{ $row }}"
                                                   value="1"
                                                   {{ isset($chapter_awards[$row]['awards_approved']) &&
                                                      $chapter_awards[$row]['awards_approved'] == true ? 'checked' : '' }}>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio"
                                                   name="ChapterAwardsApproved{{ $row }}"
                                                   value="0"
                                                   {{ isset($chapter_awards[$row]['awards_approved']) &&
                                                      $chapter_awards[$row]['awards_approved'] == false ? 'checked' : '' }}>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endfor
                        </tbody>
                    </table>

                    <!-- Add/Remove Row Buttons -->
                    <div class="col-md-12 float-left">
                        <button type="button" class="btn btn-sm btn-success" onclick="AddChapterAwardsRow()">
                            <i class="fas fa-plus"></i>&nbsp; Add Row
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="DeleteChapterAwardsRow()">
                            <i class="fas fa-minus"></i>&nbsp; Remove Row
                        </button>
                    </div>
                    <input type="hidden" name="ChapterAwardsRowCount" id="ChapterAwardsRowCount" value="{{ $ChapterAwardsRowCount }}" />

                  </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Chapter Awards</button>
                    <br>
                @endif
                 @if ($confId == $chConfId)
                        <button type="button" id="back-awards" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoyawards') }}'"><i class="fas fa-reply mr-2"></i>Back to Awards Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-awards" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.eoyawards', ['check5' => 'yes']) }}'"><i class="fas fa-reply mr-2"></i>Back to International Awards Report</button>
                    @endif
                <button type="button" id="back-eoy" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to EOY Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
    var $chActiveId = @json($chActiveId);
    var $coordinatorCondition = @json($coordinatorCondition);
    var $eoyTestCondition = @json($eoyTestCondition);
    var $eoyReportCondition = @json($eoyReportCondition);
    var $ITCondition = @json($ITCondition);
    var $chConfId = @json($chConfId);
    var $confId = @json($confId);

    var hasCoordinatorAccess = $coordinatorCondition && ($confId == $chConfId);
    var hasEoyTestCondition = $eoyTestCondition && ($confId == $chConfId);
    var hasEoyReportCondition = $eoyReportCondition && ($confId == $chConfId);
    var hasITAccess = $ITCondition;
    var shouldEnable = ($chActiveId == 1) && (hasCoordinatorAccess || hasEoyTestCondition || hasEoyReportCondition || hasITAccess);

$(document).ready(function () {
    if (!shouldEnable) {
        $('input, select, textarea, button').prop('disabled', true);

        $('a[href^="mailto:"]').each(function() {
            $(this).addClass('disabled-link'); // Add disabled class for styling
            $(this).attr('href', 'javascript:void(0);'); // Prevent navigation
            $(this).on('click', function(e) {
                e.preventDefault(); // Prevent link click
            });
        });

        // Re-enable the specific "Back" buttons
        $('#back-awards').prop('disabled', false);
        $('#back-eoy').prop('disabled', false);
    }
});

$(document).ready(function() {
    // Function to load the coordinator list based on the selected value
    function loadCoordinatorList(id) {
        if(id != "") {
            $.ajax({
                url: '{{ url("/load-coordinator-list") }}' + '/' + id,
                type: "GET",
                success: function(result) {
                $("#display_corlist").html(result);
                },
                error: function (jqXHR, exception) {
                console.log("Error: ", jqXHR, exception);
                }
            });
        }
    }

    // Get the selected coordinator ID on page load
    var selectedCorId = $("#ch_primarycor").val();
        loadCoordinatorList(selectedCorId);

        // Update the coordinator list when the dropdown changes
        $("#ch_primarycor").change(function() {
            var selectedValue = $(this).val();
            loadCoordinatorList(selectedValue);
    });
});

function AddChapterAwardsRow() {
    var rowCount = document.getElementById("ChapterAwardsRowCount").value;
    var table = document.getElementById("awards");
    var row = table.insertRow(-1);

    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);  // Add third cell for approval

    // Get the options from an existing select element
    var existingSelect = document.getElementById("ChapterAwardsType0");
    var options = Array.from(existingSelect.options).map(opt => {
        return `<option value="${opt.value}">${opt.text}</option>`;
    }).join('');

    // Create the cells
    cell1.innerHTML = `
        <div class="form-group">
            <select class="form-control"
                    name="ChapterAwardsType${rowCount}"
                    id="ChapterAwardsType${rowCount}"
                    onchange="toggleOutstandingCriteria(${rowCount})">
                ${options}
            </select>
        </div>`;

    cell2.innerHTML = `
        <div class="form-group">
            <textarea class="form-control"
                      rows="2"
                      name="ChapterAwardsDesc${rowCount}"
                      id="ChapterAwardsDesc${rowCount}"></textarea>
        </div>`;

    // Add the approval radio buttons
    cell3.innerHTML = `
        <div class="form-check">
            <input class="form-check-input" type="radio"
                   id="ChapterAwardsApprovedYes${rowCount}"
                   name="ChapterAwardsApproved${rowCount}"
                   value="1">
            <label class="form-check-label"
                   for="ChapterAwardsApprovedYes${rowCount}">Yes</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio"
                   id="ChapterAwardsApprovedNo${rowCount}"
                   name="ChapterAwardsApproved${rowCount}"
                   value="0">
            <label class="form-check-label"
                   for="ChapterAwardsApprovedNo${rowCount}">No</label>
        </div>`;

    rowCount++;
    document.getElementById('ChapterAwardsRowCount').value = rowCount;
}

function DeleteChapterAwardsRow() {
    var table = document.getElementById("awards");
    var rowCount = document.getElementById("ChapterAwardsRowCount").value;

    if (rowCount > 1) {  // Keep at least one row
        table.deleteRow(-1);

        // Remove the corresponding criteria section
        var criteriaToRemove = document.getElementById(`OutstandingCriteria${rowCount-1}`);
        if (criteriaToRemove) {
            criteriaToRemove.remove();
        }

        rowCount--;
        document.getElementById('ChapterAwardsRowCount').value = rowCount;

        // Update displays
        toggleAwardBlocks();
    }
}

</script>
@endsection
