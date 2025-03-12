<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class CoordinatorTree extends Model
{
    use HasFactory;
    use Notifiable;

    public $timestamps = false;

    protected $table = 'coordinator_reporting_tree';

    protected $fillable = [
        'coordinator_id', 'layer0', 'layer1', 'layer2', 'layer3', 'layer4', 'layer5', 'layer6', 'layer7', 'layer8',
    ];

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'coordinator_id', 'primary_coordinator_id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in chapters
    }
}
