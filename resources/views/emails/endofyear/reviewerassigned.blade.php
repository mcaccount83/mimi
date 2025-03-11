@component('mail::message')
# Assigned Reviewer Notification

{{$mailData['userName']}} has assigned you to review the financial report for {{$mailData['chapterName']}}, {{$mailData['chapterState']}}. After reviewing, assign to the next reviewer or mark as review complete.<br>
<br>
Message from {{$mailData['userName']}}:<br>
{{$mailData['reviewerEmailMessage']}}<br>
<br>
The Financial Report PDF is attached and other documnets that can be downloaded are listed below.<br>
<br>
Submitted by: {{$mailData['completedName']}}, <a href="mailto:{{$mailData['completedEmail']}}">{{$mailData['completedEmail']}}</a><br>
<br>
Downloads Available:
<ul>
    <li>@if (isset($mailData['rosterPath']))
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['rosterPath']; ?>">Chapter Roster</a>
        @else
        No Roster Attached
        @endif</li>
    <li>@if (isset($mailData['statement1Path']))
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['statement1Path']; ?>">Primary Bank Statement</a>
        @else
        No Statement Attached
        @endif</li>
    @if (isset($mailData['statement2Path']))<li>
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['statement2Path']; ?>">Additional Bank Statement</a>
        </li>@endif
    <li>@if (isset($mailData['irsPath']))
        <a href="https://drive.google.com/uc?export=download&id=<?php echo $mailData['irsPath']; ?>">990N Confirmation File</a>
        @else
        No 990N File Attached
        @endif</li>
</ul>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
