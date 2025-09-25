@extends('layouts.coordinator_theme')

@section('page_title', 'Admin Tasks/Reports')
@section('breadcrumb', 'Job Queue')

<script src="https://cdn.tailwindcss.com"></script>

@if(config('queue-monitor.ui.refresh_interval'))
    <meta http-equiv="refresh" content="{{ config('queue-monitor.ui.refresh_interval') }}">
@endif

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
                                Mail Queue
                            </h3>
                            @include('layouts.dropdown_menus.menu_admin')
                        </div>
                    </div>
            <!-- /.card-header -->
            <div class="card-body">

        <nav class="flex items-center py-4 border-b border-gray-100 dark:border-gray-600">
            <h1 class="px-4 w-full font-semibold text-lg">
                {{ __('Queue Monitor') }}
            </h1>
            <div class="w-[24rem] px-4 text-sm text-gray-700 font-light">
                Statistics
            </div>
        </nav>

        <main class="flex">

            <article class="w-full p-4">
                <h2 class="mb-4 text-gray-800 text-sm font-medium">
                    {{ __('Filter') }}
                </h2>

                @include('queue-monitor::partials.filter', [
                    'filters' => $filters,
                ])

                <h2 class="mb-4 text-gray-800 text-sm font-medium">
                    {{ __('Jobs') }}
                </h2>

                @include('queue-monitor::partials.table', [
                    'jobs' => $jobs,
                ])

                @if(config('queue-monitor.ui.allow_purge'))
                    <div class="mt-12">
                        <form action="{{ route('queue-monitor::purge') }}" method="post">
                            @csrf
                            @method('delete')
                            <button class="py-2 px-4 bg-red-50 dark:bg-red-200 hover:dark:bg-red-300 hover:bg-red-100 text-red-800 text-xs font-medium rounded-md transition-colors duration-150">
                                {{ __('Delete all entries') }}
                            </button>
                        </form>
                    </div>
                @endif
            </article>

            <aside class="flex flex-col gap-4 w-[24rem] p-4">
                @foreach($metrics->all() as $metric)
                    @include('queue-monitor::partials.metrics-card', [
                        'metric' => $metric,
                    ])
                @endforeach
            </aside>

    </main>

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

        if (itemPath == currentPath) {
            item.classList.add("active");
        }
    });
});

</script>
@endsection

