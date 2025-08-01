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
                        990N Filing Report
                    </h3>
                    <span class="ml-2">Chapters that were added after June 30, <?php echo date('Y');?> will not be listed</span>
                    @include('layouts.dropdown_menus.menu_eoy')
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
                <th>EIN</th>
                <th>990N Attached</th>
                <th>IRS Verified</th>
                <th>Filing Issues</th>
                <th>Issue Details</th>
                <th>IRS Notified</th>
                <th>Filing Notes</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    <tr>
                        <td class="text-center align-middle">
                            @if ($assistConferenceCoordinatorCondition)
                               <a href="{{ url("/eoy/editirssubmission/{$list->id}") }}"><i class="fas fa-eye"></i></a>
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
                        <td>{{ $list->ein }}</td>
                        </td>
                        <td @if($list->documents?->irs_path != null) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_path != null) YES @else NO @endif
                        </td>
                        <td @if($list->documents?->irs_verified == 1) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_verified == 1) YES @else NO @endif
                        </td>
                        <td @if($list->documents?->irs_issues != 1) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_issues == 1) YES @else NO @endif
                        </td>
                        <td @if($list->documents?->irs_wrongdate != 1 && $list->documents?->irs_notfound != 1) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_wrongdate == 1) WRONG DATES @else  @endif
                            @if($list->documents?->irs_wrongdate == 1 && $list->documents?->irs_notfound == 1) <br> @endif
                            @if($list->documents?->irs_notfound == 1) NOT FOUND @else  @endif
                        </td>
                       <td @if($list->documents?->irs_notified != 1 && $list->documents?->irs_issues == 1) style="background-color:#dc3545; color: #ffffff;"
                            @elseif($list->documents?->irs_notified == 1 && $list->documents?->irs_issues == 1) style="background-color:#28a745; color: #ffffff;"
                            @else style="background-color: #transparent;" @endif>
                            @if($list->documents?->irs_notified == 1) YES @elseif($list->documents?->irs_notified != 1 && $list->documents?->irs_issues == 1) NO @else  @endif
                        </td>
                        <td>{{ $list->documents?->irs_notes?? null }}</td>
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
    var base_url = '{{ url("/eoy/attachments") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

function showReviewer() {
    var base_url = '{{ url("/eoy/attachments") }}';

    if ($("#showReviewer").prop("checked") == true) {
        window.location.href = base_url + '?check2=yes';
    } else {
        window.location.href = base_url;
    }
}

function confirmSendReminder() {
    return confirm('This action will send a Late Notice to all chapters who have not submitted their Board Election Report OR their Financial Report, excluding those with an extension or an assigned reviewer. \n\nAre you sure you want to send the EOY Late Notices?');
}

</script>
@endsection
