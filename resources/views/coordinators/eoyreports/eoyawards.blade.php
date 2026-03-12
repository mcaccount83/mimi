@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE)
    @section('page_title', $fiscalYearEOY.' EOY Reports *ADMIN*')
    @section('breadcrumb', 'Chapter Awards Report')
@elseif ($eoyTestCondition && $displayEOYTESTING)
    @section('page_title', $fiscalYearEOY.' EOY Reports *TESTING*')
    @section('breadcrumb', 'Chapter Awards Report')
@else
    @section('page_title', $fiscalYearEOY.' EOY Reports')
    @section('breadcrumb', 'Chapter Awards Report')
@endif

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown d-flex align-items-center">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Chapter Awards Report
                        </h3>
                        <span class="ms-3">Chapters that were added after June 30, {{ $thisYearEOY }} will not be listed</span>
                        @include('layouts.dropdown_menus.menu_eoy')
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                   @php
    $hasAnyAwards = false;
    $actualMaxAwards = 0;

    // Check if any chapter has actual awards (not just blank entries)
    foreach($chapterList as $list) {
        if (isset($list->financialReport->chapter_awards)) {
            $blobData = base64_decode($list->financialReport->chapter_awards);
            $chapter_awards = unserialize($blobData);
            if ($chapter_awards != false && !empty($chapter_awards)) {
                // Count only awards that have an actual awards_type selected
                $validAwards = 0;
                foreach ($chapter_awards as $award) {
                    if (!empty($award['awards_type'])) {
                        $validAwards++;
                    }
                }
                if ($validAwards > 0) {
                    $hasAnyAwards = true;
                    $actualMaxAwards = max($actualMaxAwards, $validAwards);
                }
            }
        }
    }
@endphp

<table id="chapterlist" class="table table-sm table-hover">
    <thead>
        <tr>
            <th>Award<br>Details</th>
            <th>Conf/Reg</th>
            <th>State</th>
            <th>Name</th>
            @if ($hasAnyAwards)
                @for ($i = 0; $i < $actualMaxAwards; $i++)
                    <th>Award {{ $i + 1 }}</th>
                @endfor
            @endif
            <th>History</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chapterList as $list)
            @php
                $chapter_awards = null;
                $validChapterAwards = [];

                if (isset($list->financialReport->chapter_awards)) {
                    $blobData = base64_decode($list->financialReport->chapter_awards);
                    $chapter_awards = unserialize($blobData);
                    if ($chapter_awards != false && !empty($chapter_awards)) {
                        // Only include awards that have an awards_type selected
                        foreach ($chapter_awards as $award) {
                            if (!empty($award['awards_type'])) {
                                $validChapterAwards[] = $award;
                            }
                        }
                    }
                }
            @endphp
            @if(!empty($validChapterAwards))
                <tr>
                    <td class="text-center">
                        @if ($assistConferenceCoordinatorCondition)
                            <a href="{{ url("/eoyreports/editawards/{$list->id}") }}"><i class="bi bi-award-fill"></i></a>
                        @endif
                    </td>
                    <td>
                        @if ($list->state->conference_id > 0)
                            {{ $list->state->conference->short_name }} / {{ $list->state->region->short_name }}
                        @else
                            {{ $list->state->conference->short_name }}
                        @endif
                    </td>
                    <td>
                        @if($list->state_id < 52)
                            {{$list->state->state_short_name}}
                        @else
                            {{$list->state->country?->short_name}}
                        @endif
                    </td>
                    <td>{{ $list->name }}</td>
                    @for ($i = 0; $i < $actualMaxAwards; $i++)
                        <td>
                            @if (isset($validChapterAwards[$i]))
                                @php
                                    $awardType = "Unknown";
                                    foreach ($allAwards as $allAward) {
                                        if ($allAward->id == $validChapterAwards[$i]['awards_type']) {
                                            $awardType = $allAward->award_type;
                                            break;
                                        }
                                    }
                                @endphp
                                {{ $awardType }}<br>
                                @if ($validChapterAwards[$i]['awards_approved'])
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                @else
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                @endif
                            @else
                                &nbsp;
                            @endif
                        </td>
                    @endfor
                    <th>
                        <a href="{{ url("/eoyreports/awardhistory/{$list->id}") }}"><i class="bi bi-file-earmark-text"></i></a>
                    </th>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
                </div>
            <!-- /.card-body -->

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                        <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showReviewer" id="showReviewer" class="form-check-input" {{$checkBox2Status ? 'checked' : '' }} onchange="showReviewer()" />
                        <label class="form-check-label" for="showReviewer">Only show chapters I am Assigned Reviewer for</label>
                    </div>
                </div>
                 @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                                @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Conference (Export Available)</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Region (Export Available)</label>
                                @endif
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{$checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                <div>
            <!-- /.card-body for checkboxes -->

                    <div class="card-body text-center mt-3">
                      </div>
            <!-- /.card-body for buttons -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection
