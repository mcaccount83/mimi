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
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Mail Log Details
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
    <!-- /.card-header -->
    <div class="card-body">
            <div class="mb-4">
                    <a href="{{ route('adminreports.maillog') }}" class="text-blue-600 hover:underline">
                            &larr; Back to Mail Logs
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold">{{ $log->subject}}</h3>
                    <h4 class="text-md font-semibold mb-4">{{ $log->date }}
                        <br>From: {{ $log->from }}
                        <br>To: {{ $log->to }}
                        @if($log->cc)
                            <br>cc: {{ $log->cc }}
                        @endif
                        @if($log->bcc)
                            <br>bcc: {{ $log->bcc }}
                        @endif</h4>
                    <div class="border rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-3">
                                        <div id='emailcontent'>
                                            {!! $log->body !!}
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

                <div>

                </div>
            </div>
        </div>

 <div class="card-body text-center mt-3">
            </div>
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

@section('customscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailcontent = document.getElementById('emailcontent');

    // Load email content by ID
    function load(id) {
        fetch('{{ url(config('sentemails.routepath').'/body') }}/' + id)  // Changed /email to /body
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.text();
        })
        .then(function(string) {
            emailcontent.innerHTML = string;
        })
        .catch(function(error) {
            console.error('Fetch error: ', error);
            emailcontent.innerHTML = '<p class="text-red-500">Failed to load email content. Please try again.</p>';
        });
    }

    // Call the load function with the email ID
    load({{ $log->id }});
});
</script>
@endsection
