<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class InquiryApplication extends Model
{
    protected $table = 'inquiry_application';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'state' in chapters BelongsTo 'id' in state
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');  // 'state' in chapters BelongsTo 'id' in state
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');  // 'country_id' in chapters BelongsTo 'id' in country
    }

     public function inquirystate(): BelongsTo
    {
        return $this->belongsTo(State::class, 'inquiry_state', 'id');  // 'state' in chapters BelongsTo 'id' in state
    }

    public function inquirycountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'inquiry_country', 'id');  // 'country_id' in chapters BelongsTo 'id' in country
    }

    public function regioninquiry(): HasOneThrough
    {
        return $this->hasOneThrough(
            RegionInquiry::class,     // Final model we want
            State::class,             // Intermediate model
            'id',                     // Foreign key on states table (what inquiry_applications.state_id points to)
            'region_id',              // Foreign key on region_inquiry table (what points to states.region_id)
            'state_id',               // Local key on inquiry_applications table
            'region_id'               // Local key on states table (the region_id column)
        );
    }

}
