@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Reports</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Utilization Report</li>
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
                            Coordinator Utilization Report
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if ($supervisingCoordinatorCondition)

                            <a class="dropdown-item" href="{{ route('coordreports.coordrptvolutilization') }}">Coordinator Utilization Report</a>
                            @endif
                            @if ($assistConferenceCoordinatorCondition)

                            <a class="dropdown-item" href="{{ route('coordreports.coordrptappreciation') }}">Coordinator Appreciation Report</a>
                            <a class="dropdown-item" href="{{ route('coordreports.coordrptbirthdays') }}">Coordinator Birthday Report</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('coordreports.coordrptreportingtree') }}">Coordinator Reporting Tree</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
			        <th>Details</th>
			        <th>Conf/Reg</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Position</th>
					<th>Secondary Position</th>
					<th>Direct Report</th>
                    <th>InDirect Report</th>
                    <th>Total Report</th>
                </tr>
                </thead>
                <tbody>
                @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coorddetails/{$list->cor_id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td>
                            @if ($list->reg != "None")
                                {{ $list->conf }} / {{ $list->reg }}
                            @else
                                {{ $list->conf }}
                            @endif
                        </td>
                    <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
					<td>{{ $list->position }}</td>
        			<td>{{ $list->sec_pos }}</td>
                    <td>{{ $list->direct_report }}</td>
                    <td>{{ $list->indirect_report }}</td>
                    <td>{{ $list->total_report }}</td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            </div>
            <div class="card-body text-center">&nbsp;</div>
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

</script>
@endsection