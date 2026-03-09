@extends('layouts.mimi_theme')

@section('page_title', 'List Subscriptions')
@section('breadcrumb', 'Chapter Subscription List')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown d-flex align-items-center">
                        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Chapter Subscription List
                        </h3>
                        @include('layouts.dropdown_menus.menu_listadmin')
                        <span class="ms-2 text-muted " style="font-size: 0.75rem; align-self: flex-end; line-height: 1.8;">PA=Public Announcements | BL=BoardList</span>
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
                    <th>Chapter</th>
                    <th>President<br>
                        PA|BL</th>
                    <th>AVP<br>
                        PA|BL</th>
                    <th>MVP<br>
                        PA|BL</th>
                    <th>Secretary<br>
                        PA|BL</th>
                    <th>Treasurer<br>
                        PA|BL</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                  <tr>
                    <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="bi bi-eye"></i></a></td>
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
                        <td>
                            @php
                                $presSubscriptions = $list->president?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            @endphp
                            {{ in_array(1, $presSubscriptions) ? 'YES' : 'NO' }} |
                            {{ in_array(3, $presSubscriptions) ? 'YES' : 'NO' }}
                        </td>
                        <td>
                            @if($list->avp)
                                @php
                                    $avpSubscriptions = $list->avp?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $avpSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $avpSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->mvp)
                                @php
                                    $mvpSubscriptions = $list->mvp?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $mvpSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $mvpSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->secretary)
                                @php
                                    $secSubscriptions = $list->secretary?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $secSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $secSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->treasurer)
                                @php
                                    $tresSubscriptions = $list->treasurer?->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                @endphp
                                {{ in_array(1, $tresSubscriptions) ? 'YES' : 'NO' }} |
                                {{ in_array(3, $tresSubscriptions) ? 'YES' : 'NO' }}
                            @else
                                &nbsp;
                            @endif
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
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showPrimary()" />
                        <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
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
                @if ($ITCondition)
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

