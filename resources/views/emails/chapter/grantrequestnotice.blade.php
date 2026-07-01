@component('mail::message')
# New Grant Request Notification

A new grant request has been submitted by the MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}. Please review and pass along to the M2M Committee for review.

Details are below as well as in MIMI. If you need more information, please reach out to the board member submitting the request:

{{ $mailData['board_name'] }}, {{ $mailData['board_position'] }}
{{ $mailData['board_email'] }}
{{ $mailData['board_phone'] }}

**MCL,**
MIMI Database Administrator

---

{!! $mailData['mailTable'] !!}
@endcomponent
