<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && !$webReviewCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapwebsite') }}">Website List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapsocialmedia') }}">Social Media List</a>
     @elseif ($webReviewCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapwebsite', [\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION => 'yes']) }}">Website List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapsocialmedia', [\App\Enums\CheckboxFilterEnum::CONFERENCE_REGION => 'yes']) }}">Social Media List</a>
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapwebsite', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Website List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapsocialmedia', [\App\Enums\CheckboxFilterEnum::INTERNATIONAL => 'yes']) }}">International Social Media List</a>
    @endif
</div>
