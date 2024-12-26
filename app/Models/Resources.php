<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Resources extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'resources';

    protected $fillable = [
        'name', 'description', 'version', 'link',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category', 'id');  // 'category' in resrouces HasOne 'id' in resource_category
    }

}
