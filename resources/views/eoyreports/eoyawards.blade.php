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
                    <table id="chapterlist" class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Details</th>
                                <th>Conf/Reg</th>
                                <th>State</th>
                                <th>Name</th>
                                @for ($i = 0; $i < $maxAwards; $i++)
                                <th>Award {{ $i + 1 }}</th>
                            @endfor
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($chapterList as $list)
                        @php
                            $chapter_awards = null;
                            if (isset($list->financialReport->chapter_awards)) {
                                $blobData = base64_decode($list->financialReport->chapter_awards);
                                $chapter_awards = unserialize($blobData);
                                if ($chapter_awards === false) {
                                    $chapter_awards = [];
                                }
                            }
                        @endphp
                        @if($chapter_awards != null)

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
                                    <td>{{ $list->state->state_short_name }}</td>
                                    <td>{{ $list->name }}</td>
                                    @for ($i = 0; $i < $maxAwards; $i++)
                                        <td>
                                            @if ($chapter_awards && isset($chapter_awards[$i]))
                                                @php
                                                    $awardType = "Unknown";
                                                    foreach ($allAwards as $allAward) {
                                                        if ($allAward->id == $chapter_awards[$i]['awards_type']) {
                                                            $awardType = $allAward->award_type;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                {{ $awardType }}<br>
                                                @if ($chapter_awards[$i]['awards_approved'])
                                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                                @else
                                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                                @endif
                                            @else
                                                <!-- Empty cell for chapters with fewer awards -->
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
