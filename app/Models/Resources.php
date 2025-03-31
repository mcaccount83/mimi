<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Resources extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name', 'description', 'version', 'link', 'file_path',
    ];

    public function resourceCategory(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category', 'id');  // 'category' in resrouces belongsTo 'id' in resource_category
    }

    public function toolkitCategory(): BelongsTo
    {
        return $this->belongsTo(ToolkitCategory::class, 'category', 'id');  // 'category' in resrouces belongsTo 'id' in toolkit_category
    }
}
