@component('mail::message')
# ListAdmin unZapped Notification

The follownig chapter has been unZapped in MIMI:  MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, Conference {{$mailData['chapterConf']}}.<br>
<br>
Please re-add members of this chapter to any groups, forums and mailing lists.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>
{!! $mailData['mailTable'] !!}
<br>

@endcomponent
