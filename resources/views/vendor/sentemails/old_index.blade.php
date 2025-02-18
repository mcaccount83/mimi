<!DOCTYPE html>
<html lang="en">
<head>
<title>{{ __('Sent Emails') }}</title>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">

<header class="flex flex-shrink-0 bg-gray-800">
    <div class="w-full flex-shrink-0 px-4 py-3">
        <span class="ml-4 mr-2 text-lg font-medium text-white">{{ __('Sent Emails') }}</span>
    </div>
</header>

<div class="flex flex-1 overflow-hidden">

    <main class="flex-1 flex bg-gray-200">

        <div class="relative flex flex-col w-full max-w-xs flex-grow border-l border-r bg-gray-200">
            <div class="flex-1 overflow-y-auto">
                @foreach($emails as $email)
                    @php
                    $parts = explode('<', $email->from);
                    $from = isset($parts[1]) ? $parts[0] : $parts[0];
                    @endphp
                    <a href="#" class="block px-6 pt-3 pb-4 bg-white emailitem border-b-2" data-id="{{ $email->id }}">
                        <div class="flex justify-between">
                            <span class="text-sm font-semibold text-gray-900">{{ $from }}</span>
                            <span class="text-xs text-gray-500">{{ $email->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-900">{{ $email->subject }}</p>
                        @if(config('sentemails.storeAttachments'))
                            {{ __('Attachments') }}: {{ $email->attachments->count() }}
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


<script>
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

