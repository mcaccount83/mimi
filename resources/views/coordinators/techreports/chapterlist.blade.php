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
                                <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acitve Board Pages
                                </h3>
                                <span class="ms-3">View Board Pages as President</span>
                                @include('layouts.dropdown_menus.menu_reports_tech')
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
                                    <th>View Board Pages</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chapters as $chapter)
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
                                        <td>
                                            <div class="btn-group">
                                                {{-- <a href="{{ route('board.editpresident', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">President Profile</a> --}}
                                                <a href="{{ route('board.editprofile', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Board Profile</a>
                                                <a href="{{ route('board.editboardreport', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Board Report</a>
                                                <a href="{{ route('board.editfinancialreport', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Financial Report</a>
                                                <a href="{{ route('board.editreregpayment', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Re-reg Payment</a>
                                                <a href="{{ route('board.editprobation', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Probation</a>
                                                <a href="{{ route('board.viewresources', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-primary bg-gradient btn-sm me-2">Resources</a>
                                            </div>
                                        </td>
                                    </tr>
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

