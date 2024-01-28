@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Coordinator Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Coordinator Report</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapter Coordinators</h3>

            </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
              <thead>
			    <tr>
				  <th>Details</th>
				  <th>State</th>
                  <th>Name</th>
				   <th>Conference Coordinator</th>
				   <th>Assistant Conference Coordinator</th>
				  <th>Regional Coordinator</th>
				  <th>Assistant Regional Coordinator</th>
				  <th>State Coordinator</th>
				  <th>Area Coordinator</th>
				  <th>Big Sister</th>
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
                            $coordinator_data = DB::table('coordinator_details as cd')
                                ->select('cd.first_name as first_name', 'cd.last_name as last_name', 'cp.short_title as position')
                                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                ->where('cd.coordinator_id', $val)
                                ->get();

                            $coordinator_array[] = $coordinator_data;
                        }

                        $cord_row_count = count($coordinator_array);

                        echo "<tr>";
                        echo "<td><center><a href='/chapter/edit/{$row->id}'><i class='fa fa-edit fa-lg' aria-hidden='true'></i></a></center></td> \n";
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
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>

                  </label>
                  <span> Only show chapters I am Primary For</span>
                </div>
              </div>
              </div>
            </div>

			  <div class="box-body text-center">

              <a href="{{ route('export.chaptercoordinator') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Chapter Coordinator List</button></a>
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
