@if ($coordinatorCondition && $conferenceCoordinatorCondition)
    <div class="mb-2">
        @php
            $month = (int) $currentMonth;
            $campaigns = [
                8  => ['monthInWords' => 'August',       'id' => 'BudgetMeetingCampaign',           'label' => 'Budget & Meeting Email',           'route' => route('campaigns.sendbudgetmeeting')],
                9  => ['monthInWords' => 'September',       'id' => 'CodeOfConductCampaign',            'label' => 'Code of Conduct Email',            'route' => route('campaigns.sendcodeofconduct')],
                11 => ['monthInWords' => 'November',       'id' => 'RecordsRetentionCampaign',         'label' => 'Records Retention Email',          'route' => route('campaigns.sendrecordsretention')],
                12 => ['monthInWords' => 'December',       'id' => 'HappyHolidaysCampaign',            'label' => 'Happy Holidays Email',             'route' => route('campaigns.sendhappyholidays')],
                1  => ['monthInWords' => 'January',       'id' => 'VolunteerPush',                     'label' => 'Volunteer Push Email',              'route' => route('campaigns.sendvolunteerpush')],
                2  => ['monthInWords' => 'February',       'id' => 'ElectionsTimelineCampaign',        'label' => 'Elections Timeline Email',         'route' => route('campaigns.sendelectionstimeline')],
                3  => ['monthInWords' => 'March',       'id' => 'ProcessingReimbursementsCampaign', 'label' => 'Processing Reimbursements Email',  'route' => route('campaigns.sendprocessingreimbursements')],
                4  => ['monthInWords' => 'April',       'id' => 'AnnualReportCampaign',             'label' => 'Annual Report Info Email',         'route' => route('campaigns.sendannualreport')],
                5  => ['monthInWords' => 'May',       'id' => 'BoardReportCampaign',              'label' => 'Board Report Reminder Email',      'route' => route('campaigns.sendboardreport')],
                6  => ['monthInWords' => 'June',       'id' => 'FinancialReportCampaign',          'label' => 'Financial Report Reminder Email',  'route' => route('campaigns.sendfinancialreport')],
            ];
            @endphp

            @if($month == 7)
                <button type="button" class="btn btn-sm btn-primary">
                    Send New Board Welcome/Old Board Thank You Emails</button> (These auto send with board activation)
            @elseif(isset($campaigns[$month]))
                @php $campaign = $campaigns[$month]; @endphp
                    {{ $campaign['monthInWords'] }}: <button type="button" class="btn btn-xs btn-outline-primary"
                        onclick="confirmSendCampaign('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')">
                        <i class="bi bi-envelope-open"></i> Send {{ $campaign['label'] }}
                </button>
            @endif
    </div>
@endif
