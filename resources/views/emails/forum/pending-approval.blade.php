@component('mail::message')
# {{ $thread->category->title }} | {{ $type === 'thread' ? 'New Thread' : 'New Reply' }} Pending Approval

## {{ $thread->title }}

{{ $post->content }}

**Posted by:**
{!! $authorNameWithPosition !!}
@endcomponent
