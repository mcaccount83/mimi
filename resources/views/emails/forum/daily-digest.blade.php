@component('mail::message')
# Your Daily Forum Digest

@foreach ($threadsByCategory as $categoryId => $threads)
## {{ $threads->first()->category->title }}

@foreach ($threads as $thread)
### {{ $thread->title }}

@foreach ($thread->posts as $post)
{!! $post->content !!}

{{ $post->author?->authorNameForDisplay($categoryId) ?? 'Unknown Author' }}

---
@endforeach

@component('mail::button', ['url' => $thread->route])
View Thread
@endcomponent

@endforeach
@endforeach

To stop receiving emails of new posts/replies: Log into your MIMI account, navigate to "Update Profile"
and "Unsubscribe" from the appropriate list.
@endcomponent
