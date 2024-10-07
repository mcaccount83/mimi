@component('mail::message')
<center>
    <img src="https://momsclub.org/mimi/images/LOGO-W-MOMS-CLUB-old.jpg" alt="MC" style="width: 125px;">
</center>
<br>
<h1><center>MOMS Club of {{$mailData['chapter']}}, {{$mailData['state']}}</center></h1>
<h4> {{ $mailData['firstName'] }},</h4>
<p>CONGRATULATIONS on getting your chapter officially started!  I've really enjoyed getting to know you and going through the startup process with you.  I'm excited to see what your new chapter will be able to accomplish!</p>
<hr>
<h4>Your Mentoring Coordinator and Her Role</h4>
<p>To help you through your MOMS Club journey, you will be assigned a Primary Coordinator. I've cc'd her on this email so you have her contact information. Be expecting to hear from her with a formal introduction soon.</p>
<p>In the meantime, below are some "Next Steps" to consider as you get started with your new chapter.</p>
<hr>
<h4>Step 1 - Meet your Coordinator</h4>
<p>All MOMS Club chapters have an International Coordinator assigned to help them. She is there for anything that you need! Any questions you have or good news you want to share -- talk to her, she loves to hear from you!</p>
<p><center>{{ $mailData['cor_fname'] }} {{ $mailData['cor_lname'] }}<br>
                <a href="mailto:{{ $mailData['cor_email'] }}">{{ $mailData['cor_email'] }}</a></center></p>
<br>
<hr>
<h4>Step 2 - MOMS Information Management Interface (MIMI)</h4>
<p>MIMI is where important information about your chapter is held. When logged in you can see your EIN number, boundaries, update your contact information, add additional board members, pay your annual re-registration dues, link your website, see who your Coordinators, find additional resources are and more. Always keep your information up to date in MIMI as that is our official record of your chapter.</p>
<p><center><a href="https://momsclub.org/mimi/login">https://momsclub.org/mimi/login</a><br>
    Username: {{ $mailData['email'] }}<br>
    Password: TempPass4You</center></p>
<br>
<hr>
<h4>Step 3 - Open a Bank Account</h4>
<p>Now that you have your EIN and the Group Exemption Letter you should open a Chapter Checking Account. Check your area for banks that offer free accounts to non-profits.</p>
<hr>
<h4>Step 4 - Set your Dues</b>
<p>Most chapters charge between $20-$35 per year to their members. You will have an annual re-registration payment due to International at $5/member ($50 minimum) so be sure to include that in your calculations. Read more about how income from dues may be used.</p>
<hr>
<h4>Step 5 - Hold your first Meeting</h4>
<p>As a chapter you’ll hold a monthly meeting where business can be discussed and voted on. All members (and potential members)  are invited to attend the monthly meeting. It may be held at a local park, a library, a church, whatever works for your area, time of year and budget.</p>
<hr>
<h4>Step 6 - Look for Board Members</h4>
<p>To be successful, you will need help!  As you start getting new members, be on the lookout for any potential board members. If someone isn’t interested in a board position, get them involved as a playgroup or service project coordinator. The more people are personally involved, the more interest they will have in helping the new chapter to become successful!</p>
<hr>
<h4>Step 7 - Additional Resources</h4>
<p>As previously mentioned, be sure to check the "Chapter Resources" section in your MIMI profile. You’ll find FACT Sheets on various topics, sample files, logo downloads, etc.</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['positionTitle'] }}<br>
    Conference {{ $mailData['conf'] }}, {{ $mailData['conf_name'] }}<br>
    International MOMS Club</p>
@endcomponent
