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
{{--
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
    @endif --}}

<!-- Main content -->
<section class="content">
        {{-- <form method="POST" action='{{ route("admin.eoyupdate",$admin->id) }}'>
        @csrf --}}
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <h4>Fiscal Year: {{ $admin->fiscal_year }}</h4>
                    </div>
                    <div class="col-md-10">
                        <p><strong>Reset in December.</strong><br>
                            Reset Data to disable all menus and buttons and set for New Year before dates auto change to new year on all items.</p>
                            <h5>This CANNOT be undone!</h5>
                            <button type="button" id="reset-year" class="btn bg-gradient-danger"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Reset for New Year</button>

                    </div>

                    <div class="col-sm-12"><br></div>


            <div class="col-md-3">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Remove/Copy/Reset SQL Database Tables</h3><br>
                        {{-- <p style="color: #007bff">Check when complete.</p> --}}
                        <p style="color: #007bff; font-weight: bold;">Complete in Feb/March to prepare for Testing.</p>
                        <h5>This CANNOT be undone!</h5>
                        <button type="button" id="update-eoy-database" class="btn bg-gradient-primary"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Reset Financial Data Tables</button>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <p style="font-weight: bold;">The following functions will be performed:</p>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="truncate_incoming" id="truncate_incoming" class="custom-control-input" {{$admin->truncate_incoming ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="truncate_incoming">Remove data (truncate) from incoming_board_member table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="truncate_outgoing" id="truncate_outgoing" class="custom-control-input" {{$admin->truncate_outgoing ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="truncate_outgoing">Remove data (truncate) from outgoing_board_member table</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_FRtoCH" id="copy_FRtoCH" class="custom-control-input" {{$admin->copy_FRtoCH ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_FRtoCH">Copy ending balance from financial_repot table to chapters table</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_financial" id="copy_financial" class="custom-control-input" {{$admin->copy_financial ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_financial">Copy/Rename Financial Reports Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_CHtoFR" id="copy_CHtoFR" class="custom-control-input" {{$admin->copy_CHtoFR ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_CHtoFR">Insert chapter list and starting balance from chapters table to financial_repot table</label>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="copy_BDtoOUT" id="copy_BDtoOUT" class="custom-control-input" {{$admin->copy_BDtoOUT ? 'checked' : '' }} disabled />
                                    <label class="custom-control-label" for="copy_BDtoOUT">Copy Active Board Details to Outgoing Board Member Table</label>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Activate MIMI Menus and Information for Viewing</h3><br>
                        {{-- <p style="color: #007bff">Check to activate the menu and information items for Coordinators.</p> --}}
                        <p style="color: #007bff; font-weight: bold;">Complete in Feb/March when ready for Testing.</p>
                        <button type="button" id="update-eoy-coordinator" class="btn bg-gradient-primary"><i class="fas fa-eye" ></i>&nbsp;&nbsp;&nbsp;Display EOY Coordinator Menu</button>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <p style="font-weight: bold;">The following functions will be performed:</p>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_testers" id="eoy_testers" class="custom-control-input" {{$admin->eoy_testers ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="eoy_testers">Report Menu for Testers- menu will be available when activated.</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_coordinators" id="eoy_coordinators" class="custom-control-input" {{$admin->eoy_coordinators ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="eoy_coordinators">Report Menu for Coordinators - menu will be available after May 1st.</label>
                                </div>
                            </div>
                </div>
                </div>
            </div>

            <div class="col-md-3">

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Activate Board and Financial Report Buttons for Viewing</h3><br>
                        {{-- <p style="color: #007bff">Check to activate the buttons/links for Board Members.</p> --}}
                        <p style="color: #007bff; font-weight: bold;">Complete in May, after testubg is complete.</p>
                        <button type="button" id="update-eoy-chapter" class="btn bg-gradient-primary"><i class="fas fa-eye" ></i>&nbsp;&nbsp;&nbsp;Display EOY Chapter Buttons</button>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <p style="font-weight: bold;">The following functions will be performed:</p>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_boardreport" id="eoy_boardreport" class="custom-control-input" {{$admin->eoy_boardreport ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="eoy_boardreport">Board Election Report for Chapters - button will be available after May 1st.</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="eoy_financialreport" id="eoy_financialreport" class="custom-control-input" {{$admin->eoy_financialreport ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="eoy_financialreport">Financial Report for Chapters - button will be available after June 1st.</label>
                                </div>
                            </div>
                </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Copy Data from SQL Database Tables</h3><br>
                        {{-- <p style="color: #007bff">Check when complete.</p> --}}
                        <p style="color: #007bff; font-weight: bold;">Complete in May.</p>
                        <h5>This CANNOT be undone!</h5>
                        <button type="button" id="update-data-database" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save User Data Tables</button>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-sm-12">
                            <p style="font-weight: bold;">The following functions will be performed:</p>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_chapters" id="copy_chapters" class="custom-control-input" {{$admin->copy_chapters ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_chapters">Copy/Rename Chapters Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_users" id="copy_users" class="custom-control-input" {{$admin->copy_users ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_users">Copy/Rename Users Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_boarddetails" id="copy_boarddetails" class="custom-control-input" {{$admin->copy_boarddetails ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_boarddetails">Copy/Rename Board Details Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="copy_coordinatordetails" id="copy_coordinatordetails" class="custom-control-input" {{$admin->copy_coordinatordetails ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="copy_coordinatordetails">Copy/Rename Coordinator Details Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="delete_outgoing" id="delete_outgoing" class="custom-control-input" {{$admin->delete_outgoing ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="delete_outgoing">Delete Outgoing Board Member Table</label>
                                </div>
                            </div>
                        <div class="col-sm-12">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="outgoing_inactive" id="outgoing_inactive" class="custom-control-input" {{$admin->outgoing_inactive ? 'checked' : '' }} disabled />
                                <label class="custom-control-label" for="outgoing_inactive">Make Outgoing Users Inactive</label>
                                </div>
                            </div>
                </div>
                </div>
            </div>

                <div class="card-body text-center">
                        {{-- <button type="submit" class="btn bg-gradient-primary"><i class="fas fa-save" ></i>&nbsp;&nbsp;&nbsp;Save</button> --}}
                </div>
            </div>
        {{-- </form> --}}

            {{-- <div class="card-body text-center">
                <div id="readOnlyText" class="description text-center">
                    <p>Resetting for New Year will also turn MIMI menus and information items <strong><u>OFF</u></strong> for Coordinators by default.<br>
                        Resetting for New Year will also turn Board Election and Financial Report buttons <strong><u>OFF</u></strong> for Chapters by default.
                </p>
                <h4>This CANNOT be undone!</h4>
                </div> --}}
            {{-- <form method="POST" action="{{ route('resetYear') }}">
                @csrf
                <button type="submit" class="btn bg-gradient-danger"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Reset for New Year</button> --}}
            </form>
        </div>
        <!-- /.box-body -->
        {{-- <button type="button" id="update-eoy-database" class="btn bg-gradient-primary"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Reset Financial Data Tables</button>
        <button type="button" id="update-data-database" class="btn bg-gradient-primary"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Reset User Data Tables</button>
        <button type="button" id="update-eoy-coordinator" class="btn bg-gradient-primary"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Display EOY Coordinator Menu</button>
        <button type="button" id="update-eoy-chapter" class="btn bg-gradient-primary"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Display EOY Chapter Buttons</button> --}}
        </div>
    </div>
</div>
</section>

@endsection
@section('customscript')
<script>
$(document).ready(function() {
    // URL for resetting financial data tables
    var eoyBaseUrl = '{{ url("/admin/updateeoydatabase") }}';
    // URL for resetting user data tables
    var dataBaseUrl = '{{ url("/admin/updatedatadatabase") }}';
    // URL for  displaying coordinator menu
    var coordinatorBaseUrl = '{{ url("/admin/updateeoycoordinator") }}';
    // URL for  displaying chapter buttons
    var chapterBaseUrl = '{{ url("/admin/updateeoychapter") }}';
     // URL for reseting New Year
     var resetBaseUrl = '{{ url("/admin/resetyear") }}';

    // Handle click event for the 'Reset Financial Data Tables' button
    $("#update-eoy-database").click(function() {
        $.ajax({
            url: eoyBaseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Include CSRF token for security
            },
            success: function(response) {
                // Reload the page to show the session flash message (success or error)
                location.reload();
            },
            error: function(response) {
                // Reload the page even on error to display session flash message
                location.reload();
            }
        });
});


    // Handle click event for the 'Reset User Data Tables' button
    $("#update-data-database").click(function() {
        $.ajax({
            url: dataBaseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Include CSRF token for security
            },
            success: function(response) {
                // Reload the page to show the session flash message (success or error)
                location.reload();
            },
            error: function(response) {
                // Reload the page even on error to display session flash message
                location.reload();
            }
        });
    });

     // Handle click event for the 'Display Coordinator Menu' button
     $("#update-eoy-coordinator").click(function() {
        $.ajax({
            url: coordinatorBaseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Include CSRF token for security
            },
            success: function(response) {
                // Reload the page to show the session flash message (success or error)
                location.reload();
            },
            error: function(response) {
                // Reload the page even on error to display session flash message
                location.reload();
            }
        });
    });

     // Handle click event for the 'Display Chapter Buttons' button
     $("#update-eoy-chapter").click(function() {
        $.ajax({
            url: chapterBaseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Include CSRF token for security
            },
            success: function(response) {
                // Reload the page to show the session flash message (success or error)
                location.reload();
            },
            error: function(response) {
                // Reload the page even on error to display session flash message
                location.reload();
            }
        });
    });

    $("#reset-year").click(function() {
        $.ajax({
            url: resetBaseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Include CSRF token for security
            },
            success: function(response) {
                // Reload the page to show the session flash message (success or error)
                location.reload();
            },
            error: function(response) {
                // Reload the page even on error to display session flash message
                location.reload();
            }
        });
    });
});

    // // Function to fetch and update admin data based on selected fiscal year
    // function updateAdminData(selectedYear) {
    //     // Make an AJAX request to fetch data for the selected fiscal year
    //     $.ajax({
    //         url: '/admin-data', // Update URL to your endpoint that fetches data
    //         method: 'GET',
    //         data: {
    //             fiscalYear: selectedYear
    //         },
    //         success: function(response) {
    //             // Update the HTML content with the fetched data
    //             $('#adminData').html(response);
    //         },
    //         error: function(xhr, status, error) {
    //             console.error(error);
    //         }
    //     });
    // }

    // // Event listener for dropdown change
    // $('#fiscalYearDropdown').change(function() {
    //     var selectedYear = $(this).val();
    //     updateAdminData(selectedYear);
    // });

    // // Initial load
    // $(document).ready(function() {
    //     var selectedYear = $('#fiscalYearDropdown').val();
    //     updateAdminData(selectedYear);
    // });
</script>
@endsection
