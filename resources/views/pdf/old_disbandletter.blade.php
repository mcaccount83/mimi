<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }} | Disband Letter</title>
</head>
<body>
    <center>
        <img src="{{ config('settings.base_url') }}images/logo-mc.png" alt="MC" style="width: 125px;">
    </center>
<br>
    <p>{{ $pdfData['today'] }}</p>
    <br>
    <p>MOMS Club of {{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }}<br>
        {{ $pdfData['pres_fname'] }} {{ $pdfData['pres_lname'] }}<br>
        {{ $pdfData['pres_addr'] }}<br>
        {{ $pdfData['pres_city'] }},  {{ $pdfData['pres_state'] }}  {{ $pdfData['pres_zip'] }}</p>
    <br>
    <p>Dear {{ $pdfData['pres_fname'] }}:</p>
    <p>This is to inform you that the MOMS Club of {{ $pdfData['chapter_name'] }}, {{ $pdfData['state'] }} has disbanded.  If you believe this information is incorrect,
        then please contact me immediately.</p>
    <p>To be disbanded, all members of the chapter must have been polled and have unanimously decided that they did not, individually, wish to continue with the chapter.
        If that decision was unanimous, then the chapter is disbanded and its affiliation with the MOMS Club is terminated and all benefits of its association
        with the MOMS Club are revoked.  If the vote was not unanimous, then the chapter may still be continuing and you need to forward to me the names and
        contact information of those who wish to continue the chapter.</p>
    <p>If the chapter has disbanded, as its last president, there are certain things that must be handled to fulfill the responsibilities you took on as an officer.
        If you have not already taken care of the items below, these items must be taken care of by {{ $pdfData['nextMonth'] }}.</p>
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
    <p>{{ $pdfData['cc_fname'] }} {{ $pdfData['cc_lname'] }}<br>
    {{ $pdfData['cc_pos'] }}<br>
    {{ $pdfData['conf_name'] }}, {{ $pdfData['conf_desc'] }}<br>
    International MOMS Club<sub>&reg;</sub></p>
</div>
</body>
</html>
