@extends('layouts.mimi_theme')

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
                    <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Download Reports
                    </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
					    <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('chapter', 'Chapter List')"><i class="bi bi-download me-2"></i>Export Chapter List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('zapchapter', 'Zapped Chapter List')"><i class="bi bi-download me-2"></i>Export Zapped Chapter List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('coordinator', 'Coordinator List')"><i class="bi bi-download me-2"></i>Export Coordinator List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('retiredcoordinator', 'Retired Coordinator List')"><i class="bi bi-download me-2"></i>Export Retired Coordinator List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('appreciation', 'Coordinator Appreciation List')"><i class="bi bi-download me-2"></i>Export Coordinator Appriciation List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('chaptercoordinator', 'Chapter/Coordinator List')"><i class="bi bi-download me-2"></i>Export Chapter/Coordinator List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('reregoverdue', 'Overdue Re-Reg List')"><i class="bi bi-download me-2"></i>Export Overdue Re-Reg List</button>
                        </div>
					    <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('einstatus', 'EIN Status List')"><i class="bi bi-download me-2"></i>Export EIN Status List</button>
                        </div>
					    <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('eoystatus', 'EOY Status List')"><i class="bi bi-download me-2"></i>Export EOY Report Status List</button>
                        </div>
                </div>
            </div>
        </div>

            @if ($founderCondition  || $ITCondition)
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Available International Reports</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('intchapter', 'International Chapter List')"><i class="bi bi-download me-2"></i>Export Chapter List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('intzapchapter', 'International Zapped Chapter List')"><i class="bi bi-download me-2"></i>Export Zapped Chapter List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('intcoordinator', 'International Coordinator List')"><i class="bi bi-download me-2"></i>Export Coordinator List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('intretiredcoordinator', 'International Retired Coordinator List')"><i class="bi bi-download me-2"></i>Export Retired Coordinator List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('intreregoverdue', 'International Overdue Re-Reg List')"><i class="bi bi-download me-2"></i>Export Overdue Re-Reg List</button>
                        </div>
					    <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('inteinstatus', 'International EIN Status List')"><i class="bi bi-download me-2"></i>Export EIN Status List</button>
                        </div>
					    <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('inteoystatus', 'International EOY Status List')"><i class="bi bi-download me-2"></i>Export EOY Report Status List</button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-themeBlue margin" onclick="startExport('constantcontact', 'Constant Contact List')"><i class="bi bi-download me-2"></i>Export Constant Contact List</button>
                        </div>
                </div>
           </div>
           </div>
        @endif
          <!-- /.box -->

           </div>
        </div>
    </section>
    <!-- /.content -->

@endsection
