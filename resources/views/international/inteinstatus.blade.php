@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'International IRS Status Report')

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
                            International IRS Status Report
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">IRS Status Report</a>
                            @if ($userAdmin)
                                <a class="dropdown-item" href="{{ route('international.intchapter') }}">International IRS Status Report</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptcoordinators') }}">Chapter Coordinators Report</a>
                        </div>
                    </div>
                </div>
            <!-- /.card-header -->
        <div class="card-body">
              {{-- <table id="chapterlist_inteinStatus" class="table table-bordered table-hover"> --}}
                <table id="chapterlist"  class="table table-sm table-hover">
              <thead>
			    <tr>
                    <th>Details</th>
                    <th>Letter</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter On File</th>
                    <th>EIN/IRS Notes</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    <tr >
                        <td class="text-center align-middle">
                            @if ($conferenceCoordinatorCondition)
                                <a href="{{ url("/chapterirsedit/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if ($list->documents->ein_letter_path != null)
                                <a href="{{ $list->documents->ein_letter_path }}"
                                    onclick="event.preventDefault(); openPdfViewer('{{ $list->documents->ein_letter_path }}');">
                                    <i class="far fa-file-pdf"></i>
                                    </a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
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
                        <td data-sort="{{ $list->start_year . '-' . str_pad($list->start_month_id, 2, '0', STR_PAD_LEFT) }}">
                            {{ $list->startMonth->month_short_name }} {{ $list->start_year }}
                        </td>
						<td>{{ $list->ein }}</td>
                        <td @if($list->documents->ein_letter_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td>{{ $list->documents->irs_notes }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    <button class="btn bg-gradient-primary mb-3" onclick="startExport('inteinstatus', 'International EIN Status List')"><i class="fas fa-download mr-2" ></i>Export EIN Status List</button>
                    <button class="btn bg-gradient-primary  mb-3" onclick="startExport('intirsfiling', 'Subordinate Filing Report')"><i class="fas fa-download mr-2" ></i>Export Subordinate Filing</button>
                    <br>
                    {{-- <button class="btn bg-gradient-primary" onclick="window.open('{{ route('pdf.subordinatefiling') }}', '_blank')"><i class="fas fa-file-pdf mr-2" ></i>Subordinate Filing PDF</button> --}}
                    <button class="btn bg-gradient-primary mb-3" onclick="showEODeptCoverSheetModal()"><i class="fas fa-file-pdf mr-2" ></i>EO Dept Fax Coversheet</button>
                    <button class="btn bg-gradient-primary mb-3" onclick="showIRSUpdatesModal()"><i class="fas fa-file-pdf mr-2" ></i>IRS Updates to EO Dept</button>
                    <button class="btn bg-gradient-primary  mb-3" onclick="showSubordinateFilingModal()"><i class="fas fa-file-pdf mr-2" ></i>Subordinate Filing PDF</button>
                </div>
            </div>
             <!-- /.box -->
           </div>
         </div>
        </div>
       </section>
       <!-- Main content -->

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

function showEODeptCoverSheetModal() {
    Swal.fire({
        title: 'IRS EO Department Fax',
        html: `
            <p>This will generate the Fax Coversheet for the IRS EO Department. Enter the total number of pages (including the coversheet) to be faxed as well as
                a brief message describing the contents of the fax.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            <div style="display: flex; align-items: center;">
                <textarea id="email_message" name="email_message" class="swal2-textarea" placeholder="Enter Message" required style="width: 100%; height: 150px;"></textarea>
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
            const emailMessage = Swal.getPopup().querySelector('#email_message').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            if (!emailMessage || emailMessage.trim() === '') {
                Swal.showValidationMessage('Please enter a message');
                return false;
            }

            return {
                total_pages: totalPages,
                email_message: emailMessage,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.eodeptfaxcover') }}?pages=${data.total_pages}&message=${encodeURIComponent(data.email_message)}&title=${encodeURIComponent('IRS EO Department Fax')}`;
            window.open(url, '_blank');
        }
    });
}

function showIRSUpdatesModal() {
    Swal.fire({
        title: 'IRS Updates to EO Dept',
        html: `
            <p>This will generate the Fax Coversheet for the IRS EO Department. Enter the total number of pages (including the coversheet) to be faxed as well as
                a brief message describing the contents of the fax.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            <div style="display: flex; align-items: center;">
                <input type="date" id="from_date" name="from_date" class="swal2-input" required style="width: 100%;">
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
            const fromDate = Swal.getPopup().querySelector('#from_date').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            if (!fromDate || fromDate.trim() === '') {
                Swal.showValidationMessage('Please enter a start date for report');
                return false;
            }

            return {
                total_pages: totalPages,
                from_date: fromDate,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.combinedirsupdates') }}?pages=${data.total_pages}&date=${data.from_date}`;
            window.open(url, '_blank');
        }
    });
}

function showSubordinateFilingModal() {
    Swal.fire({
        title: 'IRS Updates to EO Dept',
        html: `
            <p>This will generate the Fax Coversheet for the IRS EO Department. Enter the total number of pages (including the coversheet) to be faxed as well as
                a brief message describing the contents of the fax.</p>
            <div style="display: flex; align-items: center;">
                <input type="text" id="total_pages" name="total_pages" class="swal2-input" placeholder="Enter Total Pages" required style="width: 100%;">
            </div>
            <div style="display: flex; align-items: center;">
                <input type="date" id="from_date" name="from_date" class="swal2-input" required style="width: 100%;">
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
            const fromDate = Swal.getPopup().querySelector('#from_date').value;

            if (!totalPages || isNaN(totalPages) || totalPages < 1) {
                Swal.showValidationMessage('Please enter a valid number of pages');
                return false;
            }

            if (!fromDate || fromDate.trim() === '') {
                Swal.showValidationMessage('Please enter a start date for report');
                return false;
            }

            return {
                total_pages: totalPages,
                from_date: fromDate,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;

            // Open PDF in new window with pages parameter
            const url = `{{ route('pdf.combinedsubordinatefiling') }}?pages=${data.total_pages}&date=${data.from_date}`;
            window.open(url, '_blank');
        }
    });
}

</script>
@endsection
