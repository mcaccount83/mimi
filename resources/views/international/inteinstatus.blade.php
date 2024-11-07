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
                            @if ($adminReportCondition)
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
                    <th>Conference</th>
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
                  <tr>
                    <td class="text-center align-middle">
                        <a href="{{ url("/chapterirsedit/{$list->id}") }}"><i class="fas fa-eye"></i></a>
                    </td>
                        <td class="text-center align-middle">
                            @if($list->ein_letter_path != null)
                                <a href="{{ $list->ein_letter_path }}" target="_blank"><i class="far fa-file-pdf"></i></a>
                            @else
                                &nbsp; <!-- Placeholder to ensure the cell isn't completely empty -->
                            @endif
                        </td>

                        <td>
                        @if ($list->reg != "None")
                                {{ $list->conf }} / {{ $list->reg }}
                            @else
                                {{ $list->conf }}
                            @endif
                        </td>
                        <td>{{ $list->state }}</td>
                        <td>{{ $list->name }}</td>
                        <td data-sort="{{ $list->start_year . '-' . str_pad($list->start_month, 2, '0', STR_PAD_LEFT) }}">
                            {{ $list->start_month }} {{ $list->start_year }}
                        </td>
                        <td>{{ $list->ein }}</td>
                        <td  @if($list->ein_letter_path != null)style="background-color: transparent;"
                                @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>
                        <td>{{ $list->ein_notes }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    <a href="{{ route('export.einstatus')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export EIN Status List</button></a>
                    <a href="{{ route('export.irsfiling')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export Subordinate Filing List</button></a>

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
</script>
@endsection
