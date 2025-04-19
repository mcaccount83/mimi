<!-- resources/views/payment-logs/index.blade.php -->
@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Payment Logs')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid table-container">
            <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Payment Logs
                            </h3>
                            @include('layouts.dropdown_menus.menu_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
    <!-- /.card-header -->
    <div class="card-body">


                    <!-- Filters -->
                    <form method="GET" action="{{ route('payment-logs.index') }}" class="mb-6">
                        <div class="flex gap-4">
                            <div>
                                <label for="status" >Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All</option>
                                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                </select>

                                <label for="date" >Date</label>
                                <input type="date" name="date" id="date" value="{{ request('date') }}" >

                                <button type="submit" class="btn bg-gradient-primary btn-sm ml-2">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Logs Table -->
                    <table id="chapterlist" class="table table-sm table-hover" >
                        <thead>
                          <tr>
                            <th>Details</th>
                            <th>Conf/Reg</th>
                            <th>Chapter</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Response</th>
                            <th>Date</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentLogs as $log)
                            <tr>
                                <td  class="text-center align-middle"><a href="{{ route('payment-logs.show', $log->id) }}"><i class="fas fa-eye"></i></a></td>
                                <td>
                                    @if ($log->board->chapters->region->short_name != "None")
                                        {{ $log->board->chapters->conference->short_name ?? 'N/A'}} / {{ $log->board->chapters->region->short_name ?? 'N/A'}}
                                    @else
                                        {{ $log->board->chapters->conference->short_name ?? 'N/A'}}
                                    @endif
                                </td>
                                <td>{{ $log->board->chapters->name ?? 'N/A' }}, {{ $log->board->chapters->state->state_short_name ?? 'N/A' }}</td>
                                     <td>${{ number_format($log->amount, 2) }}</td>
                                    <td>
                                        @if($log->status == 'success')
                                            <span class="badge bg-success text-white">
                                        @elseif($log->status == 'failed')
                                            <span class="badge bg-danger text-white">
                                        @else
                                            <span class="badge bg-secondary text-white">
                                        @endif
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>
                                    <td style="white-space: normal; word-wrap: break-word; max-width: 200px;">
                                        {{ $log->response_message ?? 'N/A' }}
                                    </td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


</div>
</div>
<!-- /.box -->
</div>
</div>
</section>
<!-- Main content -->

<!-- /.content -->
@endsection
