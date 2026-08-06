@component('mail::message')
# {{ $category->title }} Forum Broadcast Sent

A notification was sent to **{{ $recipientCount }} subscribers** with the following post.

---

## {{ $thread->title }}

{!! $post->content !!}

**Posted by:**
{!! $authorNameWithPosition !!}

@component('mail::button', ['url' => $thread->route])
View Thread
@endcomponent
@endcomponent
