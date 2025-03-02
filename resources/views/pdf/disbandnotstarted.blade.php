<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} | Disband Letter</title>
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
{{-- <div class="keep-together" style="page-break-inside: avoid;"> --}}
    <p>{{ $todayDate }}</p>
    <br>
    <p>MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }}<br>
        {{ $pdfData['presName'] }}<br>
        {{ $pdfData['presAddress'] }}<br>
        {{ $pdfData['presCity'] }},  {{ $pdfData['presState'] }}  {{ $pdfData['presZip'] }}</p>
    <br>
    <p>Dear {{ $pdfData['presName'] }}:</p>
    <p>It’s our understanding that you did not start your MOMS Club chapter as you had planned. We know that plans sometimes change, so we want you to know that we appreciate
        your good intentions.</p>
    <p>Since you did not start your chapter, there is still one thing you need to do according to the commitment you signed on the registration form. Because only on-going
        registered chapters may have MOMS Club manuals, you need to make sure the chapter manual and all its contents are destroyed or is returned, postage paid, to the
        International MOMS Club.  To complete this, send your chapter’s manual, postage paid to: 208 Hewitt Dr., Ste 103 #328, Waco, TX 76712.</p>
    <p>When you return the manual, the original registration fee of $30 and the postage for returning the manual should be tax-deductible for your income tax as a donation to the
        MOMS Club. Any other expenses you incurred related to the chapter you did not start should also be tax-deductible.</p>
    <p>Good luck with your future endeavors! Whether or not you started a MOMS Club chapter, as an at-home mother, you are very important!</p>

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
