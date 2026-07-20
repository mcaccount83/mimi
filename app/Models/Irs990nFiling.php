<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('irs_990n_filings', 'id')]
class Irs990nFiling extends Model
{
    protected $fillable = ['chapter_id', 'ein', 'tax_year', 'tax_period_begin', 'tax_period_end', 'organization_name', 'synced_at'];
    protected $casts = ['tax_period_begin' => 'date', 'tax_period_end' => 'date', 'synced_at' => 'datetime'];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapters::class, 'chapter_id');
    }
}
