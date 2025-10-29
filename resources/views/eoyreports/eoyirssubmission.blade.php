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
                        990N Filing Report
                    </h3>
                    <span class="ml-2">Chapters that were added after June 30, <?php echo date('Y');?> will not be listed</span>
                    @include('layouts.dropdown_menus.menu_eoy')
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
                <th>EIN</th>
                <th>990N Attached</th>
                <th>IRS Verified</th>
                <th>Filing Issues</th>
                <th>Issue Details</th>
                <th>IRS Notified</th>
                <th>Filing Notes</th>
				</tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                    <tr>
                        <td class="text-center align-middle">
                            @if ($assistConferenceCoordinatorCondition)
                               <a href="{{ url("/eoy/editirssubmission/{$list->id}") }}"><i class="fas fa-eye"></i></a>
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
                        <td>{{ $list->ein }}</td>
                        </td>
                        <td @if($list->documents?->irs_path != null) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_path != null) YES @else NO @endif
                        </td>
                      <td @if($list->documents?->irs_verified) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->documents?->irs_verified) YES @else NO @endif
                        </td>
                        <td @if(!$list->documents?->irs_issues) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_issues) YES @else NO @endif
                        </td>
                        <td @if(!$list->documents?->irs_wrongdate && !$list->documents?->irs_notfound && !$list->documents?->irs_filedwrong) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documents?->irs_wrongdate) WRONG DATES @endif
                            @if($list->documents?->irs_notfound) NOT FOUND @endif
                            @if($list->documents?->irs_filedwrong) FILED W/WRONG DATES @endif
                        </td>
                        <td @if(!$list->documents?->irs_notified && $list->documents?->irs_issues) style="background-color:#dc3545; color: #ffffff;"
                            @elseif($list->documents?->irs_notified && $list->documents?->irs_issues) style="background-color:#28a745; color: #ffffff;"
                            @else style="background-color: #transparent;" @endif>
                            @if($list->documents?->irs_notified) YES @elseif(!$list->documents?->irs_notified && $list->documents?->irs_issues) NO @endif
                        </td>
                        <td>{{ $list->documents?->irs_notes?? null }}</td>
                 </tr>
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
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show All International Chapters</label>
                        </div>
                    </div>
                @endif

                <div class="card-body text-center">
                        @if (($einCondition || $ITCondition) && ($checkBox5Status ?? '') == 'checked')
                            <button class="btn bg-gradient-primary mb-3" onclick="showIRSFilingCorrectionsModal()"><i class="fas fa-file-pdf mr-2" ></i>990N Filing corrections to EO Dept</button>
                        @endif
           </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
</div>
    </section>
    <!-- /.content -->
@endsection
