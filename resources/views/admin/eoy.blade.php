@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>EOY Procedures&nbsp;<small>(Complete for New Year Changeover/Reset)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">EOY Procedures</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
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
        <form method="POST" action='{{ route("admin.eoyupdate",$admin->id) }}'>
        @csrf
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <h4>Fiscal Year: {{ $admin->fiscal_year }}</h4>
                    </div>
                    <div class="col-md-10">
                        <p><strong>Complete in April/May to prepare for testing and going live with Annual Reports.</strong><br>
                            Board Election Report will automatically be available for Chapters on <strong>May 1st</strong> when Report is activated below.<br>
                                Financial Reports will automatically be available for Chapters on <strong>June 1st</strong> when Report is activated below.</p>
                                <br>
                    </div>

            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Remove/Copy/Reset SQL Database Tables</h3><br>
                        <p style="color: #007bff">Check when complete.</p>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="truncate_incoming" id="truncate_incoming" class="custom-control-input" {{$admin->truncate_incoming ? 'checked' : '' }} />
                                <label class="custom-control-label" for="truncate_incoming">Remove data (truncate) from incoming_board_member table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="truncate_outgoing" id="truncate_outgoing" class="custom-control-input" {{$admin->truncate_outgoing ? 'checked' : '' }} />
                                <label class="custom-control-label" for="truncate_outgoing">Remove data (truncate) from outgoing_board_member table</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_FRtoCH" id="copy_FRtoCH" class="custom-control-input" {{$admin->copy_FRtoCH ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_FRtoCH">Copy ending balance from financial_repot table to chapters table</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_financial" id="copy_financial" class="custom-control-input" {{$admin->copy_financial ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_financial">Copy/Rename Financial Reports Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_CHtoFR" id="copy_CHtoFR" class="custom-control-input" {{$admin->copy_CHtoFR ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_CHtoFR">Insert chapter list and starting balance from chapters table to financial_repot table</label>
                                </div>
                            </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Activate MIMI Menus and Information for Viewing</h3><br>
                        <p style="color: #007bff">Check to activate the menu and information items for Coordinators.</p>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_testers" id="eoy_testers" class="custom-control-input" {{$admin->eoy_testers ? 'checked' : '' }} />
                                <label class="custom-control-label" for="eoy_testers">Annual Reports - Display items for Testers</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_coordinators" id="eoy_coordinators" class="custom-control-input" {{$admin->eoy_coordinators ? 'checked' : '' }} />
                                <label class="custom-control-label" for="eoy_coordinators">Annual Reports - Display items for Coordinators</label>
                                </div>
                            </div>
                </div>
                </div>

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Activate Board and Financial Report Buttons for Viewing</h3><br>
                        <p style="color: #007bff">Check to activate the buttons/links for Board Members.</p>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_boardreport" id="eoy_boardreport" class="custom-control-input" {{$admin->eoy_boardreport ? 'checked' : '' }} />
                                <label class="custom-control-label" for="eoy_boardreport">Board Election Report for Chapters</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_financialreport" id="eoy_financialreport" class="custom-control-input" {{$admin->eoy_financialreport ? 'checked' : '' }} />
                                <label class="custom-control-label" for="eoy_financialreport">Financial Report for Chapters</label>
                                </div>
                            </div>
                </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Copy Data from SQL Database Tables</h3><br>
                        <p style="color: #007bff">Check when complete.</p>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_chapters" id="copy_chapters" class="custom-control-input" {{$admin->copy_chapters ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_chapters">Copy/Rename Chapters Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_users" id="copy_users" class="custom-control-input" {{$admin->copy_users ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_users">Copy/Rename Users Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_boarddetails" id="copy_boarddetails" class="custom-control-input" {{$admin->copy_boarddetails ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_boarddetails">Copy/Rename Board Details Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_coordinatordetails" id="copy_coordinatordetails" class="custom-control-input" {{$admin->copy_coordinatordetails ? 'checked' : '' }} />
                                <label class="custom-control-label" for="copy_coordinatordetails">Copy/Rename Coordinator Details Table</label>
                                </div>
                            </div>
                    </div>
                </div>
            </div>

                <div class="card-body text-center">
                        <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button>
                </div>
            </div>
        </form>

            <div class="card-body text-center">
                <div id="readOnlyText" class="description text-center">
                    <p>Resetting for New Year will also turn MIMI menus and information items <strong><u>OFF</u></strong> for Coordinators by default.<br>
                        Resetting for New Year will also turn Board Election and Financial Report buttons <strong><u>OFF</u></strong> for Chapters by default.
                </p>
                <h4>This CANNOT be undone!</h4>
                </div>
            <form method="POST" action="{{ route('resetYear') }}">
                @csrf
                <button type="submit" class="btn bg-gradient-warning"><i class="fas fa-share" ></i>&nbsp;&nbsp;&nbsp;Complete | Reset for New Year</button>
            </form>
        </div>
        <!-- /.box-body -->
        </div>
    </div>
</div>
</section>

@endsection

<script>
    // Function to fetch and update admin data based on selected fiscal year
    function updateAdminData(selectedYear) {
        // Make an AJAX request to fetch data for the selected fiscal year
        $.ajax({
            url: '/admin-data', // Update URL to your endpoint that fetches data
            method: 'GET',
            data: {
                fiscalYear: selectedYear
            },
            success: function(response) {
                // Update the HTML content with the fetched data
                $('#adminData').html(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Event listener for dropdown change
    $('#fiscalYearDropdown').change(function() {
        var selectedYear = $(this).val();
        updateAdminData(selectedYear);
    });

    // Initial load
    $(document).ready(function() {
        var selectedYear = $('#fiscalYearDropdown').val();
        updateAdminData(selectedYear);
    });
</script>
