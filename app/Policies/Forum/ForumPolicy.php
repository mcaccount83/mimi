<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\ForumPolicy as BaseForumPolicy;

use App\Models\Coordinators;
use Illuminate\Support\Facades\Log;

class ForumPolicy extends BaseForumPolicy
{
    protected $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    // private function canManageLists($user): bool
    // {
    //     $userType = $this->checkUserType($user);
    //     $position = $this->checkPosition($user, $userType);

    //     return $userType['isCoordinator'] && ($position['isITCondition'] || $position['isListAdminCondition']);
    //     // return $userType['isCoordinator'] && $position['isFounderCondition']; //use this line for TESTING a false return
    // }

    // protected function checkUserType($user): array
    // {
    //     $userTypes = getUserType($user->user_type);

    //     return [
    //         'isCoordinator' => $userTypes['coordinator'],
    //         'isBoard' => $userTypes['board'],
    //         'isOutgoing' => $userTypes['outgoing'],
    //     ];
    // }

    // protected function checkPosition($user, $userType): array
    // {
    //     $cdPositionid = null;
    //     $cdSecPositionid = null;

    //     if ($userType['isCoordinator']) {
    //         $userId = $user->id;
    //         $cdDetails = Coordinators::where('user_id', '=', $userId)->first();

    //         if ($cdDetails) {
    //             $cdPositionid = $cdDetails->position_id;
    //             $cdSecPositionid = $cdDetails->sec_position_id;
    //         }
    //     }

    //     $positions = getPositionConditions($cdPositionid, $cdSecPositionid);

    //     return [
    //         'isITCondition' => $positions['ITCondition'],
    //         'isListAdminCondition' => $positions['listAdminCondition'],
    //         'isFounderCondition' => $positions['founderCondition'],
    //     ];
    // }

    public function createCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function moveCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function editCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function deleteCategories($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function markThreadsAsRead($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function viewTrashedThreads($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }

    public function viewTrashedPosts($user): bool
    {
        return $this->forumConditions->canManageLists($user);
    }
}
