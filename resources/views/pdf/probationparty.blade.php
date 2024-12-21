<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }} | Probation Letter</title>
        @php
        $todayDate = date('F j, Y'); // e.g., July 9, 2024
        $date = new DateTime();
        $date->modify('+1 month');
        $nextMonthDate = $date->format('F j, Y'); // e.g., August 9, 2024
        @endphp
</head>
<body>
    <center>
        <img src="https://momsclub.org/mimi/theme/dist/img/logo-old.jpg" alt="MC" style="width: 125px;">
    </center>
<br>
    <p>{{ $todayDate }}</p>
    <br>
    <p>MOMS Club of {{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }}<br>
        {{ $pdfData['pres_fname'] }} {{ $pdfData['pres_lname'] }}<br>
        {{ $pdfData['pres_addr'] }}<br>
        {{ $pdfData['pres_city'] }},  {{ $pdfData['pres_state'] }}  {{ $pdfData['pres_zip'] }}</p>
    <br>
    <p>Dear {{ $pdfData['pres_fname'] }}:</p>
    <p>After final review of your chapter’s Annual Reports, there was an excessive amount spent by your chapter for parties and other related activities benefiting
        members only.  By IRS rules, all of our local chapters must follow the same local chapter Bylaws, and all instruction from International.  All chapters have been
        instructed that the total paid for parties and other activities benefiting members only are not to exceed an amount equal to 15% of the dues received by the chapter
        during the year.</p>
    <p>Because your chapter exceeded the guidelines relating to party expenses, we have no choice but to place your chapter on probation.  The terms of your chapter’s probation
        are simple: </p>
        <ul>
            <li>Submit quarterly Financial Reports to your Primary Coordiator outlining the amount of dues received both during that quarter and year-to-date, and the amount of
                funds spent on parties or other related activities, both during the quarter and year-to-date.</li>
                <br>
            <li>Submit a budget for each individual party or event held by your chapter, for which the chapter pays more than $25.  This budget is due to the Primary Coordinator
                at least 2 weeks prior to the planned activity or party.</li>
        </ul>
    <p>While your chapter is on probation, at International’s discretion, you may be suspended from the Board Member Forum and/or the chapter’s website link on momsclub.org may
        be dropped.</p>
    <p>Normally probation lasts for at least a year, but when we are assured that the chapter is fulfilling the terms of their probation and is following the Bylaws and other
@ -54,7 +53,7 @@
    <br>
    <p>{{ $pdfData['cc_fname'] }} {{ $pdfData['cc_lname'] }}<br>
    {{ $pdfData['cc_pos'] }}<br>
    {{ $pdfData['cc_conf_name'] }}, {{ $pdfData['cc_conf_desc'] }}<br>
    International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
