@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Payments/Donations</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">M2M & Sustaining Donations</li>
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
                            M2M & Sustaining Donations
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if ($coordinatorCondition)
                                <a class="dropdown-item" href="{{ route('chapters.chapreregistration') }}">Re-Registration Payments</a>
                                <a class="dropdown-item" href="{{ route('chapreports.chaprptdonations') }}">M2M & Sustaining Donations</a>
                            @endif
                            @if ($m2mCondition || $adminReportCondition)
                                <a class="dropdown-item" href="{{ route('international.intdonation') }}">International M2M & Sustaining Donations</a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_donation" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
					<?php if(Session::get('positionid') >=6 && Session::get('positionid') <=7){ ?><th>Donation</th><?php }?>
				  <th>Conf/Reg</th><th>State</th>
                  <th>Name</th>
                    <th>M2M Fund Donation</th>
                    <th>Donation Date</th>
                    <th>Sustaining Chapter Donation</th>
                    <th>Donation Date</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                            <td class="text-center align-middle">
                                @if ($conferenceCoordinatorCondition)
	                                <a href="{{ url("/chapterpaymentedit/{$list->id}") }}"><i class="far fa-credit-card "></i></a>
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
						<td>${{ $list->m2m_payment }}</td>
						<td><span class="date-mask">{{ $list->m2m_date }}</span></td>
						<td>${{ $list->sustaining_donation }}</td>
						<td><span class="date-mask">{{ $list->sustaining_date }}</span></td>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <div class="card-body text-center">&nbsp;</div>
            </div>
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
