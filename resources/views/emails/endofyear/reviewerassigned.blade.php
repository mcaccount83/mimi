@component('mail::message')
# Assigned Reviewer Notification

You have been assigned to review the financial report for {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}. After reviewing, assign to the next reviewer or mark as review complete.

Attachments:
<ul>
    <li><a href="{{$mailData['roster']}}">Chapter Roster</a></li>
    <li><a href="{{$mailData['bank_statement_path']}}">Bank Statement</a></li>
    <li><a href="{{$mailData['bank_statemet_2_path']}}">Addiational Statement</a></li>
    <li><a href="{{$mailData['irs_path']}}">990N Filing</a></li>
</ul>

**MCL**,<br>
MIMI Database Administrator

@endcomponent
