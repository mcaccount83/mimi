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
                    <th>States</th>
                    <th></th>
                    <th></th>
                  <th>Email</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($regList as $list)
                    <tr>
                        <td>
                            {{ $list->conference->conference_name }}
                        </td>
                        <td>
                            {{ $list->long_name }}
                        </td>
                        <td>

                    {{ $confStates[$list->id] ?? 'N/A' }}
                        </td>
                        <td></td>
                        <td></td>
                        <td class="email-column">
                            {{-- <a href="mailto:{{ $list->email }}"></a> --}}
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
