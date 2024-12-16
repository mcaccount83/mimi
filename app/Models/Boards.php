<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boards extends Model
{
    use HasFactory;

    // Disable automatic timestamps
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'board_position_id',
        'chapter_id', 'street_address', 'city', 'state', 'zip', 'country',
        'phone', 'last_updated_by', 'last_updated_date', 'is_active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'state_short_name');
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
