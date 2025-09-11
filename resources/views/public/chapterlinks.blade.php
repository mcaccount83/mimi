@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">

        <!-- International Chapters Section -->
        <div class="border-horiz2"></div>
        <h4>International Chapters</h4>
        <div class="border-horiz2"></div>

        <div class="row" id="internationalAccordion">
            @php
                $previousCountry = null;
            @endphp

            @foreach($international as $chapter)
                @if($chapter->country_id !== $previousCountry)
                    <div class="col-md-3 mb-1">
                        <div class="card card-primary">
                            <div class="card-header" >
                                <h4 class="card-title w-100">
                                    <a class="d-block"  >

                                    </a>
                                </h4>
                            </div>
                            <div >
                            {{-- <div class="card-header" id="heading{{ $loop->index }}">
                                <h4 class="card-title w-100">
                                    <a class="d-block" data-toggle="collapse" href="#collapse{{ $loop->index }}" >
                                        {{ $chapter->country_id }}
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $loop->index }}" class="collapse" data-parent="#internationalAccordion"> --}}
                                <div class="card-body">
                @endif
                                    <div class="chapter">
                                        <a href="{{ route('chapter.info', $chapter->id) }}" target="_blank">
                                            {{ $chapter->name }}
                                        </a>
                                    </div>

                                    {{--<div class="chapter">
                                             @if($chapter->website_status == 1)
                                            <a href="{{ $chapter->website_url }}" target="_blank">{{ $chapter->name }}</a>
                                        @else
                                            <a href="https://momsclub.org/chapters/find-a-chapter/" target="_blank">{{ $chapter->name }}</a>
                                        @endif --}}
                                    {{-- </div> --}}
                @php
                    $previousCountry = $chapter->country_id;
                @endphp

                @if(!$loop->last && $chapter->country_id !== $international[$loop->index + 1]->country_id)
                                </div>
                           </div>
                        </div>
                    </div>
                @endif

                @if($loop->last)
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- USA Chapters Section -->
        <div class="border-horiz2"></div>
        <h4>USA Chapters</h4>
        <div class="border-horiz2"></div>

        <div class="row" id="usaAccordion">
            @php
                $previousState = null;
            @endphp

            @foreach($chapters as $chapter)
                @if($chapter->state_long_name !== $previousState)
                    <div class="col-md-3 mb-1">
                        <div class="card card-primary">
                            <div class="card-header" id="heading{{ $loop->index + count($international) }}">
                                <h4 class="card-title w-100">
                                    <a class="d-block" data-toggle="collapse" href="#collapse{{ $loop->index + count($international) }}" >
                                        {{ $chapter->state_long_name }}
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $loop->index + count($international) }}" class="collapse" data-parent="#usaAccordion">
                                <div class="card-body">
                @endif

                                    <div class="chapter">
                                        <a href="{{ route('chapter.info', $chapter->id) }}" target="_blank">
                                            {{ $chapter->name }}
                                        </a>
                                    </div>

                                    {{-- <div class="chapter">
                                        @if($chapter->website_status == 1)
                                            <a href="{{ $chapter->website_url }}" target="_blank">{{ $chapter->name }}</a>
                                        @else
                                            <a href="https://momsclub.org/chapters/find-a-chapter/" target="_blank">{{ $chapter->name }}</a>
                                        @endif
                                    </div> --}}
                @php
                    $previousState = $chapter->state_long_name;
                @endphp

                @if(!$loop->last && $chapter->state_long_name !== $chapters[$loop->index + 1]->state_long_name)
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($loop->last)
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>
@endsection

@section('customscript')
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>
<script>
$(document).ready(function() {
    var elem = document.querySelector('.masonry');
    var msnry = new Masonry(elem, {
        itemSelector: '.masonry-item',
        columnWidth: '.masonry-item',
        percentPosition: true
    });
});

$(document).ready(function() {
    var elem = document.querySelector('.masonry2');
    var msnry = new Masonry(elem, {
        itemSelector: '.masonry-item',
        columnWidth: '.masonry-item',
        percentPosition: true
    });
});

</script>
