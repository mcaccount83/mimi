@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::posts.pending_approval')]])

@section ('forum_content')
    <div id="pending-approval" data-all-ids="{{ $posts->pluck('id')->toJson() }}" v-cloak>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>{{ trans('forum::posts.pending_approval') }}</h4>
        </div>

        @if ($posts->isEmpty())
            <div class="alert alert-info">
                {{ trans('forum::posts.none_found') }}
            </div>
        @else
            <div class="d-flex justify-content-end mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" v-model="selectAll" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        {{ trans('forum::posts.select_all') }}
                    </label>
                </div>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach ($posts as $post)
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <div class="pt-1">
                                    <input type="checkbox" class="form-check-input post-checkbox"
                                        name="posts[]"
                                        v-model="selectedIds"
                                        :value="{{ $post->id }}">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small mt-1">
                                        {!! $post->content !!}
                                    </div>
                                    <small class="text-muted">
                                        {{ $post->authorName }}
                                        ({{ $post->created_at->diffForHumans() }})
                                    </small>
                                    <div class="mt-2 small">
                                        {{ trans_choice('forum::threads.thread', 1) }}:
                                        <a href="{{ Forum::route('thread.show', $post->thread) }}?post={{ $post->id }}">
                                            {{ $post->thread->title }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="button"
                        :disabled="!selectedIds.length"
                        data-open-modal="delete-posts"
                        class="btn btn-danger">
                    {{ trans('forum::general.delete_selection') }}
                </button>
                <button type="button"
                        :disabled="!selectedIds.length"
                        data-open-modal="approve-posts"
                        class="btn btn-primary">
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>

            @if ($posts->hasPages())
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif

        @component('forum::modal-form')
            @slot('key', 'delete-posts')
            @slot('title', trans('forum::general.delete_selection'))
            @slot('route', Forum::route('forum.bulk.post.delete'))
            @slot('method', 'DELETE')
            @slot('actions')
                <button type="submit" class="btn btn-danger">
                    {{ trans('forum::general.proceed') }}
                </button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="posts[]" :value="id">
            </template>
        @endcomponent

        @component('forum::modal-form')
            @slot('key', 'approve-posts')
            @slot('title', trans('forum::general.approve_selection'))
            @slot('route', Forum::route('forum.bulk.post.approve'))
            @slot('method', 'POST')
            @slot('actions')
                <button type="submit" class="btn btn-primary">
                    {{ trans('forum::general.proceed') }}
                </button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="posts[]" :value="id">
            </template>
        @endcomponent

    </div>
@stop
