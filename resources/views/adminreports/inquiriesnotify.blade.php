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
            <table id="chapterlist" class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Conf</th>
                        <th>Region</th>
                        <th>States</th>
                        <th>Inquiries Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($regList as $list)
                    <tr>
                        <td>
                            {{ $list->conference->short_name }}
                        </td>
                        <td>
                            {{ $list->long_name }}
                        </td>
                        <td>
                            @if ($list->id == 0)
                                N/A
                            @else
                                {{ $list->states->pluck('state_short_name')->implode(', ') }}
                            @endif
                        </td>
                        <td class="email-column">
                            @if ($list->id == 0)
                                N/A
                            @else
                            <a href="mailto:{{ $list->inquiries_email }}">{{ $list->inquiries_email }}</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

             <div class="card-body text-center">
            </div>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
@endsection
