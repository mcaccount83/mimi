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
                            Chapter Awards Report
                        </h3>
                        <span class="ml-2">Chapters that were added after June 30, <?php echo date('Y');?> will not be listed</span>
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
            if ($chapter_awards !== false && !empty($chapter_awards)) {
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
            <th>Details</th>
            <th>Conf/Reg</th>
            <th>State</th>
            <th>Name</th>
            @if ($hasAnyAwards)
                @for ($i = 0; $i < $actualMaxAwards; $i++)
                    <th>Award {{ $i + 1 }}</th>
                @endfor
            @endif
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
                    if ($chapter_awards !== false && !empty($chapter_awards)) {
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
                            <a href="{{ url("/eoy/editawards/{$list->id}") }}"><i class="fas fa-eye"></i></a>
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
                </tr>
            @endif
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
    var base_url = '{{ url("/eoy/awards") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function showReviewer() {
    var base_url = '{{ url("/eoy/awards") }}';

    if ($("#showReviewer").prop("checked") == true) {
        window.location.href = base_url + '?check2=yes';
    } else {
        window.location.href = base_url;
    }
}
</script>
@endsection
