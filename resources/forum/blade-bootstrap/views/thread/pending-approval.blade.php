@extends ('forum::layouts.main', ['breadcrumbs_append' => [trans('forum::threads.pending_approval')]])

@section ('forum_content')
    <div id="pending-approval" data-all-ids="{{ $threads->pluck('id')->toJson() }}" v-cloak>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>{{ trans('forum::threads.pending_approval') }}</h4>
        </div>

        @if ($threads->isEmpty())
            <div class="alert alert-info">
                {{ trans('forum::threads.none_found') }}
            </div>
        @else
            <div class="d-flex justify-content-end mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" v-model="selectAll" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        {{ trans('forum::threads.select_all') }}
                    </label>
                </div>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach ($threads as $thread)
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                <div class="pt-1">
                                    <input type="checkbox" class="form-check-input thread-checkbox"
                                        name="threads[]"
                                        :value="{{ $thread->id }}"
                                        v-model="selectedIds">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ trans_choice('forum::threads.thread', 1) }}:
                                                <a href="{{ Forum::route('thread.show', $thread) }}">
                                                    {{ $thread->title }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                {{ $thread->authorName }}
                                                ({{ $thread->created_at->diffForHumans() }})
                                            </small>
                                            <div class="mt-2 small">
                                                {!! $thread->firstPost->content !!}
                                            </div>
                                        </div>
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
                        data-open-modal="delete-threads"
                        class="btn btn-danger">
                    {{ trans('forum::general.delete_selection') }}
                </button>
                <button type="button"
                        :disabled="!selectedIds.length"
                        data-open-modal="approve-threads"
                        class="btn btn-primary">
                    {{ trans('forum::general.approve_selection') }}
                </button>
            </div>

            @if ($threads->hasPages())
                <div class="mt-4">
                    {{ $threads->links() }}
                </div>
            @endif
        @endif

        @component('forum::modal-form')
            @slot('key', 'delete-threads')
            @slot('title', trans('forum::general.delete_selection'))
            @slot('route', Forum::route('forum.bulk.thread.delete'))
            @slot('method', 'DELETE')
            @slot('actions')
                <button type="submit" class="btn btn-danger">
                    {{ trans('forum::general.proceed') }}
                </button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="threads[]" :value="id">
            </template>
        @endcomponent

        @component('forum::modal-form')
            @slot('key', 'approve-threads')
            @slot('title', trans('forum::general.approve_selection'))
            @slot('route', Forum::route('forum.bulk.thread.approve'))
            @slot('method', 'POST')
            @slot('actions')
                <button type="submit" class="btn btn-primary">
                    {{ trans('forum::general.proceed') }}
                </button>
            @endslot

            <p>{{ trans('forum::general.generic_confirm') }}</p>
            <template v-for="id in selectedIds" :key="id">
                <input type="hidden" name="threads[]" :value="id">
            </template>
        @endcomponent

    </div>
@stop
