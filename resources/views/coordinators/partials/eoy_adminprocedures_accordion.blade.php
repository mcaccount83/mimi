<div class="container-fluid">
    <div class="accordion"  id="accordionAdmin"  style="column-count: 2; column-gap: 1rem;">

<!------Start Step 1 ------>
        <div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item step-complete">
        <h2 class="accordion-header" id="header-fiscal-year">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminOne"
                    aria-expanded="false" aria-controls="collapseAdminOne">
                    #1 - Reset Fiscal Year - JULY
            </button>
        </h2>
        <div id="collapseAdminOne" class="accordion-collapse collapse"data-bs-parent="#accordionAdmin">
            <div class="accordion-body">
                <section>
                    <div class="col-md-12">
                    To be used in BoardList and other display areas.<br>
                        <button type="button" id="reset-year" class="btn btn-danger bg-gradient mb-2" disabled><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year</button>
                </div>
</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
    {{------End Step 1 ------}}

        <!------Start Step 2 ------>
        <div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{ $adminYear->subscribe_list == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-subscribe-list">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminTwo"
                    aria-expanded="false" aria-controls="collapseAdminTwo">
                #2 - Subscribe Users to ForumLists - AUG
            </button>
        </h2>
        <div id="collapseAdminTwo" class="accordion-collapse collapse"data-bs-parent="#accordionAdmin">
            <div class="accordion-body">
                <section>
                    <div class="col-md-12">
                    Complete in August, after activating boards, so new board members receive subscription to Lists.<br>
                    @if ($adminYear->subscribe_list != 1 && ( $currentMonth >= 2 && $currentMonth <= 9 ))
                                <button type="button" id="update-eoy-subscribelists" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-plus-lg me-2"></i>Subscribe to Lists</button>
                    @else
                                <button type="button" id="update-eoy-subscribelists" class="btn btn-primary bg-gradient-primary mb-3" disabled><i class="bi bi-plus-lg me-2"></i>Subscribe to Lists</button>
                    @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
                     @foreach($subscribeListItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->subscribe_list == 1)
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
    {{------End Step 2 ------}}

<!------Start Step 3 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
        <div class="accordion-item {{ $adminYear->test_eoy == 1 ? 'step-complete' : ($reportYear->reset_report_year == 1 ? 'step-inprogress' : '') }}">
        <h2 class="accordion-header" id="header-display-testing">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminThree"
                    aria-expanded="false" aria-controls="collapseAdminThree">
                #3 - Reset Report Year and work on EOY Testing - JAN
            </button>
        </h2>
        <div id="collapseAdminThree" class="accordion-collapse collapse" data-bs-parent="#accordionAdmin">
            <div class="accordion-body">
        <section>
            <div class="col-md-12">
                To be used for all End of Year buttons/links/emails/forms.<br>
                @if ($reportYear->reset_report_year != 1 && ($currentMonth >= 1 && $currentMonth <= 5))
                    <button type="button" id="reset-yeareoy" class="btn btn-danger bg-gradient mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year EOY</button>
                @else
                    <button type="button" id="reset-yeareoy" class="btn btn-danger bg-gradient mb-2" disabled><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year EOY</button>
                @endif
                <br>
               {{-- @if ($adminYear->test_eoy != 1) --}}
                <button type="button" id="view-reportprocedures" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>View Report/Testing Procedures</button>
            {{-- @endif --}}
            </div>
    </section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
<!------End Step 3 ------>

<!------Start Step 4 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$adminYear->update_user_tables == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-display-live">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminFour"
                    aria-expanded="false" aria-controls="collapseAdminFour">
                #4 -  Copy/Save Data to New Tables - MAY
            </button>
        </h2>
        <div id="collapseAdminFour" class="accordion-collapse collapse" data-bs-parent="#accordionAdmin">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Complete in May, before going live to save old board/coordinator/user information.<br>
            @if ($adminYear->update_user_tables != 1 && ( $currentMonth >= 4 && $currentMonth <= 6 ))
                <button type="button" id="update-data-database" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-copy me-2"></i>Copy Data Tables</button>
            @else
                <button type="button" id="update-data-database" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-copy me-2"></i>Copy Data Tables</button>
            @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
                    @foreach($updateUserTablesItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->update_user_tables == 1)
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
<!------End Step 4 ------>

<!------Start Step 6 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$adminYear->unsubscribe_list == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-unsubscribe-list">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminFive"
                    aria-expanded="false" aria-controls="collapseAdminFive">
                #5 - Unsubscribe from BoardList - JUNE
            </button>
        </h2>
        <div id="collapseAdminFive" class="accordion-collapse collapse" data-bs-parent="#accordionAdmin">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Complete in June, before new board reports are activated.<br>
            @if ($adminYear->subscribe_list == 1 && $adminYear->unsubscribe_list != 1 && ( $currentMonth >= 5 && $currentMonth <= 7 ))
                <button type="button" id="update-eoy-unsubscribelists" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-ban me-2"></i>Unsubscribe from Lists</button>
            @else
                <button type="button" id="update-eoy-unsubscribelists" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-ban me-2"></i>Unsubscribe from Lists</button>
            @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
                    @foreach($unSubscribeListItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->unsubscribe_list == 1)
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

<!------Start Step 6 ------>
        <div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item">
        <h2 class="accordion-header" id="header-fiscal-year2">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminSix"
                    aria-expanded="false" aria-controls="collapseAdminSix">
                    #6 - Reset Fiscal Year - JULY
            </button>
        </h2>
        <div id="collapseAdminSix" class="accordion-collapse collapse"data-bs-parent="#accordionAdmin">
            <div class="accordion-body">
                <section>
                    <div class="col-md-12">
                    To be used in BoardList and other display areas.<br>
                    @if ($adminYear->unsubscribe_list == 1 && $fiscalYearStart == $reportYearEnd && ( $currentMonth >= 6 && $currentMonth <= 9 ))
                                <button type="button" id="reset-year" class="btn btn-danger bg-gradient mb-2"><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year</button>
                            @else
                                <button type="button" id="reset-year" class="btn btn-danger bg-gradient mb-2" disabled><i class="bi bi-arrow-counterclockwise me-2"></i>Reset Fiscal Year</button>
                            @endif
                </div>
</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div>
    {{------End Step 6------}}

</div><!-- end of accordion -->
</div>
