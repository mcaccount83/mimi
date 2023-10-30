@component('mail::message')
# Financial Report Check-In Notification

{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their Financial Report. It is ready to be reviewed.
<br>
Submitted by:
<br>
Attachments:
<ul>
    <li>@if (isset($financial_report_array['roster_path']))
        [Chapter Roster]({{ $financial_report_array['roster_path'] }})
        @else
        No Roster Attached
        @endif</li>
    <li>@if (isset($financial_report_array['bank_statement_included_path']))
        [Primary Bank Statement]({{ $financial_report_array['bank_statement_included_path'] }})
        @else
        No Statement Attached
        @endif</li>
    <li>@if (isset($financial_report_array['bank_statement_2_included_path']))
        [Additional Bank Statement]({{ $financial_report_array['bank_statement_2_included_path'] }})
        @endif
        </li>
    <li>@if (isset($financial_report_array['file_irs_path']))
        [990N Confirmation File]({{ $financial_report_array['file_irs_path'] }})
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

