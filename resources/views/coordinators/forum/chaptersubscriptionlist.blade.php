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
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown d-flex align-items-center">
                        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Chapter Subscription List
                        </h3>
                        <span class="ms-3">PA=Public Announcements | BL=BoardList</span>
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
          			<th>State</th>
                    <th>Chapter</th>
                    <th>President</th>
                    <th>AVP</th>
                    <th>MVP</th>
                    <th>Secretary</th>
                    <th>Treasurer</th>
                </tr>
                </thead>
                <tbody>
                    @php
                        $renderSubscriptionStatus = function ($user) {
                            $subs = $user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                            $freqs = $user?->categorySubscriptions?->pluck('notification_frequency', 'category_id')->toArray() ?? [];
                            $paSubscribed = in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $subs);
                            $paFreq = $freqs[\App\Enums\ForumCategoryEnum::PUBLICLIST] ?? null;
                            $blSubscribed = in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $subs);
                            $blFreq = $freqs[\App\Enums\ForumCategoryEnum::BOARDLIST] ?? null;

                            return [
                                'pa' => $paSubscribed ? ('YES' . ($paFreq === 'daily_digest' ? ' | Daily Digest' : ' | Individual Emails')) : 'NO',
                                'bl' => $blSubscribed ? ('YES' . ($blFreq === 'daily_digest' ? ' | Daily Digest' : ' | Individual Emails')) : 'NO',
                            ];
                        };
                    @endphp
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
                            @php $s = $renderSubscriptionStatus($list->president?->user); @endphp
                            <div>PA: {{ $s['pa'] }}</div>
                            <div>BL: {{ $s['bl'] }}</div>
                        </td>
                        <td>
                            @if($list->avp)
                                @php $s = $renderSubscriptionStatus($list->avp->user); @endphp
                                <div>PA: {{ $s['pa'] }}</div>
                                <div>BL: {{ $s['bl'] }}</div>
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->mvp)
                                @php $s = $renderSubscriptionStatus($list->mvp->user); @endphp
                                <div>PA: {{ $s['pa'] }}</div>
                                <div>BL: {{ $s['bl'] }}</div>
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->secretary)
                                @php $s = $renderSubscriptionStatus($list->secretary->user); @endphp
                                <div>PA: {{ $s['pa'] }}</div>
                                <div>BL: {{ $s['bl'] }}</div>
                            @else
                                &nbsp;
                            @endif
                        </td>
                        <td>
                            @if($list->treasurer)
                                @php $s = $renderSubscriptionStatus($list->treasurer->user); @endphp
                                <div>PA: {{ $s['pa'] }}</div>
                                <div>BL: {{ $s['bl'] }}</div>
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
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Conference</label>
                                @else
                                    <label class="form-check-label" for="showConfReg">Show All Chapters in Region</label>
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
                @if ($checkBox51Status)
                {{-- Bulk Action Buttons --}}
                    <button type="button" class="btn btn-success bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.boardpublcannouncements.bulk-subscribe') }}"
                        data-label="Add all active board members to Public Announcements">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add All to Public Announcements
                    </button>
                    <button type="button" class="btn btn-danger bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.boardpublcannouncements.bulk-unsubscribe') }}"
                        data-label="Remove all board members to Public Announcements">
                        <i class="bi bi-dash-circle-fill me-1"></i> Remove All from Public Announcements
                    </button>
                <br>
                    <button type="button" class="btn btn-success bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.boardboardlist.bulk-subscribe') }}"
                        data-label="Add all active board members to BoardList">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add All to BoardList
                    </button>
                    <button type="button" class="btn btn-danger bg-gradient mb-2 bulk-action-btn"
                        data-action="{{ route('forum.boardboardlist.bulk-unsubscribe') }}"
                        data-label="Remove all board members from BoardList">
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

