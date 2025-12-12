<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coordinators extends Model
{
    protected $guarded = [ ]; // ALL columns are mass-assignable

    protected $casts = [
        'active_status' => 'integer',
    ];

    public function coordTree(): HasOne
    {
        return $this->hasOne(CoordinatorTree::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree HasOne 'id' in coordinators
    }

    public function recognition(): HasOne
    {
        return $this->hasOne(CoordinatorRecognition::class, 'coordinator_id', 'id');  // 'coordinator_id' in recognition_gifts HasOne 'id' in coordinators
    }

    public function application(): HasOne
    {
        return $this->hasOne(CoordinatorApplication::class, 'coordinator_id', 'id');  // 'active_status' in coordinators BelongsTo 'id' in probation
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');  // 'user_id' in coordinators BelongsTo 'id' in user
    }

    public function displayPosition(): BelongsTo
    {
        return $this->belongsTo(CoordinatorPosition::class, 'display_position_id', 'id');  // 'display_position_id' in coordinators BelongsTo 'id' in coordinator_position
    }

    public function mimiPosition(): BelongsTo
    {
        return $this->belongsTo(CoordinatorPosition::class, 'position_id', 'id');   // 'position_id' in coordinators BelongsTo 'id' in coordinator_position
    }

    public function secondaryPosition(): BelongsToMany
    {
        return $this->belongsToMany(CoordinatorPosition::class, 'coordinator_secondary_positions', 'coordinator_id', 'position_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');  // 'state' in coordinators BelongsTo 'id' in state
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');  // 'region_id' in coordinators BelongsTo 'id' in region
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');  // 'conference_id' in coordinators BelongsTo 'id' in conference
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');  // 'country_short_name' in coordinators BelongsTo 'id' in country
    }

    public function birthdayMonth(): BelongsTo
    {
        return $this->belongsTo(Month::class, 'birthday_month_id', 'id');  // 'birthday_month_id' in coordinators BelongsTo 'id' in month
    }

    public function reportsTo(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'report_id', 'id');  // 'report_id' in coordinators BelongsTo 'id' in coordinators
    }

    public function reportCoordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'report_id', 'id');  // 'report_id' in coordinators BelongsTo 'id' in coordinators
    }

    public function activeStatus(): BelongsTo
    {
        return $this->belongsTo(ActiveStatus::class, 'active_status', 'id');  // 'active_status' in coordinators BelongsTo 'id' in probation
    }
}
