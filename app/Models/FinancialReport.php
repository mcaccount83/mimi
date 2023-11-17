<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [

        'name',

    ];

    protected $primaryKey = 'chapter_id';

    protected $table = 'financial_report';
}
