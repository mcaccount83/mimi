<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Belongsto;

#[Table('grant_request', 'id')]
#[Unguarded]
class GrantRequest extends Model
{
    public function chapters(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' in grant_requests BelongsTo 'id' in chapters
    }

    public function chapterstate(): BelongsTo
    {
        return $this->belongsTo(State::class, 'chapter_state', 'id');  // 'state' in grant_requests BelongsTo 'id' in state
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');  // 'state' in grant_requests BelongsTo 'id' in state
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');  // 'country_id' in grant_requests BelongsTo 'id' in country
    }

    public function boards(): BelongsTo
    {
        return $this->belongsTo(Boards::class, 'submitted_bdId', 'id'); // 'country_id' in grant_requests BelongsTo 'id' in Boards
    }

    public function boardsDisbanded(): BelongsTo
    {
        return $this->belongsTo(BoardsDisbanded::class, 'submitted_bdId', 'id');  // 'country_id' in grant_requests BelongsTo 'id' in BoardsDisbanded
    }

    public function boardsOutgoing(): BelongsTo
    {
        return $this->belongsTo(BoardsOutgoing::class, 'submitted_bdId', 'id');  // 'country_id' in grant_requests BelongsTo 'id' in BoardsOutgoing
    }
}
