@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Job Queue')

<script src="https://cdn.tailwindcss.com"></script>

@if(config('queue-monitor.ui.refresh_interval'))
    <meta http-equiv="refresh" content="{{ config('queue-monitor.ui.refresh_interval') }}">
@endif

@section('content')
<section class="content">
    <div class="container-fluid table-container">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <div class="dropdown">
                            <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Mail Queue
                            </h3>
                            @include('layouts.dropdown_menus.menu_admin')
                        </div>
                    </div>
                    <div class="card-body">
                        <h1 class="px-4 w-full font-semibold text-lg">
                            @lang('Queue Monitor')
                        </h1>

                        @include('queue-monitor::partials.filter', ['filters' => $filters])

                        <h2 class="mb-4 text-gray-800 text-sm font-medium">@lang('Jobs')</h2>

                        @include('queue-monitor::partials.table', ['jobs' => $jobs])

                        @if(config('queue-monitor.ui.allow_purge'))
                            <form action="{{ route('queue-monitor::purge') }}" method="post">
                                @csrf
                                @method('delete')
                                <button class="py-2 px-4 bg-red-50 hover:bg-red-100 text-red-800 text-xs font-medium rounded-md">
                                    @lang('Delete all entries')
                                </button>
                            </form>
                        @endif

                        <!-- Optional sidebar content as card -->
                        <div class="card mt-4 p-4">
                            <h3>Metrics</h3>
                            @foreach($metrics->all() as $metric)
                                @include('queue-monitor::partials.metrics-card', ['metric' => $metric])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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

</script>
@endsection

