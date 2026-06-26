@if ($coordinatorCondition && $conferenceCoordinatorCondition)
    <div class="mb-2">
        @php
            $month = (int) $currentMonth;
            $campaigns = [
                8  => ['monthInWords' => 'August', 'id' => 'BudgetMeetingCampaign', 'label' => 'Budget & Meeting', 'route' => route('campaigns.sendbudgetmeeting')],
                // 9  => ['monthInWords' => 'September', 'id' => 'ServiceProjectsCampaign', 'label' => 'Service Projects', 'route' => route('campaigns.sendserviceprojects')],
                10 => ['monthInWords' => 'October', 'id' => 'CodeOfConductCampaign', 'label' => 'Code of Conduct', 'route' => route('campaigns.sendcodeofconduct')],
                11 => ['monthInWords' => 'November', 'id' => 'HolidayBreakCampaign', 'label' => 'Happy Holidays', 'route' => route('campaigns.sendholidaybreak'), 'fn' => 'confirmSendHolidayBreak'],
                // 12 => ['monthInWords' => 'December', 'id' => 'CodeOfConductCampaign', 'label' => 'Code of Conduct', 'route' => route('campaigns.sendcodeofconduct')],
                1  => ['monthInWords' => 'January', 'id' => 'RecordsRetentionCampaign', 'label' => 'Records Retention', 'route' => route('campaigns.sendrecordsretention')],
                2  => ['monthInWords' => 'February', 'id' => 'ElectionsTimelineCampaign', 'label' => 'Elections Timeline', 'route' => route('campaigns.sendelectionstimeline')],
                3  => ['monthInWords' => 'March', 'id' => 'VolunteerPush', 'label' => 'Volunteer Push', 'route' => route('campaigns.sendvolunteerpush')],
                4  => ['monthInWords' => 'April', 'id' => 'ProcessingReimbursementsCampaign', 'label' => 'Processing Reimbursements', 'route' => route('campaigns.sendprocessingreimbursements')],
                5  => ['monthInWords' => 'May', 'id' => 'AnnualReportCampaign', 'label' => 'Annual Report Info', 'route' => route('campaigns.sendannualreport')],
                6  => ['monthInWords' => 'June', 'id' => 'BoardReportCampaign', 'label' => 'Board Report Reminder', 'route' => route('campaigns.sendboardreport')],
                7  => ['monthInWords' => 'July', 'id' => 'FinancialReportCampaign', 'label' => 'Financial Report Reminder', 'route' => route('campaigns.sendfinancialreport')],
            ];
        @endphp

        @if(isset($campaigns[$month]))
            @php
                $campaign = $campaigns[$month];
                $fn = $campaign['fn'] ?? 'confirmSendCampaign';
            @endphp
            Send {{ $campaign['monthInWords'] }} Email Campaign <small class="text-muted"><small>(CC Only)</small></small>:
            <button type="button"
                    id="{{ $campaign['id'] }}"
                    class="btn btn-xs btn-outline-primary ms-2"
                    onclick="{{ $fn }}('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')">
                <i class="bi bi-envelope-open me-2"></i>{{ $campaign['label'] }}
            </button>
        @endif
        @if($month == 7)
            <br>
            <i><small>Old Board Thank You & New Board Welcome campaigns are automatically sent when new boards are activated.</small></i>
            {{-- <button type="button" class="btn btn-xs btn-outline-primary ms-2">
                <i class="bi bi-envelope-open me-2"></i>New Board Welcome/Old Board Thank You
            </button> --}}
        @endif
    </div>
@endif
