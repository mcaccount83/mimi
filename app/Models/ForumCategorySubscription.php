<?php

// app/Models/ForumCategorySubscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumCategorySubscription extends Model
{
    protected $guarded = []; // ALL columns are mass-assignable

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
