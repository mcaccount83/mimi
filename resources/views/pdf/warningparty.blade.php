<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} | Probation Letter</title>
</head>
<body>
    <center>
        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
    </center>
<br>
    <p>{{ $currentDateWords }}</p>
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
    <p>Although your chapter will not be placed on probation at this time, at International’s discretion, a probationary status may be added at a later date.</p>
    <p>Because of the seriousness of this situation, you must reply to me in writing (email is fine) so that I receive your reply by {{ $nextMonthDateWords }}, indicating that you and your
        chapter understand and will take appropriate steps to ensure this doesn’t happen again.  </p>
    <p>This warning is not intended to punish your chapter, but rather to give your chapter the opportunity to be sure you thoroughly understand the guidelines, and to work
        closely with your Primary Coordinator to ensure your chapter’s operation is in compliance with the MOMS Club Bylaws and guidelines, along with all applicable IRS rules.</p>
    <p>If you have any questions concerning how to appropriately budget parties and other activities and events that benefit members only, please do not hesitate to contact your Primary
        Coordinator or myself.</p>
    <p>Sincerely,</p>
    <br>
    <br>
    <p>{{ $pdfData['userName'] }}<br>
        {{ $pdfData['userPosition'] }}<br>
        {{ $pdfData['userConfName'] }}, {{ $pdfData['userConfDesc'] }}<br>
    International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
</html>
