@if ($coordinatorCondition && $conferenceCoordinatorCondition)
    <div class="mb-2">
        @php
            $month = (int) $currentMonth;
            $campaigns = [
                8  => [['id' => 'BudgetMeetingCampaign', 'label' => 'Executive Board', 'route' => route('campaigns.sendbudgetmeeting')]],
                9  => [['id' => 'ServiceProjectsCampaign', 'label' => 'Service Projects', 'route' => route('campaigns.sendserviceprojects')]],
                10 => [['id' => 'CodeOfConductCampaign', 'label' => 'Code of Conduct', 'route' => route('campaigns.sendcodeofconduct')]],
                11 => [
                        ['id' => 'MemberBenefitsCampaign', 'label' => 'Member Benefits', 'route' => route('campaigns.sendmemberbenefits')],
                        ['id' => 'HolidayBreakCampaign', 'label' => 'Holiday Break', 'route' => route('campaigns.sendholidaybreak'), 'fn' => 'confirmSendHolidayBreak'],
                    ],
                12 => [['id' => 'RecordsRetentionCampaign', 'label' => 'Records Retention', 'route' => route('campaigns.sendrecordsretention')]],
                1  => [['id' => 'VolunteerPush', 'label' => 'Volunteer Push', 'route' => route('campaigns.sendvolunteerpush')]],
                2  => [['id' => 'ElectionsTimelineCampaign', 'label' => 'Election Information', 'route' => route('campaigns.sendelectionstimeline')]],
                3  => [['id' => 'ProcessingReimbursementsCampaign', 'label' => 'Processing Reimbursements', 'route' => route('campaigns.sendprocessingreimbursements')]],
                4  => [['id' => 'AnnualReportCampaign', 'label' => 'EOY Report Info', 'route' => route('campaigns.sendannualreport')]],
                5  => [['id' => 'BoardReportCampaign', 'label' => 'Board Report Info', 'route' => route('campaigns.sendboardreport')]],
                6  => [['id' => 'FinancialReportCampaign', 'label' => 'Financial Report Info', 'route' => route('campaigns.sendfinancialreport')]],
            ];
        @endphp

        @if(isset($campaigns[$month]))
            Monthly Email Campaign <small class="text-muted"><small>(CC Only)</small></small>:
            @foreach($campaigns[$month] as $campaign)
                @php $fn = $campaign['fn'] ?? 'confirmSendCampaign'; @endphp
                <button type="button"
                        id="{{ $campaign['id'] }}"
                        class="btn btn-xs btn-outline-primary ms-2"
                        onclick="{{ $fn }}('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')">
                    <i class="bi bi-envelope me-2"></i>{{ $campaign['label'] }}
                </button>
            @endforeach
        @endif

        @if($month == 7)
            <i><small>Old Board Thank You & New Board Welcome email campaigns are automatically sent when new boards are activated.</small></i>
        @endif
    </div>
@endif
