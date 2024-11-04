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
            <li class="breadcrumb-item active">New Chapter Report</li>
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
                            New Chapter Report
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">EIN Status Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptsocialmedia') }}">Social Media Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptcoordinators') }}">Chapter Coordinators Report</a>
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
					<th>Founded</th>
					<th>EIN Letter on File</th>
					<th>Primary Coordinator</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapterdetails/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                                @if ($list->reg != "None")
                                    {{ $list->conf }} / {{ $list->reg }}
                                @else
                                    {{ $list->conf }}
                                @endif
                            </td>
                            <td>{{ $list->ch_state }}</td>
                        <td>{{ $list->ch_name }}</td>
                        <td data-sort="{{ $list->year . '-' . str_pad($list->start_month, 2, '0', STR_PAD_LEFT) }}">
                            {{ $list->start_month }} {{ $list->year }}
                        </td>
                        <td  @if($list->ein_letter_path != null)style="background-color: transparent;"
                            @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->ein_letter_path != null)
                                YES
                            @else
                                NO
                            @endif
                        </td>

						<td>{{ $list->cor_fname }} {{ $list->cor_lname }}</td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <div class="card-body text-center"></div>
            </div>
           </div>
          <!-- /.box -->
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
