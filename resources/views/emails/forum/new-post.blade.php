@component('mail::message')
# New Reply Posted

## {{ $thread->title }}

{{ $post->content }}

<b>Posted by:</b><br>
{!! $authorNameWithPosition !!}

@component('mail::button', ['url' => url("/t/{$thread->id}-" . Str::slug($thread->title))])
View Full Thread
@endcomponent

@endcomponent

