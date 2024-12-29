@extends('layouts.coordinator_theme')

@section('page_title', 'End of Year Reports')
@section('breadcrumb', 'Chapter Awards Report')

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
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('eoyreports.eoystatus') }}">EOY Status Report</a>
                            <a class="dropdown-item" href="{{ route('eoyreports.eoyboardreport') }}">Board Election Reports</a>
                            <a class="dropdown-item" href="{{ route('eoyreports.eoyfinancialreport') }}">Financial Reports</a>
                            <a class="dropdown-item" href="{{ route('eoyreports.eoyattachments') }}">Financial Report Attachments</a>
                            <a class="dropdown-item" href="{{ route('eoyreports.eoyboundaries') }}">Boundary Issues Report</a>
                            <a class="dropdown-item" href="{{ route('eoyreports.eoyawards') }}">Chapter Awards Report</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                  <th>Details</th>
                  <th>Conf/Reg</th>
                  <th>State</th>
                  <th>Name</th>
                  <th>Award 1</th>
                  <th>Award 2</th>
                  <th>Award 3</th>
                  <th>Award 4</th>
                  <th>Award 5</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($chapterList as $list)
                    <tr>
                        <td class="text-center align-middle">
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
                        <td>{{ $list->financialReport->awardType1?->award_type }}
                            @if ($list->financialReport->check_award_1_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                            @else
                                @if ($list->financialReport->award_1_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                @endif
                            @endif
                        </td>
                        <td>{{ $list->financialReport->awardType2?->award_type }}
                            @if ($list->financialReport->check_award_2_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                            @else
                                @if ($list->financialReport->award_2_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                @endif
                            @endif
                        </td>
                        <td>{{ $list->financialReport->awardType3?->award_type }}
                            @if ($list->financialReport->check_award_3_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                            @else
                                @if ($list->financialReport->award_3_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                @endif
                            @endif
                        </td>
                        <td>{{ $list->financialReport->awardType4?->award_type }}
                            @if ($list->financialReport->check_award_4_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                            @else
                                @if ($list->financialReport->award_4_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                @endif
                            @endif
                        </td>
                        <td>{{ $list->financialReport->awardType5?->award_type }}
                            @if ($list->financialReport->check_award_5_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                            @else
                                @if ($list->financialReport->award_5_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach

                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input"
                                   {{ $checkBoxStatus }} onchange="showPrimary()" />
                            <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                        </div>
                    </div>
                    <div class="card-body text-center">

             </div>
            </div>
           </div>
        </div>
      </div>
    </section>

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
</script>
@endsection
