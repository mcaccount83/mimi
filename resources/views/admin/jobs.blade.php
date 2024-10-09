@extends('layouts.coordinator_theme')

@section('content')

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    @if(config('queue-monitor.ui.refresh_interval'))
        <meta http-equiv="refresh" content="{{ config('queue-monitor.ui.refresh_interval') }}">
    @endif
    <title>Queue Monitor</title>
    <link href="{{ config('settings.base_url') }}public/vendor/queue-monitor/app.css" rel="stylesheet">
</head>

<body class="font-sans pb-64 bg-white dark:bg-gray-800 dark:text-white">

    {{-- <nav class="flex items-center py-4 border-b border-gray-100 dark:border-gray-600">
        <h1 class="px-4 w-full font-semibold text-lg">
            Queue Monitor
        </h1>
    </nav> --}}

    <main class="flex">

            <article class="w-full p-4">
                <h1 class="px-4 w-full font-semibold text-lg mb-4">
                    Queue Monitor
                </h1>
            <table class="w-full rounded-md whitespace-no-wrap rounded-md border dark:border-gray-600 border-separate border-spacing-0 mb-4">
                <thead class="rounded-t-md">

                    <tr>
                        <th class="px-4 py-3 font-medium text-left text-xs text-gray-600 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-600">Status</th>
                        <th class="px-4 py-3 font-medium text-left text-xs text-gray-600 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-600">Job</th>
                        <th class="px-4 py-3 font-medium text-left text-xs text-gray-600 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-600">Details</th>
                        <th class="px-4 py-3 font-medium text-left text-xs text-gray-600 dark:text-gray-400 uppercase border-b border-gray-200 dark:border-gray-600">Queued</th>
                    </tr>

                </thead>

                <tbody class="bg-gray-50 dark:bg-gray-700">

                    @forelse($pendingJobs as $job)
                        <tr class="font-sm leading-relaxed">

                            <td class="p-4 text-gray-800 dark:text-gray-300 text-sm leading-5 border-b border-gray-200 dark:border-gray-600">
                                <div class="inline-flex flex-1 px-2 text-xs font-medium leading-5 rounded-full bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-50">
                                    Queued
                                </div>
                            </td>

                            <td class="p-4 text-gray-800 dark:text-gray-300 text-sm leading-5 font-medium border-b border-gray-200 dark:border-gray-600">

                                {{-- {{ $job->name }} --}}

                                <span class="ml-1 text-xs text-gray-600 dark:text-gray-400">
                                    #{{ $job->id }}
                                </span>

                            </td>

                            <td class="p-4 text-gray-800 dark:text-gray-300 text-sm leading-5 border-b border-gray-200 dark:border-gray-600">

                                <div class="text-xs">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">@lang('Queue'):</span>
                                    <span class="font-semibold">{{ $job->queue }}</span>
                                </div>

                                <div class="text-xs">
                                    <span class="text-gray-600 dark:text-gray-400 font-medium">@lang('Attempt'):</span>
                                    <span class="font-semibold">{{ $job->attempts }}</span>
                                </div>
                            </td>

                            <td class="p-4 text-gray-800 dark:text-gray-300 text-sm leading-5 border-b border-gray-200 dark:border-gray-600">
                                {{ \Carbon\Carbon::createFromTimestamp($job->created_at)->format('Y-m-d H:i:s') }}
                            </td>

                        </tr>

                        @empty
                            <tr>
                                <td colspan="100" class="">
                                    <div class="my-6">
                                        <div class="text-center">
                                            <div class="text-gray-500 text-lg">
                                                @lang('No Queued Jobs')
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                </tbody>

        {{-- </article>

        <article class="w-full p-4"> --}}
            @include('queue-monitor::partials.table', [
                'jobs' => $jobs,
            ])

            @if(config('queue-monitor.ui.allow_purge'))
                {{-- <div class="mt-12"> --}}
                    <form action="{{ route('queue-monitor::purge') }}" method="post">
                        @csrf
                        @method('delete')
                        <button class="py-2 px-4 bg-red-50 dark:bg-red-200 hover:dark:bg-red-300 hover:bg-red-100 text-red-800 text-xs font-medium rounded-md transition-colors duration-150">
                            @lang('Delete all processed entries')
                        </button>
                    </form>
                {{-- </div> --}}
            @endif
        </article>

    </main>

</body>

@endsection



