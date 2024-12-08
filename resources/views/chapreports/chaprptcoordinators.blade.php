@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Reports')
@section('breadcrumb', 'Chapter Coordinator Report')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Chapter Coordinator Report
                        </h3>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptchapterstatus') }}">Chapter Status Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprpteinstatus') }}">IRS Status Report</a>
                            @if ($adminReportCondition)
                                <a class="dropdown-item" href="{{ route('international.inteinstatus') }}">International IRS Status Report</a>
                            @endif
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptnewchapters') }}">New Chapter Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptlargechapters') }}">Large Chapter Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptprobation') }}">Chapter Probation Report</a>
                            <a class="dropdown-item" href="{{ route('chapreports.chaprptcoordinators') }}">Chapter Coordinators Report</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Details</th>
                  <th>Conf/Reg</th>
				  <th>State</th>
                  <th>Name</th>
				   <th>CC</th>
				   <th>ACC</th>
				  <th>RC</th>
				  <th>ARC</th>
				  <th>SC</th>
				  <th>AC</th>
				  <th>BS</th>
                </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = count($chapterList);

                    foreach ($chapterList as $row) {
                        $id = $row->primary_coordinator_id;
                        $reportingList = DB::table('coordinator_reporting_tree')
                            ->select('*')
                            ->where('id', '=', $id)
                            ->get();

                        $filterReportingList = array_filter((array)$reportingList[0], function ($key) {
                            return !in_array($key, ['id', 'layer0']);
                        }, ARRAY_FILTER_USE_KEY);

                        $filterReportingList = array_reverse($filterReportingList);
                        $coordinator_array = [];

                        foreach ($filterReportingList as $val) {
                            $coordinator_data = DB::table('coordinators as cd')
                                ->select('cd.first_name as first_name', 'cd.last_name as last_name', 'cp.short_title as position')
                                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                ->where('cd.id', $val)
                                ->get();

                            $coordinator_array[] = $coordinator_data;
                        }

                        $cord_row_count = count($coordinator_array);

                        echo "<tr>";
                        echo "<td class='text-center align-middle'><a href='/chapterdetails/{$row->id}'><i class='fas fa-eye' ></i></a></td> \n";
                        echo "<td>{$row->conf} / {$row->reg}</td>\n";
                        echo "<td>{$row->state}</td>\n";
                        echo "<td>{$row->name}</td>\n";

                        for ($pos_row = 7; $pos_row > 0; $pos_row--) {
                            $position_found = false;

                            foreach ($coordinator_array as $cord_row => $cord_data) {
                                if (isset($cord_data[0]) && $cord_data[0]->position == getPositionCode($pos_row) && !$position_found) {
                                    echo "<td>{$cord_data[0]->first_name} {$cord_data[0]->last_name}</td> \n";
                                    $position_found = true;
                                }
                            }

                            if (!$position_found) {
                                echo " <td></td>\n";
                            }
                        }

                        unset($coordinator_array);
                        echo "</tr>";
                    }

                    function getPositionCode($pos_row)
                    {
                        $positionCodes = ['BS', 'AC', 'SC', 'ARC', 'RC', 'ACC', 'CC'];
                        return $positionCodes[$pos_row - 1];
                    }
                    ?>

                  </tbody>
                </table>
            </div>
            <!-- /.card-body -->
                <div class="col-sm-12">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="showPrimary" id="showPrimary" class="custom-control-input" {{$checkBoxStatus}} onchange="showPrimary()" />
                        <label class="custom-control-label" for="showPrimary">Only show chapters I am primary for</label>
                    </div>
                </div>
                <div class="card-body text-center">
              <a href="{{ route('export.chaptercoordinator') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download mr-2"></i>Export Chapter Coordinator List</button></a>
             </div>

           </div>
          <!-- /.box -->
        </div>
      </div>
    </div>
    </section>
    <!-- /.content -->

@endsection
@section('customscript')
<script>
    function showPrimary() {
    var base_url = '{{ url("/chapterreports/coordinators") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}

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
