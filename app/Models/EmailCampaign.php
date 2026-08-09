<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;

#[Table('email_campaigns', 'id')]
#[Unguarded]

class EmailCampaign extends Model
{
    protected $casts = [
        'attachments' => 'array',
        'active'      => 'boolean',
        'month'       => 'integer',
    ];

    public function getSendUrlAttribute(): ?string
    {
        return $this->route_name && \Illuminate\Support\Facades\Route::has($this->route_name)
            ? route($this->route_name)
            : null;
    }

    public function getPreviewUrlAttribute(): ?string
    {
        return $this->preview_slug
            ? route('campaigns.preview', $this->preview_slug)
            : null;
    }

    public function monthRelation()
    {
        return $this->belongsTo(Month::class, 'month', 'id');
    }
}
