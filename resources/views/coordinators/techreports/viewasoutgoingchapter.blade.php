@extends('layouts.mimi_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'Admin Acitve Board Pages')

@section('content')
     <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <div class="card-header d-flex align-items-center">

                            <div class="dropdown">
        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            View Board Pages
        </h3>
        @include('layouts.dropdown_menus.menu_reports_tech')
    </div>

    @php
    $viewAsLabel = match(true) {
        request()->routeIs('techreports.viewaschapter.active')    => 'Active',
        request()->routeIs('techreports.viewaschapter.disbanded') => 'Disbanded',
        request()->routeIs('techreports.viewaschapter.pending')   => 'Pending',
        // request()->routeIs('techreports.viewaschapter.outgoing')  => 'Outgoing',
        default => 'View As',
    };
@endphp
<div class="dropdown ms-3">
    <button type="button" id="statusDropdown" class="btn btn-sm btn-outline-secondary dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
        {{ $viewAsLabel }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
        <li><a class="dropdown-item" href="{{ route('techreports.viewaschapter.active') }}">Active</a></li>
        <li><a class="dropdown-item" href="{{ route('techreports.viewaschapter.disbanded') }}">Disbanded</a></li>
        <li><a class="dropdown-item" href="{{ route('techreports.viewaschapter.pending') }}">Pending</a></li>
        {{-- <li><a class="dropdown-item" href="{{ route('techreports.viewaschapter.outgoing') }}">Outgoing</a></li> --}}
    </ul>
</div>
</div>
</div>
                     <!-- /.card-header -->
                    <div class="card-body">
                        <table id="chapterlist" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Conf/Reg</th>
                                    <th>State</th>
                                    <th>Chapter Name</th>
                                    <th>View</th>
                                    <th>Board/User Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chapters as $chapter)
                                @php
                                    $bd = $chapterBdData[$chapter->id] ?? [];
                                    $bdTypeId    = $bd['bdTypeId'] ?? null;
                                    $bdPositionId = $bd['bdPositionId'] ?? null;
                                    $borDetails  = $bd['bdDetails'] ?? null;
                                @endphp
                                    <tr id="chapter-{{ $chapter->id }}">
                                        <td>
                                            @if ($chapter->state->conference_id > 0)
                                                {{ $chapter->state->conference->short_name }} / {{ $chapter->state->region->short_name }}
                                            @else
                                                {{ $chapter->state->conference->short_name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($chapter->state_id < 52)
                                                {{$chapter->state->state_short_name}}
                                            @else
                                                {{$chapter->state->country?->short_name}}
                                            @endif
                                        </td>
                                        <td>{{ $chapter->name }}</td>

                                            @if ($userTypeId == \App\Enums\UserTypeEnum::COORD && isset($bdTypeId) && $bdTypeId !== null)
                                            @if ($bdTypeId == \App\Enums\UserTypeEnum::DISBANDED)
                                                <td>
                                                    <a href="{{ route('board-new.editdisbandchecklist', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Disband Checklist</a>
                                                </td>
                                                <td>
                                                    DISBANDED
                                                </td>
                                            @elseif ($bdTypeId == \App\Enums\UserTypeEnum::BOARD)
                                                <td>
                                                    <a href="{{ route('board-new.chapterprofile', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Chapter Profile</a>
                                                </td>
                                                <td>
                                                    ACTIVE
                                                </td>
                                            @elseif ($bdTypeId == \App\Enums\UserTypeEnum::OUTGOING)
                                                <td>
                                                    <a href="{{ route('board-new.editfinancialreport', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Financial Report</a>
                                                </td>
                                                <td>
                                                    OUTGOING
                                                </td>
                                            @elseif ($bdTypeId == \App\Enums\UserTypeEnum::PENDING)
                                                <td>
                                                    <a href="{{ route('board-new.newchapterstatus', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Chapter Status</a>
                                                </td>
                                                <td>
                                                    PENDING
                                                </td>
                                            @endif
                                        @endif
                                    </tr>
                                     @php
                                        // Unset so these don't leak into layout/sidebar
                                        $bdTypeId = null;
                                        $bdPositionId = null;
                                        $borDetails = null;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                     </div>
              <!-- /.card-body -->

              <div class="card-body">
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

