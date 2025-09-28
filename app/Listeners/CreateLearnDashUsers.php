<?php

namespace App\Listeners;

use App\Models\User; // Import the User model
use App\Services\LearnDashService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Events\Registered as RegisteredEvent;

class CreateLearnDashUsers implements ShouldQueue
{
    protected $learndashService;

    public function __construct(LearnDashService $learndashService)
    {
        $this->learndashService = $learndashService;
    }

    public function handle(RegisteredEvent $event)
    {
        // Re-fetch the user model to ensure all methods and attributes are available
        $user = User::find($event->user->id);

        if (!$user) {
            // Handle the case where the user is not found
            return;
        }

        // Create the user in LearnDash first
        $wpUserData = [
            'username' => $user->email,
            'email' => $user->email,
            'password' => 'some_secure_password',
        ];
        $wpUserResponse = $this->learndashService->createUser($wpUserData);

        if (isset($wpUserResponse['id'])) {
            $user->learndash_user_id = $wpUserResponse['id'];
            $user->save();

            // Get the course IDs based on the user's type tag
            $courses = $this->learndashService->getCoursesByTag($user->user_type);

            // Enroll the user in each matching course
            foreach ($courses as $course) {
                $this->learndashService->enrollUserInCourse($user->learndash_user_id, $course['id']);
            }
        }
    }
}

