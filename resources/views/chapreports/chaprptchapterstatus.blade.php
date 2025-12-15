@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Status Report')

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
                        Chapter Status Report
                    </h3>
                    @include('layouts.dropdown_menus.menu_reports_chap')
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
                <th>Status</th>
                <th>Status Notes</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr>
                            <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="fas fa-eye"></i></a></td>
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
                            <td @if ( $list->status_id == \App\Enums\OperatingStatusEnum::ONHOLDDNR || $list->status_id == \App\Enums\OperatingStatusEnum::PROBATIONDNR) style="background-color: #dc3545; color: #ffffff;"
                                @elseif ( $list->status_id == \App\Enums\OperatingStatusEnum::PROBATION) style="background-color: #ffc107;"
                                @endif>
                            {{ $list->status->chapter_status }}</td>
                            <td>{{ $list->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
                 <!-- /.card-body -->
            <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                    <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                </div>
            </div>
			   {{-- <div class="col-sm-12">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="showProbation" id="showProbation" class="custom-control-input" {{$checkBox4Status}} onchange="showProbation()" />
                    <label class="custom-control-label" for="showProbation">Only show chapters 'Not Ok'</label>
                </div>
            </div> --}}
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

            </div>
           </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- Main content -->

    <!-- /.content -->

@endsection

