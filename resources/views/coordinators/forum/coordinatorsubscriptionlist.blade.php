@extends('layouts.mimi_theme')

@section('page_title', 'List Subscriptions')
@section('breadcrumb', 'Coordinator Subscription List')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Coordinator Subscription List
                        </h3>
                        @include('layouts.dropdown_menus.menu_reports_listadmin')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
        <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
      			    <tr>
                        <th>Details</th>
                    <th>Conf/Reg</th>
                    <th>Coordinator Name</th>
                    <th>Primary Position</th>
                    <th>Announcements</th>
                    <th>CoordinatorList</th>
                    <th>BoardList</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($coordinatorList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/coordinator/details/{$list->id}") }}"><i class="bi bi-eye"></i></a></td>

                        <td>
                            @if ($list->region?->short_name != "None" )
                                {{ $list->conference->short_name }} / {{ $list->region?->short_name }}
                            @else
                                {{ $list->conference->short_name }}
                            @endif
                        </td>
                        <td>{{ $list->first_name }} {{ $list->last_name }}</td>
                        @if ( $list->on_leave == 1 )
                            <td @if ( $list->on_leave == 1 ) style="background-color: #ffc107;" @endif>ON LEAVE</td>
                        @else
                            <td>{{ $list->displayPosition->long_title }}</td>
                        @endif
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(1, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(2, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(3, $Subscriptions) ? 'YES' : 'NO' }}
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->

        <div class="card-body">
            <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showDirect" id="showDirect" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showDirect()" />
                        <label class="form-check-label" for="showDirect">Only show my Direct Reports</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                                @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Coordinators in Conference (Export Available)</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Coordinators in Region (Export Available)</label>
                                @endif
                        </div>
                    </div>
                @endif
                @if ($ITCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{$checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Coordinators</label>
                        </div>
                    </div>
                @endif
 </div>
            <!-- /.card-body for checkboxes -->

            <div class="card-body text-center mt-3">
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
