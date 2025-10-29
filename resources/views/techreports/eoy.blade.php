@extends('layouts.coordinator_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'EOY Procedures')

<style>
    .grid {
    display: block; /* Masonry will handle the grid layout */
    width: 100%; /* Ensure grid takes full width of container */
}

.grid-item {
    width: 400px; /* Ensure grid items match the column width in Masonry options */
    margin-bottom: 20px; /* Add bottom margin to avoid overlap */
    box-sizing: border-box; /* Include padding and border in width */
}

.card {
    width: 100%; /* Ensure card takes full width of grid item */
    box-sizing: border-box; /* Include padding and border in width */
}
</style>


@section('content')
<!-- Main content -->
<section class="content">
        <section class="content">
            <div class="container-fluid">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                          <div class="dropdown">
                              <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  End of Year Procedures
                              </h3>
                              @include('layouts.dropdown_menus.menu_reports_tech')
                          </div>
                      </div>
                      <!-- /.card-header -->
                  <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <h4>Fiscal Year: {{ $admin->fiscal_year }}</h4>
                    </div>
                    <div class="col-md-6">
                        <p><strong>RESET IN DECEMBER.</strong><br>
                            Reset Data to disable all menus and buttons back to *ADMIN* ONLY and prep for New Year before dates auto change to new year on all Buttons/Links/Emails/Forms.</p>
                            <h5>This CANNOT be undone!</h5>
                            <button type="button" id="reset-year" class="btn bg-gradient-danger"><i class="fas fa-undo" ></i>&nbsp;&nbsp;&nbsp;Reset for New Year</button>
                    </div>
                    <div class="col-md-12"><br></div>
                </div>

                <div class="row">
                <div class="grid">
                    <!-- Grid item -->
                    <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#1 - Reset Tables BEFORE testing</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in Feb/March to prepare for data for testing.</p>
                                <h5>This CANNOT be undone!</h5>
                                <button type="button" id="update-eoy-database" class="btn bg-gradient-primary mb-3"><i class="fas fa-undo mr-2"></i>Reset EOY Tables Data Tables</button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($resetEOYTableItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->reset_eoy_tables == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                    <!-- Grid item -->
                    <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#2 - Activate *TESTING* Menus/Buttons/Links BEFORE Testing</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in Feb/March when ready for testing.</p>
                                <button type="button" id="view-eoy-testing" class="btn bg-gradient-primary mb-3"><i class="fas fa-eye mr-2"></i>Display EOY Testing Items</button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($displayTestingItemsItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->display_testing == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                    <!-- Grid item -->
                    <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#3 - Reset Tables AFTER testing is complete</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in May, after testing, so all data tables are clean and ready to go.</p>
                                <button type="button" id="reset-database-after" class="btn bg-gradient-primary mb-3"><i class="fas fa-undo mr-2"></i>Reset Database AFTER Testing</button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($resetAFTERtestingItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->reset_AFTER_testing == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}reset_AFTER_testing
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                    <!-- Grid item -->
                    <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#4 - Copy User Data to New Tables</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in May, before going live to save old board/coordinator/user information.</p>
                                <h5>This CANNOT be undone!</h5>
                                <button type="button" id="update-data-database" class="btn bg-gradient-primary mb-3"><i class="fas fa-undo mr-2"></i>Update User Data Tables</button>

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($updateUserTablesItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->update_user_tables == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                    <!-- Grid item -->
                    <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#5 - Unsubscribe from BoardList</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in May, before new board reports are submitted.</p>
                                <button type="button" id="update-eoy-unsubscribelists" class="btn bg-gradient-primary mb-3"><i class="fas fa-eye mr-2"></i>Unsubscribe from Lists</button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($unSubscribeListItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->unsubscribe_list == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                     <!-- Grid item -->
                     <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#6 - Activate Menus/Buttons/Links AFTER testing</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in May, after testing, for live viewing.</p>
                                <button type="button" id="view-eoy-live" class="btn bg-gradient-primary mb-3"><i class="fas fa-eye mr-2"></i>Display EOY LIVE Items</button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($displayLiveItemsItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->display_live == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                    <!-- Grid item -->
                    <div class="grid-item">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">#7 - Subscribe to BoardList</h3><br>
                                <p style="color: #007bff; font-weight: bold;">Complete in Augsut, after activating boards, so new board members recieve subscription to Lists.</p>
                                <button type="button" id="update-eoy-subscribelists" class="btn bg-gradient-primary mb-3"><i class="fas fa-eye mr-2"></i>Subscribe to Lists</button>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <p style="font-weight: bold;">The following functions will be performed:</p>
                                </div>
                                @foreach($subscribeListItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($admin->subscribe_list == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /.grid-item -->

                    </div>
                </div>
            </div>

                <div class="card-body text-center">
                </div>
            </div>

            </form>
        </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</section>

@endsection
@section('customscript')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>

<script>

$(document).ready(function() {
    var elem = document.querySelector('.grid');
    var msnry = new Masonry(elem, {
        itemSelector: '.grid-item',
        columnWidth: 400, // Set a fixed column width (adjust as needed)
        gutter: 20, // Set gutter for spacing between items
        percentPosition: true
    });
});

$(document).ready(function() {
    var eoyBaseUrl = '{{ url("/techreports/updateeoydatabase") }}';  // URL for resetting financial data tables
    var dataBaseUrl = '{{ url("/techreports/updatedatadatabase") }}';   // URL for resetting user data tables
    var afterTestingBaseUrl = '{{ url("/techreports/updateeoydatabaseafter") }}';  // URL for  resting database AFTER testing to go LIVE
    var resetBaseUrl = '{{ url("/techreports/resetyear") }}';  // URL for reseting New Year
    var testingBaseUrl = '{{ url("/techreports/updateeoytesting") }}';  // URL for displaying menues/buttons for testers
    var liveBaseUrl = '{{ url("/techreports/updateeoylive") }}';  // URL for displaying menues/buttons for allusers
    var subscribeBaseUrl = '{{ url("/techreports/updatesubscribelists") }}';  // URL for subscribing users to BoardList
    var unsubscribeBaseUrl = '{{ url("/techreports/updateunsubscribelists") }}';  // URL for unsubscribing users to BoardList

    function handleAjaxRequest(baseUrl) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.success,
                    timer: 2000, // Auto-close after 2 seconds
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Reload AFTER SweetAlert message
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: xhr.responseJSON?.fail || "An unexpected error occurred.",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    // Attach the function to all buttons
    $("#view-eoy-testing").click(function() {
        handleAjaxRequest(testingBaseUrl);
    });

    $("#view-eoy-live").click(function() {
        handleAjaxRequest(liveBaseUrl);
    });

    $("#update-eoy-subscribelists").click(function() {
        handleAjaxRequest(subscribeBaseUrl);
    });

    $("#update-eoy-unsubscribelists").click(function() {
        handleAjaxRequest(unsubscribeBaseUrl);
    });

    $("#update-eoy-database").click(function() {
        handleAjaxRequest(eoyBaseUrl);
    });

    $("#update-data-database").click(function() {
        handleAjaxRequest(dataBaseUrl);
    });

    $("#reset-database-after").click(function() {
        handleAjaxRequest(afterTestingBaseUrl);
    });

    $("#reset-year").click(function() {
        handleAjaxRequest(resetBaseUrl);
    });
});

</script>
@endsection
