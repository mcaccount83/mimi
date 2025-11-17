@extends('layouts.public_theme')

@section('content')

        @php
            $thisDate = \Illuminate\Support\Carbon::now();
        @endphp

<div class="container-fluid">
    <div class="row">
        {{-- <!-- Left Column -->
        <div class="col-md-6" id="accordion-left">
            @php
                $totalCategories = count($resourceCategories);
                $halfCount = ceil($totalCategories / 2);
                $counter = 0;
            @endphp

            @foreach($resourceCategories as $category)
                @php $counter++; @endphp
                @if($counter <= $halfCount)
                    <div class="card card-primary">
                        <div class="card-header" id="accordion-header-left-{{ Str::slug($category->category_name) }}">
                            <h4 class="card-title w-100">
                                <a class="d-block" data-toggle="collapse" href="#collapse-left-{{ Str::slug($category->category_name) }}" style="width: 100%;">{{ $category->category_name }}</a>
                            </h4>
                        </div>
                        <div id="collapse-left-{{ Str::slug($category->category_name) }}" class="collapse" data-parent="#accordion-left">
                            <div class="card-body">
                                <section>
                                    @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
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
                                        @if($category->category_name == "COPY READY MATERIAL")
                                            <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                                {{ $resourceItem->description }}
                                            </div>
                                        @endif
                                    @endforeach
                                </section>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Right Column -->
        <div class="col-md-6" id="accordion-right">
            @php $counter = 0; @endphp
            @foreach($resourceCategories as $category)
                @php $counter++; @endphp
                @if($counter > $halfCount)
                    <div class="card card-primary">
                        <div class="card-header" id="accordion-header-right-{{ Str::slug($category->category_name) }}">
                            <h4 class="card-title w-100">
                                <a class="d-block" data-toggle="collapse" href="#collapse-right-{{ Str::slug($category->category_name) }}" style="width: 100%;">{{ $category->category_name }}</a>
                            </h4>
                        </div>
                        <div id="collapse-right-{{ Str::slug($category->category_name) }}" class="collapse" data-parent="#accordion-right">
                            <div class="card-body">
                                <section>
                                    @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                                    @if($category->category_name != "END OF YEAR")
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
                                            @if($category->category_name == "COPY READY MATERIAL")
                                                <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                                    {{ $resourceItem->description }}
                                                </div>
                                            @endif
                                    @endif
                                    @endforeach
                                    @if($category->category_name == "END OF YEAR")
                                        <div class="col-md-12" style="margin-bottom: 5px;">
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
                                                @if ($resourceItem->name == '990N Filing Instructions')
                                                    <td>
                                                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                            990N Filing Instructions
                                                        </a>
                                                    </td>
                                                @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                @foreach($resources as $resourceItem)
                                                @if ($resourceItem->name == '990N Filing FAQs')
                                                    <td>
                                                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                            990N Filing FAQs
                                                        </a>
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
                                @endif
                                </section>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div> --}}

        <!-- Left Column -->
<div class="col-md-6" id="accordion-left">
    @php
        $totalCategories = count($resourceCategories);
        $halfCount = ceil($totalCategories / 2);
        $counter = 0;
    @endphp

    @foreach($resourceCategories as $category)
        @php $counter++; @endphp
        @if($counter <= $halfCount)
            <div class="card card-primary">
                <div class="card-header" id="accordion-header-left-{{ Str::slug($category->category_name) }}">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapse-left-{{ Str::slug($category->category_name) }}" style="width: 100%;">{{ $category->category_name }}</a>
                    </h4>
                </div>
                <div id="collapse-left-{{ Str::slug($category->category_name) }}" class="collapse" data-parent="#accordion-left">
                    <div class="card-body">
                        <section>
                            @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                            <div class="col-md-12" style="margin-bottom: 5px;">
                                @if ($resourceItem->file_type == 2)
                                    {{-- External Link --}}
                                    <a href="{{ $resourceItem->link }}" target="_blank">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                @elseif ($resourceItem->file_type == 3)
                                    {{-- Laravel Route - Just show title, no link for admin --}}
                                    {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    <span style="font-size: smaller; color: #6c757d;">(Chapter Sepcific Route)</span>
                                @elseif ($resourceItem->file_type == 1)
                                    {{-- File Download --}}
                                    <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                @else
                                    {{-- Fallback for no file type --}}
                                    {{ $resourceItem->name }}
                                @endif
                            </div>
                            @if($category->category_name == "COPY READY MATERIAL")
                                <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                    {{ $resourceItem->description }}
                                </div>
                            @endif
                            @endforeach
                        </section>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- Right Column -->
<div class="col-md-6" id="accordion-right">
    @php $counter = 0; @endphp
    @foreach($resourceCategories as $category)
        @php $counter++; @endphp
        @if($counter > $halfCount)
            <div class="card card-primary">
                <div class="card-header" id="accordion-header-right-{{ Str::slug($category->category_name) }}">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapse-right-{{ Str::slug($category->category_name) }}" style="width: 100%;">{{ $category->category_name }}</a>
                    </h4>
                </div>
                <div id="collapse-right-{{ Str::slug($category->category_name) }}" class="collapse" data-parent="#accordion-right">
                    <div class="card-body">
                        <section>
                            @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                            @if($category->category_name != "END OF YEAR")
                                <div class="col-md-12" style="margin-bottom: 5px;">
                                    @if ($resourceItem->file_type == 2)
                                        {{-- External Link --}}
                                        <a href="{{ $resourceItem->link }}" target="_blank">
                                            {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                        </a>
                                    @elseif ($resourceItem->file_type == 3)
                                        {{-- Laravel Route - Just show title, no link for admin --}}
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                        <span style="font-size: smaller; color: #6c757d;">(Chapter Sepcific Route)</span>
                                    @elseif ($resourceItem->file_type == 1)
                                        {{-- File Download --}}
                                        <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                            {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                        </a>
                                    @else
                                        {{-- Fallback for no file type --}}
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                @if($category->category_name == "COPY READY MATERIAL")
                                    <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                        {{ $resourceItem->description }}
                                    </div>
                                @endif
                            @endif
                            @endforeach
                            @if($category->category_name == "END OF YEAR")
                                <div class="col-md-12" style="margin-bottom: 5px;">
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
                                When you have filled in all the answers, submit the report and save a copy in your chapter's permanent files. The International MOMS Club does not keep copies of your reports long term. You need to be sure your chapter has a copy and keeps it for the life of your chapter, as this would be the information you would need if the IRS were to do an audit. The Financial Report and all required additional documents must be received by July 15th. <strong>NEW CHAPTERS</strong> who have not started meeting prior to June 30th, do NOT need to fill out this report!<br>
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
                                        @if ($resourceItem->name == '990N Filing Instructions')
                                            <td>
                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                    990N Filing Instructions
                                                </a>
                                            </td>
                                        @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        @foreach($resources as $resourceItem)
                                        @if ($resourceItem->name == '990N Filing FAQs')
                                            <td>
                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                    990N Filing FAQs
                                                </a>
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
                        @endif
                        </section>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
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
@endsection
