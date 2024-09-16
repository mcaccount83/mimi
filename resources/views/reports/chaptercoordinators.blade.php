@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Chapter Coordinator Report</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Chapter Coordinator Report</li>
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
                <div class="card-header">
                  <h3 class="card-title">Report of Chapter Coordinators</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
              <thead>
			    <tr>
				  <th>Details</th>
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
                        echo "<td class='text-center align-middle'><a href='/chapter/edit/{$row->id}'><i class='fas fa-edit' ></i></a></td> \n";
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
              <a href="{{ route('export.chaptercoordinator') }}"><button class="btn bg-gradient-primary" <?php if($countList ==0) echo "disabled";?>><i class="fas fa-download" ></i>&nbsp;&nbsp;&nbsp;Export Chapter Coordinator List</button></a>
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
    var base_url = '{{ url("/reports/chaptercoordinators") }}';

    if ($("#showPrimary").prop("checked") == true) {
        window.location.href = base_url + '?check=yes';
    } else {
        window.location.href = base_url;
    }
}


</script>
@endsection
