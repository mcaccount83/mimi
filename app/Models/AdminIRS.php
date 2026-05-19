<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminIRS extends Model
{
    protected $table = 'admin_irs';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id', 'id');
    }
}
