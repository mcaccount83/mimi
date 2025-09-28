<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LearnDashService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->baseUrl = config('services.learndash.url');
        // Set default credentials
        $this->username = config('services.learndash.user');
        $this->password = config('services.learndash.password');
    }

    public function setUser($user)
    {
        if ($user && $user->wp_username && $user->wp_app_password) {
            $this->username = $user->wp_username;
            $this->password = $user->wp_app_password;
        }
        return $this;
    }


public function getAutoLoginUrl($course, $laravelUser)
{
    // Create a secure token
    $payload = [
        'user_id' => $laravelUser->id,
        'email' => $laravelUser->email,
        'first_name' => $laravelUser->first_name,
        'last_name' => $laravelUser->last_name,
        'expires' => time() + 300
    ];

    $token = encrypt($payload);

    // Debug what we're working with
    Log::info('Building auto-login URL:', [
        'course_id' => $course['id'],
        'course_link' => $course['link']
    ]);

    return route('course.redirect', [
        'course_id' => $course['id']
    ]) . '?token=' . urlencode($token) . '&course_url=' . urlencode($course['link']);
}

public function getCoursesForUserType($userType)
{
    $response = Http::get("https://momsclub.org/elearning/wp-json/public/v1/courses/{$userType}");

    if ($response->successful()) {
        return $response->json();
    }

    return [];
}




    public function enrollUserInCourse($userId, $courseId)
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->post("{$this->baseUrl}users/{$userId}/courses/{$courseId}");

        return $response->json();
    }

    public function createUser(array $userData)
    {
            $response = Http::withBasicAuth($this->username, $this->password)
            ->post("{$this->baseUrl}users", $userData);

        return $response->json();
    }

}
