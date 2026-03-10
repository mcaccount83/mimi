@php
    $forumMenuLabel = match(true) {
        request()->routeIs('forum.recent')                    => 'Recent',
        request()->routeIs('forum.unread')                    => 'Unread Threads',
        request()->routeIs('forum.category.manage')           => 'Manage Categories',
        request()->routeIs('forum.pending-approval.threads')  => 'Threads pending approval',
        request()->routeIs('forum.pending-approval.posts')    => 'Posts pending approval',
        default                                               => 'Forum Menu',
    };
@endphp
<div class="dropdown ms-3">
    <button type="button" id="forumMenuDropdown" class="btn btn-sm btn-outline-secondary dropdown-toggle"
        data-bs-toggle="dropdown" aria-expanded="false">
        {{ $forumMenuLabel }}
    </button>
    <ul class="dropdown-menu" aria-labelledby="forumMenuDropdown">
        <li><a class="dropdown-item" href="{{ route('forum.recent') }}">Recent</a></li>
        @auth
            <li><a class="dropdown-item" href="{{ route('forum.unread') }}">Unread Threads</a></li>
        @endauth
        @if (Gate::allows('moveCategories') || Gate::allows('approveThreads') || Gate::allows('approvePosts'))
            <li><hr class="dropdown-divider"></li>
            @can('moveCategories')
                <li><a class="dropdown-item" href="{{ route('forum.category.manage') }}">Manage Categories</a></li>
            @endcan
            @can('approveThreads')
                <li><a class="dropdown-item" href="{{ route('forum.pending-approval.threads') }}">{{ trans('forum::threads.pending_approval') }}
                    @if($pendingThreadsCount > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingThreadsCount }} Pending</span>
                    @endif</a></li>
            @endcan
            @can('approvePosts')
                <li><a class="dropdown-item" href="{{ route('forum.pending-approval.posts') }}">{{ trans('forum::posts.pending_approval') }}
                    @if($pendingPostsCount > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingPostsCount }} Pending</span>
                    @endif</a></li>
            @endcan
        @endif
    </ul>
</div>
