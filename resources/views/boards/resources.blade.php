@extends('layouts.board_theme')

@section('content')

<div class="container" id="test">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
         <!-- Widget: user widget style 1 -->
         <div class="card card-widget widget-user">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}theme/dist/img/logo.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                        </div>
                        <div class="card-body">
                    @php
                        $thisDate = \Carbon\Carbon::now();
                    @endphp
                    <div class="col-md-12"><br><br></div>
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{ $stateShortName }} </h2>
                        <h4 class="text-center"> General Chapter Resources</h4>

                        </div>
                    </div>
                </div>

        <div class="container-fluid">
        <div class="row">
            <div class="col-6"  id="accordion">
            <!-- Accordion for Left Column -->
                <!------Start Bylaws ------>
                <div class="card card-primary ">
                    <div class="card-header" id="accordion-header-bylaws">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseOne" style="width: 100%;">BYLAWS</a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="collapse" data-parent="#accordion">
                        <div class="card-body">
                            <section>
                                @foreach($resources->where('resourceCategory.category_name', 'BYLAWS') as $resourceItem)
                                <div class="col-md-12" style="margin-bottom: 5px;">
                                    @if ($resourceItem->link)
                                        <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @elseif ($resourceItem->file_path)
                                    <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                    @else
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                @endforeach
                                <div class="col-md-12"><br></div>
                            </section>
                        </div>
                    </div>
                </div>
                    <!------End Bylaws ------>
                    <!------Start Fact Sheets ------>
                <div class="card card-primary ">
                    <div class="card-header" id="accordion-header-factsheets">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseTwo" style="width: 100%;">FACT SHEETS</a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="collapse" data-parent="#accordion">
                        <div class="card-body">
                            <section>
                                @foreach($resources->where('resourceCategory.category_name', 'FACT SHEETS') as $resourceItem)
                                <div class="col-md-12"style="margin-bottom: 5px;">
                                    @if ($resourceItem->link)
                                        <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @elseif ($resourceItem->file_path)
                                    <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                    @else
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                @endforeach
                                <div class="col-md-12"><br></div>
                            </section>
                        </div>
                    </div>
                </div>
                    <!------End Fact Sheets ------>
                    <!------Start Copy Ready Materials ------>
                <div class="card card-primary ">
                    <div class="card-header" id="accordion-header-materials">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseThree" style="width: 100%;">COPY READY MATERIAL</a>
                        </h4>
                    </div>
                    <div id="collapseThree" class="collapse" data-parent="#accordion">
                        <div class="card-body">
                            <section>
                                @foreach($resources->where('resourceCategory.category_name', 'COPY READY MATERIAL') as $resourceItem)
                                <div class="col-md-12"style="margin-bottom: 5px;">
                                    @if ($resourceItem->link)
                                        <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @elseif ($resourceItem->file_path)
                                    <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                    @else
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                    {{ $resourceItem->description }}
                                </div>
                                @endforeach
                                <div class="col-md-12"><br></div>
                            </section>
                        </div>
                    </div>
                </div>
                    <!------End Copy Ready Materials ------>
                    <!------Start Ideas & Inspirations ------>
                <div class="card card-primary ">
                    <div class="card-header" id="accordion-header-inspiration">
                        <h4 class="card-title w-100">
                            <a class="d-block" data-toggle="collapse" href="#collapseFour" style="width: 100%;">IDEAS AND INSPIRATIONS</a>
                        </h4>
                    </div>
                    <div id="collapseFour" class="collapse" data-parent="#accordion">
                        <div class="card-body">
                            <section>
                                @foreach($resources->where('resourceCategory.category_name', 'IDEAS AND INSPIRATION') as $resourceItem)
                                <div class="col-md-12"style="margin-bottom: 5px;">
                                    @if ($resourceItem->link)
                                        <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @elseif ($resourceItem->file_path)
                                    <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                    @else
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                @endforeach
                                <div class="col-md-12"><br></div>
                            </section>
                        </div>
                    </div>
                </div>
                    <!------End Ideas & Inspirations ------>
            </div>

                <div class="col-6"  id="accordion">
                    <!-- Accordion for Right Column -->
                        <!------Start Resources ------>
                        <div class="card card-primary ">
                            <div class="card-header" id="accordion-header-resources">
                                <h4 class="card-title w-100">
                                    <a class="d-block" data-toggle="collapse" href="#collapseFive" style="width: 100%;">CHAPTER RESOURCES</a>
                                </h4>
                            </div>
                            <div id="collapseFive" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    <section>
                                        @foreach($resources->where('resourceCategory.category_name', 'CHAPTER RESOURCES') as $resourceItem)
                                        <div class="col-md-12"style="margin-bottom: 5px;">
                                            @if ($resourceItem->link)
                                                <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                            @elseif ($resourceItem->file_path)
                                            <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                            </a>
                                            @else
                                                {{ $resourceItem->name }}
                                            @endif
                                        </div>
                                        @endforeach
                                        <div class="col-md-12"><br></div>
                                    </section>
                                </div>
                            </div>
                        </div>
                            <!------End Resources ------>
                            <!------Start Samples ------>
                        <div class="card card-primary ">
                            <div class="card-header" id="accordion-header-factsheets">
                                <h4 class="card-title w-100">
                                    <a class="d-block" data-toggle="collapse" href="#collapseSix" style="width: 100%;">SAMPLE CHAPTER FILES</a>
                                </h4>
                            </div>
                            <div id="collapseSix" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    <section>
                                        @foreach($resources->where('resourceCategory.category_name', 'SAMPLE CHAPTER FILES') as $resourceItem)
                                        <div class="col-md-12"style="margin-bottom: 5px;">
                                            @if ($resourceItem->link)
                                                <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                            @elseif ($resourceItem->file_path)
                                            <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                            </a>
                                            @else
                                                {{ $resourceItem->name }}
                                            @endif
                                        </div>
                                        @endforeach
                                        <div class="col-md-12"><br></div>
                                    </section>
                                </div>
                            </div>
                        </div>
                            <!------End Samples ------>
                            <!------Start End of Year ------>
                        <div class="card card-primary ">
                            <div class="card-header" id="accordion-header-materials">
                                <h4 class="card-title w-100">
                                    <a class="d-block" data-toggle="collapse" href="#collapseSeven" style="width: 100%;">END OF YEAR</a>
                                </h4>
                            </div>
                            <div id="collapseSeven" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    <section>
                                        <div class="col-md-12">
                                            <h4><u>Read carefully before starting!</u></h4>
                                            All chapters must complete the <?php echo date('Y')-1 .'-'.date('Y');?> End of Year Reports.<br>
                                            <br>
                                            @if($thisDate->month >= 1 && $thisDate->month <= 5)
                                            <table>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    <td><span class="text-danger">EOY Reports are not available at this time.</span></td>
                                                </tr>
                                            </table>
                                            @endif
                                            @if($thisDate->month >= 6 && $thisDate->month <= 12)
                                            <table>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    <td><?php echo date('Y') .'-'.date('Y')+1;?> Board Report</li></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    <td><?php echo date('Y')-1 .'-'.date('Y');?> Financial Report</li></td>
                                                </tr>
                                            </table>
                                            @endif
                                            <br>
                                            <strong><u>Board Report</u></strong><br>
                                            This report should be filled out as soon as your chapter has held its election but is due no later than June 30th.<br>
                                            <br>
                                            <strong><u>Financial Report</u></strong><br>
                                            When you have filled in all the answers, submit the report and save a copy in your chapter’s permanent files. The International MOMS Club does not keep copies of your reports long term. You need to be sure your chapter has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS were to do an audit. The Financial Report and all required additional documents must be received by July 15th. <strong>NEW CHAPTERS</strong> who have not started meeting prior to June 30th, do NOT need to fill out this report!<br>
                                            <br>
                                            <table>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    <td><a href="https://momsclub.org/elearning/courses/annual-financial-report-bank-reconciliation/">Step-by-Step Guide to Bank Reconciliation.</a></td>
                                                </tr>
                                            </table>
                                            <br>
                                            <strong><u>990N (e-Postcard) Information</u></strong><br>
                                            990N cannot be filed before July 1st.  All chapters should file their 990N directly with the IRS and not through a third party. <i>The IRS does not charge a fee for 990N filings.</i><br>
                                            <br>
                                            @if($thisDate->month >= 1 && $thisDate->month <= 6)
                                            <table>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    <td><span class="text-danger">990N Filing Instructions will be available on July 1st. Since chapter cannot file until then, we are also unable to verify that instructions/screenshots have not changed since last year until that date, so please bear with us until we get them updated and posted.</span><br></td>
                                                </tr>
                                            </table>
                                            @endif
                                            @if($thisDate->month >= 7 && $thisDate->month <= 12)
                                            <table>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    <td><a href="https://www.irs.gov/charities-non-profits/annual-electronic-filing-requirement-for-small-exempt-organizations-form-990-n-e-postcard" target="_blank">990N IRS Website Link to File</a></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    @foreach($resources as $resourceItem)
                                                    @if ($resourceItem->name === '990N Filing Instructions')
                                                        <td>
                                                            <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                                990N Filing Instructions
                                                            </a>
                                                            {{-- <a href="{{ $resourceItem->file_path }}" target="_blank">990N Filing Instructions
                                                        </a> --}}
                                                    </td>
                                                    @endif
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                    @foreach($resources as $resourceItem)
                                                    @if ($resourceItem->name === '990N Filing FAQs')
                                                        <td>
                                                            <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                                990N Filing FAQs
                                                            </a>
                                                            {{-- <a href="{{ $resourceItem->file_path }}" target="_blank">990N Filing FAQs
                                                        </a> --}}
                                                    </td>
                                                    @endif
                                                    @endforeach
                                                </tr>
                                            </table>
                                            @endif
                                            <br>
                                            <strong><u>Some other important things to remember:</u></strong><br>
                                            <br>
                                            Any board member of your chapter may fill out the report. We recommend that the Treasurer and President work together but any board member may complete it. All the information needed to complete it should be found in your financial records, newsletters, and meeting minutes.<br>
                                            <br>
                                            Your report must be submitted no later than July 15th! It may be sent in earlier as long as you have included all of your financial information for the fiscal year of July 1, <?php echo date('Y')-1?> - June 30, <?php echo date('Y');?>, and all necessary supporting files.<br>
                                            <br>
                                            If you need help or extra time for ANY reason, contact your Primary Coordinator BEFORE July 15th. A chapter may be put on probation for a late report, and a late report may put your chapter at risk of losing its non-profit status for the year. The report is very easy to complete, so please make sure you send it in on time!<br>
                                            <br>
                                            <br>
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                            <!------End End of Year ------>
                    </div>

                <br>
                <div class="card-body text-center">
                        <a href="{{ route('home') }}" class="btn btn-primary"><i class="fas fa-reply" ></i>&nbsp; Back to Profile</a>
                        <button type="button" onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-primary"><i class="fas fa-graduation" ></i>&nbsp; eLearning Library</button>
                    </div>
                </div>
            </div>

@endsection

@section('customscript')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Listen for collapse events on both accordions
    $('#accordion .collapse').on('show.bs.collapse', function() {
        $('#accordion .collapse').not(this).collapse('hide');
    });
    $('#accordion-right .collapse').on('show.bs.collapse', function() {
        $('#accordion-right .collapse').not(this).collapse('hide');
        $('#accordion .collapse').collapse('hide');
    });
    $('#accordion .collapse').on('show.bs.collapse', function() {
        $('#accordion .collapse').not(this).collapse('hide');
        $('#accordion-right .collapse').collapse('hide');
    });
});

</script>
