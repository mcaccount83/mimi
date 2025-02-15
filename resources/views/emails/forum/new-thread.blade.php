@component('mail::message')
# New Topic Created

## {{ $thread->title }}

{{ $post->content }}

<b>Posted by:</b><br>
{!! $authorNameWithPosition !!}

@component('mail::button', ['url' => route('forum.thread.show', [$thread->id, Str::slug($thread->title)])])

View Full Thread
@endcomponent

@endcomponent
