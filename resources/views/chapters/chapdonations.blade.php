@extends('layouts.coordinator_theme')

@section('page_title', 'Payments/Donations')
@section('breadcrumb', 'M2M & Sustaining Donations')

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
                                    @if ($list->region->short_name != "None")
                                    {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                                @else
                                    {{ $list->conference->short_name }}
                                @endif
                                </td>
                                <td>{{ $list->state->state_short_name }}</td>
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
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
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

function showPrimary() {
var base_url = '{{ url("/chapter/donations") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}


</script>
@endsection