<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Coordinators extends Model
{
    use HasFactory;
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'conference_id', 'region_id', 'layer_id', 'first_name', 'last_name', 'position_id', 'display_position_id', 'email', 'sec_email', 'report_id',
        'address', 'city', 'state_id', 'zip', 'country_id', 'phone', 'alt_phone', 'birthday_month_id', 'birthday_day', 'home_chapter', 'coordinator_start_date',
        'last_updated_by', 'last_updated_date', 'active_status',
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

    // public function secondaryPosition(): BelongsTo
    // {
    //     return $this->belongsTo(CoordinatorPosition::class, 'sec_position_id', 'id');  // 'sec_position_id' in coordinators BelongsTo 'id' in coordinator_position
    // }

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
