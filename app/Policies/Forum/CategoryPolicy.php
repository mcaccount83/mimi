<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\CategoryPolicy as ForumCategoryPolicy;
use TeamTeaTime\Forum\Models\Category;

use Illuminate\Foundation\Auth\User;

use App\Models\Coordinators;
use Illuminate\Support\Facades\Log;

class CategoryPolicy extends ForumCategoryPolicy
{
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

    private function canManageLists($user): bool
    {
        $userType = $this->checkUserType($user);
        $position = $this->checkPosition($user, $userType);

        return $userType['isCoordinator'] && ($position['isITCondition'] || $position['isListAdminCondition']);
        // return $userType['isCoordinator'] && $position['isFounderCondition']; //use this line for TESTING a false return
    }

    protected function checkUserType($user): array
    {
        $userTypes = getUserType($user->user_type);

        return [
            'isCoordinator' => $userTypes['coordinator'],
            'isBoard' => $userTypes['board'],
            'isOutgoing' => $userTypes['outgoing'],
        ];
    }

    protected function checkPosition($user, $userType): array
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

    public function view(User $user, Category $category): bool
    {
        return $this->canAccessCoordinatorList($user, $category);
    }

    public function edit(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function delete(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function createThreads(User $user, Category $category): bool
    {
        return $this->canAccessCoordinatorList($user, $category);
    }

    public function manageThreads(User $user, Category $category): bool
    {
        return $this->deleteThreads($user, $category)
            || $this->restoreThreads($user, $category)
            || $this->moveThreadsFrom($user, $category)
            || $this->lockThreads($user, $category)
            || $this->pinThreads($user, $category);
    }

    public function deleteThreads(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function restoreThreads(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function moveThreadsFrom(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function moveThreadsTo(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function lockThreads(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function pinThreads(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }

    public function markThreadsAsRead(User $user, Category $category): bool
    {
        return $this->canManageLists($user);
    }
}
