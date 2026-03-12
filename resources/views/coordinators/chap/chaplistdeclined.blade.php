@extends('layouts.mimi_theme')

@section('page_title', 'Chapters')
@section('breadcrumb', 'Not Approved Chapter List')

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
                            Not Approved Chapter List
                        </h3>
                        <span class="ms-2">New Chapter Applications Declined</span>
                        @include('layouts.dropdown_menus.menu_chapters_new')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                <thead>
                  <tr>
                    <th>Declined<br>Details</th>
                    <th>Application Date</th>
                    <th>Conf</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>President</th>
                    <th>Reason Not Approved</th>
                    @if ($ITCondition && ($checkBox51Status ?? '') == 'checked')
                        <th>Delete</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                    @foreach($chapterList as $list)


                        <tr id="chapter-{{ $list->id }}">
                            <td class="text-center align-middle"><a href="{{ url("/application/chapterpendingedit/{$list->id}") }}"><i class="bi bi-house-x-fill"></i></a></td>
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
                            <td>{{ $list->disband_reason }}</td>
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
            @if ($ITCondition || $einCondition)
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
