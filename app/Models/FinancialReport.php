<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    protected $table = 'financial_report';

    protected $primaryKey = 'chapter_id';

    protected $guarded = []; // ALL columns are mass-assignable

}
