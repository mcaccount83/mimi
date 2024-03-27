@component('mail::message')
# Financial Report Submitted

{{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }}, thank you for submitting your Financial Report.<br>
<br>
Your Coordintor Team will review your report shortly and reach out with any questions they may have!<br>
<br>
A copy of your report is attached.  Please save/keep a copy for your records.<br>
<br>
<strong>Coordinators:</strong><br>
Primary Coordinator: {{ $coordinator_array[0]['first_name'] }} {{ $coordinator_array[0]['last_name'] }}<br>
@if (isset($coordinator_array[1]['first_name']))
Secondary Coordinator: {{ $coordinator_array[1]['first_name'] }} {{ $coordinator_array[1]['last_name'] }}
@endif<br>

<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

