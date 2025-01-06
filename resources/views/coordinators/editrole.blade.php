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
                                    {{-- <option value="0" {{ $cdDetails->region_id == 0 ? 'selected' : '' }}>None</option> --}}
                                    @foreach($allRegions as $region)
                                    <option value="{{$region->id}}"  {{ $cdDetails->region_id == $region->id ? 'selected' : '' }}>
                                        {{$region->long_name}}
                                    </option>
                                @endforeach

                                    {{-- @foreach($regionList as $reg)
                                        <option value="{{ $reg->id }}" {{ $cdDetails->region_id == $reg->id ? 'selected' : '' }}>
                                            {{ $reg->long_name }}
                                        </option>
                                    @endforeach --}}
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
                                    <option value="{{ $coordinator['cid'] }}" data-region-id="{{ $coordinator['regid'] }}">
                                        {{ $coordinator['cname'] }} ({{ $coordinator['cpos'] }})
                                        </option>
                                    @endforeach


                                    {{-- @foreach($primaryCoordinatorList as $pcl)
                                        <option value="{{ $pcl->cid }}" {{ $cdDetails->report_id == $pcl->cid ? 'selected' : '' }}>
                                            {{ $pcl->cor_f_name }} {{ $pcl->cor_l_name }} ({{ $pcl->pos }})
                                        </option>
                                    @endforeach --}}
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
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($coordinator_list as $index => $coordinator)
                                                    <tr>
                                                        <td>{{ $coordinator->cor_f_name }}</td>
                                                        <td>{{ $coordinator->cor_l_name }}</td>
                                                        <td>
                                                            <select name="Report{{ $index }}" id="Report{{ $index }}" required>
                                                                @foreach ($coordinator_options as $option)
                                                                    <option value="{{ $option->cid }}"
                                                                        {{ $option->cid == $cdDetails->id ? 'selected' : '' }}>
                                                                        {{ $option->cor_f_name }} {{ $option->cor_l_name }} ({{ $option->pos }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td style="display:none;">
                                                            <input type="hidden" name="CoordinatorIDRow{{ $index }}" id="CoordinatorIDRow{{ $index }}" value="{{ $coordinator->cid }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        <input type="hidden" name="CoordinatorCount" id="CoordinatorCount" value="{{ $row_count }}" />
                                    </div>
                                    <div class="form-group">
                                        <label class="mrg-b-25">Select Direct Report To</label>
                                        <select name="SelectCoordinator" id="SelectCoordinator" class="form-control" onChange="ActivateCoordinatorButton(this)">
                                            <option value=""></option>
                                            @foreach($allCoordinators as $coordinator)
                                            <option value="{{$coordinator->id}}" data-region-id="{{ $coordinator->region_id }}">
                                                {{$coordinator->first_name}} {{$coordinator->last_name}}
                                            </option>
                                        @endforeach

                                            {{-- @foreach ($directReportTo as $pcl)
                                                <option value="{{ $pcl->cid }}">{{ $pcl->cor_f_name }} {{ $pcl->cor_l_name }} ({{ $pcl->pos }})</option>
                                            @endforeach --}}
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
                                            @foreach ($chapter_list as $index => $chapter)
                                                <tr>
                                                    <td>{{ $chapter->state }}</td>
                                                    <td>{{ $chapter->name }}</td>
                                                    <td>
                                                        <select name="PCID{{ $index }}" id="PCID{{ $index }}" required>
                                                            @foreach ($coordinator_options as $option)
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
    const reportCorDropdown = document.getElementById('cord_report_pc'); // Coordinator dropdown
    const directReportCorDropdown = document.getElementById('cord_report_pc'); // Coordinator dropdown

    // Filter options based on the selected region
    Array.from(reportCorDropdown.options && directReportCorDropdown.options).forEach(option => {
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
    if (reportCorDropdown.value !== "" &&
        reportCorDropdown.querySelector(`option[value="${reportCorDropdown.value}"]`).style.display === "none") {
        reportCorDropdown.value = "";
    }
    if (directReportCorDropdown.value !== "" &&
        directReportCorDropdown.querySelector(`option[value="${reportCorDropdown.value}"]`).style.display === "none") {
        directReportCorDropdown.value = "";
    }
}

// Attach the event listener to the region dropdown
document.getElementById('cord_region').addEventListener('change', filterCoordinators);

// Run the filtering logic on page load
document.addEventListener('DOMContentLoaded', filterCoordinators);




 // Functions for Adding Chapers anc Coordinators
    const chapterCount = @json($chapter_count);
    const coordinatorCount = @json($row_count);
    const conid = @json($cdId);
    const coordinatorOptions = @json($coordinator_options);

    let iCoordinatorCount = coordinatorCount;
    let iChapterCount = chapterCount;

    function AddCoordinator() {
        const table = document.getElementById("coordinator-list");
        const row = table.insertRow(-1);

        const cell1 = row.insertCell(0);
        const cell2 = row.insertCell(1);
        const cell3 = row.insertCell(2);
        const cell4 = row.insertCell(3);

        const strChapter = getSelectedText('SelectCoordinator');
        const nCoordinatorID = getSelectedValue('SelectCoordinator');

        cell1.innerHTML = strChapter.split(" ")[0];
        cell2.innerHTML = strChapter.split(" ")[1].split(" (")[0];

        const selectElement = document.createElement('select');
        selectElement.id = "Report" + iCoordinatorCount;
        selectElement.name = "Report" + iCoordinatorCount;

        coordinatorOptions.forEach(option => {
            const opt = document.createElement('option');
            opt.text = `${option.cor_f_name} ${option.cor_l_name} (${option.pos})`;
            opt.value = option.cid;
            selectElement.add(opt);
        });

        selectElement.value = conid;
        cell3.appendChild(selectElement);

        cell4.innerHTML = `<input type="hidden" name="CoordinatorIDRow${iCoordinatorCount}" id="CoordinatorIDRow${iCoordinatorCount}" value="${nCoordinatorID}">`;

        iCoordinatorCount++;
        document.getElementById('CoordinatorCount').value = iCoordinatorCount;
    }

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
