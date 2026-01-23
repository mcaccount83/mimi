@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Inquiries Notifications')

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
                                    Inquiries Notifications
                                </h3>
                                @include('layouts.dropdown_menus.menu_reports_admin')
                            </div>
                        </div>
                     <!-- /.card-header -->
        <div class="card-body">
            <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
                    <th>Conf</th>
                    <th>Region</th>
                    <th>State</th>
                    <th></th>
                    <th></th>
                  <th>Email</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            {{-- {{ $list->conference->short_name }} --}}
                        </td>
                        <td>
                            {{-- {{ $list->conference->short_name }} --}}
                        </td>
                        <td>
                           {{-- @if($list->state_id < 52)
                                {{$list->state->state_short_name}}
                            @else
                                {{$list->country->short_name}}
                            @endif --}}
                        </td>
                        <td></td>
                        <td></td>
                        <td class="email-column">
                            {{-- <a href="mailto:{{ $list->email }}"></a> --}}
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>
            <div class="card-body text-center">

             </div>
        </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->
@endsection
