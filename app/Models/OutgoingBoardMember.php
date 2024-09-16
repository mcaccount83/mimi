<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingBoardMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'board_id',
        'first_name',
        'last_name' ,
        'email',
        'board_position_id' ,
        'chapter_id'
    ];

    protected $table = 'outgoing_board_member';

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
