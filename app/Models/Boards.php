<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Boards extends Model
{
    use HasFactory;
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'board_position_id', 'chapter_id', 'street_address', 'city', 'state', 'zip', 'country',
        'phone', 'last_updated_by', 'last_updated_date', 'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');  // 'user_id' in boards BelongsTo 'id' in user
    }

    public function chapters(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' in boards BelongsTo 'id' in chapters
    }

    public function stateName(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'state_short_name');  // 'state' in boards BelongsTo 'state_short_name' in state
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(BoardPosition::class, 'board_position_id', 'id');  // 'board_position_id' in boards BelongsTo 'id' in board_position
    }

    public function categorySubscriptions()
    {
        return $this->hasMany(ForumCategorySubscription::class);
    }

}
