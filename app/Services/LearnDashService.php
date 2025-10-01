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

// public function getAutoLoginUrl($course, $laravelUser)
// {
//     $payload = [
//         'user_id' => $laravelUser->id,
//         'email' => $laravelUser->email,
//         'first_name' => $laravelUser->first_name,
//         'last_name' => $laravelUser->last_name,
//         'user_type' => $laravelUser->user_type, // Add this line
//         'expires' => time() + 300
//     ];

//     $token = base64_encode(json_encode($payload));

//     return route('course.redirect', [
//         'course_id' => $course['id']
//     ]) . '?token=' . urlencode($token) . '&course_url=' . urlencode($course['link']);
// }


// public function getCoursesForUserType($userType)
// {
//     // Add a cache-busting parameter to force fresh data
//     $response = Http::withHeaders([
//         'Cache-Control' => 'no-cache, no-store, must-revalidate',
//         'Pragma' => 'no-cache',
//     ])->get("https://momsclub.org/elearning/wp-json/public/v1/courses/{$userType}?nocache=" . time());

//     if ($response->successful()) {
//         return $response->json();
//     }

//     return [];
// }

// public function getCoursesBySpecificTag($tagSlug)
// {
//     $response = Http::withHeaders([
//         'Cache-Control' => 'no-cache, no-store, must-revalidate',
//         'Pragma' => 'no-cache',
//     ])->get("https://momsclub.org/elearning/wp-json/public/v1/courses/{$tagSlug}?nocache=" . time());

//     if ($response->successful()) {
//         return $response->json();
//     }

//     return [];
// }




public function getAutoLoginUrl($course, $laravelUser)
{
    $payload = [
        'user_id' => $laravelUser->id,
        'email' => $laravelUser->email,
        'first_name' => $laravelUser->first_name,
        'last_name' => $laravelUser->last_name,
        'user_type' => $laravelUser->user_type, // 'coordinator' or 'board'
        'expires' => time() + 300
    ];

    $token = base64_encode(json_encode($payload));

    return route('course.redirect', [
        'course_id' => $course['id']
    ]) . '?token=' . urlencode($token) . '&course_url=' . urlencode($course['link']);
}

// Get courses for a specific user type - user_type=group -- board=board, coordinator=coordinator
public function getCoursesForUserType($userType)
{
    $response = Http::withHeaders([
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
    ])->get("https://momsclub.org/elearning/wp-json/public/v1/courses/group/{$userType}?nocache=" . time());

    if ($response->successful()) {
        return $response->json();
    }

    return [];
}

// Keep this if you still need tag-based filtering for other purposes
// Otherwise you can remove it
// public function getCoursesBySpecificTag($tagSlug)
// {
//     $response = Http::withHeaders([
//         'Cache-Control' => 'no-cache, no-store, must-revalidate',
//         'Pragma' => 'no-cache',
//     ])->get("https://momsclub.org/elearning/wp-json/public/v1/courses/{$tagSlug}?nocache=" . time());

//     if ($response->successful()) {
//         return $response->json();
//     }

//     return [];
// }

}
