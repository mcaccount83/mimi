@extends('layouts.mimi_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'EOY Procedures')

<style>
/* Completed accordion step - AdminLTE success */
.accordion-item.step-complete > h2 .accordion-button {
    background-color: var(--bs-success) !important;
    color: white !important;
}

.accordion-item.step-complete > h2 .accordion-button::after {
    filter: brightness(0) invert(1);
}

.accordion-item.step-complete {
    border-color: var(--bs-success);
}

/* In-progress accordion step - AdminLTE warning */
.accordion-item.step-inprogress > h2 .accordion-button {
    background-color: var(--bs-warning) !important;
    color: var(--bs-dark) !important;  /* dark text for yellow contrast */
}

.accordion-item.step-inprogress > h2 .accordion-button::after {
    filter: none;  /* keep arrow dark on yellow */
}

.accordion-item.step-inprogress {
    border-color: var(--bs-warning);
}

.accordion-item.fiscalyear .accordion-button::after {
    display: none;
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



                    <div class="card-header p-2">
                {{-- Tab Headers --}}
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#admin" data-bs-toggle="tab">ADMIN PROCEDURES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#report" data-bs-toggle="tab">REPORT PROCEDURES</a>
                    </li>
                </ul>
                </div>
                {{-- Tab Content --}}
                <div class="card-body">
                <div class="tab-content">

                    {{-- Admin Tab --}}
                    <div class="active tab-pane" id="admin">
                        <div class="card-header bg-transparent border-0">
                            <h3>Fiscal Year: {{ $fiscalYear }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <div class="row">
                                @include('coordinators.partials.eoy_adminprocedures_accordion')
                            </div>
                        </div>
                    </div>

                    {{-- EOY Tab --}}
                        <div class="tab-pane" id="report">
                            <div class="card-header bg-transparent border-0">
                                <h3>Report Year: {{ $fiscalYearEOY }}</h3>
                            </div>
                        <!-- /.card-header -->
                            <div class="card-body">

                        <div class="row">
                            @include('coordinators.partials.eoy_reportprocedures_accordion')
                        </div>
                                </div>
                            </div>
                        </div>

                </div>

            </div>


                {{-- <div class="row mb-3">
                    <div class="col-md-12">
                        <h3>Admin Procedures</h3>
                            <h4>Fiscal Year: {{ $fiscalYear }}</h4>
                    </div>
                </div>

                <div class="row">
                    @include('coordinators.partials.eoy_adminprocedures_accordion')
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h3>Report Procedures</h3>
                            <h4>Fiscal Year EOY: {{ $fiscalYearEOY }}</h4>
                    </div>

                </div>

     <div class="row">
        @include('coordinators.partials.eoy_reportprocedures_accordion')
     </div> --}}
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

<script>
$(document).on('click', '#view-reportprocedures', function() {
    // Deactivate all tabs and panes
    $('.nav-pills .nav-link').removeClass('active');
    $('.tab-pane').removeClass('active show');

    // Activate the report tab and pane
    $('[href="#report"]').addClass('active');
    $('#report').addClass('active show');
});

$(document).ready(function() {
    var fiscalYearBaseUrl = '{{ route("techreports.resetyear") }}';  // Route for reseting New Year
    var subscribeBaseUrl = '{{ route("techreports.updatesubscribelists") }}';  // Route for subscribing users to BoardList
    var irsSeptBaseUrl = '{{ route("techreports.updateirssept") }}';
    var irsDecBaseUrl = '{{ route("techreports.updateirsdec") }}';
    var irsSubordinateBaseUrl = '{{ route("techreports.updateirssubordinate") }}';
    var irsJuneBaseUrl = '{{ route("techreports.updateirsjune") }}';
    var unsubscribeBaseUrl = '{{ route("techreports.updateunsubscribelists") }}';  // Route for unsubscribing users to BoardList

    var reportYearBaseUrl = '{{ route("techreports.resetyeareoy") }}';  // Route for reseting EOY Year
    var eoyBaseUrl = '{{ route("techreports.updateeoydatabase") }}';  // Route for resetting financial data tables
    var dataBaseUrl = '{{ route("techreports.updatedatadatabase") }}';   // Route for resetting user data tables
    var testingBaseUrl = '{{ route("techreports.updateeoytesting") }}';  // Route for displaying menues/buttons for testers
    var afterTestingBaseUrl = '{{ route("techreports.updateeoydatabaseafter") }}';  // Route for  resting database AFTER testing to go LIVE
    var liveBaseUrl = '{{ route("techreports.updateeoylive") }}';  // Route for displaying menues/buttons for allusers


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
    $("#reset-year").click(function() {
        handleAjaxRequest(fiscalYearBaseUrl);
    });

    $("#update-eoy-subscribelists").click(function() {
        handleAjaxRequest(subscribeBaseUrl);
    });

    $("#update-eoy-irssept").click(function() {
        handleAjaxRequest(irsSeptBaseUrl);
    });

    $("#update-eoy-irsdec").click(function() {
        handleAjaxRequest(irsDecBaseUrl);
    });

    $("#update-eoy-irssubordinate").click(function() {
        handleAjaxRequest(irsSubordinateBaseUrl);
    });

    $("#update-eoy-irsjune").click(function() {
        handleAjaxRequest(irsJuneBaseUrl);
    });


    $("#update-eoy-unsubscribelists").click(function() {
        handleAjaxRequest(unsubscribeBaseUrl);
    });

     $("#reset-yeareoy").click(function() {
        handleAjaxRequest(reportYearBaseUrl);
    });



    $("#view-eoy-testing").click(function() {
        handleAjaxRequest(testingBaseUrl);
    });

    $("#view-eoy-live").click(function() {
        handleAjaxRequest(liveBaseUrl);
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




});

</script>
@endsection
