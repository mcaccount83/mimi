<?php

namespace App\Models;

// use Carbon\Carbon;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FinancialReportLastYear extends Model
{
    use HasFactory;
    use Notifiable;

    protected $primaryKey = 'chapter_id';

    public $timestamps = false;

    protected $fillable = [
        'chapter_id',
        'pre_balance',
        'amount_reserved_from_previous_year',
        'name',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;
        $this->table = 'financial_report_12_'.$lastYear;
    }
}
