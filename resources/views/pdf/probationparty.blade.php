<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} | Probation Letter</title>
        @php
        $todayDate = date('F j, Y'); // e.g., July 9, 2024
        $date = new DateTime();
        $date->modify('+1 month');
        $nextMonthDate = $date->format('F j, Y'); // e.g., August 9, 2024
        @endphp
</head>
<body>
    <center>
        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
    </center>
<br>
    <p>{{ $todayDate }}</p>
    <br>
    <p>MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }}<br>
        {{ $pdfData['presName'] }}<br>
        {{ $pdfData['presAddress'] }}<br>
        {{ $pdfData['presCity'] }},  {{ $pdfData['presState'] }}  {{ $pdfData['presZip'] }}</p>
    <br>
    <p>Dear {{ $pdfData['presName'] }}:</p>
    <p>After final review of your chapter’s Annual Reports, there was an excessive amount spent by your chapter for parties and other related activities benefiting
        members only.  By IRS rules, all of our local chapters must follow the same local chapter Bylaws, and all instruction from International.  All chapters have been
        instructed that the total paid for parties and other activities benefiting members only are not to exceed an amount equal to 15% of the dues received by the chapter
        during the year.</p>
    <p>Because your chapter exceeded the guidelines relating to party expenses, we have no choice but to place your chapter on probation.  The terms of your chapter’s probation
        are simple: </p>
        <ul>
            <li>Submit quarterly Financial Reports to your Primary Coordiator outlining the amount of dues received both during that quarter and year-to-date, and the amount of
                funds spent on parties or other related activities. This can be submitted directly through your MIMI login.</li>
                <br>
            <li>Submit a budget for each individual party or event held by your chapter, for which the chapter pays more than $25.  This budget is due to the Primary Coordinator
                at least 2 weeks prior to the planned activity or party.</li>
        </ul>
    <p>While your chapter is on probation, at International’s discretion, you may be suspended from the Board Member Forum and/or the chapter’s website link on momsclub.org may
        be dropped.</p>
    <p>Normally probation lasts for at least a year, but when we are assured that the chapter is fulfilling the terms of their probation and is following the Bylaws and other
        guidelines, we will consider shortening the length.  If the terms of probation are not fulfilled, we will have no choice but to revoke your chapter’s registration, and
        your chapter will have to disband, losing its nonprofit status (for both the current and past fiscal years) and MOMS Club support.</p>
    <p>
        Because of the seriousness of this situation, you must reply to me in writing (email is fine) so that I receive your reply by {{ $nextMonthDate }}, indicating that you and
        your chapter understand and agree to comply with the terms of this probation.</p>
    <p>
        This probation is not intended to punish your chapter, but rather to give your chapter the opportunity to be sure you thoroughly understand the guidelines, and to work
        closely with your Primary Coordinator to ensure your chapter’s operation is in compliance with the MOMS Club Bylaws and guidelines, along with all applicable IRS rules.</p>
    <p>If you have any questions concerning either your chapter’s probationary status, or how to appropriately budget parties and other activities and events that benefit members only,
        please do not hesitate to contact your Primary Coordinator or myself.</p>
    <br>
    <p>{{ $pdfData['userName'] }}<br>
        {{ $pdfData['userPosition'] }}<br>
        {{ $pdfData['userConfName'] }}, {{ $pdfData['userConfDesc'] }}<br>
    International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
