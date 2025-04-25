<?php

namespace App\Policies\Forum;

use App\Models\Coordinators;
use App\Services\PositionConditionsService;
use Illuminate\Foundation\Auth\User;
use TeamTeaTime\Forum\Models\Category;
use TeamTeaTime\Forum\Models\Thread;

class ForumConditions
{
    protected $positionService;

    public function __construct(PositionConditionsService $positionService)
    {
        $this->positionService = $positionService;
    }

    public function canAccessCoordinatorList(User $user, Category $category): bool
    {
        if ($user->user_type === 'outgoing') {
            return false; // Hide ALL from outgoing
        }

        if ($user->user_type === 'disbanded') {
            return false; // Hide ALL from outgoing
        }

        if ($category->title === 'CoordinatorList' && $user->user_type !== 'coordinator') {
            return false; // Hide from everyone except coordinators
        }

        return true; // Default: allow access
    }

    public function canManageLists($user): bool
    {
        $userType = $this->checkUserType($user);
        $userAdmin = $this->checkUserAdmin($user);
        $position = $this->checkPosition($user, $userType);

        return $userType['isCoordinator'] && ($userAdmin['userAdmin'] || $userAdmin['userModerator']);
        // return $userType['isCoordinator'] && $position['isFounderCondition']; //use this line for TESTING a false return
    }

    public function getCategoryFromThread(Thread $thread): ?Category
    {
        return $thread->category;
    }

    public function checkPublicAnnouncements($user, Thread $thread): bool
    {
        $category = $this->getCategoryFromThread($thread);

        if ($category->title === 'Public Announcements') {
            return $this->canManageLists($user);
        }

        // For non-Public Announcement threads, check coordinator list access
        return $this->canAccessCoordinatorList($user, $category);
    }

    public function checkUserType($user): array
    {
        $userTypes = $this->positionService->getUserType($user->user_type);

        return [
            'isCoordinator' => $userTypes['coordinator'],
            'isBoard' => $userTypes['board'],
            'isOutgoing' => $userTypes['outgoing'],
            'isDisbanded' => $userTypes['disbanded'],
        ];
    }

    public function checkUserAdmin($user): array
    {
        $userTypes = $this->positionService->getUserAdmin($user->is_admin);

        return [
            'userAdmin' => $userTypes['userAdmin'],
            'userModerator' => $userTypes['userModerator'],
        ];
    }

    public function checkPosition($user, $userType): array
    {
        $cdPositionid = null;
        $cdSecPositionid = null;

        if ($userType['isCoordinator']) {
            $userId = $user->id;
            $cdDetails = Coordinators::where('user_id', '=', $userId)->first();

            if ($cdDetails) {
                $cdPositionid = $cdDetails->position_id;
                $cdSecPositionid = $cdDetails->sec_position_id;
            }
        }

        $positions = $this->positionService->getConditionsForUser($cdPositionid, $cdSecPositionid);

        return [
            'isITCondition' => $positions['ITCondition'],
            'isListAdminCondition' => $positions['listAdminCondition'],
            'isFounderCondition' => $positions['founderCondition'],
        ];
    }
}
