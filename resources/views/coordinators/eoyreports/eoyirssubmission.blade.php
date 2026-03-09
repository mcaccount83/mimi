@extends('layouts.mimi_theme')

@if ($ITCondition && !$displayEOYTESTING && !$displayEOYLIVE)
    @section('page_title', $fiscalYearEOY.' EOY Reports *ADMIN*')
    @section('breadcrumb', 'EOY 990N Filing Reports')
@elseif ($eoyTestCondition && $displayEOYTESTING)
    @section('page_title', $fiscalYearEOY.' EOY Reports *TESTING*')
    @section('breadcrumb', 'EOY 990N Filing Reports')
@else
    @section('page_title', $fiscalYearEOY.' EOY Reports')
    @section('breadcrumb', 'EOY 990N Filing Reports')
@endif

@section('content')
 <!-- Main content -->
 <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        990N Filing Report
                    </h3>
                    @include('layouts.dropdown_menus.menu_eoy')
                </div>
            </div>
            <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				<th>990N<br>Details</th>
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
                               <a href="{{ url("/eoyreports/editirssubmission/{$list->id}") }}"><i class="bi bi-bank"></i></i></a>
                           @endif
                        </td>
                        <td>
                            @if ($list->state->conference_id > 0)
                                {{ $list->state->conference->short_name }} / {{ $list->state->region->short_name }}
                            @else
                                {{ $list->state->conference->short_name }}
                            @endif
                        </td>
                        <td>
                            @if($list->state_id < 52)
                                {{$list->state->state_short_name}}
                            @else
                                {{$list->state->country?->short_name}}
                            @endif
                        </td>
						<td>{{ $list->name }}</td>
                        <td>{{ $list->ein }}</td>
                        </td>
                        <td @if($list->documentsEOY?->irs_path != null) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documentsEOY?->irs_path != null) YES @else NO @endif
                        </td>
                      <td @if($list->documentsEOY?->irs_verified) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                        @if($list->documentsEOY?->irs_verified) YES @else NO @endif
                        </td>
                        <td @if(!$list->documentsEOY?->irs_issues) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documentsEOY?->irs_issues) YES @else NO @endif
                        </td>
                        <td @if(!$list->documentsEOY?->irs_wrongdate && !$list->documentsEOY?->irs_notfound && !$list->documentsEOY?->irs_filedwrong) style="background-color: #transparent;" @else style="background-color:#dc3545; color: #ffffff;" @endif>
                            @if($list->documentsEOY?->irs_wrongdate) WRONG DATES @endif
                            @if($list->documentsEOY?->irs_notfound) NOT FOUND @endif
                            @if($list->documentsEOY?->irs_filedwrong) FILED W/WRONG DATES @endif
                        </td>
                        <td @if(!$list->documentsEOY?->irs_notified && $list->documentsEOY?->irs_issues) style="background-color:#dc3545; color: #ffffff;"
                            @elseif($list->documentsEOY?->irs_notified && $list->documentsEOY?->irs_issues) style="background-color:#28a745; color: #ffffff;"
                            @else style="background-color: #transparent;" @endif>
                            @if($list->documentsEOY?->irs_notified) YES @elseif(!$list->documentsEOY?->irs_notified && $list->documentsEOY?->irs_issues) NO @endif
                        </td>
                        <td>{{ $list->documentsEOY?->irs_notes?? null }}</td>
                 </tr>
                  @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->

            <div class="card-body">
            <div class="col-sm-12">
                <div class="form-check form-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                    <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-check form-switch">
                    <input type="checkbox" name="showReviewer" id="showReviewer" class="form-check-input" {{$checkBox2Status ? 'checked' : '' }} onchange="showReviewer()" />
                    <label class="form-check-label" for="showReviewer">Only show chapters I am Assigned Reviewer for</label>
                </div>
            </div>
            @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                                @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Conference (Export Available)</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Region (Export Available)</label>
                                @endif
                        </div>
                    </div>
                @endif
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{$checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
                  </div>
            <!-- /.card-body for checkboxes -->


                <div class="card-body text-center mt-3">
                        @if (($einCondition || $ITCondition) && ($checkBox51Status ?? '') == 'checked')
                            <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="showIRSFilingCorrectionsModal()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>990N Filing corrections to EO Dept</button>
                        @endif
          </div>
            <!-- /.card-body for buttons -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection

