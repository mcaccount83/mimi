@component('mail::message')
# MOMS Club Chapter Information

{{ $mailData['inquiryFirstName'] }},
<p>Thanks for your interest in MOMS Club.  I am sorry there is not a chapter in your area.  If you are interested in starting one, we would LOVE to help!<br>
<br>
I know the idea of starting a new chapter can be intimidating but it is actually really easy.  If you’d like more information – there is information on our website at <a href="https://momsclub.org/chapters">https://momsclub.org/chapters</a>, and the direct application is <a href="https://momsclub.org/mimi/newchapter">https://momsclub.org/mimi/newchapter</a>.  But, if you have any questions or just want more information, please feel free to ask.<br>
<br>
When you register a MOMS Club, you receive a MOMS Club manual.  This  manual helps you through getting your chapter started.  You also  receive a MOMS Club Coordinator.  This is a person who is there to  help whenever you need it.<br>
<br>
Should you decide that starting one is not for you, please check back from time to time as we do have new chapters starting all the time!<br>
<br>
<strong>MCL,</strong><br>
{{ $mailData['inqCoordName'] }}<br>
Inquiries Coordinator<br>
{{ $mailData['regionLongName'] }} Region<br>
{{ $mailData['conferenceDescription'] }} Conference<br>
International MOMS Club
@endcomponent
