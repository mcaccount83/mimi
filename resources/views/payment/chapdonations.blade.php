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
                        @include('layouts.dropdown_menus.menu_payment')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              {{-- <table id="chapterlist_donation" class="table table-bordered table-hover"> --}}
              <thead>
			    <tr>
                <th>
                    @if ($conferenceCoordinatorCondition)
                    Donation
                    @endif
                </th>
				  <th>Conf/Reg</th>
                  <th>State</th>
                  <th>Name</th>
                    <th>M2M Fund Donation</th>
                    <th>Donation Date</th>
                    <th>Sustaining Chapter Donation</th>
                    <th>Donation Date</th>
                    <th>History</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                            <td class="text-center align-middle">
                                @if ($conferenceCoordinatorCondition)
	                                <a href="{{ url("/payment/chapterpaymentedit/{$list->id}") }}"><i class="far fa-credit-card "></i></a>
                                @endif
                            </td>
                                <td>
                                    @if ($list->region->short_name != "None")
                                    {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                                @else
                                    {{ $list->conference->short_name }}
                                @endif
                                </td>
                                <td>
                                @if($list->state_id < 52)
                                    {{$list->state->state_short_name}}
                                @else
                                    {{$list->country->short_name}}
                                @endif
                            </td>
                                <td>{{ $list->name }}</td>
						<td>
                            @if( $list->payments->m2m_donation != null )
                                ${{ $list->payments->m2m_donation }}
                            @endif
                        </td>
						<td><span class="date-mask">{{ $list->payments->m2m_date }}</span></td>
						<td>
                            @if( $list->payments->sustaining_donation != null )
                                ${{ $list->payments->sustaining_donation }}
                            @endif
                        </td>
						<td><span class="date-mask">{{ $list->payments->sustaining_date }}</span></td>
                        <th>
                            <a href="{{ url("/payment/chapterpaymenthistory/{$list->id}") }}"><i class="fas fa-file-invoice-dollar "></i></a>
                        </th>
			        </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showChPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
            </div>
             @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showChAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Chapters</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
            <div class="card-body text-center">&nbsp;</div>
            </div>
           </div>
        </div>
      </div>
    </section>
    <!-- /.content -->

@endsection
