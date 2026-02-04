<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Belongsto;

class GrantRequest extends Model
{
    protected $table = 'grant_request';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function chapters(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' in grant_requests BelongsTo 'id' in chapters
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
