<?php

// app/Models/ForumCategorySubscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ForumCategorySubscription extends Model
{
    protected $fillable = ['user_id', 'category_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
