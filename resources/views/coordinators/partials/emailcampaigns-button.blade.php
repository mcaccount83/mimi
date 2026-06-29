    @if(isset($campaigns[(int) $currentMonthInt]))
    Monthly Email Campaign <small class="text-muted"><small>(CC Only)</small></small>:
    @foreach($campaigns[$currentMonthInt ] as $campaign)
        @php $fn = $campaign['fn'] ?? 'confirmSendCampaign'; @endphp
        <button type="button"
                id="{{ $campaign['id'] }}"
                class="btn btn-xs btn-outline-primary ms-2"
                onclick="{{ $fn }}('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')">
            <i class="bi bi-envelope me-2"></i>{{ $campaign['label'] }}
        </button>
    @endforeach
    @endif

    @if($currentMonth == 7)
    <i><small>Old Board Thank You & New Board Welcome email campaigns are automatically sent when new boards are activated.</small></i>
    @endif
