<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Unguarded]
class Resources extends Model
{
    public function resourceCategory(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category', 'id');  // 'category' in resrouces belongsTo 'id' in resource_category
    }

    public function toolkitCategory(): BelongsTo
    {
        return $this->belongsTo(ToolkitCategory::class, 'toolkit_category', 'id');  // 'category' in resrouces belongsTo 'id' in toolkit_category
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_id', 'id');  // 'updated_id' in resources BelongsTo 'id' in users
    }
}
