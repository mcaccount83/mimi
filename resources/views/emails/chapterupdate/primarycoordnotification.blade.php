@component('mail::message')
# Primary Coordinator Notification

The MOMS Club of  {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}} has been updated through the MOMS Information Management Interface.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>


---

@component('mail::table')
| Field             | Previous Information     | Updated Information       |
|--------------------|--------------------------|---------------------------|
@foreach ($mailData['fields'] as $field => $values)
| {{ $field }}      | {{ $values['previous'] }} | {{ $values['updated'] }}  |
@endforeach
@endcomponent

@endcomponent

