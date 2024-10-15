@extends('layouts.coordinator_theme')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1>Retired Coordinator List</h1>
            </div>
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="breadcrumb-item active">Retired Coordinator List</li>
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
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">List of Retired Coordinators</h3>
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
                  <th>Position</th>
                  <th>Retire Date</th>
                  <th>Reason</th>
                </tr>
                </thead>
                <tbody>
                @foreach($retiredCoordinatorList as $list)
                  <tr>
                        <td><center><a href="<?php echo url("/coordinator/retired/view/{$list->cor_id}") ?>"><i class="fas fa-eye"></i></a></center></td>
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
                         <td><span class="date-mask">{{ $list->cor_zapdate }}</span></td>
						<td>{{ $list->cor_reason }}</td>

                  </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
              <!-- /.card-body -->
              <div class="card-body text-center">
                <a href="{{ route('export.retiredcoordinator')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export Retired Coordinator List</button></a>
            </div>
            </div>

           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>

    <!-- /.content -->

@endsection
