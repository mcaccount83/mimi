@component('mail::message')
# Financial Report Check-In Notification

{{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }} has submitted their Financial Report. It is ready to be reviewed.<br>
<br>
Submitted by: {{$mailData['completed_name']}}, <a href="mailto:{{$mailData['completed_email']}}">{{$mailData['completed_email']}}</a><br>
<br>
Attachments:
<ul>
    <li>@if (isset($mailData['roster_path']))
        <a href="{{ $mailData['roster_path'] }}" target="_blank">Chapter Roster</a>
        @else
        No Roster Attached
        @endif</li>
    <li>@if (isset($mailData['bank_statement_included_path']))
        <a href="{{ $mailData['bank_statement_included_path'] }}" target="_blank">Primary Bank Statement</a>
        @else
        No Statement Attached
        @endif</li>
    @if (isset($mailData['bank_statement_2_included_path']))<li>
        <a href="{{ $mailData['bank_statement_2_included_path'] }}" target="_blank">Additional Bank Statement</a>
        </li>@endif
    <li>@if (isset($mailData['file_irs_path']))
        <a href="{{ $mailData['file_irs_path'] }}" target="_blank">990N Confirmation File</a>
        @else
        No 990N File Attached
        @endif</li>
    <li><a href="{{ route('pdf.financialreport', ['id' => $mailData['chapterid']]) }}" target="_blank">Financial Report PDF</a>
        </li>
</ul>
Coordinators:
<ul>
    <li>@if (isset($coordinator_array[0]['first_name']))
        Primary Coordinator: {{ $coordinator_array[0]['first_name'] }} {{ $coordinator_array[0]['last_name'] }}
        @endif</li>
    @if (isset($coordinator_array[1]['first_name']))<li>
        Secondary Coordinator: {{ $coordinator_array[1]['first_name'] }} {{ $coordinator_array[1]['last_name'] }}
        </li>@endif
</ul>

@endcomponent

