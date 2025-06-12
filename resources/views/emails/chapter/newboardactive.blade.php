@component('mail::message')
<center>
    <img src="{{ config('settings.base_url') }}images/logo-mc.png" alt="MC" style="width: 125px;">
</center>
<br>
<h1><center>MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}</center></h1>
<p><b><strong>Welcome to the {{$mailData['yearRange']}} Executive Board!</b></p>
<br>
<p>Welcome, {{$mailData['chapterName']}}, {{$mailData['chapterState']}} board members! Congratulations on being elected to the executive board for your chapter. We hope you have a fantastic year in store. Read on for some tips and information that will help you have a successful year. We look forward to working with you!</p>
<hr>
<p><b>Officer Checklist</b></p>
<p>The following are a few things every Officer needs to know!</p>
<ul>
    <li>Get all your members involved and protect your officers by having the members vote on everything at the monthly business meeting. Plan something fun at each business meeting so members will want to come — a fun activity or speaker — but also have your members vote on everything that the chapter is doing, being sure to get them to volunteer to help at the same time!</li>
    <li>The IRS requires that all nonprofit groups use their money for true nonprofit purposes, so make sure that your treasury pays less than 15% of your chapter's dues income for party expenses or anything that benefits members specifically (like t-shirts or something that goes directly to the members.) Potluck parties or things that the members pay for directly don't count, and will help keep your dues low, too.</li>
    <li>We want to know what you're doing so we can spread the word about how great you are! Don't be modest — contact your Primary Coordinator once a month and send her and your secondary Coordinator a copy of your newsletter, calendar or a summary of your activities!</li>
    <li>Let your members know who we are! Please include the name, title and email address of your Primary Coordinator in each month's newsletter/calendar. You should also include our general email address support@momsclub.org. </li>
    <li>Remember you're there specifically for at-home mothers, so keep your activities during the day. One evening activity a month is fine, but the rest need to be during the day so at-home mothers can easily attend.</li>
    <li>Take pride in your name! MOMS Club is a registered service mark, so it's important that everyone uses it correctly. No periods, no apostrophe, and always include your chapter's geographic name so everyone knows you're you!</li>
    <li>Have FUN! Being president is a lot of fun! Delegate, then relax, smile and enjoy your term!</li>
</ul>
<hr>
<p><b>Office Packet</b></p>
<p>This year's Officer's Resource Packet is attached.</p>
<br>
<hr>
<p><b>MOMS Information Management Interface (MIMI)</b>
<p>MIMI (MOMS Information Management Interface) is the database system for International MOMS Club. To access MIMI go to the following link:</p>
<p><center><a href="https://momsclub.org/mimi/login">https://momsclub.org/mimi/login</a></center></p>
<p>You can log on with the e-mail address we have on file for you. If you are a new board member, your default password is: TempPass4You</p>
<p>The chapter president will have access to all chapter information, including all board members. Other board members will have access to chapter information as well as their own details. (All board members have access to MIMI!).</p>
<p>Things to check the first time you log in:</p>
<ul>
    <li>Change your password if it is still set with the default password.</li>
    <li>Check that all contact details are correct, making updates as necessary.</li>
    <li>Read through your chapter’s boundaries. If you feel these are not correct, contact your Primary Coordinator.</li>
    <li>Check out the current website we have listed for your chapter, and update if necessary. Click to have your site linked to the International site, if it is not already.</li>
    <li>Make sure your chapter’s e-mail address and the e-mail to give to any inquiries is up to date.</li>
    <li>Note the contact information listed for your chapter’s volunteers. If you ever have trouble reaching your Primary Coordinator, you can click on the name of your Secondary Coordinator to email them.</li>
    <li>You can log on to MIMI anytime. if you receive error messages or if you have any questions at all, please let your Primary Coordinator know!</li>
</ul>
<br>
<hr>
<p><b>eLearning Portal</b></p>
<p>Now that you have your EIN Number ({{ $mailData['chapterEIN'] }}) you should open a Chapter Checking Account. Check your area for banks that offer free accounts to non-profits. In addtion to your EIN , you will likely need a copy of the Group Exemption Letter (attached) as well as the </p>
<hr>
<p><b>Step 4 - Set your Dues</b></p>
<p>Most chapters charge between $20-$35 per year to their members. You will have an annual re-registration payment due to International at $5/member ($50 minimum) so be sure to include that in your calculations. Read more about how income from dues may be used.</p>
<hr>
<p><b>Step 5 - Hold your first Meeting</b></p>
<p>As a chapter you’ll hold a monthly meeting where business can be discussed and voted on. All members (and potential members) are invited to attend the monthly meeting. It may be held at a local park, a library, a church, whatever works for your area, time of year and budget.</p>
<hr>
<p><b>Step 6 - Look for Board Members</b></p>
<p>To be successful, you will need help!  As you start getting new members, be on the lookout for any potential board members. If someone isn’t interested in a board position, get them involved as a playgroup or service project coordinator. The more people are personally involved, the more interest they will have in helping the new chapter to become successful!</p>
<hr>
<p><b>Step 7 - Additional Resources</b></p>
<p>As previously mentioned, be sure to check the "Chapter Resources" section in your MIMI profile. You’ll find FACT Sheets on various topics, sample files, logo downloads, etc.</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
