@component('mail::message')
The following chapters received the **{{ $mailData['campaignLabel'] }}** email campaign:

@foreach($mailData['chapterNames'] as $chapter)
- {{ $chapter['name'] }}, {{ $chapter['state'] }}
@endforeach

**MCL,**
MIMI Database Administrator

---

**Message sent to chapters:**

{!! $mailData['campaignMessage'] !!}
@endcomponent
