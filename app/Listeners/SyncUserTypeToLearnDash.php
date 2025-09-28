<?php

namespace App\Listeners; // Add namespace

use App\Events\UserUpdated;
use App\Services\LearnDashService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncUserTypeToLearnDash implements ShouldQueue
{
        protected $learndashService;

    public function __construct(LearnDashService $learndashService)
    {
        $this->learndashService = $learndashService;
    }

public function handle(UserUpdated $event)
{
    $user = $event->user;

    if ($user->wasChanged('user_type')) {
        $wpUserId = $user->learndash_user_id;

        if ($wpUserId) {
            // Use the injected service instance
            // Get the course IDs for the new user type
            $newCourses = $this->learndashService->getCoursesByTag($user->user_type);

            // Enroll the user in the new courses
            foreach ($newCourses as $course) {
                $this->learndashService->enrollUserInCourse($wpUserId, $course['id']);
            }
        }
    }
}

}
