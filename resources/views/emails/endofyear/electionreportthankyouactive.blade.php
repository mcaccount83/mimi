@component('mail::message')
# Election Report Submitted & Activated

<p>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, thank you for submitting
    your Election Report.</p>
<p>
    Your incoming board members have automaticaly been activated and have access to your
    chapter's MIMI account.</p>
<p>
    Outgoing board members will still be able to log in and access the Financial Report.  Financial
    Reports are due on July 15th.</p>
<br>
<p><strong>MCL,</strong><br>
International MOMS Club</p>
@endcomponent
