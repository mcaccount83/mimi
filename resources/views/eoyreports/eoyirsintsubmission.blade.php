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
                        International 990N Filing Report
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
            <div class="card-body text-center">
                    <button class="btn bg-gradient-primary mb-3" onclick="showIRSFilingCorrectionsModal()"><i class="fas fa-file-pdf mr-2" ></i>990N Filing corrections to EO Dept</button>

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

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});


function showIRSFilingCorrectionsModal() {
    Swal.fire({
        title: 'IRS 990N Filing Corrections to EO Dept',
        html: `
            <p>This will generate the Fax Coversheet & Letter for the IRS EO Department listing 990N Filing Corrections. Enter the total number of pages (including the coversheet) to be faxed.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            `,
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Close',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        },
        preConfirm: () => {
            const totalPages = Swal.getPopup().querySelector('#total_pages').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            return {
                total_pages: totalPages,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.combinedirsfilingcorrections') }}?pages=${data.total_pages}`;
            window.open(url, '_blank');
        }
    });
}
</script>
@endsection
