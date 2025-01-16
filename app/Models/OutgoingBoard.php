<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class OutgoingBoard extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'outgoing_board_member';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'chapter_id', 'board_position_id', 'first_name', 'last_name', 'email', 'phone', 'street_address', 'city', 'state', 'zip', 'country',
        'is_active', 'last_updated_by', 'last_updated_date',
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
}
