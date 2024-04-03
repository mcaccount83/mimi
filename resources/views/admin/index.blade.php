@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Admin
       <small>Dashboard</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Admin Dashboard</li>
      </ol>
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
    {{-- {{ dd($admin) }} --}}


    {{-- @foreach ($admin as $adminItem) --}}

<!-- Main content -->
<section class="content">
        <form method="POST" action='{{ route("admin.update",$admin->id) }}'>
        @csrf

        <div class="row">
          <div class="col-md-12">
            <div class="box card">
              <div class="box-header with-border">
            <h3 class="box-title">End of Year Procedures</h3>
            <h4>Complete and Reset for New Year</h4>
          </div>

              <!-- /.box-header -->

 <!-- /.box-header -->
 <div class="box-body">
    <!-- /.form group -->
    <div class="row">
      <div class="col-md-2">
        <div class="form-group">
    <h4><label>Fiscal Year: {{ $admin->fiscal_year }}</label>
        {{-- <select id="fiscalYearDropdown">
            @foreach ($fiscalYears as $year)
                <option value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select> --}}

    </h4>
    </div>
</div>

        <div class="col-md-10">

        <p><strong>Complete in May/June to prepare for testing and going live with Annual Reports.</strong><br>
            Board Election Report will automatically be available for Chapters on <strong>May 1st</strong>.<br>
                Financial Reports will automatically be available for Chapters on <strong>June 1st</strong>.</p>
                <br>
        </div>
    </div>
</div>
          <div class="grid">
            <div class="row">
                <div class="col-md-12">

            <!-- Grid item -->
            <div class="grid-item col-md-4">
                <div class="box">

                        <h4>Remove/Copy/Reset SQL Database Tables</h4>
                        <p>Check YES when complete.</p>
                        <br>

                        <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Remove data (truncate) from incoming_board_member table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="truncate_incoming" id="" class="ios-switch green bigswitch" {{ $admin->truncate_incoming == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Remove data (truncate) from outgoing_board_member table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="truncate_outgoing" id="" class="ios-switch green bigswitch" {{ $admin->truncate_outgoing == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Copy ending balance from financial_repot table to chapters table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_FRtoCH" id="" class="ios-switch green bigswitch" {{ $admin->copy_FRtoCH == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Copy/Rename Financial Reports Table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_financial" id="" class="ios-switch green bigswitch" {{ $admin->copy_financial == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Insert chapter list and starting balance from chapters table to financial_repot table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_CHtoFR" id="" class="ios-switch green bigswitch" {{ $admin->copy_CHtoFR == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <!-- Grid item -->
 <div class="grid-item col-md-4">
    <div class="box">

            <h4>Activate MIMI Menus and Information for Viewing</h4>
            <p>Check YES to activate the menu and information items for Coordinators.</p>
            <br>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Annual Reports - Display items for Testers</label>
                        <label style="display: block;">
                            <input type="checkbox" name="eoy_testers" id="" class="ios-switch green bigswitch" {{ $admin->eoy_testers == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Annual Reports - Display items for Coordinators</label>
                        <label style="display: block;">
                            <input type="checkbox" name="eoy_coordinators" id="" class="ios-switch green bigswitch" {{ $admin->eoy_coordinators == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
     <!-- Grid item -->
 <div class="grid-item col-md-4">
    <div class="box">

            <h4>Copy Data from SQL Database Tables</h4>
            <p>Check YES when complete.</p>
            <br>

            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Copy/Rename Chapters Table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_chapters" id="" class="ios-switch green bigswitch" {{ $admin->copy_chapters == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Copy/Rename Users Table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_users" id="" class="ios-switch green bigswitch" {{ $admin->copy_users == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Copy/Rename Board Details Table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_boarddetails" id="" class="ios-switch green bigswitch" {{ $admin->copy_boarddetails == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="radio-chk">
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label>Copy/Rename Coordinator Details Table</label>
                        <label style="display: block;">
                            <input type="checkbox" name="copy_coordinatordetails" id="" class="ios-switch green bigswitch" {{ $admin->copy_coordinatordetails == '1' ? 'checked' : '' }} />
                            <div><div></div></div>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
</div>

        <div class="box-body text-center">
            <button type="submit" class="btn btn-themeBlue margin"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save</button>
        <div>
    </div>

    <hr>
    <div id="readOnlyText" class="description text-center">
        <p><span style="color: red;">Resetting for New Year will also change MIMI menus and information items <strong><u>OFF</u></strong> for Coordinators by default.
    </span></p>
    </div>
</form>
<form method="POST" action="{{ route('resetYear') }}">
    @csrf
    <button type="submit" class="btn btn-themeBlue margin"><i class="fa fa-share fa-fw" aria-hidden="true" ></i>&nbsp; Complete | Reset for New Year</button>
</form>

        <!-- /.box-body -->
        </div>
    </div>
</div>
</section>


    <!-- /.content -->

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
