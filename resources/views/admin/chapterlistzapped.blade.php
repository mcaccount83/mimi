@extends('layouts.coordinator_theme')

@section('page_title', 'Admin')
@section('breadcrumb', 'Admin Zapped Board Pages')

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
                                    Admin Zapped Board Pages
                                </h3>
                                <span class="ml-2">View Board Pages as President</span>
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
                                    <th>Disband Date</th>
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
                                                <a href="{{ route('board.editdisbandchecklist', ['id' => $chapter->id]) }}" target="_blank" class="btn btn-sm btn-primary mr-2">Disband Checklist & Financial Report</a>
                                            </div>
                                        </td>
                                        <td><span class="date-mask">{{ $chapter->zap_date }}</span></td>
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
