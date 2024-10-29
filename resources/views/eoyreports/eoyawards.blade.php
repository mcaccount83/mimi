@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>End of Year Reports</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Awards</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Chapter Awards
                        </h3>
                        <span class="ml-2">Chapters that were added after June 30, <?php echo date('Y');?> will not be listed</span>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="/eoy/status">Report Status</a>
                            <a class="dropdown-item" href="/eoy/boardreport">Board Election Reports</a>
                            <a class="dropdown-item" href="/eoy/financialreport">Financial Reports</a>
                            <a class="dropdown-item" href="/eoy/attachments">Financial Report Attachments</a>
                            <a class="dropdown-item" href="/eoy/boundaries">Boundary Issues</a>
                            <a class="dropdown-item" href="/eoy/awards">Chapter Awards</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                  <th>Add/Edit</th>
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
                                <a href="{{ url("/eoy/awardsview/{$list->id}") }}"><i class="fas fa-edit"></i></a>
                          @endif
                        </td>
                        <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            <td>@if($list->award_1_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_1_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_1_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_1_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_1_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_1_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_1_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_1_approved)
                                        <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_1_nomination_type)
                                            <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>
                            <td>@if($list->award_2_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_2_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_2_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_2_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_2_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_2_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_2_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_2_approved)
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_2_nomination_type)
                                        <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>
                            <td>@if($list->award_3_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_3_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_3_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_3_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_3_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_3_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_3_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_3_approved)
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_3_nomination_type)
                                        <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>
                            <td>@if($list->award_4_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_4_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_4_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_4_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_4_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_4_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_4_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                @if ($list->award_4_approved)
                                <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                @else
                                    @if ($list->award_4_nomination_type)
                                    <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                    @endif
                            @endif</td>
                            <td>@if($list->award_5_nomination_type=='1')
                                Outstanding Specific Service Project
                                @elseif($list->award_5_nomination_type=='2')
                                Outstanding Overall Service Program
                                @elseif($list->award_5_nomination_type=='3')
                                Outstanding Children's Activity
                                @elseif($list->award_5_nomination_type=='4')
                                Outstanding Spirit
                                @elseif($list->award_5_nomination_type=='5')
                                Outstanding Chapter
                                @elseif($list->award_5_nomination_type=='6')
                                Outstanding New Chapter
                                @elseif($list->award_5_nomination_type=='7')
                                Other Outstanding Award
                                @else

                                @endif
                                    @if ($list->award_5_approved)
                                    <div style="background-color:#28a745; color: #ffffff;">YES</div>
                                    @else
                                        @if ($list->award_5_nomination_type)
                                        <div style="background-color:#dc3545; color: #ffffff;">NO</div>
                                        @endif
                                @endif</td>

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
                            <label class="custom-control-label" for="showPrimary">Show All Chapters</label>
                        </div>
                    </div>
                    <div class="card-body text-center">
                          {{-- <a class="btn bg-gradient-primary" href="{{ route('report.addawards') }}"><i class="fas fa-eye" ></i>&nbsp;&nbsp;&nbsp;View All Chapers</a> --}}

        {{-- @if ($checkBoxStatus)
				<a href="{{ route('export.chapteraward',$corId) }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Export Award List</button></a>

			 @else
				<a href="{{ route('export.chapteraward','0') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-envelope" ></i>&nbsp;&nbsp;&nbsp;Export Award List</button></a>
		@endif --}}



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
        // Check if the item's href matches the current path
        if (item.getAttribute("href") === currentPath) {
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
