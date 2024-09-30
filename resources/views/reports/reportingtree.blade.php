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
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header">
                        <h3 class="card-title">Coordinator Reporting Tree</h3>
                    </div> --}}
                    <!-- /.card-header -->
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
                                            $node_label = "$name<br>$position";
                                            if ($sec_position) $node_label .= " / $sec_position";
                                            if ($region != "None") $node_label .= "<br>$region";
                                            if ($region == "None") $node_label .= "<br>&nbsp";
                                        @endphp
                                        {{ $id }}["{{ $node_label }}"]
                                        @php
                                            $report_id = $coordinator['report_id'];
                                            $shouldExclude = ($report_id == "366" && $cord_pos_id == "7") || ($report_id == "1" && $cord_pos_id != "7");
                                        @endphp
                                        @if (!$shouldExclude)
                                            {{ $report_id }} --- {{ $id }}
                                        @endif
                                    @endforeach
                                </div>


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
document.addEventListener('DOMContentLoaded', function() {
    mermaid.initialize({
        startOnLoad: true,
        flowchart: {
            useMaxWidth: false  // Prevents automatic width shrinking
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
            primaryColor: '#007bff'
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
