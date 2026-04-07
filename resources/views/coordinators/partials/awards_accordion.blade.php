<div class="container-fluid">
    <div class="accordion" id="accordion-badges" style="column-count: 2; column-gap: 1rem;">
        @php $firstOpen = true; @endphp
        @foreach($reportYears as $year)
            @if($year->awardBadges->isNotEmpty())
                <div style="break-inside: avoid; margin-bottom: 0.5rem;">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="header-year-{{ $year->id }}">
                            <button class="accordion-button {{ $firstOpen ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse-year-{{ $year->id }}"
                                    aria-expanded="{{ $firstOpen ? 'true' : 'false' }}"
                                    aria-controls="collapse-year-{{ $year->id }}">
                                {{ $year->fiscal_year }}
                            </button>
                        </h2>
                        <div id="collapse-year-{{ $year->id }}" class="accordion-collapse collapse {{ $firstOpen ? 'show' : '' }}"
                             data-bs-parent="#accordion-badges">
                            <div class="accordion-body">
                                @foreach($year->awardBadges as $badge)
                                    <div class="col-md-12" style="margin-bottom: 5px;">
                                        <a href="javascript:void(0)"
                                           onclick="openImageViewer('{{ $badge->file_path }}', '{{ addslashes($badge->eoyAward->award_type) }}', '{{ addslashes($badge->fiscalYear->fiscal_year) }}')">
                                            {{ $badge->eoyAward->award_type }}
                                        </a>
                                        @if($userTypeId == \App\Enums\UserTypeEnum::COORD)
                                            @if($canEditFiles)
                                                <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-bs-toggle="modal" data-bs-target="#editBadgeModal{{ $badge->id }}">UPDATE</a></span>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @php $firstOpen = false; @endphp
            @endif
        @endforeach
    </div>
</div>
