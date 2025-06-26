<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class BoardsOutgoing extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'boards_outgoing';

     public $timestamps = false;

    protected $fillable = [
        'user_id', 'chapter_id', 'board_position_id', 'first_name', 'last_name', 'email', 'phone', 'street_address', 'city', 'state_id', 'zip', 'country_id',
        'last_updated_by', 'last_updated_date',
    ];

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
