<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportingTree extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'coordinator_id ',  // Add this if not already present
        'layer0',
        'layer1',
        'layer2',
        'layer3',
        'layer4',
        'layer5',
        'layer6',
        'layer7',
        'layer8',
    ];

    public function reporting()
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');
    }

    protected $table = 'coordinator_reporting_tree';
}
