@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Status Report')

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
                        Chapter Status Report
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
                <th>Status</th>
                <th>Status Notes</th>
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
                            <td>{{ $list->state }}</td>
                            <td>{{ $list->name }}</td>
                            <td @if($list->status_id == '4' ||$list->status_id == '6' ) style="background-color:#dc3545; color: #ffffff;"
                                @elseif ($list->status_id == '5') style="background-color:#ffc107;"
                                @else style="background-color: transparent;" @endif>
                                {{ $list->status }}
                            </td>
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
