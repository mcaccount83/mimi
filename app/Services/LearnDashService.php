<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LearnDashService
{
    protected $baseUrl;

    protected $username;

    protected $password;

    public function __construct()
    {
        $this->baseUrl = config('services.learndash.url');
        $this->username = config('services.learndash.user');
        $this->password = config('services.learndash.password');
    }

public function getAutoLoginUrl($course, $laravelUser)
{
    $payload = [
        'user_id' => $laravelUser->id,
        'email' => $laravelUser->email,
        'first_name' => $laravelUser->first_name,
        'last_name' => $laravelUser->last_name,
        'user_type' => $laravelUser->user_type, // 'coordinator' or 'board'
        'expires' => time() + 300,
    ];

    $token = base64_encode(json_encode($payload));

    return route('course.redirect', [
        'course_id' => $course['id'],
    ]) . '?token=' . urlencode($token) . '&course_url=' . urlencode($course['link']);
}

// Get courses for a specific user type - user_type=group -- board=board, coordinator=coordinator
public function getCoursesForUserType($userType)
{
    $response = Http::withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
    // ])->get("https://momsclub.org/elearning/wp-json/public/v1/courses/group/{$userType}?nocache=" . time());
    ])->get("https://momsclub.org/elearning/wp-json/public/v1/courses/group/{$userType}?nocache=".time());

    if ($response->successful()) {
        return $response->json();
    }

    return [];
}

}
