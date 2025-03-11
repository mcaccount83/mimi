@component('mail::message')
# Financial Report Check-In Notification

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their Financial Report. It is ready to be reviewed. The Financial Report PDF is attached and other
documents that can be downloaded are listed below.<br>
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
Coordinators:
<ul>
    <li>Primary Coordinator: {{$mailData['pcName']}} </li>

</ul>

@endcomponent

