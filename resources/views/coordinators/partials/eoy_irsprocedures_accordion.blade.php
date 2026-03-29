<div class="container-fluid">
    <div class="accordion"  id="accordionIRS"  style="column-count: 2; column-gap: 1rem;">


<!------Start Step 1 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{ $irsYear->file_sept == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-file_irs_sept">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseIRSOne"
                    aria-expanded="false" aria-controls="collapseIRSOne">
                #1 - Submit IRS Filing Updates - SEPT
            </button>
        </h2>
        <div id="collapseIRSOne" class="accordion-collapse collapse" data-bs-parent="#accordionIRS">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Submit filing updates to the IRS to include all chapters added or removed since the last update.<br>
            @if ($irsYear->file_sept != 1 && ( $currentMonth >= 9 && $currentMonth <= 10 ))
                @php $juneFileDate = $irsYear->june_file_date ? \Carbon\Carbon::parse($irsYear->june_file_date)->format('Y-m-d') : ''; @endphp
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
                                        @if($irsYear->file_sept == 1)
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
<!------End Step 1 ------>

<!------Start Step 2 ------>
<div style="break-inside: avoid; margin-bottom: 0.5rem;">
<div class="accordion-item {{ $irsYear->file_dec == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-eoy-tables">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseIRSTwo"
                    aria-expanded="false" aria-controls="collapseIRSTwo">
                #2 - Submit IRS Filing Updates - DEC
            </button>
        </h2>
        <div id="collapseIRSTwo" class="accordion-collapse collapse" data-bs-parent="#accordionIRS">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Submit filing updates to the IRS to include all chapters added or removed since the last update.<br>
            @if ($irsYear->file_sept == 1 && $adminYear->file_dec != 1 && ($currentMonth >= 12 || $currentMonth <= 1))
                @php $septFileDate = $irsYear->sept_file_date ? \Carbon\Carbon::parse($irsYear->sept_file_date)->format('Y-m-d') : ''; @endphp
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
                                        @if($irsYear->file_dec == 1)
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
<div class="accordion-item {{$irsYear->file_subordinate == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-reset-tables-after">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseIRSThree"
                    aria-expanded="false" aria-controls="collapseIRSThree">
                #3 - Submit IRS Subordinate Filing - MAR
            </button>
        </h2>
        <div id="collapseIRSThree" class="accordion-collapse collapse" data-bs-parent="#accordionIRS">
            <div class="accordion-body">
    <section>
            <div class="col-md-12">
            Submit subordinate filing updates to the IRS to include a list of all current chapters, noting chapters added or removed during the current fiscal year.<br>
            @if ($irsYear->file_dec == 1 && $irsYear->file_subordinate != 1 && ( $currentMonth >= 2 && $currentMonth <= 4 ))
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
                                        @if($irsYear->file_subordinate == 1)
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
<div class="accordion-item {{$irsYear->file_june == 1 ? 'step-complete' : '' }}">
        <h2 class="accordion-header" id="header-user-tables">
            <button class="accordion-button collapsed"type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseIRSFour"
                    aria-expanded="false" aria-controls="collapseIRSFour">
                #4 - Submit IRS Filing Updates - JUNE
            </button>
        </h2>
        <div id="collapseIRSFour" class="accordion-collapse collapse" data-bs-parent="#accordionIRS">
            <div class="accordion-body">
    <section>
        <div class="col-md-12">
            Submit filing updates to the IRS to include all chapters added or removed since the last update.<br>
            @if ($irsYear->file_subordinate == 1 && $irsYear->file_june != 1 && ( $currentMonth >= 6 && $currentMonth <= 7 ))
                <button type="button" id="file-irsjune" class="btn btn-primary bg-gradient mb-2" onclick="showIRSUpdatesModal()"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irsjune" class="btn btn-primary bg-gradient mb-2"><i class="bi bi-envelope-paper me-2"></i>Record Update as Sent</button>
            @else
                <button type="button" id="file-irsjune" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-file-earmark-pdf-fill me-2"></i>Generate IRS Update</button>
                <button type="button" id="update-eoy-irsjune" class="btn btn-primary bg-gradient mb-2" disabled><i class="bi bi-envelope-paper me-2"></i>Record Update as Sent</button>
            @endif
            <p style="font-weight: bold;">The following will be included in update:</p>
                     @foreach($irsUpdateListItems1 as $item)
                                    <li style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                        @if($irsYear->file_june == 1)
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

</div><!-- end of accordion -->
</div>
