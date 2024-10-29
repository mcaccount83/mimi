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
            <li class="breadcrumb-item active">Chapter Status Report</li>
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
                        Chapter Status Report
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">EIN Status Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
                        <a class="dropdown-item" href="{{ route('chapreports.chaprptdonations') }}">Chapter Donation Report</a>
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
                <th>Status</th>
                <th>Status Notes</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr>
                            <td class="text-center align-middle"><a href="{{ url("/chapter/chapterview/{$list->id}") }}"><i class="fas fa-edit"></i></a></td>
                            <td>
                                @if ($list->reg != "None")
                                    {{ $list->conf }} / {{ $list->reg }}
                                @else
                                    {{ $list->conf }}
                                @endif
                            </td>
                            <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            @switch($list->status)
                                @case(1)
                                    <td>OK</td>
                                    @break
                                @case(4)
                                    <td style="background-color: #dc3545; color: #ffffff;">On Hold Do Not Refer</td>
                                    @break
                                @case(5)
                                    <td style="background-color: #ffc107;">Probation</td>
                                    @break
                                @case(6)
                                    <td style="background-color: #dc3545; color: #ffffff;">Probation Do Not Refer</td>
                                    @break
                            @endswitch
                            <td>{{ $list->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
			   <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only show chapters 'Not Ok'</label>
                </div>
            </div>

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
    function showPrimary() {
    var base_url = '{{ url("/chapterreports/chapterstatus") }}';

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
