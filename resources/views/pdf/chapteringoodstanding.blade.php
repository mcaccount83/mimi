<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} | Chapter In Good Standing</title>
        @php
        $todayDate = date('F j, Y');
        @endphp
</head>
<body>
    <center>
        <img src="{{ config('settings.base_url') }}images/logo-mc.png" alt="MC" style="width: 125px;">
    </center>
<br>
<div class="keep-together" style="page-break-inside: avoid;">
    <br>
    <p>{{ $todayDate }}</p>
    <br>
    <p>To Whom It May Concern:</p>
    <p>This is to certify that the MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} is a local support chapter registered with the
        International MOMS Club and that its members have agreed to operate their support chapter in a nonprofit manner in accordance with all national and state laws.</p>
    <p>The International MOMS Club’s group exemption number is #3706.  This number is given by the IRS to certify that our registered chapters are nonprofit 501(C)(3)
        public charities.  Upon registering with the International MOMS Club, each chapter also applies for and receives its own employer identification number from the IRS.
        The chapter’s employer identification number is the chapter’s unique number for the IRS and banking purposes.  The EIN number assigned to this local chapter
        is {{ $pdfData['chapterEIN'] }}.</p>
    <p>{{ $pdfData['presName'] }}, is the current President of this local chapter.  She is authorized to speak and act on behalf of the
        members of this local support chapter.</p>
    <br>
    <p>Sincerely,</p>
    <br>
    <br>
    <p>{{ $pdfData['ccName'] }}<br>
        {{ $pdfData['ccPosition'] }}<br>
        {{ $pdfData['ccConfName'] }}, {{ $pdfData['ccConfDescription'] }}<br>
        International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
</html>
