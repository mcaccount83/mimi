@component('mail::message')
# Assigned Reviewer Notification

You have been assigned to review the financial report for {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}. After reviewing, assign to the next reviewer or mark as review complete.<br>
<br>
The Financial Report PDF is attached and other documnets that can be downloaded are listed below.<br>
<br>
Submitted by: {{$mailData['completed_name']}}, <a href="mailto:{{$mailData['completed_email']}}">{{$mailData['completed_email']}}</a><br>
<br>
Downloads Available:
<ul>
    <li>@if (isset($mailData['roster_path']))
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['roster_path']; ?>">Chapter Roster</a>
        @else
        No Roster Attached
        @endif</li>
    <li>@if (isset($mailData['bank_statement_included_path']))
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['bank_statement_included_path']; ?>">Primary Bank Statement</a>
        @else
        No Statement Attached
        @endif</li>
    @if (isset($mailData['bank_statement_2_included_path']))<li>
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['bank_statement_2_included_path']; ?>">Additional Bank Statement</a>
        </li>@endif
    <li>@if (isset($mailData['file_irs_path']))
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['file_irs_path']; ?>">990N Confirmation File</a>
        @else
        No 990N File Attached
        @endif</li>
</ul>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
