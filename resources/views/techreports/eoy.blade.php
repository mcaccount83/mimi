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
                              <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  End of Year Procedures
                              </h3>
                              @include('layouts.dropdown_menus.menu_reports_tech')
                          </div>
                      </div>
                      <!-- /.card-header -->
                  <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <h3>Fiscal Year: {{ $admin->fiscal_year }}</h3>
                    </div>
                    <div class="col-md-6">
                        <p><strong>RESET IN DECEMBER.</strong><br>
                            Reset Data to disable all menus and buttons back to *ADMIN* ONLY and prep for New Year before dates auto change to new year on all Buttons/Links/Emails/Forms.</p>
                            <h5>This CANNOT be undone!</h5>
                            <button type="button" id="reset-year" class="btn btn-danger bg-gradient mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset for New Year</button>
                    </div>
                    <div class="col-md-12"><br></div>
                </div>

     <div class="row">
    <div class="col-12">

        <!-- Card 1 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                    <h3 class="card-title">#1 - Reset Tables BEFORE testing  <small><small><span style="color: #007bff;">Complete in Feb/March to prepare for data for testing.</span></small></small></h3><br>
                <br>
                <h5>This CANNOT be undone!</h5>
                <button type="button" id="update-eoy-database" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset EOY Tables Data Tables</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($resetEOYTableItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->reset_eoy_tables == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">#2 - Activate *TESTING* Menus/Buttons/Links BEFORE Testing  <small><small><span style="color: #007bff;">Complete in Feb/March when ready for data for testing.</span></small></small></h3><br>
                <br>
                <button type="button" id="view-eoy-testing" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-eye-fill me-2"></i>Display EOY Testing Items</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($displayTestingItemsItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->display_testing == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">#3 - Reset Tables AFTER testing is complete  <small><small><span style="color: #007bff;">Complete in May, after testing, so all data tables are clean and ready to go.</span></small></small></h3><br>
                <br>
                <button type="button" id="reset-database-after" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Database AFTER Testing</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($resetAFTERtestingItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->reset_AFTER_testing == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">#4 - Copy User Data to New Tables  <small><small><span style="color: #007bff;">Complete in May, before going live to save old board/coordinator/user information.</span></small></small></h3><br>
                <br>
                <h5>This CANNOT be undone!</h5>
                <button type="button" id="update-data-database" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Update User Data Tables</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($updateUserTablesItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->update_user_tables == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">#5 - Unsubscribe from BoardList  <small><small><span style="color: #007bff;">Complete in May, before new board reports are submitted.</span></small></small></h3><br>
                <br>
                <button type="button" id="update-eoy-unsubscribelists" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-eye-fill me-2"></i>Unsubscribe from Lists</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($unSubscribeListItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->unsubscribe_list == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">#6 - Activate Menus/Buttons/Links AFTER testing  <small><small><span style="color: #007bff;">Complete in May, after testing, for live viewing.</span></small></small></h3><br>
                <br>
                <button type="button" id="view-eoy-live" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-eye-fill me-2"></i>Display EOY LIVE Items</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($displayLiveItemsItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->display_live == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Card 7 -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">#7 - Subscribe to BoardList  <small><small><span style="color: #007bff;">Complete in August, after activating boards, so new board members receive subscription to Lists.</span></small></small></h3><br>
                <br>
                <button type="button" id="update-eoy-subscribelists" class="btn btn-primary bg-gradient btn-sm mb-2"><i class="bi bi-eye-fill me-2"></i>Subscribe to Lists</button>
            </div>
            <div class="card-body">
                <p style="font-weight: bold;">The following functions will be performed:</p>
                <div class="row">
                    @foreach($subscribeListItems as $item)
                        <div class="col-md-4">
                            <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                @if($admin->subscribe_list == 1)
                                    <i class="bi bi-check2-square ms-2"></i>{{ $item }}
                                @else
                                    <i class="bi bi-square ms-2"></i>{{ $item }}
                                @endif
                            </li>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
            </div>

                <div class="card-body text-center mt-3">
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

@include('layouts.scripts.masonrygrid')

<script>
$(document).ready(function() {
    var eoyBaseUrl = '{{ route("techreports.updateeoydatabase") }}';  // Route for resetting financial data tables
    var dataBaseUrl = '{{ route("techreports.updatedatadatabase") }}';   // Route for resetting user data tables
    var afterTestingBaseUrl = '{{ route("techreports.updateeoydatabaseafter") }}';  // Route for  resting database AFTER testing to go LIVE
    var resetBaseUrl = '{{ route("techreports.resetyear") }}';  // Route for reseting New Year
    var testingBaseUrl = '{{ route("techreports.updateeoytesting") }}';  // Route for displaying menues/buttons for testers
    var liveBaseUrl = '{{ route("techreports.updateeoylive") }}';  // Route for displaying menues/buttons for allusers
    var subscribeBaseUrl = '{{ route("techreports.updatesubscribelists") }}';  // Route for subscribing users to BoardList
    var unsubscribeBaseUrl = '{{ route("techreports.updateunsubscribelists") }}';  // Route for unsubscribing users to BoardList

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
