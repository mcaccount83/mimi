<div class="container-fluid">
    <div class="accordion"  id="accordion"  style="column-count: 2; column-gap: 1rem;">

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
        <div id="collapseAdminOne" class="accordion-collapse collapse"data-bs-parent="#accordion">
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
        <div id="collapseAdminTwo" class="accordion-collapse collapse"data-bs-parent="#accordion">
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
<div class="accordion-item {{ $adminYear->file_sept == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-file_irs_sept">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminThree"
                    aria-expanded="false" aria-controls="collapseAdminThree">
                #3 - Submit IRS Filing Updates - SEPT
            </button>
        </h2>
        <div id="collapseAdminThree" class="accordion-collapse collapse" data-bs-parent="#accordion">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Submit filing updates to the IRS to include all chapters added or removed since the last update.<br>
            @if ($adminYear->file_sept != 1 && ( $currentMonth >= 9 && $currentMonth <= 10 ))
                @php $juneFileDate = $adminYearEOY->june_file_date ? \Carbon\Carbon::parse($adminYearEOY->june_file_date)->format('Y-m-d') : ''; @endphp
                <button type="button" id="file-irssept" class="btn btn-primary bg-gradient mb-2" onclick="showIRSUpdatesModal('{{ $juneFileDate }}')">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                    <button type="button" id="update-eoy-irssept" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-envelope-paper me-2"></i>Record Filing as Sent</button>
            @else
                <button type="button" id="file-irssept" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irssept" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-envelope-paper me-2"></i>Record Filing as Sent</button>
            @endif
            <p style="font-weight: bold;">The following will be included in update:</p>
                     @foreach($irsUpdateListItems2 as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->file_sept == 1)
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
<div class="accordion-item {{ $adminYear->file_dec == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-eoy-tables">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminFour"
                    aria-expanded="false" aria-controls="collapseAdminFour">
                #4 - Submit IRS Filing Updates - DEC
            </button>
        </h2>
        <div id="collapseAdminFour" class="accordion-collapse collapse" data-bs-parent="#accordion">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Submit filing updates to the IRS to include all chapters added or removed since the last update.<br>
            @if ($adminYear->file_sept == 1 && $adminYear->file_dec != 1 && ($currentMonth >= 12 || $currentMonth <= 1))
                @php $septFileDate = $adminYear->sept_file_date ? \Carbon\Carbon::parse($adminYear->sept_file_date)->format('Y-m-d') : ''; @endphp
                <button type="button" id="file-irsdec" class="btn btn-primary bg-gradient mb-2" onclick="showIRSUpdatesModal('{{ $septFileDate }}')">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irsdec" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-envelope-paper me-2"></i>Record Filing as Sent</button>
            @else
                <button type="button" id="file-irsdec" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irsdec" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-envelope-paper me-2"></i>Record Filing as Sent</button>
            @endif
            <p style="font-weight: bold;">The following will be included in update:</p>
                     @foreach($irsUpdateListItems2 as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->file_dec == 1)
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

<!------Start Step 5 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
        <div class="accordion-item {{ $adminYear->test_eoy == 1 ? 'step-complete' : ($admin->reset_year == 1 ? 'step-inprogress' : '') }}">
        <h2 class="accordion-header" id="header-display-testing">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminFive"
                    aria-expanded="false" aria-controls="collapseAdminFive">
                #5 - Reset Report Year and work on EOY Testing - JAN
            </button>
        </h2>
        <div id="collapseAdminFive" class="accordion-collapse collapse" data-bs-parent="#accordion">
            <div class="accordion-body">
        <section>
            <div class="col-md-12">
                To be used for all End of Year buttons/links/emails/forms.<br>
                @if ($admin->reset_year != 1 && ($currentMonth >= 1 && $currentMonth <= 5))
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
<!------End Step 5 ------>

<!------Start Step 6 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$adminYear->file_subordinate == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-reset-tables-after">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminSix"
                    aria-expanded="false" aria-controls="collapseAdminSix">
                #6 - Submit IRS Subordinate Filing - MAR
            </button>
        </h2>
        <div id="collapseAdminSix" class="accordion-collapse collapse" data-bs-parent="#accordion">
            <div class="accordion-body">
    <section>
            <div class="col-md-12">
            Submit subordinate filing updates to the IRS to include a list of all current chapters, noting chapters added or removed during the current fiscal year.<br>
            @if ($adminYear->file_dec == 1 && $adminYear->file_subordinate != 1 && ( $currentMonth >= 2 && $currentMonth <= 4 ))
                <button type="button" id="file-irsjune" class="btn btn-primary bg-gradient mb-2" onclick="showSubordinateFilingModal('{{ $fiscalYearStartDate }}')">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irssubordinate" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-envelope-paper me-2"></i>Record Filing as Sent</button>
            @else
                <button type="button" id="file-irssubordinate" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate Subordinate Filing</button>
                <button type="button" id="update-eoy-irssubordinate" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-envelope-paper me-2"></i>Record Filing as Sent</button>
            @endif
            <p style="font-weight: bold;">The following will be included in filing:</p>
                     @foreach($irsSubordinateListItems as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->file_subordinate == 1)
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
<!------End Step 6 ------>

<!------Start Step 7 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$adminYear->file_june == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-user-tables">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminSeven"
                    aria-expanded="false" aria-controls="collapseAdminSeven">
                #7 - Submit IRS Filing Updates - JUNE
            </button>
        </h2>
        <div id="collapseAdminSeven" class="accordion-collapse collapse" data-bs-parent="#accordion">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Submit filing updates to the IRS to include all chapters added or removed since the last update.<br>
            @if ($adminYear->file_subordinate == 1 && $adminYear->file_june != 1 && ( $currentMonth >= 6 && $currentMonth <= 7 ))
                <button type="button" id="file-irsjune" class="btn btn-primary bg-gradient mb-2" onclick="showIRSUpdatesModal()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irsjune" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-envelope-paper me-2"></i>Record Update as Sent</button>
            @else
                <button type="button" id="file-irsjune" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irsjune" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-envelope-paper me-2"></i>Record Update as Sent</button>
            @endif
            <p style="font-weight: bold;">The following will be included in update:</p>
                     @foreach($irsUpdateListItems1 as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($adminYear->file_june == 1)
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
<!------End Step 7 ------>


<!------Start Step 9 ------>
{{-- <div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$fiscalYearEOYReset && $admin->display_live == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-display-live">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseNine"
                    aria-expanded="false" aria-controls="collapseNine">
                #8 -  Activate Menus/Buttons/Links AFTER testing - MAY
            </button>
        </h2>
        <div id="collapseNine" class="accordion-collapse collapse" data-bs-parent="#accordion">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Complete in May, after testing, for live viewing.<br>
            @if ($fiscalYearEOYReset && $admin->update_user_tables == 1 && $admin->display_live != 1 && ( $currentMonth >= 3 && $currentMonth <= 6 ))
                                <button type="button" id="view-eoy-live" class="btn btn-primary bg-gradient mb-3"><i class="bi bi-toggle-on me-2"></i>Display EOY LIVE Items</button>
            @else
                                <button type="button" id="view-eoy-live" class="btn btn-primary bg-gradient mb-3" disabled><i class="bi bi-toggle-on me-2"></i>Display EOY LIVE Items</button>
            @endif
                <p style="font-weight: bold;">The following functions will be performed:</p>
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

</section>
</div><!-- end of accordion body -->
</div><!-- end of accordion item -->
</div>
</div> --}}
<!------End Step 9 ------>

<!------Start Step 8 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{$adminYear->unsubscribe_list == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-unsubscribe-list">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminEight"
                    aria-expanded="false" aria-controls="collapseAdminEight">
                #8 - Unsubscribe from BoardList - JUNE
            </button>
        </h2>
        <div id="collapseAdminEight" class="accordion-collapse collapse" data-bs-parent="#accordion">
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
<!------End Step 8 ------>


<!------Start Step 10 ------>
        <div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item">
        <h2 class="accordion-header" id="header-fiscal-year2">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseAdminTen"
                    aria-expanded="false" aria-controls="collapseAdminTen">
                    #9 - Reset Fiscal Year - JULY
            </button>
        </h2>
        <div id="collapseAdminTen" class="accordion-collapse collapse"data-bs-parent="#accordion">
            <div class="accordion-body">
                <section>
                    <div class="col-md-12">
                    To be used in BoardList and other display areas.<br>
                    @if ($adminYear->unsubscribe_list == 1 && $fiscalYearStart == $thisYearEOY && ( $currentMonth >= 6 && $currentMonth <= 9 ))
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
    {{------End Step 10 ------}}

</div><!-- end of accordion -->
</div>
