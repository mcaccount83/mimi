<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable; // Used for sending automated emails like resetting password.

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'type_id' => 'integer',
        'is_active' => 'integer',
        'is_admin' => 'integer',
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

    public function boardPending(): HasOne
    {
        return $this->hasOne(BoardsPending::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    public function boardDisbanded(): HasOne
    {
        return $this->hasOne(BoardsDisbanded::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    public function boardOutgoing(): HasOne
    {
        return $this->hasOne(BoardsOutgoing::class, 'user_id', 'id');  // 'user_id' in boards HasOne 'id' in users
    }

    public function adminRole(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class, 'is_admin', 'id');  // 'is_admin' in users HasOne 'id' in admin_roles
    }

    public function userStatus(): BelongsTo
    {
        return $this->belongsTo(UserStatus::class, 'is_active', 'id');  // 'is_active' in users BelongsTo 'id' in user_status
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'type_id', 'id');  // 'type_id' in users BelongsTo 'id' in user_status
    }

    public function authorFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function authorNameWithPosition()
    {
        if ($this->user_type == 'coordinator') {
            $regionText = ($this->coordinator->region && $this->coordinator->region->long_name != 'None')
                ? ', '.$this->coordinator->region->long_name.' Region'
                : ', '.$this->coordinator->conference->conference_description;

            return $this->first_name.' '.$this->last_name.', '.
                   $this->coordinator?->displayPosition->long_title.' <br> '.
                   $this->coordinator?->conference->conference_name.$regionText;
        } elseif ($this->user_type == 'board') {
            return $this->first_name.' '.$this->last_name.', '.
                   $this->board?->position->position.' <br> '.
                   $this->board?->chapters->name.', '.
                   $this->board?->chapters->state->state_short_name;
        } elseif ($this->user_type == 'disbanded') {
            return $this->first_name.' '.$this->last_name.', '.
                $this->boardDisbanded?->position->position.' <br> '.
                $this->boardDisbanded?->chapters->name.', '.
                $this->boardDisbanded?->chapters->state->state_short_name;
        }

        return $this->first_name.' '.$this->last_name;
    }

    public function categorySubscriptions(): HasMany
    {
        return $this->hasMany(ForumCategorySubscription::class);
    }
}
