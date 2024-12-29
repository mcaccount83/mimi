<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function coordinator(): HasOne
    {
        return $this->hasOne(Coordinators::class, 'user_id', 'id');  // 'user_id' in coordinators HasOne 'id' in users
    }

    public function board(): HasOne
    {
        return $this->hasOne(Boards::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    public function outgoing(): HasOne
    {
        return $this->hasOne(BoardOutgoing::class, 'user_id', 'id');  // 'user_id' in outgoing_board_members HasOne 'id' in users
    }

}
