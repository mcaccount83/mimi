@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Volunteer Appreciation Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Volunteer Appreciation Report</li>
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
                  <h3 class="card-title">Report of Volunteer Utilization</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
			        <th>Edit</th>
                    <th>Conf/Reg</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Start Date</th>
					<th>< 1 Year</th>
					<th>1 Year</th>
					<th>2 Years</th>
                    <th>3 Years</th>
                    <th>4 Years</th>
                    <th>5 Years</th>
                    <th>6 Years</th>
                    <th>7 Years</th>
                    <th>8 Years</th>
                    <th>9 Years</th>
                    <th>Necklace</th>
                    <th>Top Tier/Other</th>
                </tr>
                </thead>
                <tbody>

                @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle">
                        <a href="<?php echo url("/coordinator/appreciation/{$list->cor_id}") ?>"><i class="fas fa-edit "></i></a></td>
                        <td>
                            @if ($list->reg != "None")
                                {{ $list->conf }} / {{ $list->reg }}
                            @else
                                {{ $list->conf }}
                            @endif
                        </td>
                        <td>{{ $list->cor_fname }}</td>
                    <td>{{ $list->cor_lname }}</td>
                    <td><span class="date-mask">{{ $list->start_date }}</span></td>
                    <td>{{ $list->yr_0 }}</td>
					<td>{{ $list->yr_1 }}</td>
					<td>{{ $list->yr_2 }}</td>
					<td>{{ $list->yr_3 }}</td>
					<td>{{ $list->yr_4 }}</td>
					<td>{{ $list->yr_5 }}</td>
					<td>{{ $list->yr_6 }}</td>
					<td>{{ $list->yr_7 }}</td>
					<td>{{ $list->yr_8 }}</td>
					<td>{{ $list->yr_9 }}</td>
					<td>@if($list->necklace=='1')
							YES
							@endif
						</td>
					<td>{{ $list->toptier }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
           </div>
           <div class="card-body text-center">
           <a href="{{ route('export.appreciation')}}"><button class="btn bg-gradient-primary"><i class="fas fa-download " ></i>&nbsp;&nbsp;&nbsp;Export Coordinator Appreciation List</button></a>
        </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->

@endsection
