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
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead>
			    <tr>
				  <th></th>
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
					$row_count=count($chapterList);
					for ($row = 0; $row < $row_count; $row++){
						 $id = $chapterList[$row]->primary_coordinator_id;
						 $reportingList = DB::table('coordinator_reporting_tree')
										->select('*')
										->where('id', '=', $id)
										->get();

						foreach($reportingList as $key => $value)
						{
							$reportingList[$key] = (array) $value;
						}
						$filterReportingList = array_filter($reportingList[0]);
						unset($filterReportingList['id']);
						unset($filterReportingList['layer0']);
 					    $filterReportingList = array_reverse($filterReportingList);
					    foreach($filterReportingList as $key =>$val){
                            $coordinator_data = DB::table('coordinator_details as cd')
                                ->select('cd.first_name as first_name', 'cd.last_name as last_name', 'cp.short_title as position')
                                ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
                                ->where('cd.coordinator_id', $val)
                                ->get();

                            $coordinator_array[] = $coordinator_data;

						}
						$cord_row_count = count($coordinator_array);

						echo "<tr>";
						echo "<td><a href='/mimi/chapter/edit/".$chapterList[$row]->id."'><i class='fa fa-pencil-square' aria-hidden='true'></i></a></td> \n";
						echo "<td>" . $chapterList[$row]->state . "</td>\n";
						echo "<td>" . $chapterList[$row]->name . "</td>\n";
						for($pos_row = 7; $pos_row > 0; $pos_row--){
							$position_found=false;

							for($cord_row = 0; $cord_row < $cord_row_count; $cord_row++){
								switch ($pos_row) {
									case 1:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="BS" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name . "</td> \n";
											$position_found=true;
										}
										break;
									case 2:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="AC" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name . "</td> \n";
											$position_found=true;
										}
										break;
									case 3:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="SC" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name . "</td> \n";
											$position_found=true;
										}
										break;
									case 4:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="ARC" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name. "</td> \n";
											$position_found=true;
										}
										break;
									case 5:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="RC" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name . "</td> \n";
											$position_found=true;
										}
										break;
									case 6:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="ACC" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name . "</td> \n";
											$position_found=true;
										}
										break;
									case 7:
										if (isset($coordinator_array[$cord_row][0]) && $coordinator_array[$cord_row][0]->position=="CC" && !$position_found){
											echo " <td>" . $coordinator_array[$cord_row][0]->first_name . " " . $coordinator_array[$cord_row][0]->last_name .  "</td> \n";
											$position_found=true;
										}
										break;
									}

								}
							if(!$position_found)
								echo " <td></td>\n";

						}
						unset($coordinator_array);
					}
					echo "</tr>";
					?>

			        <!--</tr>-->

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
			  <div class="box-body text-center">

              <a href="{{ route('export.chaptercoordinator') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Chapter Coordinator List</button></a>
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
