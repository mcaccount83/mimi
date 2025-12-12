@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Probation Report')

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
                            Chapter Probation Report
                        </h3>
                        <span class="ml-2">Includes chapters that have a Probationary status</span>
                        @include('layouts.dropdown_menus.menu_reports_chap')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Details</th>
                  <th>QTR Report</th>
                  <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
                 <th>Status</th>
                 <th>Reason</th>
				 <th>Notes</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
                    <td class="text-center align-middle">
                        @if ($list->probation_id == '3')
                            <a href="{{ url("/board/probation/{$list->id}") }}"><i class="fas fa-file"></i></a>
                        @else
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
                        <td @if ( $list->status_id == 4 || $list->status_id == 6) style="background-color: #dc3545; color: #ffffff;"
                            @elseif ( $list->status_id == 5) style="background-color: #ffc107;"
                            @endif>
                        {{ $list->status->chapter_status }}</td>
                        <td>{{ $list->probation?->probation_reason }}</td>
						<td>{{ $list->notes }}</td>
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
                @if ($ITCondition || $einCondition)
                    <div class="col-sm-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="showAll" id="showAll" class="custom-control-input" {{$checkBox5Status}} onchange="showChAll()" />
                            <label class="custom-control-label" for="showAll">Show International Chapters</label>
                        </div>
                    </div>
                @endif
                <div class="card-body text-center">
                    @if ($ITCondition)
                        @if ($checkBox5Status)
                            <button type="button" class="btn bg-gradient-primary mb-3" onclick="showResetProbationSubmisionModel()" disabled><i class="fas fa-undo mr-2"></i>Reset Quarterly Report Data</button>
                        @else
                            <button type="button" class="btn bg-gradient-primary mb-3 disabled" onclick="showResetProbationSubmisionModel()" disabled><i class="fas fa-undo mr-2"></i>Reset Quarterly Report Data</button>
                        @endif
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- /.box -->
    </div>
</section>
<!-- Main content -->

<!-- /.content -->
@endsection
