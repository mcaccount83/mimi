<?php

namespace App\Policies\Forum;

use TeamTeaTime\Forum\Policies\ThreadPolicy as ForumThreadPolicy;
use TeamTeaTime\Forum\Models\Thread;
use TeamTeaTime\Forum\Models\Category;
use Illuminate\Foundation\Auth\User;
use App\Models\Coordinators;

class ThreadPolicy extends ForumThreadPolicy
{
    protected $forumConditions;

    public function __construct()
    {
        $this->forumConditions = app(ForumConditions::class);
    }

    // public function canAccessCoordinatorList(User $user, Category $category): bool
    // {
    //     if ($user->user_type === 'outgoing'){
    //         return false; // Hide ALL from outgoing
    //     }

    //     if ($category->title === 'CoordinatorList' && $user->user_type !== 'coordinator') {
    //         return false; // Hide from everyone except coordinators
    //     }

    //     return true; // Default: allow access
    // }

    // private function getCategoryFromThread(Thread $thread): ?Category
    // {
    //     return $thread->category;
    // }

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

    public function view($user, Thread $thread): bool
    {
        return $this->forumConditions->checkPublicAnnouncements($user, $thread);

        // return true;
    }

    public function rename($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return $user->getKey() === $thread->author_id;
    }

    public function reply($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return !$thread->locked;
    }

    public function delete($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return $user->getKey() === $thread->author_id;
    }

    public function restore($user, Thread $thread): bool
    {
        if (!$this->forumConditions->checkPublicAnnouncements($user, $thread)) {
            return false;
        }

        return $user->getKey() === $thread->author_id;
    }

    public function deletePosts($user, Thread $thread): bool
    {
        return $this->forumConditions->checkPublicAnnouncements($user, $thread);

        // return true;
    }

    public function restorePosts($user, Thread $thread): bool
    {
        return $this->forumConditions->checkPublicAnnouncements($user, $thread);

        // return true;
    }
}
