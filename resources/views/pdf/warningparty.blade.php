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
    <p>Although your chapter will not be placed on probation at this time, at International’s discretion, a probationary status may be added at a later date.</p>
    <p>Because of the seriousness of this situation, you must reply to me in writing (email is fine) so that I receive your reply by {{ $nextMonthDate }}, indicating that you and your
        chapter understand and will take appropriate steps to ensure this doesn’t happen again.  </p>
    <p>This warning is not intended to punish your chapter, but rather to give your chapter the opportunity to be sure you thoroughly understand the guidelines, and to work
        closely with your Primary Coordinator to ensure your chapter’s operation is in compliance with the MOMS Club Bylaws and guidelines, along with all applicable IRS rules.</p>
    <p>If you have any questions concerning how to appropriately budget parties and other activities and events that benefit members only, please do not hesitate to contact your Primary
        Coordinator or myself.</p>
    <p>Sincerely,</p>
    <br>
    <br>
    <p>{{ $pdfData['cc_fname'] }} {{ $pdfData['cc_lname'] }}<br>
    {{ $pdfData['cc_pos'] }}<br>
    {{ $pdfData['cc_conf_name'] }}, {{ $pdfData['cc_conf_desc'] }}<br>
    International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
</html>