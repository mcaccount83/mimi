@extends('layouts.coordinator_theme')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
          <h1>Resources</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Download Reports</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="container-fluid">
        <div class="row">

    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <div class="dropdown">
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Download Reports
                    </h3>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="{{ route('admin.toolkit') }}">Coordinator Toolkit</a>
                        <a class="dropdown-item" href="{{ route('admin.resources') }}">Chapter Resources</a>
                        @if ($assistConferenceCoordinatorCondition)
                        <a class="dropdown-item" href="{{ route('admin.downloads') }}">Download Reports</a>
                        @endif
                        @if ($regionalCoordinatorCondition)
                        <a class="dropdown-item" href="{{ route('admin.bugs') }}">MIMI Bugs & Wishes</a>
                        @endif
                        <a class="dropdown-item" href="https://momsclub.org/elearning/" target="_blank">eLearning</a>
                      </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

					    <div>
                            <a href="{{ route('export.chapter',0)}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.zapchapter')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Zapped Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.coordinator',0)}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.retiredcoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Retired Coordinator List</button></a>
                        </div>
                        <div>
					        <a href="{{ route('export.appreciation')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Coordinator Appriciation List</button></a>
					    </div>
                        <div>
                            <a href="{{ route('export.chaptercoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Chapter/Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.rereg')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Overdue Re-Reg List</button></a>
                        </div>
					    <div>
					        <a href="{{ route('export.einstatus')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export EIN Status List</button></a>
					    </div>
					    <div>
					        <a href="{{ route('export.eoystatus')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export EOY Report Status List</button></a>
					    </div>
					    <div>
					        <a href="{{ route('export.chapteraward',0)}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Chapter Awards List</button></a>
					    </div>


                </div>
            </div>
        </div>

         <?php if($positionId == 8  || $positionId == 3 || $secPositionId == 13){?>
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Available International Reports</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <div>
                            <a href="{{ route('export.intchapter')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intzapchapter')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Zapped Chapter List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intcoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intretcoordinator')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Retired Coordinator List</button></a>
                        </div>
                        <div>
                            <a href="{{ route('export.intrereg')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export Overdue Re-Reg List</button></a>
                        </div>
					    <div>
					        <a href="{{ route('export.inteinstatus')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export EIN Status List</button></a>
					    </div>
					    <div>
					        <a href="{{ route('export.inteoystatus')}}"><button class="btn btn-themeBlue margin"><i class="fas fa-download"></i>&nbsp; Export EOY Report Status List</button></a>
					    </div>
                </div>
    <?php } ?>
           </div>
           </div>
          <!-- /.box -->

           </div>
        </div>
    </section>
    <!-- /.content -->

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

</script>


@endsection