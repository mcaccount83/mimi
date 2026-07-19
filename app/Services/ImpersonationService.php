<?php

namespace App\Services;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationService
{
    /**
     * Start impersonating another user. Only callable by someone
     * with permission to do so (e.g. an admin, or a coordinator
     * with authority over the target).
     */

    private function resolveReturnId(User $targetUser): ?int
{
    return match ($targetUser->type_id) {
        UserTypeEnum::COORD => $targetUser->coordinator?->id,
        UserTypeEnum::BOARD => $targetUser->board?->chapter_id,
        UserTypeEnum::OUTGOING => $targetUser->boardOutgoing?->chapter_id,
        UserTypeEnum::DISBANDED => $targetUser->boardDisbanded?->chapter_id,
        UserTypeEnum::PENDING => $targetUser->boardPending?->chapter_id,
        default => null,
    };
}

    public function start(int $targetUserId, ?string $returnToRoute = null): void
{
    $currentUser = Auth::user();

    abort_unless($this->canImpersonate($currentUser, $targetUserId), 403);

    if (! session()->has('impersonator_id')) {
        session(['impersonator_id' => $currentUser->id]);
    }

    $targetUser = User::findOrFail($targetUserId);

    session([
        'impersonation_return_type' => $targetUser->type_id,
        'impersonation_return_id' => $this->resolveReturnId($targetUser),
        'impersonation_return_override' => $this->validReturnOverride($returnToRoute),
    ]);

    Auth::loginUsingId($targetUserId);
    session()->forget('viewing_as');
}

private function validReturnOverride(?string $routeName): ?string
{
    $allowed = [
        'techreports.viewaschapter.active',
        'techreports.viewaschapter.disbanded',
        'techreports.viewaschapter.pending',
        'techreports.viewascoordinator.active',
        'techreports.viewascoordinator.retired',
        'techreports.viewascoordinator.pending',
    ];

    return in_array($routeName, $allowed) ? $routeName : null;
}

public function getReturnRoute(): ?array
{
    if ($override = session('impersonation_return_override')) {
        return ['route' => $override, 'params' => []];
    }

    $type = session('impersonation_return_type');
    $id = session('impersonation_return_id');

    if ($type === null || $id === null) {
        return null;
    }

    return match ($type) {
        UserTypeEnum::COORD => ['route' => 'coordinators.view', 'params' => ['id' => $id]],
        UserTypeEnum::PENDING => ['route' => 'chapters.editpending', 'params' => ['id' => $id]],
        UserTypeEnum::BOARD, UserTypeEnum::OUTGOING, UserTypeEnum::DISBANDED => ['route' => 'chapters.view', 'params' => ['id' => $id]],
        default => null,
    };
}

public function stop(): void
{
    $originalId = session('impersonator_id');

    abort_unless($originalId, 403);

    Auth::loginUsingId($originalId);
    session()->forget(['impersonator_id', 'impersonation_return_type', 'impersonation_return_id', 'impersonation_return_override']);
}

    public function isImpersonating(): bool
    {
        return session()->has('impersonator_id');
    }

    private function canImpersonate($currentUser, int $targetUserId): bool
    {
        // your authorization rule here — e.g.
        // $currentUser->type_id == UserTypeEnum::COORD
        // and target is within their chapter/region
        return true;
    }
}
