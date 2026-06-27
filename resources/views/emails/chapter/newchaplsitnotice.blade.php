@component('mail::message')
# ListAdmin New Chapter Add Notification

<p>The follownig chapter has added to MIMI:</p>
<br>
<p>MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}, Conference {{$mailData['chapterConf']}}.</p>
<br>
<p>Please add members of this chapter to any groups, forums and mailing lists.</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>

{!! $mailData['mailTableNewChapter'] !!}
<br>
@endcomponent
