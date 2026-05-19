<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AdminReport extends Model
{
    protected $table = 'admin_report';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'report_year_id', 'id');
    }
}
