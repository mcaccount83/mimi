@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Website/Social Media')
@section('breadcrumb', 'Social Media List')

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
                        Social Media List
                    </h3>
                    @include('layouts.dropdown_menus.menu_website')
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
                    <th>Facebook</th>
                    <th>Twitter</th>
                    <th>Instagram</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/websiteedit/{$list->id}") }}"><i class="fas fa-eye "></i></a></td>
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
						<td>{{ $list->social1 }}</td>
						<td>{{ $list->social2 }}</td>
						<td>{{ $list->social3 }}</td>
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
