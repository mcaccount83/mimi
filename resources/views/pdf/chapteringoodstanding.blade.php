<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }} | Chapter In Good Standing</title>
        @php
        $todayDate = date('F j, Y');
        @endphp
</head>
<body>
    <center>
        <img src="images\LOGO-W-MOMS-CLUB-old.jpg" alt="MC" style="width: 125px;">
    </center>
<br>
<div class="keep-together" style="page-break-inside: avoid;">
    <br>
    <p>{{ $todayDate }}</p>
    <br>
    <p>To Whom It May Concern:</p>
    <p>This is to certify that the MOMS Club of {{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }} is a local support chapter registered with the
        International MOMS Club and that its members have agreed to operate their support chapter in a nonprofit manner in accordance with all national and state laws.</p>
    <p>The International MOMS Club’s group exemption number is #3706.  This number is given by the IRS to certify that our registered chapters are nonprofit 501(C)(3)
        public charities.  Upon registering with the International MOMS Club, each chapter also applies for and receives its own employer identification number from the IRS.
        The chapter’s employer identification number is the chapter’s unique number for the IRS and banking purposes.  The EIN number assigned to this local chapter
        is {{ $pdfData['ein'] }}.</p>
    <p>{{ $pdfData['pres_fname'] }} {{ $pdfData['pres_lname'] }}, is the current President of this local chapter.  She is authorized to speak and act on behalf of the
        members of this local support chapter.</p>
    <br>
    <p>Sincerely,</p>
    <br>
    <br>
    <p>{{ $pdfData['cc_fname'] }} {{ $pdfData['cc_lname'] }}<br>
    {{ $pdfData['cc_pos'] }}<br>
    {{ $pdfData['conf_name'] }}, {{ $pdfData['conf_desc'] }}<br>
    International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
</html>
