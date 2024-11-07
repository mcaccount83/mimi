@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinators')
@section('breadcrumb', 'International Retired Coordinator List')

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
                        International Retired Coordinator List
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($coordinatorCondition)
                            <a class="dropdown-item" href="{{ route('coordinators.coordlist') }}">Active Coordinator List</a>
                            <a class="dropdown-item" href="{{ route('coordinators.coordretired') }}">Retired Coordinator List</a>
                        @endif
                        @if ($adminReportCondition)
                            <a class="dropdown-item" href="{{ route('international.intcoord') }}">International Active Coordinator List</a>
                            <a class="dropdown-item" href="{{ route('international.intcoordretired') }}">International Retired Coordinator List</a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
              <table id="coordinatorlist" class="table table-sm table-hover">
              <thead>
			    <tr>
			      <th>Details</th>
			      <th>Conf/Reg</th>
				  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Retired Date</th>
                  <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($intCoordinatorList as $list)
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
                        <td><span class="date-mask">{{ $list->zapdate }}</span></td>
                        <td>{{ $list->reason }}</td>

                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
       <!-- /.card-body -->
       <div class="card-body text-center">&nbsp; </div>
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

