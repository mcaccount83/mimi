<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('admin_report', 'id')]
#[Unguarded]
class AdminReport extends Model
{
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'report_year_id', 'id');
    }
}
