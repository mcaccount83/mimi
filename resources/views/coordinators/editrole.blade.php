@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Details')
@section('breadcrumb', 'Chapters & Coordinators')

<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.updaterole",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <input type="hidden" name="coordinator_id" value="{{ $cdDetails->id }}">
            <input type="hidden" name="OldReportPC" value="{{ $cdDetails->report_id }}">
            <input type="hidden" name="OldPosition" value="{{ $cdDetails->position_id }}">
            <input type="hidden" name="OldDisplayPosition" value="{{ $cdDetails->display_position_id }}">
            <input type="hidden" name="OldSecPosition" value="{{$cdDetails->sec_position_id}}">
            <input type="hidden" name="OldAdmin" value="{{$cdUserAdmin}}">
            <input type="hidden" name="CoordinatorPromoteDateNew" id="CoordinatorPromoteDateNew" value="{{$cdDetails->last_promoted}}"/>

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference
                    @if ($regionLongName != "None")
                    , {{ $regionLongName }} Region
                    @else
                    @endif
                </p>
                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Region:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_region" id="cord_region" class="form-control select2-sb4" style="width: 100%;" required>
                                    @foreach($allRegions as $region)
                                        <option value="{{$region->id}}"
                                            @if($cdDetails->region_id == $region->id) selected @endif>
                                            {{$region->long_name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Reports To:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_report_pc" id="cord_report_pc" class="form-control select2-sb4" style="width: 100%;" required>
                                    @foreach($rcDetails as $coordinator)
                                        <option value="{{ $coordinator['cid'] }}"
                                            @if($cdDetails->report_id == $coordinator['cid']) selected @endif
                                            data-region-id="{{ $coordinator['regid'] }}">
                                            {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Display Position:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_disp_pos" id="cord_disp_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" required>
                                    @foreach($allPositions as $pos)
                                            <option value="{{ $pos->id }}" {{ $cdDetails->display_position_id == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->long_title }}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">MIMI Position:<a href="javascript:void(0);" onclick="showPositionInformation()" title="Show Position Information">
                                    <i class="fas fa-circle-question text-primary"></i></a></label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_pos" id="cord_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" required>
                                    @foreach($allPositions as $pos)
                                        @if($pos->id >= 1 && $pos->id <= 7)
                                            <option value="{{$pos->id}}" {{$cdDetails->position_id == $pos->id  ? 'selected' : ''}}>
                                                {{$pos->long_title}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Secondary Position:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_sec_pos[]" id="cord_sec_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" multiple>
                                    <option value="" {{ (!isset($cdDetails->secondaryPosition) || $cdDetails->secondaryPosition->isEmpty()) ? 'selected' : '' }}>None</option>
                                    @foreach($allPositions as $pos)
                                        @if($pos->id >= 9)
                                            <option value="{{$pos->id}}"
                                                {{ isset($cdDetails->secondaryPosition) && $cdDetails->secondaryPosition->contains('id', $pos->id) ? 'selected' : '' }}>
                                                {{$pos->long_title}}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">Promoation Date:</label>
                            </div>
                            <div class="col-sm-6">
                                <input type="date" name="CoordinatorPromoteDate" class="form-control" data-inputmask-alias="datetime" data-inputmask-inputformat="mm/dd/yyyy" data-mask value="{{ $cdDetails->last_promoted }}">
                            </div>
                        </div>

                        @if ($userAdmin)
                            <div class="row align-items-center">
                                <div class="col-sm-6 mt-1">
                                    <label class="col-form-label">MIMI Admin:</label>
                                </div>
                                <div class="col-sm-6">
                                    <select name="is_admin" id="is_admin" class="form-control select2-sb4" style="width: 100%;" required>
                                        @foreach($allAdminRoles as $admin)
                                                <option value="{{$admin->id}}" {{$cdUserAdmin == $admin->id  ? 'selected' : ''}}>
                                                    {{$admin->admin_role}}
                                                </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                    </li>

                </ul>
               <div class="text-center">
                     @if ($cdDetails->active_status == 1 && $cdDetails->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span><br>
                    @else
                        @if ($cdDetails->active_status == 1 && $cdDetails->on_leave != 1)
                            <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                        @elseif ($cdDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Coordinator is PENDING</span></b>
                        @elseif ($cdDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Coordinator was NOT APPROVED</span></b><br>
                            Rejected Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @elseif ($cdDetails->active_status == 0)
                            <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b><br>
                            Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                            {{ $cdDetails->reason_retired }}
                        @endif
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
                <h3 class="profile-username">Chapters & Coordinators</h3>
                    <!-- /.card-header -->
                        <div class="row">
                                <div class="col-sm-6">
                            <div class="form-group">
                                <label class="mrg-b-25">Coordinators Directly Reporting to {{ $cdDetails->first_name }}:</label>
                                <table id="coordinator-list" class="nowraptable" width="100%">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Change To Report To</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($drList as $index => $coordinator)
                                        <tr>
                                            <td>{{ $coordinator->first_name }}</td>
                                            <td>{{ $coordinator->last_name }}</td>
                                            <td>
                                                <select name="Report{{ $index }}" id="Report{{ $index }}" required>
                                                    @foreach ($drOptions as $option)
                                                        <option value="{{ $option->id }}"
                                                            {{ $option->id == $cdId ? 'selected' : '' }}>
                                                            {{ $option->first_name }} {{ $option->last_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td style="display:none;">
                                                <input type="hidden" name="CoordinatorIDRow{{ $index }}" id="CoordinatorIDRow{{ $index }}" value="{{ $coordinator->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            <input type="hidden" name="CoordinatorCount" id="CoordinatorCount" value="{{ $drRowCount }}" />
                        </div>
                        <div class="form-group">
                            <label class="mrg-b-25">Select Direct Report To</label>
                            <select name="SelectCoordinator" id="SelectCoordinator" class="form-control" onChange="ActivateCoordinatorButton(this)">
                                <option value=""></option>
                                @foreach($drDetails as $coordinator)
                                    <option value="{{ $coordinator->id }}" data-region-id="{{ $coordinator->region_id }}">
                                        {{ $coordinator->first_name }} {{ $coordinator->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn bg-gradient-primary btn-sm" id="AssignCoordinator" disabled onclick="AddCoordinator()">Assign Coordinator</button>
                </div>



                <div class="col-sm-6">
                    <div class="form-group mrg-b-30">
                        <label class="mrg-b-25">{{ $cdDetails->first_name }} is Primary For:</label>
                        <table id="chapter-list" class="nowraptable" width="100%">
                            <thead>
                                <tr>
                                    <th>State</th>
                                    <th>Chapter Name</th>
                                    <th>Change Primary To</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chList as $index => $chapter)
                                    <tr>
                                        <td>{{ $chapter->state->state_short_name }}</td>
                                        <td>{{ $chapter->name }}</td>
                                        <td>
                                            <select name="PCID{{ $index }}" id="PCID{{ $index }}" required>
                                                @foreach ($pcOptions as $option)
                                                    <option value="{{ $option->id }}"
                                                        {{ $option->id == $cdId ? 'selected' : '' }}>
                                                        {{ $option->first_name }} {{ $option->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td style="display:none;">
                                            <input type="hidden" name="ChapterIDRow{{ $index }}" id="ChapterIDRow{{ $index }}" value="{{ $chapter->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <input type="hidden" name="ChapterCount" id="ChapterCount" value="{{ $pcRowCount }}" />
                    </div>
                    <div class="form-group">
                        <label class="mrg-b-25">Select Chapter</label>
                        <select name="SelectChapter" id="SelectChapter" class="form-control" onChange="ActivateChapterButton(this)">
                            <option value=""></option>
                            @foreach($chDetails as $chapter)
                                <option value="{{ $chapter->id}}" data-region-id="{{ $chapter->region_id }}">
                                    {{ $chapter->state->state_short_name }} - {{ $chapter->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="btn bg-gradient-primary btn-sm" id="AssignChapter" disabled onclick="AddChapter()">Assign Chapter</button>
                </div>
                        </div>
                    </div>
              <!-- /.card-body -->
           </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center">
                <button type="submit" class="btn bg-gradient-primary mb-3"><i class="fas fa-save mr-2" ></i>Save Changes</button>
                <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('coordinators.view', ['id' => $cdDetails->id]) }}'"><i class="fas fa-reply mr-2"></i>Back to Coordinator Details</button>
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
function showPositionInformation() {
    Swal.fire({
        title: '<strong>Position Information</strong>',
        html: `
            <h4>Display Position</h4>
            <p>The Display Position will be used in areas that are publically visible. Examples: MIMI chpater screens, emails, pdf letters, forum signature, etc.</p>
            <br>
            <h4>MIMI Position</h4>
            <p>The MIMI Position is used for chapter hierarchy/level purposes and is required for anyone who oversees chapters. Even if this is not their role title, one of
                these needs to be selected for MIMI to function properly.</p>
            <br>
            <h4>Secondary Positions</h4>
            <p>Multiple Secondary Positions may be chosen. Secondary Posistions may allow additional access outside of normal chapter/coordinator menus/screens based on the
                job requirements while others may be for information/visual purposes only and will not affect MIMI interaction.</p>
            `,
        focusConfirm: false,
        confirmButtonText: 'Close',
        customClass: {
            popup: 'swal-wide',
            confirmButton: 'btn btn-danger'
        }
    });
}

// Filter the dropdown based on the region
function filterByRegion(dropdown, selectedRegion) {
    Array.from(dropdown.options).forEach(option => {
        if (
            option.value == "" || // Always show the default empty option
            selectedRegion == "0" || // If region 0 is selected, show ALL options
            option.dataset.regionId == selectedRegion || // Match the selected region
            option.dataset.regionId == "0" // Always show options with region_id = 0
        ) {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });

    // Reset the selected value if it's no longer valid
    if (
        dropdown.value != "" &&
        dropdown.querySelector(`option[value="${dropdown.value}"]`).style.display == "none"
    ) {
        dropdown.value = "";
    }
}

// Filter the dropdown based on the region -- continued
function filterDropdown() {
    const regionDropdown = document.getElementById('cord_region');
    const selectedRegion = regionDropdown.value; // Get the selected region ID

    const dropdownsToFilter = [
        document.getElementById('cord_report_pc'),
        document.getElementById('SelectCoordinator'),
        document.getElementById('SelectChapter')
    ];

    dropdownsToFilter.forEach(dropdown => filterByRegion(dropdown, selectedRegion));
}

// Attach the event listener to the region dropdown
document.getElementById('cord_region').addEventListener('change', filterDropdown);

// Run the filtering logic on page load
document.addEventListener('DOMContentLoaded', filterDropdown);

// Functions for Adding Chapers anc Coordinators
const coordinatorCount = @json($drRowCount);
const coordinatorOptions = @json($drOptions);
const chapterCount = @json($pcRowCount);
const chCoordinatorOptions = @json($pcOptions);
const conid = @json($cdId);

let iCoordinatorCount = coordinatorCount;
let iChapterCount = chapterCount;

function isCoordinatorAlreadyAssigned(nCoordinatorID) {
    const table = document.getElementById("coordinator-list");
    for (let i = 1; i < table.rows.length; i++) {
        const existingId = document.getElementById(`CoordinatorIDRow${i-1}`);
        if (existingId && existingId.value == nCoordinatorID) {
            return true;
        }
    }
    return false;
}

function isChapterAlreadyAssigned(nChapterID) {
    const table = document.getElementById("chapter-list");
    for (let i = 1; i < table.rows.length; i++) {
        const existingId = document.getElementById(`ChapterIDRow${i-1}`);
        if (existingId && existingId.value == nChapterID) {
            return true;
        }
    }
    return false;
}

function AddCoordinator() {
    const nCoordinatorID = getSelectedValue('SelectCoordinator');

    if (isCoordinatorAlreadyAssigned(nCoordinatorID)) {
        alert("This coordinator is already assigned!");
        return;
    }

    const table = document.getElementById("coordinator-list");
    const row = table.insertRow(-1);

    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    const cell3 = row.insertCell(2);
    const cell4 = row.insertCell(3);

    const strChapter = getSelectedText('SelectCoordinator');

    cell1.innerHTML = strChapter.split(" ")[0];
    cell2.innerHTML = strChapter.split(" ")[1].split(" (")[0];

    const selectElement = document.createElement('select');
    selectElement.id = "Report" + iCoordinatorCount;
    selectElement.name = "Report" + iCoordinatorCount;

    coordinatorOptions.forEach(option => {
        const opt = document.createElement('option');
        opt.text = `${option.first_name} ${option.last_name}`;
        opt.value = option.id;
        selectElement.add(opt);
    });

    selectElement.value = conid;
    cell3.appendChild(selectElement);

    cell4.innerHTML = `<input type="hidden" name="CoordinatorIDRow${iCoordinatorCount}" id="CoordinatorIDRow${iCoordinatorCount}" value="${nCoordinatorID}">`;

    iCoordinatorCount++;
    document.getElementById('CoordinatorCount').value = iCoordinatorCount;
}

function AddChapter() {
    const nChapterID = getSelectedValue('SelectChapter');

    if (isChapterAlreadyAssigned(nChapterID)) {
        alert("This chapter is already assigned!");
        return;
    }

    const table = document.getElementById("chapter-list");
    const row = table.insertRow(-1);

    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    const cell3 = row.insertCell(2);
    const cell4 = row.insertCell(3);

    const strChapter = getSelectedText('SelectChapter');

    cell1.innerHTML = strChapter.split(" - ")[0];
    cell2.innerHTML = strChapter.split(" - ")[1];

    const selectElement = document.createElement('select');
    selectElement.id = "PCID" + iChapterCount;
    selectElement.name = "PCID" + iChapterCount;

    chCoordinatorOptions.forEach(option => {
        const opt = document.createElement('option');
        opt.text = `${option.first_name} ${option.last_name}`;
        opt.value = option.id;
        selectElement.add(opt);
    });

    selectElement.value = conid;
    cell3.appendChild(selectElement);

    cell4.innerHTML = `<input type="hidden" name="ChapterIDRow${iChapterCount}" id="ChapterIDRow${iChapterCount}" value="${nChapterID}">`;

    iChapterCount++;
    document.getElementById('ChapterCount').value = iChapterCount;
}

function getSelectedText(elementId) {
    const element = document.getElementById(elementId);
    return element && element.selectedIndex != -1 ? element.options[element.selectedIndex].text : null;
}

function getSelectedValue(elementId) {
    const element = document.getElementById(elementId);
    return element && element.selectedIndex != -1 ? element.options[element.selectedIndex].value : null;
}

function ActivateCoordinatorButton(element) {
    const coordinatorButton = document.getElementById("AssignCoordinator");
    coordinatorButton.disabled = element.value <= 0;
}

function ActivateChapterButton(element) {
    const chapterButton = document.getElementById("AssignChapter");
    chapterButton.disabled = element.value <= 0;
}

</script>
@endsection
