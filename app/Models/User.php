<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $primaryKey = 'id';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'user_type', 'is_active',

    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function coordinator(): HasOne
    {
        return $this->hasOne(Coordinators::class, 'user_id', 'id');  // 'user_id' in coordinators HasOne 'id' in users
    }

    public function coordinators(): HasMany
    {
        return $this->hasMany(Coordinators::class, 'user_id', 'id');
    }

    public function board(): HasOne
    {
        return $this->hasOne(Boards::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    public function boardDisbanded(): HasOne
    {
        return $this->hasOne(BoardsDisbanded::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    public function boardOutgoing(): HasOne
    {
        return $this->hasOne(BoardsOutgoing::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    // public function outgoing(): HasOne
    // {
    //     return $this->hasOne(OutgoingBoard::class, 'user_id', 'id');  // 'user_id' in outgoing_board_members HasOne 'id' in users
    // }

    // public function disbanded(): HasOne
    // {
    //     return $this->hasOne(DisbandedBoard::class, 'user_id', 'id');  // 'user_id' in disbanded_board_members HasOne 'id' in users
    // }

    public function authorFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function authorNameWithPosition()
    {
        if ($this->user_type == 'coordinator') {
            $regionText = ($this->coordinator->region && $this->coordinator->region->long_name !== 'None')
                ? ', '.$this->coordinator->region->long_name.' Region'
                : ', '.$this->coordinator->conference->conference_description;

            return $this->first_name.' '.$this->last_name.', '.
                   $this->coordinator->displayPosition->long_title.' <br> '.
                   $this->coordinator->conference->conference_name.$regionText;
        } elseif ($this->user_type == 'board') {
            return $this->first_name.' '.$this->last_name.', '.
                   $this->board->position->position.' <br> '.
                   $this->board->chapters->name.', '.
                   $this->board->chapters->state->state_short_name;
        }

        return $this->first_name.' '.$this->last_name;
    }

    public function categorySubscriptions(): HasMany
    {
        return $this->hasMany(ForumCategorySubscription::class);
    }
}
