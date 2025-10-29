@extends('layouts.coordinator_theme')

@section('page_title', 'Coordinator Reports')
@section('breadcrumb', 'Coordinator Reporting Tree')

@section('content')
@php
    $founderCondition = isset($checkBox6Status) && $checkBox6Status === 'checked' ? true : $founderCondition;
@endphp

    <!-- Main content -->
<section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Coordinator Reporting Tree
                    </h3>
                    @include('layouts.dropdown_menus.menu_reports_coor')
                </div>
            </div>
            <!-- /.card-header -->

<!-- Main content -->
<div class="card-body">
<div class="mermaid-container">
<div class="mermaid flowchart" id="mermaid-chart">
    flowchart TD

    %% Define all nodes first
    @foreach ($coordinatorList as $coordinator)
    @php
        $id = $coordinator['id'] ?? '';
        $name = htmlspecialchars(($coordinator['first_name'] ?? '') . ' ' . ($coordinator['last_name'] ?? ''));
        $position = htmlspecialchars($coordinator['displayPosition']['short_title'] ?? '');
        $sec_titles = '';
        if (!empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0) {
            $sec_titles_array = $coordinator->secondaryPosition->pluck('short_title')->toArray();
            $sec_titles = htmlspecialchars(implode('/', $sec_titles_array));
        }
        $region = htmlspecialchars($coordinator['region']['short_name'] ?? '');
        $conf = htmlspecialchars($coordinator['conference']['short_name'] ?? '');

        $node_label = "$name<br>$position";
        if ($sec_titles) $node_label .= "/$sec_titles";
        if ($region != "None") $node_label .= "<br>$region";
        if ($region == "None") $node_label .= "<br>$conf";
    @endphp
        {{ $id }}["{!! $node_label !!}"]
    @endforeach

    %% Build manager groups data structure
    @php
        $manager_groups = [];
        foreach ($coordinatorList as $coordinator) {
            $report_id = $coordinator['report_id'];
            $id = $coordinator['id'];
            $shouldExclude = ($report_id == "0" && $founderCondition) || ($report_id == "1" && !$founderCondition);

            if (!$shouldExclude) {
                if (!isset($manager_groups[$report_id])) {
                    $manager_groups[$report_id] = [];
                }
                $manager_groups[$report_id][] = $id;
            }
        }
    @endphp

    %% Group nodes into geographical subgraphs with nested manager groups
    @php
        $conference_groups = [];
        $region_groups = [];

        foreach ($coordinatorList as $coordinator) {
            $id = $coordinator['id'] ?? '';

            if ($founderCondition) {
                $conf = $coordinator['conference']['short_name'];
                if ($conf != "Intl") {
                    if (!isset($conference_groups[$conf])) {
                        $conference_groups[$conf] = [];
                    }
                    $region = $coordinator['region']['short_name'];
                    if ($region != "None") {
                        if (!isset($conference_groups[$conf][$region])) {
                            $conference_groups[$conf][$region] = [];
                        }
                        $conference_groups[$conf][$region][] = $id;
                    } else {
                        if (!isset($conference_groups[$conf]['_conf'])) {
                            $conference_groups[$conf]['_conf'] = [];
                        }
                        $conference_groups[$conf]['_conf'][] = $id;
                    }
                }
            } else {
                $region = $coordinator['region']['short_name'];
                if ($region != "None") {
                    if (!isset($region_groups[$region])) {
                        $region_groups[$region] = [];
                    }
                    $region_groups[$region][] = $id;
                }
            }
        }
    @endphp

    %% Founder Condition Subgraphs with nested manager groups
    @if ($founderCondition)
        @foreach ($conference_groups as $conference => $regions_data)
            subgraph {{ $conference }}
                direction TB
                style {{ $conference }} fill:none,stroke:none

                %% Conference-level coordinators with their manager groups
                @if (isset($regions_data['_conf']))
                    @php
                        $conf_ids = $regions_data['_conf'];
                        // Group conference-level coordinators by their managers
                        $conf_by_manager = [];
                        foreach ($conf_ids as $id) {
                            foreach ($coordinatorList as $coordinator) {
                                if ($coordinator['id'] == $id) {
                                    $report_id = $coordinator['report_id'];
                                    if (!isset($conf_by_manager[$report_id])) {
                                        $conf_by_manager[$report_id] = [];
                                    }
                                    $conf_by_manager[$report_id][] = $id;
                                    break;
                                }
                            }
                        }
                    @endphp

                    @foreach ($conf_by_manager as $manager_id => $subordinates)
                        @if (count($subordinates) > 1)
                            subgraph confmgr{{ $conference }}{{ $manager_id }} [" "]
                                direction TB
                                style confmgr{{ $conference }}{{ $manager_id }} fill:transparent,stroke:transparent
                                @foreach ($subordinates as $id)
                                    {{ $id }}
                                @endforeach
                            end
                        @else
                            @foreach ($subordinates as $id)
                                {{ $id }}
                            @endforeach
                        @endif
                    @endforeach
                @endif

                %% Region subgraphs with nested manager groups
                @foreach ($regions_data as $region => $ids)
                    @if ($region != '_conf')
                        subgraph {{ $region }}
                            direction TB
                            style {{ $region }} fill:none,stroke:none

                            @php
                                // Group region coordinators by their managers
                                $region_by_manager = [];
                                foreach ($ids as $id) {
                                    foreach ($coordinatorList as $coordinator) {
                                        if ($coordinator['id'] == $id) {
                                            $report_id = $coordinator['report_id'];
                                            if (!isset($region_by_manager[$report_id])) {
                                                $region_by_manager[$report_id] = [];
                                            }
                                            $region_by_manager[$report_id][] = $id;
                                            break;
                                        }
                                    }
                                }
                            @endphp

                            @foreach ($region_by_manager as $manager_id => $subordinates)
                                @if (count($subordinates) > 1)
                                    subgraph regmgr{{ $region }}{{ $manager_id }} [" "]
                                        direction TB
                                        style regmgr{{ $region }}{{ $manager_id }} fill:transparent,stroke:transparent
                                        @foreach ($subordinates as $id)
                                            {{ $id }}
                                        @endforeach
                                    end
                                @else
                                    @foreach ($subordinates as $id)
                                        {{ $id }}
                                    @endforeach
                                @endif
                            @endforeach
                        end
                    @endif
                @endforeach
            end
        @endforeach
    @else
        %% Non-Founder Condition Subgraphs with nested manager groups
        @foreach ($region_groups as $region => $ids)
            subgraph {{ $region }}
                direction TB
                style {{ $region }} fill:none,stroke:none

                @php
                    // Group region coordinators by their managers
                    $region_by_manager = [];
                    foreach ($ids as $id) {
                        foreach ($coordinatorList as $coordinator) {
                            if ($coordinator['id'] == $id) {
                                $report_id = $coordinator['report_id'];
                                if (!isset($region_by_manager[$report_id])) {
                                    $region_by_manager[$report_id] = [];
                                }
                                $region_by_manager[$report_id][] = $id;
                                break;
                            }
                        }
                    }
                @endphp

                @foreach ($region_by_manager as $manager_id => $subordinates)
                    @if (count($subordinates) > 1)
                        subgraph regmgr{{ $region }}{{ $manager_id }} [" "]
                            direction TB
                            style regmgr{{ $region }}{{ $manager_id }} fill:transparent,stroke:transparent
                            @foreach ($subordinates as $id)
                                {{ $id }}
                            @endforeach
                        end
                    @else
                        @foreach ($subordinates as $id)
                            {{ $id }}
                        @endforeach
                    @endif
                @endforeach
            end
        @endforeach
    @endif

    %% Connect Coordinators - AFTER all subgraphs are defined
    @foreach ($coordinatorList as $coordinator)
        @php
            $report_id = $coordinator['report_id'];
            $id = $coordinator['id'];
            $shouldExclude = ($report_id == "0" && $founderCondition) || ($report_id == "1" && !$founderCondition);
        @endphp
        @if (!$shouldExclude)
            {{ $report_id }} --- {{ $id }}
        @endif
    @endforeach
</div>
</div>

<div class="card-body">
    <br>
        <button type="button" class="btn bg-gradient-primary" onclick="showPositionAbbreviations()">Position Abbreviations</button>
</div>

@if ($ITCondition)
    <div class="col-sm-12">
        <div class="custom-control custom-switch">
            <input type="checkbox" name="showFullTree" id="showFullTree" class="custom-control-input" {{$checkBox6Status}} onchange="showFullTree()" />
            <label class="custom-control-label" for="showFullTree">Show Full International Reporting Tree</label>
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

function showFullTree() {
    var base_url = '{{ route("coordreports.coordrptreportingtree") }}';
    if ($("#showFullTree").prop("checked") == true) {
        window.location.href = base_url + '?{{ \App\Enums\CoordinatorCheckbox::REPORTING_TREE }}=yes';
    } else {
        window.location.href = base_url;
    }
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
