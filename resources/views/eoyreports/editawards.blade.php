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
               <div class="card-body">
                    <div class="card-header text-center bg-transparent">
                    <h3 class="mb-0">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                    <p class="mb-0">{{ $chDetails->confname }} Conference, {{ $chDetails->regname }} Region
                  </p>
                </div>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">New Board Submitted:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->new_board_submitted == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">New Board Activated:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->new_board_active == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Financial Report Received</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->financial_report_received == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Financial Review Complete:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->financial_review_complete == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">Report Extension Given:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->report_extension == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto fw-bold">990N Verifed on irs.gov:</div>
                            <div class="col text-end">
                                {{ $chDetails->documentsEOY->irs_verified == 1 ? 'YES' : 'NO' }}
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-auto fw-bold">Assigned Reviewer:</div>
                            <div class="col text-end">
                                    @if($chFinancialReport->reviewer_id != null)
                                    {{ $chDetails->reportReviewer->first_name }} {{ $chDetails->reportReviewer->last_name }}
                                    @else
                                        No Reviewer Assigned
                                    @endif
                            </div>
                        </div>
                    </li>

                     <li class="list-group-item">
                          <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                            <div class="row mb-2">
                          <span id="display_corlist"></span>
                            </div>
                        </li>
                  <li class="list-group-item">
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
                </li>
                  </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="card-header bg-transparent border-0">
                        <h3>{{ $fiscalYear }} Chapter Awards</h3>
                   </div>
                    <!-- /.card-header -->
                    <div class="card-body">
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
                                <div class="mb-3">
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
                                <div class="mb-3">
                                    <textarea class="form-control" rows="2" name="ChapterAwardsDesc{{ $row }}"
                                            id="ChapterAwardsDesc{{ $row }}">{{ $chapter_awards[$row]['awards_desc'] ?? '' }}</textarea>
                                </div>
                            </td>
                            <td>
                                <div class="row mb-3">
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
                    <div class="col-md-12 mt-1">
                        <button type="button" class="btn btn-success bg-gradient btn-sm" onclick="AddChapterAwardsRow()">
                            <i class="bi bi-plus me-2"></i>Add Row
                        </button>
                        <button type="button" class="btn btn-danger bg-gradient btn-sm" onclick="DeleteChapterAwardsRow()">
                            <i class="bi bi-dash me-2"></i>Remove Row
                        </button>
                    </div>
                    <input type="hidden" name="ChapterAwardsRowCount" id="ChapterAwardsRowCount" value="{{ $ChapterAwardsRowCount }}" />

                  </div>
              </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                @if ($coordinatorCondition)
                    <button type="submit" class="btn btn-primary bg-gradient mb-2" ><i class="bi bi-floppy-fill me-2"></i>Save Chapter Awards</button>
                    <button type="button" id="awards-history" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.awardhistory', ['id' => $chDetails->id]) }}'"><i class="bi bi-file-earmark-text me-2"></i>View Awards History</button>
                    <br>
                @endif
                 @if ($confId == $chConfId)
                        <button type="button" id="back-awards" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyawards') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-award-fill me-2"></i>Back to Awards Report</button>
                    @elseif ($confId != $chConfId && $ITCondition)
                        <button type="button" id="back-awards" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.eoyawards', ['check5' => 'yes']) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-award-fill me-2"></i>Back to International Awards Report</button>
                    @endif
                <button type="button" id="back-eoy" class="btn btn-primary bg-gradient mb-2 keep-enabled" onclick="window.location.href='{{ route('eoyreports.view', ['id' => $chDetails->id]) }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Back to EOY Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields', ['includeEoyConditions' => true])

<script>

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
        <div class="mb-3">
            <select class="form-control"
                    name="ChapterAwardsType${rowCount}"
                    id="ChapterAwardsType${rowCount}"
                    onchange="toggleOutstandingCriteria(${rowCount})">
                ${options}
            </select>
        </div>`;

    cell2.innerHTML = `
        <div class="mb-3">
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
        // toggleAwardBlocks();
    }
}

</script>
@endsection
