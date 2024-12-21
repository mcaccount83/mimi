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
    <p>As your chapter has previously been made aware, Financial Reports were to be completed by July 15th and any chapter not having submitted
        reports by July 31st would be put on probation.  Any chapter that is on probation is at risk for having their MOMS Club affiliation revoked and not being covered by our
        nonprofit status for LAST year.  Chapters on probation may not attend multi-chapter events, such as luncheons or training sessions, or participate in the PrezList on-line
        discussion group for chapter presidents.  In addition, the International MOMS Club may not direct any inquiries to your chapter from the MOMSClub.org website, and if your
        chapter has a site linked to MOMSClub.org it may be removed.  It is up to the chapter to request to be reconnected to the PrezList and/or to have their site re-linked
        once your probationary status is lifted.</p>
    <p>To date, your chapter’s complete reports have not been received so your chapter has been placed on probation. Please complete both the Board Report and the Financial Report
        by {{ $nextMonthDate }} or your chapter’s MOMS Club affiliation will be revoked and your chapter will be forced to disband.</p>
    <p>While we would hate for your chapter to lose its MOMS Club affiliation, our nonprofit status is very important, and all chapters must meet the same reporting deadlines for
        us to ensure that it is preserved.  If you are unable to complete the reports yourself, they may be filled out by another member of your chapter.  However, as I have
        previously stated, they must be completed by {{ $nextMonthDate }}.  </p>
    <p>If you have further questions, or need assistance, please contact me or your primary coordinator immediately.</p>
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
