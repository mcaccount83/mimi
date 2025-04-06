<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProbationSubmission extends Model
{
    use HasFactory;
    use Notifiable;

    protected $primaryKey = 'chapter_id';

    protected $table = 'probation_submission';

    protected $fillable = [
        'chapter_id',
        'q1_dues',
        'q1_benefit',
        'q2_dues',
        'q2_benefit',
        'q3_dues',
        'q3_benefit',
        'q4_dues',
        'q4_benefit',
        'created_at',
        'updated_at',
    ];

}
