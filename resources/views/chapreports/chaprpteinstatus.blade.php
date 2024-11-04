@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Reports</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">EIN Status Report</li>
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
                        EIN Status Report
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">EIN Status Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptcoordinators') }}">Chapter Coordinators Report</a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_einStatus" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
                    <th>Notes</th>
                    <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
                  <th>Start Date</th>
                    <th>EIN</th>
                    <th>Letter On File</th>
                    <!--<th>Letter On File</th>-->
                    <th>Letter Link</th>
                    <th>EIN/IRS Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td>
                        @if ($conferenceCoordinatorCondition)
                        <center><a href="{{ url("/chapterreports/einstatusview/{$list->id}") }}"><i class="fas fa-pencil-alt"></i></a></center>
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
						<td>
						    @if(empty($list->ein_letter_path))

						    @else
						    <a href="{{ $list->ein_letter_path }}" target="blank">{{ $list->name }} EIN Letter</a>
						    @endif</td>
                            <td>{{ $list->ein_notes }}</td>

			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
                <div class="card-body text-center">
                    <a href="{{ route('export.einstatus')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export EIN Status List</button></a>
                </div>

              </div>
            </div>

           </div>
          <!-- /.box -->
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

</script>
@endsection
