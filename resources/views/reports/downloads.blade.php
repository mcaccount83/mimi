@extends('layouts.coordinator_theme')

@section('content')
 <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Downloads List
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Downloads List</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
		<div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Download Reports</h3>
            </div>
            <!-- /.box-header -->
            <div class="col-md-6">
        <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Available Conference Reports</h3>
              </div>

					    <div>
                            <a href="{{ route('export.chapter',0)}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.zapchapter')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Zapped Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.coordinator',0)}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.retiredcoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Retired Coordinator List</button></a>
                        </div>
                        <div>
					        <a href="{{ route('export.appreciation')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Coordinator Appriciation List</button></a>
					    </div>
                        <div>
                            <a href="{{ route('export.chaptercoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Chapter/Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.rereg')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Overdue Re-Reg List</button></a>
                        </div>
					    <div>
					        <a href="{{ route('export.einstatus')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export EIN Status List</button></a>
					    </div>
					    <div>
					        <a href="{{ route('export.eoystatus')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export EOY Report Status List</button></a>
					    </div>
					    <div>
					        <a href="{{ route('export.chapteraward',0)}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Chapter Awards List</button></a>
					    </div>


                </div>

    <?php if($positionId == 7  || $positionId == 3 || $secPositionId == 13){?>
<div class="col-md-6">
            <div class="box-header with-border mrg-t-10">
                <h3 class="box-title">Available International Reports</h3>
              </div>

					    <div>
                            <a href="{{ route('export.intchapter')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intzapchapter')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Zapped Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intcoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intretcoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Retired Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intrereg')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export Overdue Re-Reg List</button></a>
                        </div>
					    <div>
					        <a href="{{ route('export.inteinstatus')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export EIN Status List</button></a>
					    </div>
					    <div>
					        <a href="{{ route('export.inteoystatus')}}"><button class="btn btn-themeBlue margin"><i class="fa fa-download fa-fw" aria-hidden="true" ></i>&nbsp; Export EOY Report Status List</button></a>
					    </div>

                </div>
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

@endsection
