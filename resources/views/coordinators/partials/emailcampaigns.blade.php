@if ($coordinatorCondition && $conferenceCoordinatorCondition)
    <div class="mb-2">
        @php
            $month = (int) $currentMonth;
            $campaigns = [
                8  => ['monthInWords' => 'August', 'id' => 'BudgetMeetingCampaign', 'label' => 'Budget & Meeting', 'route' => route('campaigns.sendbudgetmeeting')],
                9  => ['monthInWords' => 'September', 'id' => 'CodeOfConductCampaign', 'label' => 'Code of Conduct', 'route' => route('campaigns.sendcodeofconduct')],
                10 => ['monthInWords' => 'October', 'id' => 'RecordsRetentionCampaign', 'label' => 'Records Retention', 'route' => route('campaigns.sendrecordsretention')],
                11 => ['monthInWords' => 'Novemer', 'id' => 'HolidayBreakCampaign', 'label' => 'Happy Holidays', 'route' => route('campaigns.sendholidaybreak'), 'fn' => 'confirmSendHolidayBreak'],
                1  => ['monthInWords' => 'January', 'id' => 'VolunteerPush', 'label' => 'Volunteer Push', 'route' => route('campaigns.sendvolunteerpush')],
                2  => ['monthInWords' => 'February', 'id' => 'ElectionsTimelineCampaign', 'label' => 'Elections Timeline', 'route' => route('campaigns.sendelectionstimeline')],
                3  => ['monthInWords' => 'March', 'id' => 'ProcessingReimbursementsCampaign', 'label' => 'Processing Reimbursements', 'route' => route('campaigns.sendprocessingreimbursements')],
                4  => ['monthInWords' => 'April', 'id' => 'AnnualReportCampaign', 'label' => 'Annual Report Info', 'route' => route('campaigns.sendannualreport')],
                5  => ['monthInWords' => 'May', 'id' => 'BoardReportCampaign', 'label' => 'Board Report Reminder', 'route' => route('campaigns.sendboardreport')],
                6  => ['monthInWords' => 'June', 'id' => 'FinancialReportCampaign', 'label' => 'Financial Report Reminder', 'route' => route('campaigns.sendfinancialreport')],
            ];
        @endphp

        @if($month == 7)
            July email campaigns auto send with board activation:
            <button type="button" class="btn btn-xs btn-outline-primary ms-2">
                <i class="bi bi-envelope-open me-2"></i>New Board Welcome/Old Board Thank You
            </button>
        @elseif(isset($campaigns[$month]))
            @php
                $campaign = $campaigns[$month];
                $fn = $campaign['fn'] ?? 'confirmSendCampaign';
            @endphp
            Send {{ $campaign['monthInWords'] }} Email Campaign:
            <button type="button"
                    id="{{ $campaign['id'] }}"
                    class="btn btn-xs btn-outline-primary ms-2"
                    onclick="{{ $fn }}('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')">
                <i class="bi bi-envelope-open me-2"></i>{{ $campaign['label'] }}
            </button>
        @endif
    </div>
@endif
