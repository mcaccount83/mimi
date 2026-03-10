@extends('layouts.mimi_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'Sent Mail Log')

<script src="https://cdn.tailwindcss.com"></script>

@section('content')
@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Attachment Not Found',
            text: '{{ session('error') }}',
        });
    });
</script>
@endif
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid table-container">
            <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Sent Mail Log
                            </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="mb-3">
                    <span class="text font-medium text-gray-600">Viewing: </span>
                    @if ($checkBox81Status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">All International Emails</span>
                    @elseif ($checkBox5Status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">List Admin Emails</span>
                    @elseif ($checkBox57Status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800">International Inquiries Emails</span>
                    @elseif ($checkBox7Status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">My Inquiries Emails</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">My Personal Emails</span>
                    @endif
                </div>

                <div class="flex flex-shrink-0">
                    <form action="">
                        <div class="grid grid-cols-6 gap-4">
                            <div class="mb-5">
                                <label for="date" class="block text-sm font-medium leading-5 text-gray-700">Date </label>
                                <div class="rounded-md shadow-sm">
                                    <input type="date" id="date" name="date" value="{{ request('date') }}" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-light-blue-500 focus:border-light-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="from" class="block text-sm font-medium leading-5 text-gray-700">From </label>
                                <div class="rounded-md shadow-sm">
                                    <input type="text" id="from" name="from" value="{{ request('from') }}" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-light-blue-500 focus:border-light-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="to" class="block text-sm font-medium leading-5 text-gray-700">To </label>
                                <div class="rounded-md shadow-sm">
                                    <input type="text" id="to" name="to" value="{{ request('to') }}" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-light-blue-500 focus:border-light-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="cc" class="block text-sm font-medium leading-5 text-gray-700">CC </label>
                                <div class="rounded-md shadow-sm">
                                    <input type="text" id="cc" name="cc" value="{{ request('cc') }}"
                                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-light-blue-500 focus:border-light-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="subject" class="block text-sm font-medium leading-5 text-gray-700">Subject </label>
                                <div class="rounded-md shadow-sm">
                                    <input type="text" id="subject" name="subject" value="{{ request('subject') }}" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-light-blue-500 focus:border-light-blue-500 sm:text-sm">
                                </div>
                            </div>
                            <div class="">
                                <div class="mt-6">
                                    <button type="submit" class="inline-flex items-center py-1 px-2 border border-transparent text-xs leading-4 font-medium rounded text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">Search Emails</button>
                                    <a href="{{ url(config('sentemails.routepath')) }}" class="inline-flex items-center py-1 px-2 border border-transparent text-xs leading-4 font-medium rounded text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition ease-in-out duration-150">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="flex flex-1">
    <main class="flex-1 flex bg-gray-200">
        <div class="relative flex flex-col w-full max-w-xs flex-grow border-l border-r bg-gray-200">
            <div>  {{-- removed overflow-y-auto and flex-1 --}}
                @foreach($emails as $email)
                                    @php
                                    $parts = explode('<', $email->from);
                                    $from = isset($parts[1]) ? $parts[0] : $parts[0];
                                    @endphp
                                    <div class="block px-6 pt-2 pb-2 bg-white border-b-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-semibold text-gray-900">{{ $from }}</span>
                                            <span class="text-xs text-gray-500">{{ $email->created_at->diffForHumans() }}</span>
                                        </div>
                                        <a href="#" class="emailitem" data-id="{{ $email->id }}">
                                            <p class="text-sm text-gray-900">{{ $email->subject }}</p>
                                            </a>
                                        @if(config('sentemails.storeAttachments'))
                                        <p class="text-sm text-gray-500">{{ __('Attachments') }}: {{ $email->attachments->count() }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col w-0">
                                <div id='emailcontent' style="height: 100%;"></div>
                        </div>
                    </main>
                </div>

                @if ($emails->count() == 0)
                    <div class="m-10 x-5">
                        <h2 class="text-2xl">{{ config('sentemails.noEmailsMessage') }}</h2>
                    </div>
                @endif
                {{ $emails->links('sentemails::pagination') }}

                @if ($listAdminCondition || $ITCondition)
                        <div class="col-sm-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="showListAdmin" id="showListAdmin" class="form-check-input" {{ $checkBox5Status ? 'checked' : '' }} onchange="showListAdmin()" />
                                <label class="form-check-label" for="showListAdmin">Show ListAdmin Emails</label>
                            </div>
                        </div>
                    @endif
                    @if(($coordinatorCondition && $conferenceCoordinatorCondition) || $inquiriesCondition || $inquiriesInternationalCondition || $ITCondition)
                    <div class="col-sm-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="showInquiries" id="showInquiries" class="form-check-input" {{ $checkBox7Status ? 'checked' : '' }} onchange="showInquiries()" />
                                <label class="form-check-label" for="showInquiries">Show Inquiry Emails</label>
                            </div>
                        </div>
                    @endif
                    @if ($ITCondition)
                        <div class="col-sm-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="showIntlInquiries" id="showIntlInquiries" class="form-check-input" {{ $checkBox57Status ? 'checked' : '' }} onchange="showIntlInquiries()" />
                                <label class="form-check-label" for="showIntlInquiries">Show All Inquiry Emails</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="showAdminAll" id="showAdminAll" class="form-check-input" {{ $checkBox81Status ? 'checked' : '' }} onchange="showAdminAll()" />
                                <label class="form-check-label" for="showAdminAll">Show All Emails</label>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <!-- /.box -->
</div>
</section>
<!-- /.content -->
@endsection
@section('customscript')
<script>
const sentEmailsUrl = '{{ url(config("sentemails.routepath")."/email") }}';

document.addEventListener('DOMContentLoaded', function() {
    const emailcontent = document.getElementById('emailcontent');
    const elements = document.querySelectorAll('.emailitem');

    function load(id) {
        fetch(sentEmailsUrl + '/' + id)
        .then(response => response.text())
        .then(string => {
            emailcontent.innerHTML = string;

            const iframe = emailcontent.querySelector('iframe');
            if (iframe) {
                iframe.style.width = '100%';
                iframe.style.border = 'none';
                iframe.addEventListener('load', function() {
                    try {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        iframe.style.height = iframeDoc.body.scrollHeight + 'px';
                    } catch(e) {
                        iframe.style.height = '800px';
                    }
                });
            }
        })
        .catch(error => {
            emailcontent.innerHTML = '<p class="text-red-500">Failed to load email content.</p>';
        });
    }

    if (elements.length > 0) {
        elements.forEach(function(el){
            el.addEventListener('click', function(event) {
                event.preventDefault();
                load(el.dataset.id);
            });
        });
        load(elements[0].dataset.id);
    }
});
</script>
@endsection


