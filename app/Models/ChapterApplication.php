<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class ChapterApplication extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'chapter_application';

    protected $primaryKey = 'chapter_id';

    protected $fillable = [
        'chapter_id', 'sistered', 'sistered_by', 'hear_about', 'created_at', 'updated_at',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id', 'id');  // 'coordinator_id' in coordinator_tree BelongsTo 'id' in coordinators
    }

}
