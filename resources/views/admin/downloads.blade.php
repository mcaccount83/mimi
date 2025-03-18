@extends('layouts.coordinator_theme')

@section('page_title', 'Resources')
@section('breadcrumb', 'Download Reports')

@section('content')
  <!-- Main content -->
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
                    @include('layouts.dropdown_menus.menu_resources')
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
					    <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('chapter', 'Chapter List')"><i class="fas fa-download"></i>&nbsp; Export Chapter List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('zapchapter', 'Zapped Chapter List')"><i class="fas fa-download"></i>&nbsp; Export Zapped Chapter List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('coordinator', 'Coordinator List')"><i class="fas fa-download"></i>&nbsp; Export Coordinator List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('retiredcoordinator', 'Retired Coordinator List')"><i class="fas fa-download"></i>&nbsp; Export Retired Coordinator List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('appreciation', 'Coordinator Appreciation List')"><i class="fas fa-download"></i>&nbsp; Export Coordinator Appriciation List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('chaptercoordinator', 'Chapter/Coordinator List')"><i class="fas fa-download"></i>&nbsp; Export Chapter/Coordinator List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('reregoverdue', 'Overdue Re-Reg List')"><i class="fas fa-download"></i>&nbsp; Export Overdue Re-Reg List</button>
                        </div>
					    <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('einstatus', 'EIN Status List')"><i class="fas fa-download"></i>&nbsp; Export EIN Status List</button>
                        </div>
					    <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('eoystatus', 'EOY Status List')"><i class="fas fa-download"></i>&nbsp; Export EOY Report Status List</button>
                        </div>
					    <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('chapterawards', 'Chapter Awards List')"><i class="fas fa-download"></i>&nbsp; Export Chapter Awards List</button>
                        </div>
                </div>
            </div>
        </div>

            <?php if($founderCondition  || $ITCondition){?>
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Available International Reports</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('intchapter', 'International Chapter List')"><i class="fas fa-download"></i>&nbsp; Export Chapter List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('intzapchapter', 'International Zapped Chapter List')"><i class="fas fa-download"></i>&nbsp; Export Zapped Chapter List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('intcoordinator', 'International Coordinator List')"><i class="fas fa-download"></i>&nbsp; Export Coordinator List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('intretiredcoordinator', 'International Retired Coordinator List')"><i class="fas fa-download"></i>&nbsp; Export Retired Coordinator List</button>
                        </div>
                        <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('intreregoverdue', 'International Overdue Re-Reg List')"><i class="fas fa-download"></i>&nbsp; Export Overdue Re-Reg List</button>
                        </div>
					    <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('inteinstatus', 'International EIN Status List')"><i class="fas fa-download"></i>&nbsp; Export EIN Status List</button>
                        </div>
					    <div>
                            <button class="btn btn-themeBlue margin" onclick="startExport('inteoystatus', 'International EOY Status List')"><i class="fas fa-download"></i>&nbsp; Export EOY Report Status List</button>
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
