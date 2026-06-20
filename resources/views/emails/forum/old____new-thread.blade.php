@component('mail::message')
# {{ $category->title }} New Topic Created

## {{ $thread->title }}

{{ $post->content }}

<b>Posted by:</b><br>
{!! $authorNameWithPosition !!}

--------------------------------------------------------

To stop receiving emails of new posts/replies: Log into your MIMI account, navigate to "Update Profile" and "Unsubscribe" from the appropriate list.

{{-- @component('mail::button', ['url' => route('forum.thread.show', [$thread->id, Str::slug($thread->title)])])

View Full Thread
@endcomponent --}}

@endcomponent
