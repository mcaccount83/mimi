@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">

    @if($international && $international->count() > 0)
    <!-- International Chapters Section -->
    <div class="border-horiz2"></div>
    <h4>International Chapters</h4>
    <div class="border-horiz2"></div>

    {{-- <div class="chapter-columns"> --}}
    <div style="margin-bottom: 1rem;">
        @foreach($international as $chapter)
            <div style="margin-bottom: 0.25rem;">
                <a href="javascript:void(0)"
                   data-chapter="{!! htmlspecialchars(json_encode([
                       'name' => $chapter->name,
                       'state_short_name' => $chapter->state_short_name,
                       'territory' => $chapter->territory,
                       'inquiries_contact' => $chapter->inquiries_contact,
                       'website_status' => $chapter->website_status,
                       'website_url' => $chapter->website_url
                   ])) !!}"
                   onclick="showChapterInfo(JSON.parse(this.dataset.chapter))">
                    {{ $chapter->name }}
                </a>
            </div>
        @endforeach
    </div>
    {{-- </div> --}}
    @endif

    <!-- USA Chapters Section -->
    <div class="border-horiz2"></div>
    @if($international && $international->count() > 0)
        <h4>USA Chapters</h4>
    @else
        <h4>&nbsp;</h4>
    @endif
    <div class="border-horiz2"></div>

    <div class="chapter-columns">
    <div class="accordion" id="accordion-usa">
        @php
            $previousState = null;
            $usaIndex = 0;
        @endphp

        @foreach($chapters as $chapter)
            @if($chapter->state_long_name != $previousState)
                @if($previousState !== null)
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @php $usaIndex++; @endphp
                <div style="break-inside: avoid; margin-bottom: 0.5rem;">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="usa-header-{{ $usaIndex }}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#usa-collapse-{{ $usaIndex }}"
                                    aria-expanded="false"
                                    aria-controls="usa-collapse-{{ $usaIndex }}">
                                {{ $chapter->state_long_name }}
                            </button>
                        </h2>
                        <div id="usa-collapse-{{ $usaIndex }}" class="accordion-collapse collapse" data-bs-parent="#accordion-usa">
                            <div class="accordion-body">
            @endif

                                <div class="chapter">
                                    <a href="javascript:void(0)"
                                       data-chapter="{!! htmlspecialchars(json_encode([
                                           'name' => $chapter->name,
                                           'state_short_name' => $chapter->state_short_name,
                                           'territory' => $chapter->territory,
                                           'inquiries_contact' => $chapter->inquiries_contact,
                                           'website_status' => $chapter->website_status,
                                           'website_url' => $chapter->website_url
                                       ])) !!}"
                                       onclick="showChapterInfo(JSON.parse(this.dataset.chapter))">
                                        {{ $chapter->name }}
                                    </a>
                                </div>

            @php
                $previousState = $chapter->state_long_name;
            @endphp

            @if($loop->last)
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    </div>

</div>
@endsection

@section('customscript')
<style>
.chapter-columns {
    column-gap: 1rem;
    column-count: 1;
}
@media (min-width: 576px) {
    .chapter-columns { column-count: 2; }
}
@media (min-width: 992px) {
    .chapter-columns { column-count: 3; }
}
@media (min-width: 1200px) {
    .chapter-columns { column-count: 4; }
}
</style>

<script>
function showChapterInfo(chapter) {
    Swal.fire({
        title: `<h4><strong>${chapter.name}, ${chapter.state_short_name}</strong></h4>`,
        html: `
            <div style="text-align: left;">
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; flex-wrap: wrap; align-items: baseline;">
                        <span style="font-weight: bold; margin-right: 8px; white-space: nowrap;">Boundaries:</span>
                        <span style="flex: 1;">${chapter.territory}</span>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; align-items: baseline;">
                        <span style="font-weight: bold; margin-right: 8px; white-space: nowrap;">Contact Email:</span>
                        <span style="flex: 1;"><a href="mailto:${chapter.inquiries_contact}">${chapter.inquiries_contact}</a></span>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; align-items: baseline;">
                        <span style="font-weight: bold; margin-right: 8px; white-space: nowrap;">Website:</span>
                        <span style="flex: 1;">${
                            chapter.website_status == 1 &&
                            chapter.website_url &&
                            chapter.website_url != 'http://' &&
                            chapter.website_url != 'https://' &&
                            chapter.website_url.length > 8
                            ? `<a href="${chapter.website_url}" target="_blank">${chapter.website_url}</a>`
                            : 'No Website'
                        }</span>
                    </div>
                </div>
            </div>
        `,
        focusConfirm: false,
        confirmButtonText: 'Close',
        customClass: {
            popup: 'swal-wide',
            confirmButton: 'btn btn-danger'
        }
    });
}
</script>
@endsection
