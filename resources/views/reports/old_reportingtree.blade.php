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
                <div class="card-header">
                  <h3 class="card-title">Coordinator Reporting Tree</h3>
                </div>
                <!-- /.card-header -->
            <div class="card-body text-center">
                <div class="row">
              <div id="chart_div">

              </div>
            </div>
            <div class="card-body text-center">
            </div>
          </div>
        </div>
      </div>
    </div>
    </section>
@endsection
@section('customscript')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
  google.charts.load('current', {packages:["orgchart"]});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Name');
  data.addColumn('string', 'Manager');
  data.addColumn('string', 'ToolTip');

  // For each orgchart box, provide the name, manager, and tooltip to show.
  data.addRows([
    @php

      $rowcount = count($coordinator_array);

      //Now make the tree
      for($a=0;$a<$rowcount;$a++){
        $first_name = str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars($coordinator_array[$a]['first_name'] ) );
        $last_name = str_replace(array("'", "\"", "&quot;"), "", htmlspecialchars($coordinator_array[$a]['last_name'] ) );

        echo "[{v:'" . $coordinator_array[$a]['id'] . "', f:'". $first_name . " " . $last_name . "<br>" . $coordinator_array[$a]['position_title'];

        if ($coordinator_array[$a]['sec_position_title']!="")
          echo " / " . $coordinator_array[$a]['sec_position_title'];

        if ($coordinator_array[$a]['region']!="None")
        echo "<br>" . $coordinator_array[$a]['region'];

        echo "'},'";
        if ($coordinator_array[$a]['report_id']==366 && $cord_pos_id == 8)
          echo "'";
        else if($coordinator_array[$a]['report_id']==1 && $cord_pos_id != 8)
          echo "'";
        else
          echo $coordinator_array[$a]['report_id'] . "'";

        echo ",''],\n";
      }

    @endphp

  ]);

  // Create the chart.
  var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
  // Draw the chart, setting the allowHtml option to true for the tooltips.
  chart.draw(data, {allowHtml: true, size: 'medium'});
  }
</script>

<style>
.google-visualization-orgchart-lineleft {
	border-left: 2px solid #333!important;
}
.google-visualization-orgchart-linebottom {
	border-bottom: 2px solid #333!important;
}
.google-visualization-orgchart-lineright {
	border-right: 2px solid #333!important;
}

.google-visualization-orgchart-linetop{
	border-right: 2px solid #333!important;
}

</style>

@endsection
