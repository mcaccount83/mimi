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
    <p>As your chapter has previously been made aware, your Re-Registration renewal date was {{ $pdfData['month'] }} {{ $pdfData['year'] }} and if not received in a timely manner the chapter would be put on
        probation.  Any chapter that is on probation is at risk for having their MOMS Club affiliation revoked and not being covered by our nonprofit status. </p>
    <p>Chapters on probation may not attend multi-chapter events, such as luncheons or training sessions, or participate in the Board/Member on-line discussion groups.  In
        addition, the International MOMS Club may not direct any inquiries to your chapter from the MOMSClub.org website, and if your chapter has a site linked to MOMSClub.org
        it may be removed.</p>
    <p>To date, your chapter’s re-registration payment has not been received so your chapter has been placed on probation. Please remit payment by {{ $nextMonthDate }} or your chapter’s
        MOMS Club affiliation will be revoked and your chapter will be forced to disband.</p>
    <p>Payment can be submitted by check or online thorugh your MIMI login at https://momsclub.org/mimi/.</p>
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