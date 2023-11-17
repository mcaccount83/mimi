@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chpater's anniversy month is <b>{{$mailData['startMonth']}}</b>.

As of today we have not received your chapter’s re-registration fee. All re-registration fees are due annually on the month of your MOMS Club anniversary and it is now considered <b><u>PAST DUE</u></b>.

Below is information for how to calculate your payment as well as the different options available to submit payment.

If you have already submitted your payment, please let us know. Sometimes clerical errors are made and payments do not get applied correctly.
<ul><li>If you paid online, please forward the receipt you received via email.</li>
<li>If you paid via check, please send a copy of your cleared check.</li>
<li>If you paid via check, and it has not cleared yet, please provide us with an approximate mailing date.</li>
<li>If there was an error, we’ll be sure to get it corrected as quickly as possible.</li>
<li>If you have not submitted your payment, please follow the instructions below and include a $10 late fee when submitting.</li></ul>

To calculate your payment:
<ul><li>Determine how many people paid dues to your chapter from <b>{{$mailData['startRange']}}</b> through <b>{{$mailData['endRange']}}</b></li>
<li>Add in any people who paid reduced dues or had their dues waived due to financial hardship</li>
<li>If this total amount of members is less than 10, make your check for the amount of $50</li>
<li>If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due</li></ul>

Pay online: https://momsclub.org/resources/re-registration-payment<br>
Password: daytime support

OR to pay by mail:
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
Sustaining chapter donations are voluntary and in addition to your chapter's re-registration dues.  At this time, the minimum sustaining chapter donation is $100.  The donation benefits the International MOMS Club, which is a 501 (c)(3) public charity.  Your support to the MOMS Club is a service project for your chapter and should be included in its own line on your chapter's Annual and Financial Reports.  Your donation will help us keep dues low and help new and existing chapters in the U.S. and around the world.
</p>
</td></tr></table>
<br>
If payment is not recevied by the last day of <b>{{$mailData['dueMonth']}}</b> your chapter will be placed on probation.

You can view/update your chapter details at any time by logging into MIMI at https://momsclub.org/mimi.

If you have any questions at all, do not hesitate to ask.

**MCL,**<br>
MIMI Database Administrator
<br>
@endcomponent
