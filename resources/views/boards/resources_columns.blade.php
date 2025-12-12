
<div class="container-fluid">
    <div class="row">

        <!-- Left Column -->
<div class="col-md-6" id="accordion-left">
    @php
        $totalCategories = count($resourceCategories);
        $halfCount = ceil($totalCategories / 2);
        $counter = 0;
    @endphp

    @foreach($resourceCategories as $category)
        @php $counter++; @endphp
        @if($counter <= $halfCount)
            <div class="card card-primary">
                <div class="card-header" id="accordion-header-left-{{ Str::slug($category->category_name) }}">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapse-left-{{ Str::slug($category->category_name) }}" style="width: 100%;">{{ $category->category_name }}</a>
                    </h4>
                </div>
                <div id="collapse-left-{{ Str::slug($category->category_name) }}" class="collapse" data-parent="#accordion-left">
                    <div class="card-body">
                        <section>
                            @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                            <div class="col-md-12" style="margin-bottom: 5px;">
                                @if ($resourceItem->file_type == 2)
                                    {{-- External Link --}}
                                    <a href="{{ $resourceItem->link }}" target="_blank">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                @elseif ($resourceItem->file_type == 3)
                                    {{-- Laravel Route - Just show title, no link for admin --}}
                                    {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    <span style="font-size: smaller; color: #6c757d;">(Chapter Sepcific Route)</span>
                                @elseif ($resourceItem->file_type == 1)
                                    {{-- File Download --}}
                                    <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                    </a>
                                @else
                                    {{-- Fallback for no file type --}}
                                    {{ $resourceItem->name }}
                                @endif
                            </div>
                            @if($category->category_name == "COPY READY MATERIAL")
                                <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                    {{ $resourceItem->description }}
                                </div>
                            @endif
                            @endforeach
                        </section>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<!-- Right Column -->
<div class="col-md-6" id="accordion-right">
    @php $counter = 0; @endphp
    @foreach($resourceCategories as $category)
        @php $counter++; @endphp
        @if($counter > $halfCount)
            <div class="card card-primary">
                <div class="card-header" id="accordion-header-right-{{ Str::slug($category->category_name) }}">
                    <h4 class="card-title w-100">
                        <a class="d-block" data-toggle="collapse" href="#collapse-right-{{ Str::slug($category->category_name) }}" style="width: 100%;">{{ $category->category_name }}</a>
                    </h4>
                </div>
                <div id="collapse-right-{{ Str::slug($category->category_name) }}" class="collapse" data-parent="#accordion-right">
                    <div class="card-body">
                        <section>
                            @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                                @if($category->category_name != "END OF YEAR")
                                    <div class="col-md-12" style="margin-bottom: 5px;">
                                        @if ($resourceItem->file_type == 2)
                                            {{-- External Link --}}
                                            <a href="{{ $resourceItem->link }}" target="_blank">
                                                {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                            </a>
                                        @elseif ($resourceItem->file_type == 3)
                                            {{-- Laravel Route - Just show title, no link for admin --}}
                                            {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                            <span style="font-size: smaller; color: #6c757d;">(Chapter Sepcific Route)</span>
                                        @elseif ($resourceItem->file_type == 1)
                                            {{-- File Download --}}
                                            <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                            </a>
                                        @else
                                            {{-- Fallback for no file type --}}
                                            {{ $resourceItem->name }}
                                        @endif
                                    </div>
                                    @if($category->category_name == "COPY READY MATERIAL")
                                        <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                            {{ $resourceItem->description }}
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                            @if($category->category_name == "END OF YEAR")
                                <div class="col-md-12" style="margin-bottom: 5px;">
                                @include('boards.resources_columns_eoy',  ['resources' => $resources])

                            @endif
                        </section>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for collapse events on both accordions
        $('#accordion .collapse').on('show.bs.collapse', function() {
            $('#accordion .collapse').not(this).collapse('hide');
        });
        $('#accordion-right .collapse').on('show.bs.collapse', function() {
            $('#accordion-right .collapse').not(this).collapse('hide');
            $('#accordion .collapse').collapse('hide');
        });
        $('#accordion .collapse').on('show.bs.collapse', function() {
            $('#accordion .collapse').not(this).collapse('hide');
            $('#accordion-right .collapse').collapse('hide');
        });
        });

</script>
@endpush
