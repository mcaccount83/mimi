@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Coordinator Reporting Tree</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active">Coordinator Reporting Tree</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->

@if ($cord_pos_id == 7)
    {{-- <div class="card-body">
        <div class="mermaid-container">
            <div class="mermaid flowchart" id="mermaid-chart">
                flowchart TD
                @foreach ($coordinator_array as $coordinator)
                    @php
                        $id = $coordinator['id'];
                        $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                        $position = htmlspecialchars($coordinator['position_title']);
                        $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                        $region = htmlspecialchars($coordinator['region']);
                        $conf = htmlspecialchars($coordinator['conference']);
                        $node_label = "$name<br>$position";
                        if ($sec_position) $node_label .= " / $sec_position";
                        if ($region != "None") $node_label .= "<br>$region";
                        if ($region == "None") $node_label .= "<br>$conf";
                    @endphp
                        {{ $id }}["{{ $node_label }}"]
                    @php
                        $report_id = $coordinator['report_id'];
                        $id = $coordinator['id'];
                        $shouldExclude = ($report_id == "0" && $cord_pos_id == "7") || ($report_id == "1" && $cord_pos_id != "7");
                        @endphp
                    @if (!$shouldExclude)
                        {{ $report_id }} --- {{ $id }}
                    @endif
                @endforeach
            </div>
    </div>
</div> --}}

{{-- <div class="card-body">
    <div class="mermaid-container">
        <div class="mermaid flowchart" id="mermaid-chart">
            flowchart TD
            @foreach ($coordinator_array as $coordinator)
                @php
                    $id = $coordinator['id'];
                    $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                    $position = htmlspecialchars($coordinator['position_title']);
                    $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                    $region = htmlspecialchars($coordinator['region']);
                    $conf = htmlspecialchars($coordinator['conference']);
                    $node_label = "$name<br>$position";
                    if ($sec_position) $node_label .= " / $sec_position";
                    if ($region != "None") $node_label .= "<br>$region";
                    if ($region == "None") $node_label .= "<br>$conf";
                @endphp
                    {{ $id }}["{{ $node_label }}"]
            @endforeach

            %% Connect Coordinators %%
            @foreach ($coordinator_array as $coordinator)
                @php
                $report_id = $coordinator['report_id'];
                $id = $coordinator['id'];
                $shouldExclude = ($report_id == "0" && $cord_pos_id == "7") || ($report_id == "1" && $cord_pos_id != "7");
                @endphp
            @if (!$shouldExclude)
                {{ $report_id }} --- {{ $id }}
                @endif
            @endforeach

            %% Dynamic Subgraphs %%
            @php
                $confs = []; // To store unique regions
                foreach ($coordinator_array as $coordinator) {
                    $conf = $coordinator['conference'];
                    if ($conf !== "International") { // Only include regions that are not "None"
                        $confs[$conf] = true; // Use the region as key for uniqueness
                    }
                }
            @endphp

            @foreach ($confs as $conf => $_)
                subgraph {{ $conf }}
                    direction TB
                    style {{ $conf }} fill:none,stroke:none
                    @foreach ($coordinator_array as $coordinator)
                        @php
                            $id = $coordinator['id'];
                            $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                            $position = htmlspecialchars($coordinator['position_title']);
                            $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                            $region = htmlspecialchars($coordinator['region']);
                            $conf = htmlspecialchars($coordinator['conference']);
                            $node_label = "$name<br>$position";
                            if ($sec_position) $node_label .= " / $sec_position";
                            if ($region != "None") $node_label .= "<br>$region";
                            if ($region == "None") $node_label .= "<br>$conf";
                        @endphp
                            {{ $id }}["{{ $node_label }}"]
                    @endforeach
                end
            @endforeach
        </div>
    </div>
</div> --}}

<div class="card-body">
    <div class="mermaid-container">
        <div class="mermaid flowchart" id="mermaid-chart">
            flowchart TD
            @foreach ($coordinator_array as $coordinator)
                @php
                $id = $coordinator['id'];
                $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                $position = htmlspecialchars($coordinator['position_title']);
                $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                $region = htmlspecialchars($coordinator['region']);
                $conf = htmlspecialchars($coordinator['conference']);
                $node_label = "$name<br>$position";
                if ($sec_position) $node_label .= " / $sec_position";
                if ($region != "None") $node_label .= "<br>$region";
                if ($region == "None") $node_label .= "<br>$conf";
            @endphp
                    {{ $id }}["{{ $node_label }}"]
            @endforeach

            %% Connect Coordinators %%
            @foreach ($coordinator_array as $coordinator)
                @php
                    $report_id = $coordinator['report_id'];
                    $id = $coordinator['id'];
                    $shouldExclude = ($report_id == "0" && $cord_pos_id == "7") || ($report_id == "1" && $cord_pos_id != "7");
                    @endphp
                @if (!$shouldExclude)
                    {{ $report_id }} --- {{ $id }}
                @endif
            @endforeach

            %% Dynamic Subgraphs %%
            @php
                $confs = []; // To store unique regions
                foreach ($coordinator_array as $coordinator) {
                    $conf = $coordinator['conference'];
                    if ($conf !== "International") { // Only include regions that are not "None"
                        $confs[$conf] = true; // Use the region as key for uniqueness
                    }
                }
            @endphp

            @foreach ($confs as $conf => $_)
                subgraph {{ $conf }}
                    direction TB
                    {{-- style {{ $conf }} fill:none,stroke:none --}}
                    @foreach ($coordinator_array as $coordinator)
                        @php
                            if ($coordinator['conference'] === $conf) {
                                $id = $coordinator['id'];
                $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                $position = htmlspecialchars($coordinator['position_title']);
                $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                $region = htmlspecialchars($coordinator['region']);
                $conf = htmlspecialchars($coordinator['conference']);
                $node_label = "$name<br>$position";
                if ($sec_position) $node_label .= " / $sec_position";
                if ($region != "None") $node_label .= "<br>$region";
                if ($region == "None") $node_label .= "<br>$conf";
                        @endphp
                        {{ $id }}["{{ $node_label }}"]
                        @php
                            } // End if for region
                        @endphp
                    @endforeach
                end
            @endforeach
        </div>
    </div>
</div>



@else
    <div class="card-body">
        <div class="mermaid-container">
            <div class="mermaid flowchart" id="mermaid-chart">
                flowchart TD
                @foreach ($coordinator_array as $coordinator)
                    @php
                    $id = $coordinator['id'];
                    $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                    $position = htmlspecialchars($coordinator['position_title']);
                    $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                    $region = htmlspecialchars($coordinator['region']);
                    $conf = htmlspecialchars($coordinator['conference']);
                    $node_label = "$name<br>$position";
                    if ($sec_position) $node_label .= " / $sec_position";
                    if ($region != "None") $node_label .= "<br>$region";
                    if ($region == "None") $node_label .= "<br>$conf";
                @endphp
                        {{ $id }}["{{ $node_label }}"]
                @endforeach

                %% Connect Coordinators %%
                @foreach ($coordinator_array as $coordinator)
                    @php
                        $report_id = $coordinator['report_id'];
                        $id = $coordinator['id'];
                        $shouldExclude = ($report_id == "0" && $cord_pos_id == "7") || ($report_id == "1" && $cord_pos_id != "7");
                        @endphp
                    @if (!$shouldExclude)
                        {{ $report_id }} --- {{ $id }}
                    @endif
                @endforeach

                %% Dynamic Subgraphs %%
                @php
                    $regions = []; // To store unique regions
                    foreach ($coordinator_array as $coordinator) {
                        $region = $coordinator['region'];
                        if ($region !== "None") { // Only include regions that are not "None"
                            $regions[$region] = true; // Use the region as key for uniqueness
                        }
                    }
                @endphp

                @foreach ($regions as $region => $_)
                    subgraph {{ $region }}
                        direction TB
                        style {{ $region }} fill:none,stroke:none
                        @foreach ($coordinator_array as $coordinator)
                            @php
                                if ($coordinator['region'] === $region) {
                                    $id = $coordinator['id'];
                $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                $position = htmlspecialchars($coordinator['position_title']);
                $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                $region = htmlspecialchars($coordinator['region']);
                $conf = htmlspecialchars($coordinator['conference']);
                $node_label = "$name<br>$position";
                if ($sec_position) $node_label .= " / $sec_position";
                if ($region != "None") $node_label .= "<br>$region";
                if ($region == "None") $node_label .= "<br>$conf";
                            @endphp
                            {{ $id }}["{{ $node_label }}"]
                            @php
                                } // End if for region
                            @endphp
                        @endforeach
                    end
                @endforeach
            </div>
        </div>
    </div>

@endif


</section>
@endsection

@section('customscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    mermaid.initialize({
        startOnLoad: true,
        maxTextSize: 20000000, // Adjust this value as needed
        flowchart: {
            useMaxWidth: false // Prevents automatic width shrinking
        }
    });
});
</script>
<script type="module">
    // import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@11/dist/mermaid.esm.min.mjs';
    import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';

    document.addEventListener('DOMContentLoaded', function() {
    mermaid.initialize({
        startOnLoad: true,
        flowchart: {
            useMaxWidth: false,  // Prevents automatic shrinking of chart width
            nodeSpacing: 15,
            rankSpacing: 25,
            rankdir: 'TB',  // Enforce top-to-bottom flow
            padding: 10
        },
        theme: 'base',
        themeVariables: {
            fontSize: '15px',
            nodeTextColor: '#fff',
            primaryBorderColor: '#343a40',
            primaryColor: '#007bff',
        }
    });

    mermaid.contentLoaded();
});

</script>
@endsection

<style>
.flowchart {
    width: 100%;         /* Full width of its parent */
    overflow-x: auto;    /* Enable horizontal scrolling */
    overflow-y: hidden;  /* No vertical scrolling */
    white-space: nowrap; /* Prevent the chart from wrapping */
}

.mermaid svg {
    width: auto !important;  /* Allow the chart to dynamically adjust width */
    height: auto !important; /* Adjust height dynamically */
}

</style>
