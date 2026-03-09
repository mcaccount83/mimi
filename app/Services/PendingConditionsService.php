<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class PendingConditionsService
{
    public function getPendingInquiryCount(?int $confId = null): int
    {
        if (!$confId) return 0;

        return \App\Models\InquiryApplication::join('state', 'inquiry_application.state_id', '=', 'state.id')
            ->where('state.conference_id', $confId)
            ->where(function ($query) {
                $query->where('response', '!=', 1)
                    ->orWhereNull('response');
            })
            ->count();
    }

    public function getPendingNewChapterCount(?int $confId = null): int
    {
        if (!$confId) return 0;

        return \App\Models\Chapters::join('state', 'chapters.state_id', '=', 'state.id')
            ->where('state.conference_id', $confId)
            ->where(function ($query) {
                $query->where('active_status', \App\Enums\ChapterStatusEnum::PENDING);
            })
            ->count();
    }

    public function getPendingNewCoordCount(?int $confId = null): int
    {
        if (!$confId) return 0;

        return \App\Models\Coordinators::join('state', 'coordinators.state_id', '=', 'state.id')
            ->where('state.conference_id', $confId)
            ->where(function ($query) {
                $query->where('active_status', \App\Enums\ChapterStatusEnum::PENDING);
            })
            ->count();
    }
}
