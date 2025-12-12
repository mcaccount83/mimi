@extends('layouts.coordinator_theme')

@section('page_title', $title)
@section('breadcrumb', $breadcrumb)

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
                            Boundary Issues Report
                        </h3>
                        @include('layouts.dropdown_menus.menu_eoy')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
					<th>Details</th>
                    <th>Conf/Reg>
				  <th>State</th>
                  <th>Name</th>
                 <th>Reported Issue</th>
				 <th>Boundary on File</th>
				 <th>Resolved</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                @if($list->boundary_issue_notes != '')
                  <tr>
						<td class="text-center align-middle">
                            <a href="{{ url("/eoy/editboundaries/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
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
                        <td>{{ $list->boundary_issue_notes}}</td>
                        <td>{{ $list->territory}}</td>
                        <td @if($list->boundary_issue_resolved == '1') style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->boundary_issue_resolved == '1') YES @else NO @endif
                        </td>
			        </tr>
                  @endif
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showChPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showReviewer" id="showReviewer" class="custom-control-input" {{$checkBox2Status}} onchange="showChReviewer()" />
                    <label class="custom-control-label" for="showReviewer">Only show chapters I am Assigned Reviewer for</label>
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
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif

                <div class="card-body text-center">&nbsp;</div>
              </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
