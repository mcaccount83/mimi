<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chapter extends Model
{
    use HasFactory;

    //
    public $timestamps = false;

    protected $fillable = [

        'name',
        'ein_letter_path',
        'ein_letter',

    ];

    // Define the relationship to the FinancialReport model
    public function financialReport(): HasOne
    {
        return $this->hasOne(FinancialReport::class, 'chapter_id', 'id'); // 'chapter_id' in financial_report references 'id' in chapters
    }
}
