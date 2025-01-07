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
            <input type="hidden" name="CoordinatorPromoteDateNew" id="CoordinatorPromoteDateNew"  value="{{$cdDetails->last_promoted}}"/>

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                <p class="text-center">{{ $cdDetails->confname }} Conference
                    @if ($cdDetails->regname != "None")
                    , {{ $cdDetails->regname }} Region
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
                                <select name="cord_disp_pos" id="disp_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" required>
                                    @foreach($allPositions as $pos)
                                        @if($positionid == 8 || $pos->level_id == 2)
                                            <option value="{{ $pos->id }}" {{ $cdDetails->display_position_id == $pos->id ? 'selected' : '' }}>
                                                {{ $pos->long_title }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-sm-6 mt-1">
                                <label class="col-form-label">MIMI Position:</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="cord_pri_pos" id="cord_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" required>
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
                                <select name="cord_sec_pos" class="form-control select2-sb4" style="width: 100%;" onChange="CheckPromotion(this)" >
                                    <option value=""></option>
                                    @foreach($allPositions as $pos)
                                    @if($positionid == 8 || $pos->level_id == 2)  <!-- Show all if position_id is 8, otherwise restrict to level_id == 2 -->
                                    <option value="{{$pos->id}}" {{$cdDetails->sec_position_id == $pos->id  ? 'selected' : ''}}>{{$pos->long_title}}</option>
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

                    </li>

                </ul>
                <div class="text-center">
                    @if ($cdDetails->is_active == 1 && $cdDetails->on_leave != 1)
                        <b><span style="color: #28a745;">Coordinator is ACTIVE</span></b>
                    @elseif ($cdDetails->is_active == 1 && $cdDetails->on_leave == 1)
                        <b><span style="color: #ff851b;">Coordinator is ON LEAVE</span></b>
                        <br>
                        Leave Date: <span class="date-mask">{{ $cdDetails->leave_date }}</span><br>
                    @else
                        <b><span style="color: #dc3545;">Coordinator is RETIRED</span></b>
                        <br>
                        Retired Date: <span class="date-mask">{{ $cdDetails->zapped_date }}</span><br>
                        {{ $cdDetails->reason_retired }}
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
                                                </tr>
                                            </thead>
                                            <select id="drOptionsHidden" style="display:none">
                                                @foreach ($drOptions as $option)
                                                    <option value="{{ $option->id }}" data-region-id="{{ $option->region_id }}">
                                                        {{ $option->first_name }} {{ $option->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <tbody>
                                                @foreach ($drList as $coordinator)
                                                    <tr data-coordinator-id="{{ $coordinator->id }}">
                                                        <td>{{ $coordinator->first_name }}</td>
                                                        <td>{{ $coordinator->last_name }}</td>
                                                        <td>
                                                            <select class="report-select"
                                                                    name="Report_{{ $coordinator->id }}"
                                                                    data-coordinator-id="{{ $coordinator->id }}"
                                                                    required>
                                                                @foreach ($drOptions as $option)
                                                                    <option value="{{ $option->id }}"
                                                                        {{ $option->id == $cdDetails->id ? 'selected' : '' }}
                                                                        data-region-id="{{ $option->region_id }}">
                                                                        {{ $option->first_name }} {{ $option->last_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <label class="mrg-b-25">Select Direct Report To</label>
                                        <select name="SelectCoordinator" id="SelectCoordinator" class="form-control" onChange="ActivateCoordinatorButton(this)">
                                            <option value="">Select a coordinator</option>
                                            @foreach($drDetails as $coordinator)
                                                <option value="{{ $coordinator['cid'] }}" reportId="{{ $coordinator['rptid'] }}" data-region-id="{{ $coordinator['regid']}}">
                                                    {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn bg-gradient-primary btn-sm" id="AssignCoordinator" disabled onclick="AddCoordinator()">
                                        Assign Coordinator
                                    </button>

                            </div>

                                        {{-- <table id="coordinator-list" class="nowraptable" width="100%">
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
                                                                        {{ $option->id == $cdDetails->id ? 'selected' : '' }}
                                                                        data-region-id="{{ $option->region_id }}">
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
                                            </table> --}}
                                        {{-- <input type="hidden" name="CoordinatorCount" id="CoordinatorCount" value="{{ $row_count }}" /> --}}

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
                                            @foreach ($chapter_list as $index => $chapter)
                                                <tr>
                                                    <td>{{ $chapter->state }}</td>
                                                    <td>{{ $chapter->name }}</td>
                                                    <td>
                                                        <select name="PCID{{ $index }}" id="PCID{{ $index }}" required>
                                                            @foreach ($drOptions as $option)
                                                                <option value="{{ $option->cid }}"
                                                                    {{ $option->cid == $cdDetails->id ? 'selected' : '' }}>
                                                                    {{ $option->cor_f_name }} {{ $option->cor_l_name }} ({{ $option->pos }})
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
                                    <input type="hidden" name="ChapterCount" id="ChapterCount" value="{{ $chapter_count }}" />
                                </div>
                                <div class="form-group">
                                    <label class="mrg-b-25">Select Chapter</label>
                                    <select name="SelectChapter" id="SelectChapter" class="form-control" onChange="ActivateChapterButton(this)">
                                        <option value=""></option>
                                        @foreach ($primaryChapterList as $pcl)
                                            <option value="{{ $pcl->id }}">{{ $pcl->state }} - {{ $pcl->chapter_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn bg-gradient-primary btn-sm" id="AssignChapter" disabled onclick="AddChapter()">Assign Coordinator</button>
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

// Function to filter the coordinator dropdown
function filterCoordinators() {
    const regionDropdown = document.getElementById('cord_region');
    const selectedRegion = regionDropdown.value; // Get the selected region ID
    const reportPrimaryCorDropdown = document.getElementById('cord_report_pc'); // Coordinator dropdown
    const SelectCoordinator = document.getElementById('SelectCoordinator'); // Coordinator dropdown

    // Filter options based on the selected region
    Array.from(reportPrimaryCorDropdown.options).forEach(option => {
        if (
            option.value === "" || // Always show the default empty option
            option.dataset.regionId === selectedRegion || // Match the selected region
            option.dataset.regionId === "0" // Always include region_id = 0
        ) {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });

    // Reset the selected value if it's no longer valid
    if (SelectCoordinator.value !== "" &&
        SelectCoordinator.querySelector(`option[value="${SelectCoordinator.value}"]`).style.display === "none") {
        SelectCoordinator.value = "";
    }

    // Filter options based on the selected region
    Array.from(SelectCoordinator.options).forEach(option => {
        if (
            option.value === "" || // Always show the default empty option
            option.dataset.regionId === selectedRegion || // Match the selected region
            option.dataset.regionId === "0" // Always include region_id = 0
        ) {
            option.style.display = "block";
        } else {
            option.style.display = "none";
        }
    });

    // Reset the selected value if it's no longer valid
    if (reportPrimaryCorDropdown.value !== "" &&
        reportPrimaryCorDropdown.querySelector(`option[value="${reportPrimaryCorDropdown.value}"]`).style.display === "none") {
        reportPrimaryCorDropdown.value = "";
    }
}

// Attach the event listener to the region dropdown
document.getElementById('cord_region').addEventListener('change', filterCoordinators);

// Run the filtering logic on page load
document.addEventListener('DOMContentLoaded', filterCoordinators);


function AddCoordinator() {
    const select = document.getElementById('SelectCoordinator');
    const selectedOption = select.options[select.selectedIndex];
    const coordinatorId = select.value;
    const regionId = selectedOption.dataset.regionId;
    const coordinatorName = selectedOption.text;

    // Validate selection
    if (!coordinatorId) {
        alert('Please select a coordinator.');
        return;
    }

    // Check if the coordinator is already added
    const tbody = document.querySelector('#coordinator-list tbody');
    const existingRow = tbody.querySelector(`tr[data-coordinator-id="${coordinatorId}"]`);
    if (existingRow) {
        alert('This coordinator is already in the list.');
        return;
    }

    // Get options from the hidden dropdown
    const defaultReportTo = ''; // Adjust as necessary for your default value
    const optionsHtml = Array.from(document.querySelectorAll('#drOptionsHidden option'))
        .map(opt => `
            <option value="${opt.value}" data-region-id="${opt.dataset.regionId}"
                ${opt.value === defaultReportTo ? 'selected' : ''}>
                ${opt.text}
            </option>
        `).join('');

    // Add new row to the table
    const newRow = `
        <tr data-coordinator-id="${coordinatorId}">
            <td>${coordinatorName.split(' ')[0]}</td>
            <td>${coordinatorName.split(' ')[1]}</td>
            <td>
                <select name="Report_${coordinatorId}" class="report-select" data-coordinator-id="${coordinatorId}" required>
                    ${optionsHtml}
                </select>
            </td>
        </tr>`;

    tbody.insertAdjacentHTML('beforeend', newRow);

    // Reset the dropdown and disable button
    select.value = '';
    document.getElementById('AssignCoordinator').disabled = true;
}



function ActivateCoordinatorButton(element) {
    console.log("Dropdown changed:", element.value);
    const coordinatorButton = document.getElementById("AssignCoordinator");
    coordinatorButton.disabled = !element.value; // Enable if value is not empty
}

document.addEventListener("DOMContentLoaded", function () {
    const select = document.getElementById("SelectCoordinator");
    const button = document.getElementById("AssignCoordinator");

    select.addEventListener("change", function () {
        button.disabled = !select.value; // Enable if value is selected
    });
});





//  Functions for Adding Chapers anc Coordinators
//     const chapterCount = @json($chapter_count);
//     // const coordinatorCount = @json($row_count);
//     // const conid = @json($cdId);
//     // const coordinatorOptions = @json($drOptions);

//     // let iCoordinatorCount = coordinatorCount;
//     let iChapterCount = chapterCount;

    // function AddCoordinator() {
    //     const table = document.getElementById("coordinator-list");
    //     const row = table.insertRow(-1);

    //     const cell1 = row.insertCell(0);
    //     const cell2 = row.insertCell(1);
    //     const cell3 = row.insertCell(2);
    //     const cell4 = row.insertCell(3);

    //     const strChapter = getSelectedText('SelectCoordinator');
    //     const nCoordinatorID = getSelectedValue('SelectCoordinator');

    //     cell1.innerHTML = strChapter.split(" ")[0];
    //     cell2.innerHTML = strChapter.split(" ")[1].split(" (")[0];

    //     const selectElement = document.createElement('select');
    //     selectElement.id = "Report" + iCoordinatorCount;
    //     selectElement.name = "Report" + iCoordinatorCount;

    //     coordinatorOptions.forEach(option => {
    //         const opt = document.createElement('option');
    //         opt.text = `${option.cor_f_name} ${option.cor_l_name} (${option.pos})`;
    //         opt.value = option.cid;
    //         selectElement.add(opt);
    //     });

    //     selectElement.value = conid;
    //     cell3.appendChild(selectElement);

    //     cell4.innerHTML = `<input type="hidden" name="CoordinatorIDRow${iCoordinatorCount}" id="CoordinatorIDRow${iCoordinatorCount}" value="${nCoordinatorID}">`;

    //     iCoordinatorCount++;
    //     document.getElementById('CoordinatorCount').value = iCoordinatorCount;
    // }




    // function ActivateCoordinatorButton(element) {
    //     const coordinatorButton = document.getElementById("AssignCoordinator");
    //     coordinatorButton.disabled = element.value <= 0;
    // }

    function AddChapter() {
        const table = document.getElementById("chapter-list");
        const row = table.insertRow(-1);

        const cell1 = row.insertCell(0);
        const cell2 = row.insertCell(1);
        const cell3 = row.insertCell(2);
        const cell4 = row.insertCell(3);

        const strChapter = getSelectedText('SelectChapter');
        const nChapterID = getSelectedValue('SelectChapter');

        cell1.innerHTML = strChapter.split(" - ")[0];
        cell2.innerHTML = strChapter.split(" - ")[1];

        const selectElement = document.createElement('select');
        selectElement.id = "PCID" + iChapterCount;
        selectElement.name = "PCID" + iChapterCount;

        coordinatorOptions.forEach(option => {
            const opt = document.createElement('option');
            opt.text = `${option.cor_f_name} ${option.cor_l_name} (${option.pos})`;
            opt.value = option.cid;
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
        return element && element.selectedIndex !== -1 ? element.options[element.selectedIndex].text : null;
    }

    function getSelectedValue(elementId) {
        const element = document.getElementById(elementId);
        return element && element.selectedIndex !== -1 ? element.options[element.selectedIndex].value : null;
    }



    function ActivateChapterButton(element) {
        const chapterButton = document.getElementById("AssignChapter");
        chapterButton.disabled = element.value <= 0;
    }

</script>
@endsection
