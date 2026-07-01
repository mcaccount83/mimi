@component('mail::message')
# {{ $category->title }} Forum Broadcast Sent

A notification was sent to **{{ $recipientCount }} subscribers** with the following post.

---

## {{ $thread->title }}

{{ $post->content }}

**Posted by:**
{!! $authorNameWithPosition !!}

@endcomponent
