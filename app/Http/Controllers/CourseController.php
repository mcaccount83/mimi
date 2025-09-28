<?php

namespace App\Http\Controllers;

use App\Services\LearnDashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class CourseController extends Controller
{
    protected $learndashService;

    public function __construct(LearnDashService $learndashService)
    {
        $this->learndashService = $learndashService;
    }

// public function myCourses()
// {
//     $user = Auth::user();

//     if (!$user || !in_array($user->user_type, ['coordinator', 'board'])) {
//         return redirect()->route('home')->with('error', 'You are not authorized to view courses.');
//     }

//     try {
//         $courses = $this->learndashService->getCoursesByLearnDashTag($user->user_type);

//         // Check if we got an error response
//         if (isset($courses['code'])) {
//             return view('courses.index')->with([
//                 'courses' => [],
//                 'error' => $courses['message']
//             ]);
//         }

//     } catch (\Exception $e) {
//         return view('courses.index')->with([
//             'courses' => [],
//             'error' => 'Unable to load courses.'
//         ]);
//     }

//     return view('courses.index', compact('courses'));
// }

// In your controller
public function myCourses()
{
    $user = Auth::user();

    if (!$user || !in_array($user->user_type, ['coordinator', 'board'])) {
        return redirect()->route('home')->with('error', 'You are not authorized to view courses.');
    }

    $courses = $this->learndashService->getCoursesForUserType($user->user_type);

    // Add auto-login URLs to each course
    foreach ($courses as &$course) {
        $course['auto_login_url'] = $this->learndashService->getAutoLoginUrl($course, $user);
    }

    return view('courses.index', compact('courses'));
}

public function redirectToCourse($courseId, Request $request)
{
    $token = $request->query('token');
    $courseUrl = $request->query('course_url'); // Don't decode yet

    Log::info('Course redirect debug:', [
        'course_id' => $courseId,
        'raw_course_url' => $courseUrl,
        'decoded_course_url' => urldecode($courseUrl),
        'has_token' => !empty($token)
    ]);

    if (empty($courseUrl)) {
        Log::error('No course URL provided');
        return redirect()->route('courses.index')->with('error', 'Invalid course link');
    }

    $decodedUrl = urldecode($courseUrl);

    // For now, just test direct redirect
    return redirect($decodedUrl);
}

}
