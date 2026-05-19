@component('mail::message')
# Financial Report Check-In Notification

{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has submitted their Financial Report. It is ready to be reviewed. The Financial Report PDF is attached and other
documents that can be downloaded are listed below.<br>
<br>
Submitted by: {{$mailData['completedName']}}, @mailto($mailData['completedEmail'])<br>
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
Coordinators:
<ul>
    <li>Primary Coordinator: {{$mailData['pcName']}} </li>

</ul>

@endcomponent

