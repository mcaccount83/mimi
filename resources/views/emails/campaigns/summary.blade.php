@component('mail::message')

<p>The following chapters received the <strong>{{ $mailData['campaignLabel'] }}</strong> email campaign:<br>
<ul>
@foreach($mailData['chapterNames'] as $chapter)
    <li>{{ $chapter['name'] }}, {{ $chapter['state'] }}</li>
@endforeach
</ul>
</p>
<br>
<p><strong>MCL,</strong><br>
MIMI Database Administrator</p>
<br>
<hr>
<br>
<p><strong>Message sent to chapters:</strong></p>
<br>

{!! $mailData['campaignMessage'] !!}
<br>
@endcomponent
