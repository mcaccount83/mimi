<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Belongsto;
use Illuminate\Notifications\Notifiable;

class Chapters extends Model
{
    use HasFactory;
    use Notifiable;

    public $timestamps = false;

    protected $table = 'chapters';

    protected $fillable = [
        'name', 'state', 'country', 'conference', 'region',
        'ein', 'status', 'territory', 'inquiries_contact',
        'start_month_id', 'start_year', 'next_renewal_year',
        'primary_coordinator_id', 'founders_name', 'last_updated_by',
        'last_updated_date', 'created_at', 'is_active',

        'ein_letter_path',
        'ein_letter',
    ];

    public function boards()
    {
        return $this->hasMany(Boards::class, 'chapter_id', 'id');
    }

    public function president()
    {
        return $this->hasOne(Boards::class, 'chapter_id', 'id')
            ->where('board_position_id', 1);
    }

    public function avp()
    {
        return $this->hasOne(Boards::class, 'chapter_id', 'id')
            ->where('board_position_id', 2);
    }

    public function mvp()
    {
        return $this->hasOne(Boards::class, 'chapter_id', 'id')
            ->where('board_position_id', 3);
    }

    public function treasurer()
    {
        return $this->hasOne(Boards::class, 'chapter_id', 'id')
            ->where('board_position_id', 4);
    }

    public function secretary()
    {
        return $this->hasOne(Boards::class, 'chapter_id', 'id')
            ->where('board_position_id', 5);
    }

    public function financialReport(): HasOne
    {
        return $this->hasOne(FinancialReport::class, 'chapter_id', 'id');  // 'chapter_id' in financial_report HasOne 'id' in chapters
    }

    public function documents(): HasOne
    {
        return $this->hasOne(Documents::class, 'chapter_id', 'id');  // 'chapter_id' in documents HasOne 'id' in chapters
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');  // 'state' in chapters BelongsTo 'id' in state
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');  // 'region' in chapters BelongsTo 'id' in region
    }

    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class,  'conference_id', 'id');  // 'conference' in chapters BelongsTo 'id' in conference
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_short_name', 'short_name');  // 'country' in chapters BelongsTo 'id' in country
    }

    public function startMonth(): BelongsTo
    {
        return $this->belongsTo(Month::class, 'start_month_id', 'id');  // 'start_month_id' in chapters BelongsTo 'id' in month
    }

    public function webLink(): BelongsTo
    {
        return $this->belongsTo(Website::class, 'website_status', 'id');  // 'website_status' in chapters BelongsTo 'id' in websie
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');  // 'status_id' in chapters BelongsTo 'id' in status
    }

    public function primaryCoordinator()
    {
        return $this->belongsTo(Coordinators::class, 'primary_coordinator_id', 'id');  // 'primary_coordinator_id' in chapters BelongsTo 'id' in coordinators
    }

    public function coordinatorTree()
    {
        return $this->belongsTo(CoordinatorTree::class, 'primary_coordinator_id', 'coordinator_id');  // 'primary_coordinator_id' in chapters BelongsTo 'coorindaotr_id' in coordinator_tree
    }

}