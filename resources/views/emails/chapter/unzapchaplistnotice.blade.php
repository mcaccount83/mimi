@component('mail::message')
# ListAdmin unZapped Notification

<p>The follownig chapter has been unZapped in MIMI:  MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, Conference {{$mailData['chapterConf']}}.</p>
<br>
<p>Please re-add members of this chapter to any groups, forums and mailing lists.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTable'] !!}
<br>
@endcomponent
