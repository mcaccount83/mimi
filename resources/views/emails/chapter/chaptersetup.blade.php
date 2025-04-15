@component('mail::message')
<center>
    <img src="{{ config('settings.base_url') }}images/logo-mc.png" alt="MC" style="width: 125px;">
</center>
<br>
<p><b>{{ $mailData['founderFirstName'] }}:</b></p>
<p>I'm super excited that you have decided to start a chapter. My name is {{ $mailData['userName'] }}, I'm the {{ $mailData['userPosition'] }} for your area and will be helping you with the initial setup of your chapter. Once we have your EIN you'll officially be a MOMS Club chapter and will start working with your Primary Coordinator to get the chapter growing in your area!</p>
<p>Below are the steps that I'll be helping you work through. A Getting Started Guide (with the steps outlined below) as well as the EIN Application and Instructions are also attached for your reference.</p>
<hr>
<p><b>Step 1 -  Establish your Boundaries</b></p>
<p>Choosing the right boundaries is important. Your area should cover what potential members consider to be “local”, allowing a large enough area to sustain a chapter and small enough that members are willing to drive to events in all areas of your boundaries.</p>
<p>{{ $mailData['boundaryDetails'] }}</p>
<hr>
<p><b>Step 2 - Choose a Name</b></p>
<p>You’ll want to choose a name that represents your area at a quick glance. Potential members should be able to tell the areas that you cover based on your chapter’s name.</p>
<p>{{ $mailData['nameDetails'] }}</p>
<hr>
<p><b>Step 3 - File for an EIN</b></p>
<p>Once you have your Chapter Name, you can apply for your EIN.  You’ll want to establish your EIN as quickly as possible so that you can be included in our master filings, can open a checking account and begin accepting donations as a 501(C)(3) nonprofit.</p>
<p>Complete the EIN Application and follow the attached instructions, emailing the completed form back to {{ $mailData['ccEmail'] }}.</p>
<p>Once you have your EIN, you will be an official MOMS Club Chapter!</p>
<hr>
<p><b>Step 4 - Review your Manual</b></p>
<p>You’ll receive a manual in the mail shortly after your new chapter has been approved. While there is a lot of information in the manual, do not feel overwhelmed and do not feel like you need to read it all right away. All of our “first steps” are listed right here in this guide. The additional information in the manual will be helpful as your chapter grows.</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
