<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminYear extends Model
{
    protected $table = 'admin_year';

    protected $primaryKey = 'id';

    protected $guarded = []; // ALL columns are mass-assignable

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id', 'id');
    }
}
