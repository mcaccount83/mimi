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
                            <div class="card-header d-flex align-items-center">
                            <div class="dropdown d-flex align-items-center">
                        <h3 class="card-title dropdown-toggle mb-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            View As Coordinator
                        </h3>
                            @include('layouts.dropdown_menus.menu_reports_tech')
                        </div>

                        {{-- @php
                            $viewAsLabel = match(true) {
                                request()->routeIs('techreports.viewascoordinator.active')    => 'Active',
                                request()->routeIs('techreports.viewascoordinator.retired') => 'Retired',
                                request()->routeIs('techreports.viewascoordinator.pending')   => 'Pending',
                                request()->routeIs('techreports.viewascoordinator.rejected')   => 'Rejected',
                                default => 'View As',
                            };
                        @endphp
                        <div class="dropdown ms-3">
                            <button type="button" id="statusDropdown" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $viewAsLabel }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                <li><a class="dropdown-item" href="{{ route('techreports.viewascoordinator.active') }}">Active</a></li>
                                <li><a class="dropdown-item" href="{{ route('techreports.viewascoordinator.retired') }}">Retired</a></li>
                                <li><a class="dropdown-item" href="{{ route('techreports.viewascoordinator.pending') }}">Pending</a></li>
                                <li><a class="dropdown-item" href="{{ route('techreports.viewascoordinator.rejected') }}">Rejected</a></li>
                        </ul>
                </div> --}}
        </div>
                     <!-- /.card-header -->
                    <div class="card-body">
                        <table id="chapterlist" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Conf/Reg</th>
                                    <th>State</th>
                                    <th>Coordinator Name</th>
                                    <th>View As...</th>
                                    {{-- <th></th> --}}
                                    {{-- <th>Board/User Type</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coordinatorList as $list)
                                @php
                                    $cd = $coordinatorData[$list->id] ?? [];
                                    $cdTypeId    = $cd['cordTypeId'] ?? null;
                                    $cdDisplayPositionId = $cd['cordDisplayPositionId'] ?? null;
                                    $cdDetails  = $cd['cdDetails'] ?? null;
                                @endphp
                                    <tr id="coordinator-{{ $list->id }}">
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
                                        <td>{{$cdDetails->first_name}} {{$cdDetails->last_name}}, {{$cdDetails->displayPosition->short_title}}</td>
                                        {{-- <td>
                                            @if ($cdDetails->first_name != null)
                                                {{$cdDetails->first_name}} {{$cdDetails->last_name}}, {{$cdDetails->displayPosition->short_title}}
                                            @endif
                                        </td> --}}
                                        <td>
                                            <button type="button" class="btn btn-primary bg-gradient btn-sm ms-2" onclick="window.location.href='{{ route('impersonate.start', ['userId' => $cdDetails->user_id]) }}'">
                                                View as {{ $cdDetails->first_name }} {{ $cdDetails->last_name }}
                                            </button>
                                            {{-- <a href="{{ route('impersonate.start', ['userId' => $cdDetails->user_id, 'returnTo' => request()->route()->getName()]) }}"
                                                class="btn btn-primary bg-gradient btn-sm ms-2">View as {{ $cdDetails->first_name }} {{ $cdDetails->last_name }}
                                            </a> --}}
                                        </td>
                                        {{-- <td>
                                            {{ \App\Enums\UserTypeEnum::label($cdTypeId) }}
                                        </td> --}}
                                    </tr>
                                     @php
                                        // Unset so these don't leak into layout/sidebar
                                        $cdTypeId = null;
                                        $cdDisplayPositionId = null;
                                        $cdDetails = null;
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

