@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Reports')
@section('breadcrumb', 'Coordinator Appreciation Report')

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
                            Coordinator Appreciation Report
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_coor')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="coordinatorlist" class="table table-sm table-hover" >
				<thead>
			    <tr>
			        <th>Details</th>
                    <th>Conf/Reg</th>
					<th>Coordinator Name</th>
					<th>Start Date</th>
                    <th>Gold Pin</th>
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
                    <td class="text-center"><a href="{{ url("/coordinator/details/editrecognition/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                        <td>
                            @if ($list->region->short_name != "None")
                                {{ $list->conference->short_name }} / {{ $list->region->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                    <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                    <td><span class="date-mask">{{ $list->coordinator_start_date }}</span></td>

                    <td>@if($list->recognition->recognition_pin=='1')
                        YES
                        @endif
                    </td>

                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift0)
                            {{ $list->recognition->recognitionGift0->recognition_gift }}
                            @if($list->recognition->year0 != null)
                                ({{$list->recognition->year0}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift1)
                            {{ $list->recognition->recognitionGift1->recognition_gift }}
                            @if($list->recognition->year1 != null)
                                ({{$list->recognition->year1}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift2)
                            {{ $list->recognition->recognitionGift2->recognition_gift }}
                            @if($list->recognition->year2 != null)
                                ({{$list->recognition->year2}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift3)
                            {{ $list->recognition->recognitionGift3->recognition_gift }}
                            @if($list->recognition->year3 != null)
                                ({{$list->recognition->year3}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift4)
                            {{ $list->recognition->recognitionGift4->recognition_gift }}
                            @if($list->recognition->year4 != null)
                                ({{$list->recognition->year4}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift5)
                            {{ $list->recognition->recognitionGift5->recognition_gift }}
                            @if($list->recognition->year5 != null)
                                ({{$list->recognition->year5}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift6)
                            {{ $list->recognition->recognitionGift6->recognition_gift }}
                            @if($list->recognition->year6 != null)
                                ({{$list->recognition->year6}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift7)
                            {{ $list->recognition->recognitionGift7->recognition_gift }}
                            @if($list->recognition->year7 != null)
                                ({{$list->recognition->year7}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift8)
                            {{ $list->recognition->recognitionGift8->recognition_gift }}
                            @if($list->recognition->year8 != null)
                                ({{$list->recognition->year8}})
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($list->recognition && $list->recognition->recognitionGift9)
                            {{ $list->recognition->recognitionGift9->recognition_gift }}
                            @if($list->recognition->year9 != null)
                                ({{$list->recognition->year9}})
                            @endif
                        @endif
                    </td>

					<td>@if($list->recognition->recognition_necklace=='1')
							YES
							@endif
						</td>
					<td>{{ $list->recognition->recognition_toptier }}</td>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>

            <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showDirect" id="showDirect" class="custom-control-input" {{$checkBoxStatus}} onchange="showCoordDirect()" />
                        <label class="custom-control-label" for="showDirect">Only show my Direct Reports</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAllConf" id="showAllConf" class="custom-control-input" {{$checkBox3Status}} onchange="showCoordAllConf()" />
                            <label class="custom-control-label" for="showAllConf">Show All Coordinators</label>
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showCoordAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Coordinators (Export Available)</label>
                        </div>
                    </div>
                @endif

           </div>
           <div class="card-body text-center">
                @if ($checkBox3Status)
                    <button class="btn bg-gradient-primary mb-3" onclick="startExport('appreciation', 'Coordinator Appreciation List')"><i class="fas fa-download mr-2" ></i>Export Coordinator Appreciation List</button>
                {{-- @else
                    <button class="btn bg-gradient-primary mb-3 disabled" onclick="startExport('appreciation', 'Coordinator Appreciation List')" disabled><i class="fas fa-download mr-2" ></i>Export Coordinator Appreciation List</button> --}}
                @endif
        </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->

@endsection
