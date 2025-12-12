<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportLastYear extends Model
{
    protected $primaryKey = 'chapter_id';

    public $timestamps = false;

    protected $guarded = [ ]; // ALL columns are mass-assignable

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $positionConditionsService = app(\App\Services\PositionConditionsService::class);
        $dateOptions = $positionConditionsService->getDateOptions();
        $lastYear = $dateOptions['lastYear'];
        $this->table = 'financial_report_12_'.$lastYear;
    }
}
