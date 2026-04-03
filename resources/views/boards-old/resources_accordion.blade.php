
<div class="container-fluid">
    <div class="accordion" id="accordion-resources" style="column-count: 2; column-gap: 1rem;">
            @foreach($resourceCategories as $category)
            <div style="break-inside: avoid; margin-bottom: 0.5rem;">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="header-{{ Str::slug($category->category_name) }}">
                        <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapse-{{ Str::slug($category->category_name) }}"
                                aria-expanded="false"
                                aria-controls="collapse-{{ Str::slug($category->category_name) }}">
                            {{ $category->category_name }}
                        </button>
                    </h2>
                    <div id="collapse-{{ Str::slug($category->category_name) }}" class="accordion-collapse collapse" data-bs-parent="#accordion-resources">
                        <div class="accordion-body">
                            <section>
                                @if($category->category_name == "END OF YEAR")
                                    <div class="col-md-12" style="margin-bottom: 5px;">
                                        @include('boards.resources_accordion_eoy', ['resources' => $resources])
                                    </div>
                                @else
                                    @foreach($resources->where('resourceCategory.category_name', $category->category_name) as $resourceItem)
                                        <div class="col-md-12" style="margin-bottom: 5px;">
                                            @if ($resourceItem->file_type == 2)
                                                <a href="{{ $resourceItem->link }}" target="_blank">
                                                    {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                                </a>
                                            @elseif ($resourceItem->file_type == 3)
                                                @if (($userTypeId && $userTypeId == \App\Enums\UserTypeEnum::COORD) || $userTypeId == NULL)
                                                    {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                                    <span style="font-size: smaller; color: #6c757d;">(Chapter Specific Route)</span>
                                                @else
                                                    <a href="{{ route($resourceItem->link, ['id' => $chDetails->id]) }}" target="_blank">
                                                        {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                                    </a>
                                                @endif
                                            @elseif ($resourceItem->file_type == 1)
                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $resourceItem->file_path }}')">
                                                    {{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}
                                                </a>
                                            @else
                                                {{ $resourceItem->name }}
                                            @endif
                                        </div>
                                        @if($category->category_name == "COPY READY MATERIAL")
                                            <div class="col-md-12" style="font-size: smaller; margin-bottom: 10px;">
                                                {{ $resourceItem->description }}
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')

@endpush
