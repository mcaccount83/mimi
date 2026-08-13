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
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Coordinator Subscription List
                        </h3>
                        @include('layouts.dropdown_menus.menu_listadmin')
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
                            <td @if ( $list->on_leave == 1 ) class="bg-warning" @endif>ON LEAVE</td>
                        @else
                            <td>{{ $list->displayPosition->long_title }}</td>
                        @endif
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                $NotificationFrequencies = $list->user?->categorySubscriptions?->pluck('notification_frequency', 'category_id')->toArray() ?? [];
                                $isSubscribed = in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $Subscriptions);
                                $frequency = $NotificationFrequencies[\App\Enums\ForumCategoryEnum::PUBLICLIST] ?? null;
                            @endphp
                            {{ $isSubscribed ? 'YES' : 'NO' }}
                            @if ($isSubscribed)
                                | {{ $frequency === 'daily_digest' ? 'Daily Digest' : 'Individual Emails' }}
                            @endif
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                $NotificationFrequencies = $list->user?->categorySubscriptions?->pluck('notification_frequency', 'category_id')->toArray() ?? [];
                                $isSubscribed = in_array(\App\Enums\ForumCategoryEnum::COORDLIST, $Subscriptions);
                                $frequency = $NotificationFrequencies[\App\Enums\ForumCategoryEnum::COORDLIST] ?? null;
                            @endphp
                            {{ $isSubscribed ? 'YES' : 'NO' }}
                            @if ($isSubscribed)
                                | {{ $frequency === 'daily_digest' ? 'Daily Digest' : 'Individual Emails' }}
                            @endif
                        </td>
                        <td>
                            @php
                                $Subscriptions = $list->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                $NotificationFrequencies = $list->user?->categorySubscriptions?->pluck('notification_frequency', 'category_id')->toArray() ?? [];
                                $isSubscribed = in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $Subscriptions);
                                $frequency = $NotificationFrequencies[\App\Enums\ForumCategoryEnum::BOARDLIST] ?? null;
                            @endphp
                            {{ $isSubscribed ? 'YES' : 'NO' }}
                            @if ($isSubscribed)
                                | {{ $frequency === 'daily_digest' ? 'Daily Digest' : 'Individual Emails' }}
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
                        <input type="checkbox" name="showDirect" id="showDirect" class="form-check-input" {{$checkBox1Status ? 'checked' : '' }} onchange="showDirect()" />
                        <label class="form-check-label" for="showDirect">Only show my Direct Reports</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{$checkBox3Status ? 'checked' : '' }} onchange="showConfReg()" />
                                @if ($assistConferenceCoordinatorCondition)
                                    <label class="form-check-label" for="showConfReg">Show All Coordinators in Conference</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Coordinators in Region</label>
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
                @if ($checkBox51Status)
                {{-- Bulk Action Buttons --}}
                    <button type="button" class="btn btn-success bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.coordinatorpublidannouncement.bulk-subscribe') }}"
                        data-label="Add all active coordinators to Public Announcements">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add All to Public Announcements
                    </button>
                    <button type="button" class="btn btn-danger bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.coordinatorpublidannouncement.bulk-unsubscribe') }}"
                        data-label="Remove all coordinators from Public Announcements">
                        <i class="bi bi-dash-circle-fill me-1"></i> Remove All from Public Announcements
                    </button>
                <br>
                    <button type="button" class="btn btn-success bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.coordinatorlist.bulk-subscribe') }}"
                        data-label="Add all active coordinators to CoordinatorList">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add All to CoordinatorList
                    </button>
                    <button type="button" class="btn btn-danger bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.coordinatorlist.bulk-unsubscribe') }}"
                        data-label="Remove all coordinators from CoordinatorList">
                        <i class="bi bi-dash-circle-fill me-1"></i> Remove All from CoordinatorList
                    </button>
                <br>
                    <button type="button" class="btn btn-success bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.coordinatorboardlist.bulk-subscribe') }}"
                        data-label="Add all active coordinators to BoardList">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add All to BoardList
                    </button>
                    <button type="button" class="btn btn-danger bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.coordinatorboardlist.bulk-unsubscribe') }}"
                        data-label="Remove all coordinators from BoardList">
                        <i class="bi bi-dash-circle-fill me-1"></i> Remove All from BoardList
                    </button>

                    {{-- Hidden form used to submit the confirmed action --}}
                    <form id="bulk-action-form" method="POST" style="display:none;">
                        @csrf
                    </form>
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
