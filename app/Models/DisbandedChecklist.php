<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DisbandedChecklist extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'disbanded_checklist';

    protected $primaryKey = 'chapter_id';

    protected $fillable = [
        'chapter_id',
        'final_payment',
        'donate_funds',
        'destroy_manual',
        'remove_online',
        'file_irs',
        'file_financial',
    ];
}
