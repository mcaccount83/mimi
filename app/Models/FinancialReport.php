<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\Belongsto;


class FinancialReport extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'financial_report';

    protected $primaryKey = 'chapter_id';

    public $timestamps = false;

    protected $fillable = [
        'chapter_id',  // Add this if not already present
        'pre_balance',
        'amount_reserved_from_previous_year',
        'name',
    ];

}
