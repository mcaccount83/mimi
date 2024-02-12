@component('mail::message')
# Financial Report Check-In Notification

{{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }} has submitted their Financial Report. It is ready to be reviewed.<br>
<br>
Submitted by:
<ul>
    <li>{{$mailData['completed_name']}}</li>
    <li>{{$mailData['completed_email']}}</li>
</ul>
<br>
Attachments:
<ul>
    <li>@if (isset($mailData['roster_path']))
        [Chapter Roster]({{ $mailData['roster_path'] }})
        @else
        No Roster Attached
        @endif</li>
    <li>@if (isset($mailData['bank_statement_included_path']))
        [Primary Bank Statement]({{$mailData['bank_statement_included_path'] }})
        @else
        No Statement Attached
        @endif</li>
    <li>@if (isset($mailData['bank_statement_2_included_path']))
        [Additional Bank Statement]({{ $mailData['bank_statement_2_included_path'] }})
        @endif
        </li>
    <li>@if (isset($mailData['file_irs_path']))
        [990N Confirmation File]({{ $mailData['file_irs_path'] }})
        @else
        No 990N File Attached
        @endif</li>
</ul>
Coordinators:
<ul>
    <li>Primary Coordinator: {{ $coordinator_array[0]['first_name'] }} {{ $coordinator_array[0]['last_name'] }}</li>
    <li>@if (isset($coordinator_array[1]['first_name']))
        Secondary Coordinator: {{ $coordinator_array[1]['first_name'] }} {{ $coordinator_array[1]['last_name'] }}
        @endif</li>
</ul>

@endcomponent

