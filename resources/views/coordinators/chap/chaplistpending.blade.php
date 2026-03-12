@extends('layouts.mimi_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Pending Chapter List')

@section('content')
     <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown d-flex align-items-center">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Pending Chapter List
                        </h3>
                        <span class="ms-2">New Chapter Applications Waiting for Review</span>
                        @include('layouts.dropdown_menus.menu_chapters_new')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Pending<br>Details</th>
                    <th>Email</th>
                    <th>Application Date</th>
                    <th>Conf</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>President</th>
                    <th>Email</th>
                    <th>Phone</th>
                    @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)
                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/application/chapterpendingedit/{$list->id}") }}"><i class="bi bi-house-add-fill"></i></a></td>
                            <td class="text-center align-middle">
                                <a onclick="showChapterSetupEmailModal({{ $list->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')"><i class="bi bi-envelope text-primary"></i></a>
                           </td>
                            <td><span class="date-mask">{{ $list->created_at }}</span></td>
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
                            <td>{{ $list->pendingPresident->first_name }} {{ $list->pendingPresident->last_name }}</td>
                            <td class="email-column">
                                <a href="mailto:{{ $list->pendingPresident->email }}">{{ $list->pendingPresident->email }}</a>
                            </td>
                            <td><span class="phone-mask">{{ $list->pendingPresident->phone }}</span></td>
                            @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
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
                 @if ($ITCondition)
                <div class="col-sm-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="showIntl" id="showIntl" class="form-check-input" {{ $checkBox51Status ? 'checked' : '' }} onchange="showIntl()" />
                            <label class="form-check-label" for="showIntl">Show All International Chapters</label>
                        </div>
                    </div>
                @endif
               </div>
            <!-- /.card-body for checkboxes -->

                <div class="card-body text-center mt-3">
                @if ($regionalCoordinatorCondition)
                If your new chapter is not listed above, you can manually add them.<br>
                    @if ($checkBox51Status)
                        <a class="btn btn-primary bg-gradient mb-2" href="{{ route('chapters.addnewint') }}"><i class="bi bi-plus me-2"></i>Manually Add New Chapter (Any Confernces)</a>
                    @else
                        <a class="btn btn-primary bg-gradient mb-2" href="{{ route('chapters.addnew') }}"><i class="bi bi-plus me-2"></i>Manually Add New Chapter</a>
                    @endif
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
