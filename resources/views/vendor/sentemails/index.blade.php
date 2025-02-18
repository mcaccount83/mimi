@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Sent Mail Log')

<script src="https://cdn.tailwindcss.com"></script>


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
                                Sent Mail Log
                            </h3>
                            @include('layouts.dropdown_menus.menu_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
            <div class="card-body">
            <div class="flex flex-1 overflow-hidden">
                <main class="flex-1 flex bg-gray-200">
                    <div class="relative flex flex-col w-full max-w-xs flex-grow border-l border-r bg-gray-200">
                        <div class="flex-1 overflow-y-auto">
                            @foreach($emails as $email)
                                @php
                                $parts = explode('<', $email->from);
                                $from = isset($parts[1]) ? $parts[0] : $parts[0];
                                @endphp
                                <a href="#" class="block px-6 pt-2 pb-2 bg-white emailitem border-b-2" data-id="{{ $email->id }}">
                                    <div class="flex justify-between">
                                        <span class="text-sm font-semibold text-gray-900">{{ $from }}</span>
                                        <span class="text-xs text-gray-500">{{ $email->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-900">{{ $email->subject }}</p>
                                    @if(config('sentemails.storeAttachments'))
                                    <p class="text-sm text-gray-900">{{ __('Attachments') }}: {{ $email->attachments->count() }}</p>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col w-0">
                        <div id='emailcontent'></div>
                    </div>
                </main>
            </div>

            @if ($emails->count() == 0)
                <div class="m-10 x-5">
                    <h2 class="text-2xl">{{ config('sentemails.noEmailsMessage') }}</h2>
                </div>
            @endif
            {{ $emails->links('sentemails::pagination') }}
            </div>
        </div>
        </div>
    <!-- /.box -->
    </div>
</div>
</section>
<!-- Main content -->

<!-- /.content -->
@endsection
@section('customscript')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownItems = document.querySelectorAll(".dropdown-item");
    const currentPath = window.location.pathname;

    dropdownItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;

        if (itemPath === currentPath) {
            item.classList.add("active");
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const emailcontent = document.getElementById('emailcontent');
    const elements = document.querySelectorAll('.emailitem');

    // Check if there are any emails
    if (elements.length > 0) {
        // Load specific email when clicked
        elements.forEach(function(el){
            el.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent default anchor behavior
                load(el.dataset.id);
            });
        });

        // Load the first email by default if present
        load(elements[0].dataset.id);
    }

    // Load email content by ID
    function load(id) {
        fetch('{{ url(config('sentemails.routepath').'/email') }}/' + id)
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
});
</script>

@endsection


