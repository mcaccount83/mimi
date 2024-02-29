@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Chapter List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter List</li>
      </ol>
    </section>
    @if ($message = Session::get('success'))
      <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif


    @if (isset($_GET['dis']) && $_GET['dis']==1)
      <div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>Chapter has been successfully Zapped</p>
      </div>
    @endif

	 @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>
         <p>{{ $message }}</p>
      </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">List of Chapters</h3>
             </div>
            <!-- /.box-header -->

            <div class="box-body table-responsive">
              <table id="chapterlist" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th></th>
                    <th>Email Board</th>
                    <th>State</th>
                    <th>Name</th>
                    <th>EIN</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Primary Coordinator</th>

                  </tr>
                </thead>
                <tbody>
                  @foreach($chapterList as $list)
                  <?php
                      $chapterEmailList = DB::table('board_details as bd')
                                          ->select('bd.email as bor_email')
                                          ->where('bd.chapter_id', '=', $list->id)
                                          ->get();
                      $emailListCord="";
                      foreach($chapterEmailList as $val){
                        $email = $val->bor_email;
                        $escaped_email=str_replace("'", "\\'", $email);
                        if ($emailListCord==""){
                            $emailListCord = $escaped_email;
                        }
                        else{
                            $emailListCord .= ";" . $escaped_email;
                        }
                      }
                      $cc_string="";
                      $reportingList = DB::table('coordinator_reporting_tree')
                                            ->select('*')
                                            ->where('id', '=', $list->primary_coordinator_id)
                                            ->get();
                            foreach($reportingList as $key => $value)
                            {
                                $reportingList[$key] = (array) $value;
                            }
                            $filterReportingList = array_filter($reportingList[0]);
                            unset($filterReportingList['id']);
                            unset($filterReportingList['layer0']);
                            $filterReportingList = array_reverse($filterReportingList);
                            $str = "";
                            $array_rows=count($filterReportingList);
                            $down_line_email="";
                            foreach($filterReportingList as $key =>$val){
                                //if($corId != $val && $val >1){
								if($val >1){
                                    $corList = DB::table('coordinator_details as cd')
                                                    ->select('cd.email as cord_email')
                                                    ->where('cd.coordinator_id', '=', $val)
                                                    ->where('cd.is_active', '=', 1)
                                                    ->get();
                                  if ($down_line_email==""){
                                    if(isset($corList[0]))
                                      $down_line_email = $corList[0]->cord_email;
                                  }
                                  else{
                                    if(isset($corList[0]))
                                      $down_line_email .= ";" . $corList[0]->cord_email;
                                  }

                                }
                            }
                            $cc_string = "?cc=" . $down_line_email;
                  ?>
                    <tr>
					<td><a href="<?php echo url("/chapter/edit/{$list->id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
							</td>
					<?php /*if (Session::get('positionid') <=2){ ?>
                        <td><a href="<?php //echo url("/chapter/edit/{$list->id}")
						echo url("/chapter/edit/{$list->id}") ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
						</td>
						<?php }
						else{
							?>
							<td><a href="<?php //echo url("/chapter/edit/{$list->id}")
							echo url("/chapter/edit/{$list->id}") ?>"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
							</td>
							<?php
						}*/?>
                      <td><a href="mailto:{{ $emailListCord }}{{ $cc_string }}&subject=MOMS Club of {{ $list->name }}, {{ $list->state }}"><i class="fa fa-envelope" aria-hidden="true"></i></a></i></td>
                      <td>{{ $list->state }}</td>
                      <td>{{ $list->name }}</td>
                      <td>{{ $list->ein }}</td>
                      <td>{{ $list->bor_f_name }}</td>
                      <td>{{ $list->bor_l_name }}</td>
                      <td><a href="mailto:{{ $list->bor_email }}">{{ $list->bor_email }}</a></td>
                      <td>{{ $list->phone }}</td>
                      <td>{{ $list->cor_f_name }} {{ $list->cor_l_name }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                    </label>
                  <span> Only show chapters I am primary for</span>
                </div>
              </div>
              </div>
            <div class="box-body text-center">
            <?php if (Session::get('positionid') >=5 && Session::get('positionid') <=7){ ?>
              <a class="btn btn-themeBlue margin" href="{{ route('chapters.create') }}">New Chapter</a>
			<?php }?>
			<?php
			 if($checkBoxStatus){ ?>
				<a href="{{ route('export.chapter',$corId) }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Chapter List</button></a>
			<?php
			 }
			 else{ ?>
				<a href="{{ route('export.chapter','0') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Chapter List</button></a>
			 <?php } ?>

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
  function showPrimary(){
    if($("#showPrimary").prop("checked") == true){
      window.location.href = "/mimi/chapterlist?check=yes";
    }
    else{
      window.location.href = "/mimi/home";
    }
	}
</script>
@endsection
