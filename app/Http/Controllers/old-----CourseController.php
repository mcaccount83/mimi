<?php

namespace App\Http\Controllers;

use App\Services\LearnDashService;
use App\Models\Coordinators;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class CourseController extends Controller
{
    protected $learndashService;
        protected $baseCoordinatorController;


    public function __construct(LearnDashService $learndashService, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->learndashService = $learndashService;
                $this->baseCoordinatorController = $baseCoordinatorController;

    }

public function myCourses(Request $request)
{

    $user = User::find($request->user()->id);

    //  $user = Auth::user();
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;

        $baseQuery = $this->baseCoordinatorController->getCoordinatorDetails($cdId);
        $cdDetails = $baseQuery['cdDetails'];
        $cdId = $baseQuery['cdId'];
        $cdActiveId = $baseQuery['cdActiveId'];
        $regionLongName = $baseQuery['regionLongName'];
        $conferenceDescription = $baseQuery['conferenceDescription'];
        $cdConfId = $baseQuery['cdConfId'];
        $cdRptId = $baseQuery['cdRptId'];
        $RptFName = $baseQuery['RptFName'];
        $RptLName = $baseQuery['RptLName'];
        $ReportTo = $RptFName.' '.$RptLName;
        $displayPosition = $baseQuery['displayPosition'];
        $mimiPosition = $baseQuery['mimiPosition'];
        $secondaryPosition = $baseQuery['secondaryPosition'];
        $cdLeave = $baseQuery['cdDetails']->on_leave;



    // $user = Auth::user();

    // if (!$user || !in_array($user->user_type, ['coordinator', 'board'])) {
    //     return redirect()->route('home')->with('error', 'You are not authorized to view courses.');
    // }

    $courses = $this->learndashService->getCoursesForUserType($user->user_type);

    // Add auto-login URLs to each course
    foreach ($courses as &$course) {
        $course['auto_login_url'] = $this->learndashService->getAutoLoginUrl($course, $user);
    }

    // return view('courses.index', compact('courses'));
        // return view('coordinators.viewelearning', compact('courses'));

         $data = ['cdDetails' => $cdDetails, 'conferenceDescription' => $conferenceDescription, 'regionLongName' => $regionLongName, 'ReportTo' => $ReportTo,
            'displayPosition' => $displayPosition, 'mimiPosition' => $mimiPosition, 'secondaryPosition' => $secondaryPosition,
            'cdLeave' => $cdLeave, 'courses' => $courses];

        return view('coordinators.viewelearning')->with($data);


}

public function redirectToCourse($courseId, Request $request)
{
    $token = $request->query('token');
    $courseUrl = urldecode($request->query('course_url'));

    // Don't call WordPress API from Laravel
    // Instead, redirect the user's browser directly to WordPress
    $wpAutoLoginUrl = "https://momsclub.org/elearning/wp-json/auth/v1/auto-login?" . http_build_query([
        'token' => $token,
        'course_url' => $courseUrl
    ]);

    Log::info('Redirecting browser to WordPress auto-login:', [
        'url' => $wpAutoLoginUrl
    ]);

    // Direct browser redirect - this ensures cookies are set in the user's browser
    return redirect($wpAutoLoginUrl);
}

}
