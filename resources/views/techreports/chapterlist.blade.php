@extends('layouts.coordinator_theme')

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
                            <div class="dropdown">
                                <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acitve Board Pages
                                </h3>
                                <span class="ml-2">View Board Pages as President</span>
                                @include('layouts.dropdown_menus.menu_reports_tech')
                            </div>
                        </div>
                     <!-- /.card-header -->
                    <div class="card-body">
                        <table id="chapterlist" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>State</th>
                                    <th>Chapter Name</th>
                                    <th>View Board Pages</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chapters as $chapter)
                                    <tr id="chapter-{{ $chapter->id }}">
                                        <td>
                                            @if($chapter->state_id < 52)
                                                {{$chapter->state->state_short_name}}
                                            @else
                                                {{$chapter->country->short_name}}
                                            @endif
                                        </td>
                                        <td>{{ $chapter->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                {{-- <a href="{{ route('board.editpresident', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">President Profile</a> --}}
                                                <a href="{{ route('board.editprofile', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Board Profile</a>
                                                <a href="{{ route('board.editboardreport', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Board Report</a>
                                                <a href="{{ route('board.editfinancialreport', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Financial Report</a>
                                                <a href="{{ route('board.editreregpayment', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Re-reg Payment</a>
                                                <a href="{{ route('board.editprobation', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Probation</a>
                                                <a href="{{ route('board.viewresources', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Resources</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});
</script>
@endsection
