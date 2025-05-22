@component('mail::message')
# ListAdmin New Chapter Add Notification

The follownig chapter has added to MIMI:  MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, Conference {{$mailData['chapterConf']}}.<br>
<br>
Please add members of this chapter to any groups, forums and mailing lists.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

{!! $mailData['mailTableNewChapter'] !!}

@endcomponent
