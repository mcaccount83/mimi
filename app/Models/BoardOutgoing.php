<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class BoardOutgoing extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'outgoing_board_member';

    protected $fillable = [
        'user_id',
        'board_id',
        'first_name',
        'last_name',
        'email',
        'board_position_id',
        'chapter_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,  'user_id', 'id');  // 'user_id' in outgoing_board_members BelongsTo 'id' in user
    }

}
