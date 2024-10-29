@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Coordinator Reports<h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
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
            <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Coordinator Reporting Tree
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($supervisingCoordinatorCondition)

                        <a class="dropdown-item" href="{{ route('coordreports.coordrptvolutilization') }}">Coordinator Utilization Report</a>
                        @endif
                        @if ($assistConferenceCoordinatorCondition)

                        <a class="dropdown-item" href="{{ route('coordreports.coordrptappreciation') }}">Coordinator Appreciation Report</a>
                        <a class="dropdown-item" href="{{ route('coordreports.coordrptbirthdays') }}">Coordinator Birthday Report</a>
                        @endif
                        <a class="dropdown-item" href="{{ route('coordreports.coordrptreportingtree') }}">Coordinator Reporting Tree</a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->

<!-- Main content -->
<div class="card-body">
    <div class="mermaid-container">
        <div class="mermaid flowchart" id="mermaid-chart">
            flowchart TD
            @foreach ($coordinator_array as $coordinator)
                @php
                $id = $coordinator['id'];
                $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                $position = htmlspecialchars($coordinator['display_position_title']);
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
                    $shouldExclude = ($report_id == "0" && $founderCondition) || ($report_id == "1" && !$founderCondition);
                @endphp
                @if (!$shouldExclude)
                    {{ $report_id }} --- {{ $id }}
                @endif
            @endforeach

            %% Dynamic Subgraphs %%
            @php
                $conference_groups = [];
                $region_groups = [];

                foreach ($coordinator_array as $coordinator) {
                    if ($founderCondition) {
                        $conf = $coordinator['conference'];
                        if ($conf !== "Intl") {
                            if (!isset($conference_groups[$conf])) {
                                $conference_groups[$conf] = [];
                            }
                            $region = $coordinator['region'];
                            if ($region !== "None") {
                                $conference_groups[$conf][$region][] = $coordinator;
                            }
                        }
                    } else {
                        $region = $coordinator['region'];
                        if ($region !== "None") {
                            if (!isset($region_groups[$region])) {
                                $region_groups[$region] = [];
                            }
                            $region_groups[$region][] = $coordinator;
                        }
                    }
                }
            @endphp

            %% Founder Condition Groups %%
            @if ($founderCondition)
                @foreach ($conference_groups as $conference => $coordinators_by_region)
                    subgraph {{ $conference }}
                        direction TB
                        style {{ $conference }} fill:none,stroke:none

                        %% Add nodes for each region under the conference
                        @foreach ($coordinators_by_region as $region => $coordinators)
                            subgraph {{ $region }}
                                direction TB
                                style {{ $region }} fill:none,stroke:none

                                %% Add coordinators for this region
                                @foreach ($coordinators as $coordinator)
                                    @php
                                        $id = $coordinator['id'];
                                        $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                                        $position = htmlspecialchars($coordinator['display_position_title']);
                                        $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                                        $node_label = "$name<br>$position";
                                        if ($sec_position) {
                                            $node_label .= " / $sec_position";
                                        }
                                        if ($coordinator['region'] != "None") {
                                            $node_label .= "<br>" . htmlspecialchars($coordinator['region']);
                                        }
                                    @endphp
                                    {{ $id }}["{{ $node_label }}"]
                                @endforeach
                            end
                        @endforeach
                    end
                @endforeach
            @endif

            %% Non-Founder Condition Groups %%
            @foreach ($region_groups as $region => $coordinators)
                subgraph {{ $region }}
                    direction TB
                    style {{ $region }} fill:none,stroke:none

                    %% Add coordinators for this region
                    @foreach ($coordinators as $coordinator)
                        @php
                            $id = $coordinator['id'];
                            $name = htmlspecialchars($coordinator['first_name'] . ' ' . $coordinator['last_name']);
                            $position = htmlspecialchars($coordinator['display_position_title']);
                            $sec_position = htmlspecialchars($coordinator['sec_position_title']);
                            $node_label = "$name<br>$position";
                            if ($sec_position) {
                                $node_label .= " / $sec_position";
                            }
                            if ($coordinator['region'] != "None") {
                                $node_label .= "<br>" . htmlspecialchars($coordinator['region']);
                            }
                        @endphp
                        {{ $id }}["{{ $node_label }}"]
                    @endforeach
                end
            @endforeach
        </div>
    </div>
</div>

<div class="card-body">
        <button type="button" class="btn bg-gradient-primary" onclick="showPositionAbbreviations()">Position Abbreviations</button>

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


document.addEventListener('DOMContentLoaded', function() {
    mermaid.initialize({
        startOnLoad: true,
        maxTextSize: 20000000, // Adjust this value as needed
        flowchart: {
            useMaxWidth: false // Prevents automatic width shrinking
        }
    });
});

function showPositionAbbreviations() {
    Swal.fire({
        title: '<strong>Position Abbreviations</strong>',
        html: `
        <h4><strong>Conference Positions</h4></strong>
            <table>
                <tr><td><h4>BS</h4></td><td><h4>Big Sister</h4></td></tr>
                <tr><td><h4>AC</h4></td><td><h4>Area Coordinator</h4></td></tr>
                <tr><td><h4>SC</h4></td><td><h4>State Coordinator</h4></td></tr>
                <tr><td><h4>ARC</h4></td><td><h4>Assistant Regional Coordinator</h4></td></tr>
                <tr><td><h4>RC</h4></td><td><h4>Regional Coordinator</h4></td></tr>
                <tr><td><h4>ACC&nbsp;&nbsp;&nbsp;&nbsp;</h4></td><td><h4>Assistant Conference Coordinator</h4></td></tr>
                <tr><td><h4>CC</h4></td><td><h4>Conference Coordinator</h4></td></tr>
                <tr><td><h4>IC</h4></td><td><h4>Inquiries Coordinator</h4></td></tr>
                <tr><td><h4>WR</h4></td><td><h4>Website Reviewer</h4></td></tr>
                <tr><td><h4>CDC</h4></td><td><h4>Chapter Development Coordinator</h4></td></tr>
                <tr><td><h4>SPC</h4></td><td><h4>Special Projects Coordinator</h4></td></tr>
                <tr><td><h4>BSM</h4></td><td><h4>Big Sister Mentor Coordinator</h4></td></tr>
                <tr><td><h4>ARR</h4></td><td><h4>Annual Report Reviewer</h4></td></tr>
                <tr><td><h4>ART</h4></td><td><h4>Annual Report Tester</h4></td></tr>
            </table>
            <br>
            <h4><strong>International Positions</h4></strong>
            <table>
                <tr><td><h4>IT</h4></td><td><h4>IT Coordinator</h4></td></tr>
                <tr><td><h4>EIN</h4></td><td><h4>EIN Coordinator</h4></td></tr>
                <tr><td><h4>SMC</h4></td><td><h4>Social Media Coordinator</h4></td></tr>
                <tr><td><h4>COR</h4></td><td><h4>Correspondence Coordinator</h4></td></tr>
                <tr><td><h4>IIC</h4></td><td><h4>Internaitonal Inquiries Coordinator</h4></td></tr>
                <tr><td><h4>M2M&nbsp;&nbsp;&nbsp;&nbsp;</h4></td><td><h4>M2M Committee</h4></td></tr>
                <tr><td><h4>LIST</h4></td><td><h4>List Admin</h4></td></tr>
            </table>`,
        focusConfirm: false,
        confirmButtonText: 'Close',
        customClass: {
            popup: 'swal-wide',
            confirmButton: 'btn btn-danger'
        }
    });
}

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

.swal-wide {
    width: 600px !important;
}

.mermaid .cluster.default.flowchart-label {
        display: none; /* Hides the text */
    }

</style>
