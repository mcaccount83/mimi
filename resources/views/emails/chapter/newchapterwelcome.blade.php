@component('mail::message')
<center>
    <img src="{{ config('settings.base_url') }}images/logo-mc.png" alt="MC" style="width: 125px;">
</center>
<br>
<h1><center>MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}</center></h1>
<p><b><strong>{{ $mailData['presName'] }},</b></p>
<br>
<p>CONGRATULATIONS on getting your chapter officially started!  I've really enjoyed getting to know you and going through the startup process with you.  I'm excited to see what your new chapter will be able to accomplish!</p>
<hr>
<p><b>Your Primary Coordinator and Her Role</b></p>
<p>To help you through your MOMS Club journey, you will be assigned a Primary Coordinator. I've cc'd her on this email so you have her contact information. Be expecting to hear from her with a formal introduction soon.</p>
<p>In the meantime, below are some "Next Steps" to consider as you get started with your new chapter.</p>
<hr>
<p><b>Step 1 - Meet your Coordinator</b></p>
<p>All MOMS Club chapters have an International Coordinator assigned to help them. She is there for anything that you need! Any questions you have or good news you want to share -- talk to her, she loves to hear from you!</p>
<p><center>{{ $mailData['pcName'] }}<br>
                <a href="mailto:{{ $mailData['pcEmail'] }}">{{ $mailData['pcEmail'] }}</a></center></p>
<br>
<hr>
<p><b>Step 2 - MOMS Information Management Interface (MIMI)</b>
<p>MIMI is where important information about your chapter is held. When logged in you can see your EIN number, boundaries, update your contact information, add additional board members, pay your annual re-registration dues, link your website, see who your Coordinators, find additional resources are and more. Always keep your information up to date in MIMI as that is our official record of your chapter.</p>
<p><center><a href="https://momsclub.org/mimi/login">https://momsclub.org/mimi/login</a><br>
    Username: {{ $mailData['presEmail'] }}<br>
    Password: TempPass4You</center></p>
<br>
<hr>
<p><b>Step 3 - Open a Bank Account</b></p>
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
