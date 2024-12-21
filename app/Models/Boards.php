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

    protected $table = 'boards';

    protected $fillable = [

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,  'user_id', 'id');  // 'user_id' in boards BelongsTo 'id' in user
    }

    public function chapters()
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'chapter_id' in boards BelongsTo 'id' in chapters
    }

    public function borPosition(): BelongsTo
    {
        return $this->belongsTo(BoardPosition::class,  'board_position_id', 'id');  // 'board_position_id' in boards BelongsTo 'id' in board_position
    }

}
