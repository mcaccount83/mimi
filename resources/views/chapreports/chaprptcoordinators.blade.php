@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Coordinator Report')

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
                            Chapter Coordinator Report
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">IRS Status Report</a>
                            @if ($adminReportCondition)
                                <a class="dropdown-item" href="{{ route('international.inteinstatus') }}">International IRS Status Report</a>
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
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Details</th>
                  <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
				   <th>CC</th>
				   <th>ACC</th>
				  <th>RC</th>
				  <th>ARC</th>
				  <th>SC</th>
				  <th>AC</th>
				  <th>BS</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chaptersData as $data)
                        @php
                            $chapter = $data['chapter'];
                            $coordinatorArray = $data['coordinatorArray'];
                        @endphp
                        <tr>
                            <td class="text-center align-middle">
                                <a href="{{ url('/chapterdetails/' . $chapter->id) }}"><i class="fas fa-eye"></i></a>
                            </td>
                            <td>
                                @if ($chapter->region->short_name != "None")
                                    {{ $chapter->conference->short_name }} / {{ $chapter->region->short_name }}
                                @else
                                    {{ $chapter->conference->short_name }}
                                @endif
                            </td>
                            <td>{{ $chapter->state->state_short_name }}</td>
                            <td>{{ $chapter->name }}</td>
                            @for ($posRow = 7; $posRow > 0; $posRow--)
                                @php $positionFound = false; @endphp
                                @foreach ($coordinatorArray as $coordinator)
                                    @if ($coordinator && $coordinator->position === $positionCodes[$posRow - 1])
                                        <td>{{ $coordinator->first_name }} {{ $coordinator->last_name }}</td>
                                        @php $positionFound = true; @endphp
                                        @break
                                    @endif
                                @endforeach
                                @if (!$positionFound)
                                    <td></td>
                                @endif
                            @endfor
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
              <a href="{{ route('export.chaptercoordinator') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download mr-2"></i>Export Chapter Coordinator List</button></a>
             </div>

           </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->

@endsection
@section('customscript')
<script>
    function showPrimary() {
    var base_url = '{{ url("/chapterreports/coordinators") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

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
