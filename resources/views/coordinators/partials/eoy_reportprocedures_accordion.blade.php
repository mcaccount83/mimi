<div class="container-fluid">
    <div class="accordion"  id="accordionReport"  style="column-count: 2; column-gap: 1rem;">

<!------Start Step 1 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{ $reportYear->reset_report_year == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-reset-yeareoy">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseReportOne"
                    aria-expanded="false" aria-controls="collapseReportOne">
                #1 - Reset EOY/Report Year - JAN
            </button>
        </h2>
        <div id="collapseReportOne" class="accordion-collapse collapse" data-bs-parent="#accordionReport">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            To be used for all End of Year buttons/links/emails/forms.<br>
            @if ($reportYear->reset_report_year != 1 && ($currentMonth >= 1 && $currentMonth <= 3))
                <button type="button" id="reset-yeareoy" class="btn btn-danger bg-gradient mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year EOY</button>
            @else
                <button type="button" id="reset-yeareoy" class="btn btn-danger bg-gradient mb-2" disabled><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year EOY</button>
            @endif
        </div>
</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
<!------End Step 1 ------>

<!------Start Step 2 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$reportYear->reset_eoy_tables == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-eoy-tables">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseReportTwo"
                    aria-expanded="false" aria-controls="collapseReportTwo">
                #2 - *TESTING* Reset Tables for Testing - FEB/MAR
            </button>
        </h2>
        <div id="collapseReportTwo" class="accordion-collapse collapse" data-bs-parent="#accordionReport">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Complete in Feb/March to prepare for data for testing.<br>
            @if ($reportYear->reset_year == 1 && $reportYear->reset_eoy_tables != 1 && ($currentMonth >= 1 && $currentMonth <= 5))
                                <button type="button" id="update-eoy-database" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset EOY Tables Data Tables</button>
                @else
                                <button type="button" id="update-eoy-database" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-arrow-counterclockwise me-2"></i>Reset EOY Tables Data Tables</button>
                @endif
                     <p style="font-weight: bold;">The following functions will be performed:</p>
                     @foreach($resetEOYTableItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($reportYear->reset_eoy_tables == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                </div>
</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
<!------End Step 2 ------>

<!------Start Step 3 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$reportYear->display_testing == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-display-testing">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseReportThree"
                    aria-expanded="false" aria-controls="collapseReportThree">
                #3 - *TESTING* Activate Menus/Buttons/Links for Testing - FEB/MAR
            </button>
        </h2>
        <div id="collapseReportThree" class="accordion-collapse collapse" data-bs-parent="#accordionReport">
            <div class="accordion-body">
    <section>
            <div class="col-md-12">
            Complete in Feb/March when ready for data for testing.<br>
                @if ($reportYear->reset_eoy_tables == 1 && $reportYear->display_testing != 1 && $currentMonth >= 1 && $currentMonth <= 5)
                                <button type="button" id="view-eoy-testing" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-toggle-on me-2"></i>Display EOY Testing Items</button>
            @else
                                <button type="button" id="view-eoy-testing" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-toggle-on me-2"></i>Display EOY Testing Items</button>
            @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
                    @foreach($displayTestingItemsItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($reportYear->display_testing == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach

                </div>

</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
<!------End Step 3 ------>

<!------Start Step 4 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$reportYear->reset_AFTER_testing == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-reset-tables-after">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseReportFour"
                    aria-expanded="false" aria-controls="collapseReportFour">
                #4 - *LIVE* Reset Tables AFTER Testing - MAY
            </button>
        </h2>
        <div id="collapseReportFour" class="accordion-collapse collapse" data-bs-parent="#accordionReport">
            <div class="accordion-body">
    <section>
            <div class="col-md-12">
            Complete in May, after testing is complete, so all data tables are clean and ready to go.<br>
            @if ($reportYear->display_testing == 1 && $reportYear->reset_AFTER_testing != 1 && ( $currentMonth >= 3 && $currentMonth <= 6 ))
                <button type="button" id="reset-database-after" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Database AFTER Testing</button>
            @else
                <button type="button" id="reset-database-after" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Database AFTER Testing</button>
            @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
                    @foreach($resetAFTERtestingItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($reportYear->reset_AFTER_testing == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}reset_AFTER_testing
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach

                </div>

</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
<!------End Step 4 ------>

<!------Start Step 6 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$reportYear->display_live == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-display-live">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseReportFive"
                    aria-expanded="false" aria-controls="collapseReportFive">
                #5 -  *LIVE* Activate Menus/Buttons/Links AFTER Testing - MAY
            </button>
        </h2>
        <div id="collapseReportFive" class="accordion-collapse collapse" data-bs-parent="#accordionReport">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Complete in May, after testing, for live viewing.<br>
            @if ($reportYear->update_user_tables == 1 && $reportYear->display_live != 1 && ( $currentMonth >= 3 && $currentMonth <= 6 ))
                <button type="button" id="view-eoy-live" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-toggle-on me-2"></i>Display EOY LIVE Items</button>
            @else
                <button type="button" id="view-eoy-live" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-toggle-on me-2"></i>Display EOY LIVE Items</button>
            @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
                    @foreach($displayLiveItemsItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($reportYear->display_live == 1)
                                            <i class="fas fa-check mr-2 ml-2"></i>{{ $item }}
                                        @else
                                            <i class="far fa-square mr-2 ml-2"></i>{{ $item }}
                                        @endif
                                    </li>
                                @endforeach
                </div>
</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
<!------End Step 5 ------>

</div><!-- end of accordion -->
</div>
