<!-- resources/views/payment-logs/show.blade.php -->
@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Payment Log Details')

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
                                Payment Log Details
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
    <!-- /.card-header -->
    <div class="card-body">


    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200"> --}}
                    <div class="mb-4">
                        <a href="{{ route('adminreports.intpaymentlist') }}" class="text-blue-600 hover:underline">
                            &larr; Back to Payment Logs
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Transaction Information</h3>
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</td>
                                            <td class="px-6 py-3">{{ $log->transaction_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Customer ID</td>
                                            <td class="px-6 py-3">{{ $log->customer_id ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</td>
                                            <td class="px-6 py-3">${{ number_format($log->amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Status</td>
                                            <td class="px-6 py-3">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $log->status == 'success' ? 'bg-green-100 text-green-800' :
                                                    ($log->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Response Code</td>
                                            <td class="px-6 py-3">{{ $log->response_code ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Response Message</td>
                                            <td class="px-6 py-3">{{ $log->response_message ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">Date</td>
                                            <td class="px-6 py-3">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">Request & Response Data</h3>
                            <div class="border rounded-lg p-4">
                                <div class="mb-6">
                                    <h4 class="font-medium mb-2">Request Data</h4>
                                    <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-60">{{ json_encode($log->request_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                                <div>
                                    <h4 class="font-medium mb-2">Response Data</h4>
                                    <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-60">{{ json_encode($log->response_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- </div>
        </div>
    </div> --}}

</div>
</div>
<!-- /.box -->
</div>
</div>
</section>
<!-- Main content -->

<!-- /.content -->
@endsection
