@component('mail::message')
# {{ $category->title }} | {{ $type === 'thread' ? 'New Thread Posted' : 'New Reply Posted' }}

## {{ $thread->title }}

{{ $post->content }}

**Posted by:**
{!! $authorNameWithPosition !!}

---

To stop receiving emails of new posts/replies: Log into your MIMI account, navigate to "Update Profile"
and "Unsubscribe" from the appropriate list.
@endcomponent

