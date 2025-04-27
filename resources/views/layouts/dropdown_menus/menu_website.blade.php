<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="{{ route('chapters.chapwebsite') }}">Website List</a>
    <a class="dropdown-item" href="{{ route('chapreports.chaprptsocialmedia') }}">Social Media List</a>
    @if ($userAdmin)
        <a class="dropdown-item" href="{{ route('international.chapwebsite') }}">International Website List</a>
        <a class="dropdown-item" href="{{ route('international.chaprptsocialmedia') }}">International Social Media List</a>
    @endif
</div>
