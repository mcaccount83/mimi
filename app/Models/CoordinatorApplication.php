<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class CoordinatorApplication extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'coordinator_application';

    protected $primaryKey = 'coordinator_id';

    protected $fillable = [
        'coordinator_id', 'home_chapter', 'home_state', 'start_date', 'jobs_programs', 'helped_me', 'problems', 'why_volunteer', 'other_volunteer', 'special_skills', 'enjoy_volunteering',
        'referred_by', 'app_status', 'created_at', 'updated_at',
    ];

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Coordinators::class, 'coordinator_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }
}
