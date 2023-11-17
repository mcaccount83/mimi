@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chapter has celebrated another year of offering support to the at-home mothers in your area!

This is the reminder that <b>{{$mailData['startMonth']}}</b> is your chapter's anniversary with the International MOMS Club and it is time to pay the chapter's re-registration fee, if you haven't done so already.

To calculate your payment:
<ul><li>Determine how many people paid dues to your chapter from <b>{{$mailData['startRange']}}</b> through <b>{{$mailData['endRange']}}</b></li>
<li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
<li>If this total amount of members is less than 10, make your check for the amount of $50</li>
<li>If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due</li>
<li>Payments received after the last day of <b>{{$mailData['startMonth']}}</b> should include a late fee of $10</li></ul>

You online: https://momsclub.org/resources/re-registration-payment<br>
Password: daytime support

OR to pay by mail:<br>
Be sure your full chapter name, including state abbreviation, is on your check so that the re-registration can be credited properly.
<ul><li>Make check payable to MOMS Club</li>
<li>Please write "Chapter Re-Registration" in the Memo field of your check</li>
<li>As requested with all chapter checks, be sure two Executive Board members sign the check</li>
<li>Print this page, fill out the box below and mail the page, along with the re-registration, check to:</li></ul>
<center>International MOMS Club<br>
Chapter Re-Registration<br>
208 Hewitt Dr., Ste 103 #328<br>
Waco, TX 76712</center>
<br>
<table style="border:1px solid #000;"><td><b>MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}</b><br>
Anniversary Month: <b>{{$mailData['startMonth']}}</b></td></tr>
<tr><td>
<br>
<u>$</u>________ Re-Registration Dues enclosed for _______ members<br>
<u>$</u>________ Late fee included<br>
<u>$</u>________ Sustaining Chapter<br>
<u>$</u>________ Total<br>
<br>
<p style="font-size:12px; ">
Sustaining chapter donations are <u>voluntary</u> and in addition to your chapter's re-registration dues.  At this time, the minimum sustaining chapter donation is $100.  The donation benefits the International MOMS Club, which is a 501 (c)(3) public charity.  Your support to the MOMS Club is a service project for your chapter and should be included in its own line on your chapter's Annual and Financial Reports.  Your donation will help us keep dues low and help new and existing chapters in the U.S. and around the world.
</p>
</td></tr></table>
<br>
Thank you for your prompt renewal payment and/or sustaining chapter donation! If you have any questions, please do not hesitate to contact your chapter's Primary Coordinator.

You can view/update your chapter details at any time by logging into MIMI at https://momsclub.org/mimi.

**MCL,**<br>
MIMI Database Administrator
<br>
@endcomponent
