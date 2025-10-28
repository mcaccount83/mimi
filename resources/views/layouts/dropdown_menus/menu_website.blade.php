<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    @if ($coordinatorCondition && !$webReviewCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapwebsite') }}">Website List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapsocialmedia') }}">Social Media List</a>
     @elseif ($webReviewCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapwebsite', ['check3' => 'yes']) }}">Website List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapsocialmedia', ['check3' => 'yes']) }}">Social Media List</a>
    @elseif ($ITCondition)
        <a class="dropdown-item" href="{{ route('chapters.chapwebsite', ['check5' => 'yes']) }}">International Website List</a>
        <a class="dropdown-item" href="{{ route('chapters.chapsocialmedia', ['check5' => 'yes']) }}">International Social Media List</a>
    @endif
</div>
