@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Chapter Awards Report
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter Awards Report</li>
      </ol>
    </section>
        	 @if ($message = Session::get('success'))
      <div class="alert alert-success">
         <p>{{ $message }}</p>
      </div>
    @endif
     @if ($message = Session::get('fail'))
      <div class="alert alert-danger">
         <p>{{ $message }}</p>
      </div>
    @endif
    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Report of Chapter Awards</h3>
            </div>
            <!-- /.box-header -->
            
            <div class="box-body table-responsive">
              <table id="chapterlist_zapped" class="table table-bordered table-hover">
              <thead> 
			    <tr>
			        <th></th>
					<th>State</th>
                  <th>Name</th>
                 <th>Award</th>
				 <th>Approved</th>
                </tr>
                </thead>
                <tbody>
                    
                    <tr>
                <?php
//                 echo "<pre>";
// 			print_r($chapter_array);die;
                      
					$row_count=count($chapter_array);
						
						for ($row = 0; $row < $row_count; $row++){
							//if((!$only_show_primary) || ($chapter_array[$row]['reviewer_id']==$_SESSION['userid'])){
								for($award=1;$award<=5;$award++){
									if($chapter_array[$row]['award_' . $award . '_type']>0){
                                        echo " <td><a href='/mimi/chapter/awardsview/".$chapter_array[$row]['id']."'><i class='fa fa-pencil-square' aria-hidden='true'></i></a></td> \n";
										echo " <td>" . $chapter_array[$row]['state'] . "</td> \n";
										echo " <td>" . $chapter_array[$row]['name'] . "</td> \n";
										

										switch ($chapter_array[$row]['award_' . $award . '_type']) {
											case 1:
												echo " <td>Outstanding Specific Service Project</td> \n";
												break;
											case 2:
												echo " <td>Outstanding Overall Service Program</td> \n";
												break;
											case 3:
												echo " <td>Outstanding Children's Activity</td> \n";
												break;
											case 4:
												echo " <td>Outstanding Spirit</td> \n";
												break;
											case 5:
												echo " <td>Outstanding Chapter</td> \n";
												break;
											case 6:
												echo " <td>Outstanding New Chapter</td> \n";
												break;
											case 7:
												echo " <td>Other Outstanding Award</td> \n";
												break;
										}										if($chapter_array[$row]['award_' . $award . '_approved'])
											echo " <td>YES</td> \n";
										else
											echo " <td bgcolor=\"#FF0000\">NO</td> \n";						
										
										echo "</tr>";
									}
								}
							//}
						}
				  ?>
                        
                  </tbody>
                </table>
				 <div class="radio-chk labelcheck">
              <div class="col-sm-6 col-xs-12">
                <div class="form-group">
                    <label style="display: block;"><input type="checkbox" name="showPrimary" id="showPrimary" class="ios-switch green bigswitch" {{$checkBoxStatus}} onchange="showPrimary()" /><div><div></div></div>
                    
                  </label>
                  <span> Only show chapters I am Primary Reviewer for</span>
                </div>
              </div>
              </div>
			  
			   <div class="box-body text-center">
                          <a class="btn btn-themeBlue margin" href="{{ route('report.addawards') }}">Add Award</a>

			  <?php
			 if($checkBoxStatus){ ?>
				<a href="{{ route('export.chapteraward',$corId) }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Award List</button></a>
			<?php 
			 }
			 else{ ?>
				<a href="{{ route('export.chapteraward','0') }}"><button class="btn btn-themeBlue margin" <?php if($countList ==0) echo "disabled";?>>Export Award List</button></a>
			 <?php } ?>
			  
			  
			  
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
  function showPrimary(){
    if($("#showPrimary").prop("checked") == true){
      window.location.href = "/mimi/yearreports/chapterawards?check=yes";
    }
    else{
      window.location.href = "/mimi/yearreports/chapterawards";
    }
	}
</script>
@endsection
