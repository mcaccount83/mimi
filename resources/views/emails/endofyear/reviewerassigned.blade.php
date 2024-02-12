@component('mail::message')
# Assigned Reviewer Notification

You have been assigned to review the financial report for {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}. After reviewing, assign to the next reviewer or mark as review complete.<br>
<br>
Attachments:
<ul>
    <li><a href="{{$mailData['roster_path']}}">Chapter Roster</a></li>
    <li><a href="{{$mailData['bank_statement_included_path']}}">Bank Statement</a></li>
    <li><a href="{{$mailData['bank_statement_2_included_path']}}">Addiational Statement</a></li>
    <li><a href="{{$mailData['file_irs_path']}}">990N Filing</a></li>
</ul>

<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
