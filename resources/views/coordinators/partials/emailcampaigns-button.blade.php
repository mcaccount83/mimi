    @if(isset($dashboardCampaigns[(int) $currentMonthInt]))
        <label>Monthly Email Campaign <small class="text-muted"><small>(CC Only)</small></small>:</label><br>

        @foreach($dashboardCampaigns[$currentMonthInt] as $campaign)
            @php $fn = $campaign['fn'] ?? 'confirmSendCampaign'; @endphp
            {{ $campaign['label'] }}
            @if(!empty($campaign['sent_date']))
                <span class="text-muted ms-2">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    Sent {{ \Carbon\Carbon::parse($campaign['sent_date'])->format('n/j/Y') }}
                </span>
            @else
                @if(isset($campaign['previewRoute']))
                    <a href="javascript:void(0)"
                    id="preview-{{ $campaign['id'] }}"
                    class="text-primary ms-2 text-decoration-none"
                    onclick="event.stopPropagation(); previewCampaign('{{ $campaign['previewRoute'] }}', '{{ $campaign['label'] }}')">
                        <i class="bi bi-eye me-1"></i>Preview
                    </a>
                @endif
                <a href="javascript:void(0)"
                id="send-{{ $campaign['id'] }}"
                class="text-primary ms-2 text-decoration-none"
                onclick="{{ $fn }}('{{ $campaign['label'] }}', '{{ $campaign['route'] }}')">
                    <i class="bi bi-envelope me-1"></i>Send
                </a>
            @endif
        @endforeach
    @endif

    @if($currentMonth == 7)
    <i><small>Old Board Thank You & New Board Welcome email campaigns are automatically sent when new boards are activated.</small></i>
    @endif
