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
    <p>At this time we have concluded that your chapter has met all of the requirements to have your probationary status lifted.  </p>
    <p>Please place this letter in your chapter’s permanent files. </p>
    <p>If you have further questions, or need assistance, please contact me or your primary coordinator.</p>
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
