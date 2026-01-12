<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardsOutgoing extends Model
{
    protected $table = 'boards_outgoing';

    protected $guarded = []; // ALL columns are mass-assignable

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');  // 'user_id' in boards BelongsTo 'id' in user
    }

    public function chapters(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' in boards BelongsTo 'id' in chapters
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');  // 'state' in coordinators BelongsTo 'id' in state
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');  // 'country_id' in coordinators BelongsTo 'id' in country
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(BoardPosition::class, 'board_position_id', 'id');  // 'board_position_id' in boards BelongsTo 'id' in board_position
    }

    public function categorySubscriptions(): HasMany
    {
        return $this->hasMany(ForumCategorySubscription::class);
    }
}
