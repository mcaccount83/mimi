<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Category;
use Illuminate\Foundation\Auth\User;
use App\Models\Coordinators;

class ForumConditions
{
    /*/Custom Helpers/*/
    // $conditions = getPositionConditions($cdPositionid, $cdSecPositionid);

    public function canAccessCoordinatorList(User $user, Category $category): bool
    {
        if ($user->user_type === 'outgoing'){
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
        $position = $this->checkPosition($user, $userType);

        return $userType['isCoordinator'] && ($position['isITCondition'] || $position['isListAdminCondition']);
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
        $userTypes = getUserType($user->user_type);

        return [
            'isCoordinator' => $userTypes['coordinator'],
            'isBoard' => $userTypes['board'],
            'isOutgoing' => $userTypes['outgoing'],
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

        $positions = getPositionConditions($cdPositionid, $cdSecPositionid);

        return [
            'isITCondition' => $positions['ITCondition'],
            'isListAdminCondition' => $positions['listAdminCondition'],
            'isFounderCondition' => $positions['founderCondition'],
        ];
    }
}
