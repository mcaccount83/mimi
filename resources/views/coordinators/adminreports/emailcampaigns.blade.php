@extends('layouts.mimi_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Email Campaigns')

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
                            Email Campaigns
                        </h3>
                        <span class="ms-3">Suggested Month to Send</span>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Month</th>
                            <th>Campaign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthNames as $m => $name)
                            @if($m == 7)
                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        <i class="bi bi-envelope text-primary"></i>
                                    </td>
                                    <td>{{ $name }}</td>
                                    <td class="fst-italic text-muted"><small>Auto-sent on board activation</small></td>
                                </tr>
                            @elseif(isset($campaigns[$m]))
                                @foreach($campaigns[$m] as $campaign)
                                    @php $fn = $campaign['fn'] ?? 'confirmSendCampaign'; @endphp
                                    <tr>
                                        <td class="text-center align-middle">
                                            <a onclick="{{ $fn }}('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')" style="cursor: pointer;">
                                                <i class="bi bi-envelope text-primary"></i>
                                            </a>
                                        </td>
                                        <td>{{ $name }}</td>
                                        <td>{{ $campaign['label'] }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td></td>
                                    <td>{{ $name }}</td>
                                    <td class="text-muted fst-italic"><small>No campaign</small></td>
                                </tr>
                            @endif
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
