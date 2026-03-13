@component('mail::message')
# {{ $category->title }} Forum Broadcast Sent

A notification was sent to **{{ $recipientCount }} subscribers** with the following post.
{{-- **{{ $broadcastSubject  }}** --}}

--------------------------------------------------------

## {{ $thread->title }}

{{ $post->content }}

<b>Posted by:</b><br>
{!! $authorNameWithPosition !!}

{{-- @component('mail::button', ['url' => route('forum.thread.show', [$thread->id, Str::slug($thread->title)])])

View Full Thread
@endcomponent --}}

@endcomponent
