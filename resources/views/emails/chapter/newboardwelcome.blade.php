@component('mail::message')
<h1><center>MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}</center></h1>
<b><center>Welcome to the {{$mailData['fiscalYear']}} Executive Board!</center></b><br>
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
<p><b>Officer Packet</b></p>
<p>You will find the Officer's Resource Packet is attached. The officer packet includes more details about a variety of topics including Party/Member Benefit Expenses, Geographic Boundaries, Sistering, Annual Budget, Ideas for Board Meeting Agendas and more.</p>
<br>
<hr>
<p><b>MOMS Information Management Interface (MIMI)</b>
<p>MIMI is the database system for International MOMS Club. To access MIMI go to the following link:<br>
<center><a href="https://momsclub.org/mimi/login">https://momsclub.org/mimi/login</a></center></p>
<br>
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
<hr>
<p><b>Coordinator Team</b></p>
<p>All MOMS Club chapters have an International Coordinator assigned to help them. She is there for anything that you need! Any questions you have or good news you want to share -- talk to her, she loves to hear from you!<br>
<center>{{ $mailData['pcName'] }}<br>
                <a href="mailto:{{ $mailData['pcEmail'] }}">{{ $mailData['pcEmail'] }}</a></center></p>
<br>
<hr>
<p><b>Chapter Resources</b></p>
<p>Other resources including the Bylaws, Fact Sheets, Sample Files, Digital Logos, etc are available in MIMI.</p>
<hr>
<p><b>BoardList Forum</b></p>
<p>The BoardList forum group will give you a chance to interact with other board members on chapter related topics. All board members are automatically added. BoardList is open from August through May.</p>
<hr>
<p><b>eLearning Portal</b></p>
<p>Be sure to check out the MOMS Club eLearning site. We will continue to add more courses throughout the year.<br>
    https://momsclub.org/elearning/chapter-training</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['ccName'] }}<br>
    {{ $mailData['ccPosition'] }}<br>
    {{ $mailData['ccConfName'] }}, {{ $mailData['ccConfDescription'] }}<br>
    International MOMS Club</p>
@endcomponent
