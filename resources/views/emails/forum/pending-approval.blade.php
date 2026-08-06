@component('mail::message')
# {{ $thread->category->title }} | {{ $type === 'thread' ? 'New Thread' : 'New Reply' }} Pending Approval

## {{ $thread->title }}

{!! $post->content !!}

**Posted by:**
{!! $authorNameWithPosition !!}

@component('mail::button', ['url' => $thread->route])
View Thread
@endcomponent

@endcomponent
