@component('mail::message')
# Assigned Reviewer Notification

{{$mailData['userName']}} has assigned you to review the financial report for {{$mailData['chapterName']}}, {{$mailData['chapterState']}}. After reviewing, assign to the next reviewer or mark as review complete.<br>
<br>
Message from {{$mailData['userName']}}:<br>
{{$mailData['reviewerEmailMessage']}}<br>
<br>
The Financial Report PDF is attached and other documnets that can be downloaded are listed below.<br>
<br>
Submitted by: {{$mailData['completedName']}}, @mailto($mailData['completedEmail'])</a><br>
<br>
Downloads Available:
<ul>
    <li>
        @isset($mailData['rosterPath'])
            <a href="https://drive.google.com/uc?export=download&id={{ $mailData['rosterPath'] }}">Chapter Roster</a>
        @else
            No Roster Attached
        @endisset
    </li>
    <li>
        @isset($mailData['statement1Path'])
            <a href="https://drive.google.com/uc?export=download&id={{ $mailData['statement1Path'] }}">Primary Bank Statement</a>
        @else
            No Statement Attached
        @endisset
    </li>
    @isset($mailData['statement2Path'])
        <li>
            <a href="https://drive.google.com/uc?export=download&id={{ $mailData['statement2Path'] }}">Additional Bank Statement</a>
        </li>
    @endisset
    <li>
        @isset($mailData['irsPath'])
            <a href="https://drive.google.com/uc?export=download&id={{ $mailData['irsPath'] }}">990N Confirmation File</a>
        @else
            No 990N File Attached
        @endisset
    </li>
</ul>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent
