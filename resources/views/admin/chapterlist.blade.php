@extends('layouts.coordinator_theme')

@section('page_title', 'Admin')
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
                                    Admin Acitve Board Pages
                                </h3>
                                @include('layouts.dropdown_menus.menu_admin')
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
                                        <td>{{ $chapter->state->state_short_name }}</td>
                                        <td>{{ $chapter->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.board.editpresident', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">President</a>
                                                <a href="{{ route('admin.board.editmember', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Member</a>
                                                <a href="{{ route('admin.board.editboardreport', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Board Report</a>
                                                <a href="{{ route('admin.board.editfinancialreport', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Financial Report</a>
                                                <a href="{{ route('admin.board.editreregpayment', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Re-reg Payment</a>
                                                <a href="{{ route('admin.board.editprobation', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Probation</a>
                                                <a href="{{ route('admin.board.viewresources', ['chapter_id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Resources</a>
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
