@extends('layouts.coordinator_theme')

<script src="https://d3js.org/d3.v7.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-org-chart@2.6.0"></script>
<script src="https://cdn.jsdelivr.net/npm/d3-flextree@2.1.2/build/d3-flextree.js"></script>

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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="chart-container" id="org-chart"></div>
                        @if (empty($coordinator_array))
                            <div>No coordinators found.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customscript')
<script>
    // Initialize the data array
    var data = [
        @foreach ($coordinator_array as $coordinator)
            @php
                $id = $coordinator['id'];
                $report_id = $coordinator['report_id'];
                $cord_pos_id = $coordinator['cord_pos_id']; // Assuming this is available in your array
                // Determine whether to exclude certain nodes based on report_id and cord_pos_id
                $shouldExclude = ($report_id == "366" && $cord_pos_id == "7") || ($report_id == "1" && $cord_pos_id != "7");

                // Set the parent based on the exclusion logic
                $parent = (!$shouldExclude && $report_id !== "None") ? $report_id : ""; // Keep root node as empty
                $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                $position = htmlspecialchars($coordinator['position_title']);
                $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                $region = htmlspecialchars($coordinator['region']);
                $node_label = "$name<br>$position";
                if ($sec_position) $node_label .= " / $sec_position";
                if ($region != "None") $node_label .= "<br>$region";
            @endphp
            {"id": "{{ $id }}", "parent": "{{ $parent }}", "name": "{{ $node_label }}"},
        @endforeach
    ];

    // Debugging: log the data to check for multiple roots
    console.log(data);

    // Count the number of root nodes (nodes without a parent)
    var rootCount = data.filter(d => !d.parent).length;
    if (rootCount > 1) {
        console.error("Multiple root nodes found! Ensure only one root node exists.");
    } else {
        // Create the chart only if there's a single root
        var chart = new d3.OrgChart()
            .container(".chart-container") // Ensure this is the correct container
            .data(data)
            .render();
    }
</script>
@endsection


