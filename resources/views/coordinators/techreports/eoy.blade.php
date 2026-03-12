@extends('layouts.mimi_theme')

@section('page_title', 'IT Reports')
@section('breadcrumb', 'EOY Procedures')

<style>

/* Completed accordion step - green header */
.accordion-item.step-complete > h2 .accordion-button {
    background-color: #198754 !important;  /* Bootstrap success green */
    color: white !important;
}

/* The collapse arrow icon also needs to be white when green */
.accordion-item.step-complete > h2 .accordion-button::after {
    filter: brightness(0) invert(1);
}

/* Optional: subtle green tint on the accordion-item border too */
.accordion-item.step-complete {
    border-color: #198754;
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
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h3>Fiscal Year: {{ $fiscalYear }}</h3>
                    </div>

                    <div class="col-md-12">
                        <h3>Fiscal Year EOY: {{ $fiscalYearEOY }}</h3>
                    </div>

                </div>

     <div class="row">
        @include('coordinators.partials.eoyprocedures_accordion')
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

<script>
$(document).ready(function() {
    var eoyBaseUrl = '{{ route("techreports.updateeoydatabase") }}';  // Route for resetting financial data tables
    var dataBaseUrl = '{{ route("techreports.updatedatadatabase") }}';   // Route for resetting user data tables
    var afterTestingBaseUrl = '{{ route("techreports.updateeoydatabaseafter") }}';  // Route for  resting database AFTER testing to go LIVE
    var resetBaseUrl = '{{ route("techreports.resetyear") }}';  // Route for reseting New Year
    var yearBaseUrl = '{{ route("techreports.resetyeareoy") }}';  // Route for reseting EOY Year
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

     $("#reset-yeareoy").click(function() {
        handleAjaxRequest(yearBaseUrl);
    });
});

</script>
@endsection
