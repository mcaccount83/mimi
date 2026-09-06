@extends('layouts.mimi_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Active Chapter List')

@section('content')
     <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Active Chapter List
                        </h3>
                        @include('layouts.dropdown_menus.menu_chapters')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <!--  Email Campaigns  -->
                @if ($coordinatorCondition && $conferenceCoordinatorCondition)
                    <div class="mb-2">
                        @include('coordinators.partials.emailcampaigns-button')
                    </div>
                @endif

                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Chapter<br>Details</th>
                    <th>Email</th>
                    <th>Conf/Reg</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>President</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Primary Coordinator</th>
                    @if ($ITCondition && ($checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/chapter/details/{$list->id}") }}"><i class="bi bi-house-fill"></i></a></td>
                            <td class="text-center align-middle">
                                <a onclick="showChapterEmailModal('{{ $list->name }}', {{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="bi bi-envelope text-primary"></i></a>
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
                            <td>{{ $list->president->first_name }} {{ $list->president->last_name }}</td>
                            <td class="email-column">
                                @mailto($list->president->email)
                            </td>
                            <td>@tel($list->president->phone)</td>
                            <td>{{ $list->primaryCoordinator?->first_name }} {{ $list->primaryCoordinator?->last_name }}</td>
                           @if ($ITCondition && ($checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] ?? '') == 'checked')
                        <td class="text-center align-middle"><i class="bi bi-ban"
                            onclick="showDeleteChapterModal({{ $list->id }}, '{{ $list->name }}', '{{ $list->activeStatus->active_status }}')"
                            style="cursor: pointer; color: #dc3545;"></i>
                        </td>
                    @endif
                        </tr>
                    @endforeach
                </tbody>
                 </table>
            </div>
            <!-- /.card-body -->

            <div class="card-body">
                <div class="col-sm-12">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="form-check-input" {{ $checkStatuses[\App\Enums\CheckboxFilterEnum::PC_DIRECT] }} onchange="showPrimary()" />
                        <label class="form-check-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                @if ($coordinatorCondition && $assistRegionalCoordinatorCondition)
                    <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showConfReg" id="showConfReg" class="form-check-input" {{ $checkStatuses[\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION] }} onchange="showConfReg()" />
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
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters (Export Available)</label>
                        </div>
                    </div>
                @endif
            </div>
            <!-- /.card-body for checkboxes -->

            <div class="card-body text-center mt-3">
                @if ($coordinatorCondition && $regionalCoordinatorCondition)
                     @if ($checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL])
                            <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('chapters.chaplistpending', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}'"><i class="bi bi-house-add-fill me-2"></i>New International Chapters Pending</button>
                        @else
                            <a class="btn btn-primary bg-gradient mb-2" href="{{ route('chapters.chaplistpending') }}"><i class="bi bi-house-add-fill me-2"></i>New Chapters Pending</a>
                        @endif
                        @if ($checkStatuses[\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION])
                            <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="startExport('chapter', 'Chapter List')"><i class="bi bi-download me-2"></i>Export Chapter List</button>
                        @elseif ($checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL])
                            <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="startExport('intchapter', 'International Chapter List')"><i class="bi bi-download me-2"></i>Export International Chapter List</button>
                        @endif
                @endif
                @if ($checkStatuses[\App\Enums\CheckboxFilterEnum::PC_DIRECT] && !$checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] && !$checkStatuses[\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION])
                    <a class="btn btn-primary bg-gradient mb-2" onclick="showChapterPrimaryEmailModal('{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="bi bi-envelope-fill me-2"></i>E-mail Primary Chapters</a>
                @endif
                @if (!$checkStatuses[\App\Enums\CheckboxFilterEnum::INTERNATIONAL] && !$checkStatuses[\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION] && !$checkStatuses[\App\Enums\CheckboxFilterEnum::PC_DIRECT])
                    <a class="btn btn-primary bg-gradient mb-2" onclick="showChapterAllEmailModal('{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="bi bi-envelope-fill me-2"></i>E-mail All Chapters</a>
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
