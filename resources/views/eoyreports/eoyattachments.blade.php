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
            <li class="breadcrumb-item active">Financial Report Attachments</li>
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
            <div class="card card-outline card-primary">
                <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Financial Report Attachments
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
				<th>Edit</th>
				<th>State</th>
                <th>Name</th>
                <th>Chapter Roster</th>
                <th>Statement 1</th>
                <th>Statement 2</th>
                <th>990N Attached</th>
                <th>990N Verified</th>
                <th>990N Notes</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    <tr>
                        <td class="text-center align-middle">
                            @if ($assistConferenceCoordinatorCondition)
                               <a href="{{ url("/eoy/attachmentsview/{$list->id}") }}"><i class="fas fa-edit"></i></a>
                           @endif
                        </td>
                        <td>{{ $list->state }}</td>
						<td>{{ $list->name }}</td>
                        </td>
                        <td @if($list->roster_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->roster_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td @if($list->bank_statement_included_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->bank_statement_included_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td>
                            @if($list->bank_statement_2_included_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td  @if($list->file_irs_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->file_irs_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td  @if($list->check_current_990N_verified_IRS == 1)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->check_current_990N_verified_IRS == 1)
                                YES
                            @else
                                NO
                            @endif
                        <td>{{ $list->check_current_990N_notes }}</td>
                 </tr>
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
    var base_url = '{{ url("/eoy/attachments") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
    return confirm('This action will send a Late Notice to all chapters who have not submitted their Board Election Report OR their Financial Report, excluding those with an extension or an assigned reviewer. \n\nAre you sure you want to send the EOY Late Notices?');
}

</script>
@endsection