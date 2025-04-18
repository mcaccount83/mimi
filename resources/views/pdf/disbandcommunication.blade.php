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
        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
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
    <p>This is to inform you that the MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} has disbanded.  If you believe this information is incorrect,
        then please contact me immediately.</p>
    <p>After multiple attempts, We have been unsuccessful in establishing communication with your chapter. As a result, your chapter is disbanded, and its affiliation with the
        International MOMS Club is terminated and all benefits of its association with the International MOMS Club are revoked.  </p>
    <p>As its last president, there are certain things that must be handled to fulfill the responsibilities you took on as an officer.
        If you have not already taken care of the items below, these items must be taken care of by {{ $nextMonthDate }}.</p>
        <ol>
            <li>Because your chapter has been disbanded, it is no longer covered under our IRS group exemption. Any money or assets not properly donated may be considered
                by the IRS as having been used personally by the former officers or members, and taxed accordingly. If the chapters’ funds are donated to the
                MOMS Club, then we will be able to vouch to the IRS that the funds were properly donated.<br>
                <br>
                If there are any chapter funds left in the treasury at the time of your disbanding, you need to send a check for that amount made out to "MOMS
                Club", or another IRS registered, 501(c)(3) charity in which none of the chapter’s members, or former members, are involved. Also, any re-registration dues
                for the past year need to be paid to the MOMS Club. Those dues are $5 per member who renewed their membership since the last time your chapter’s
                registration was due, with a minimum of $50 to be paid. If you decide to donate your treasury to the MOMS Club, we will use that money to cover
                your chapter’s dues, and any additional amount will be a charitable donation. Please note on the check that the funds are for a disbanded chapter. Please mail
                the dues and/or treasury funds to: MOMS Club, 208 Hewitt Dr., Ste 103 #328, Waco, TX 76712.</li>
                <br>
            <li>Because only on-going registered chapters may have MOMS Club manuals, you need to make sure the chapter manual and all its contents are destroyed OR it is returned,
                postage paid, to: MOMS Club, 208 Hewitt Dr., Ste 103 #328, Waco, TX 76712.</li>
                <br>
            <li>Because you are no longer affiliated with the MOMS Club, neither the former officers nor members of your disbanded chapter may use the name
                “MOMS Club,” the “MOMS Offering Moms Support” slogan, or the MOMS Club logo, as those are registered service marks of the MOMS Club and you do not
                have permission to use them any longer.  You may not represent yourselves, either individually or collectively, as connected to the MOMS Club in any way.</li>
                <br>
            <li>You must immediately dismantle and remove from the internet any websites referencing your former chapter, and dissolve any social media accounts, listserv or email
                groups, or email addresses that reference the MOMS Club.</li>
                <br>
            <li>Finally, you must complete this fiscal year’s annual financial reports.  Keep copies of those reports and any that were completed during the life of your
                chapter.  Additionally, keep your former chapter’s financial records, as any inquiries by the IRS will be directed to the last officers of the former chapter,
                not our volunteers.
                <ul>
                    <li>Final MOMS Club Financial Report</li>
                    <li>Final 990N IRS Filing (being sure to check the box that the chapter has terminated)</li>
                </ul>
            </li>
                <br>
        </ol>
    <p>Thank you for your immediate attention to the matters.  If you have any questions, please contact your former primary coordinator.</p>
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
