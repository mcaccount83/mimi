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
    <p>At this time we have concluded that your chapter has met all of the requirements to have your probationary status lifted.  </p>
    <p>Please place this letter in your chapter’s permanent files. </p>
    <p>If you have further questions, or need assistance, please contact me or your primary coordinator.</p>
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
