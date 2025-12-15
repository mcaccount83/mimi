<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resources extends Model
{
    protected $guarded = [ ]; // ALL columns are mass-assignable

    public function resourceCategory(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category', 'id');  // 'category' in resrouces belongsTo 'id' in resource_category
    }

    public function toolkitCategory(): BelongsTo
    {
        return $this->belongsTo(ToolkitCategory::class, 'category', 'id');  // 'category' in resrouces belongsTo 'id' in toolkit_category
    }

    // public function updatedBy(): BelongsTo
    // {
    //     return $this->belongsTo(Coordinators::class, 'updated_id', 'id');  // 'updated_id' in resources BelongsTo 'id' in coordinators
    // }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');  // 'updated_id' in resources BelongsTo 'id' in users
    }
}
