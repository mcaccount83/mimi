@extends('layouts.chapter_theme')

@section('content')
<div class="container">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Your custom JavaScript -->
    <script>
        // Your JavaScript code here
    </script>
    <!-- Custom CSS -->
    <style>
        .accordion__item {
            margin-left: 10px; /* Adjust the left margin */
            margin-right: 10px; /* Adjust the right margin */
        }
        /* Add any other custom CSS styles here */
    </style>

    <div>

    </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card card-user">
                    <div class="card-image color_header">
                    </div>
                    <div class="card-body">
                        <div class="author">
                                <div class="border-gray avatar">
                                    <img src="{{ asset('chapter_theme/img/logo.png') }}" alt="...">
                                </div>
                               <h2 class="moms-c"> MOMS Club of {{ $chapterList[0]->name }}, {{$chapterState}} </h2>
                               <h2 class="moms-c"> Chapter Resources</h2>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $thisDate = \Carbon\Carbon::now();
        @endphp

        <div class="row">
            <div class="col-md-6">
                <div class="accordion js-accordion custom-two-column">
                    <!-- First column of accordion items -->
                    <!------Start Step 1 ------>
                    <div class="accordion__item js-accordion-item">
                        <div class="accordion-header js-accordion-header">BYLAWS</div>
                        <div class="accordion-body js-accordion-body">
                            <section>
                                @foreach($resources->where('category', 1) as $resourceItem)
                                <div class="col-md-12" style="margin-bottom: 5px;">
                                    @if ($resourceItem->link)
                                        <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @elseif ($resourceItem->file_path)
                                        <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @else
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                @endforeach
                                <div class="col-md-12"><br></div>
                            </section>
                        </div>
                    </div>
                    <!------End Step 1 ------>

                    <!------Start Step 2 ------>
                    <div class="accordion__item js-accordion-item">
                        <div class="accordion-header js-accordion-header">FACT SHEETS</div>
                        <div class="accordion-body js-accordion-body">
                            <section>
                                @foreach($resources->where('category', 2) as $resourceItem)
                                <div class="col-md-12"style="margin-bottom: 5px;">
                                    @if ($resourceItem->link)
                                        <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @elseif ($resourceItem->file_path)
                                        <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @else
                                        {{ $resourceItem->name }}
                                    @endif
                                </div>
                                @endforeach
                                <div class="col-md-12"><br></div>
                            </section>
                        </div>
                    </div>
                    <!------End Step 2 ------>

                    <!------Start Step 1 ------>
                <div class="accordion__item js-accordion-item">
                    <div class="accordion-header js-accordion-header">COPY READY MATERIALS</div>
                    <div class="accordion-body js-accordion-body">
                        <section>
                            @foreach($resources->where('category', 3) as $resourceItem)
                            <div class="col-md-12"style="margin-bottom: 5px;">
                                @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
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
                <!------End Step 1 ------>

                <!------Start Step 1 ------>
                <div class="accordion__item js-accordion-item">
                    <div class="accordion-header js-accordion-header">IDEA AND INSPIRATIONS</div>
                    <div class="accordion-body js-accordion-body">
                        <section>
                            @foreach($resources->where('category', 4) as $resourceItem)
                            <div class="col-md-12"style="margin-bottom: 5px;">
                                @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @else
                                    {{ $resourceItem->name }}
                                @endif
                            </div>
                            @endforeach
                            <div class="col-md-12"><br></div>
                        </section>
                    </div>
                </div>
                <!------End Step 1 ------>
                </div>
            </div>

            <div class="col-md-6">
                <div class="accordion js-accordion custom-two-column">
                <!-- Second column of accordion items -->
                <!------Start Step 1 ------>
                <div class="accordion__item js-accordion-item">
                    <div class="accordion-header js-accordion-header">CHAPTER RESOURCES</div>
                    <div class="accordion-body js-accordion-body">
                        <section>
                            @foreach($resources->where('category', 5) as $resourceItem)
                            <div class="col-md-12"style="margin-bottom: 5px;">
                                @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @else
                                    {{ $resourceItem->name }}
                                @endif
                            </div>
                            @endforeach
                            <div class="col-md-12"><br></div>
                        </section>
                    </div>
                </div>
                <!------End Step 1 ------>

                <!------Start Step 2 ------>
                <div class="accordion__item js-accordion-item">
                    <div class="accordion-header js-accordion-header">SAMPLE CHAPTER FILES</div>
                    <div class="accordion-body js-accordion-body">
                        <section>
                            @foreach($resources->where('category', 6) as $resourceItem)
                            <div class="col-md-12"style="margin-bottom: 5px;">
                                @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                @else
                                    {{ $resourceItem->name }}
                                @endif
                            </div>
                            @endforeach
                            <div class="col-md-12"><br></div>
                        </section>
                    </div>
                </div>
                <!------End Step 2 ------>

                <!------Start Step 1 ------>
                <div class="accordion__item js-accordion-item">
                    <div class="accordion-header js-accordion-header">END OF YEAR</div>
                    <div class="accordion-body js-accordion-body">
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
                                        <td>990N IRS Website Link to File</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td>990N Filing Instructions</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td>990N Filing FAQs</td>
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
                <!------End Step 1 ------>
            </div>
        </div>
    </div>
        <!-- end of accordion -->
    <br>
        <div class="col-md-12 text-center">
            <br>
            <a href="{{ route('home') }}" class="btn btn-info btn-fill"><i class="fa fa-reply fa-fw" aria-hidden="true" ></i>&nbsp; Back</a>
            <button type="button"  onclick="window.open('https://momsclub.org/elearning/')" class="btn btn-info btn-fill"><i class="fa fa-graduation-cap fa-fw" aria-hidden="true" ></i>&nbsp; eLearning Library</button>
        </div>
    </div>

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script> $('.demo1').fileselect(); </script>
@section('customscript')
<script>
    var accordion = (function(){
    var $accordion = $('.js-accordion');
    var $accordion_header = $accordion.find('.js-accordion-header');
    var $accordion_item = $('.js-accordion-item');
    // default settings
    var settings = {
        speed: 400,   // animation speed
        oneOpen: false   // close all other accordion items if true
    };

  return {
    // pass configurable object literal
    init: function($settings) {
      $accordion_header.on('click', function() {
        accordion.toggle($(this));
      });

      $.extend(settings, $settings);
      // ensure only one accordion is active if oneOpen is true
      if(settings.oneOpen && $('.js-accordion-item.active').length > 1) {
        $('.js-accordion-item.active:not(:first)').removeClass('active');
      }
      // reveal the active accordion bodies
      $('.js-accordion-item.active').find('> .js-accordion-body').show();
    },
    toggle: function($this) {
      if(settings.oneOpen && $this[0] != $this.closest('.js-accordion').find('> .js-accordion-item.active > .js-accordion-header')[0]) {
        $this.closest('.js-accordion')
               .find('> .js-accordion-item')
               .removeClass('active')
               .find('.js-accordion-body')
               .slideUp()
      }
      // show/hide the clicked accordion item
      $this.closest('.js-accordion-item').toggleClass('active');
      $this.next().stop().slideToggle(settings.speed);
    }
  }
})();

$(document).ready(function(){
    // Modify this line to attach the click event handler to a static parent element
    $(document).on('click', '.js-accordion-header', function() {
        accordion.toggle($(this));
    });

    accordion.init({ speed: 300, oneOpen: true });
});

</script>
