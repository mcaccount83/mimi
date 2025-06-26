<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use App\Models\Resources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller implements HasMiddleware
{
    /**
     * Create a new controller instance.
     */
    protected $userController;

    protected $baseBoardController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController)
    {

        $this->userController = $userController;
        $this->baseBoardController = $baseBoardController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
    }

    /**
     * Home page for Coordinators & Board Members - logic for login redirect
     */
    public function index(Request $request)
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userStatus = $user['userStatus'];

        if ($userStatus != 1) {
            Auth::logout();  // logout inactive user
            $request->session()->flush();
            $request->session()->flash('error', 'User does not have an active profile');

            return redirect()->to('/login');
        }

        if ($userType == 'coordinator') {
            // Send to Coordinator Dashboard
            return redirect()->to('coordviewprofile');
        }

        if ($userType == 'pending') {
            // Send to Pending Founders to Status Inquiry Screen
            return redirect()->to('board/newchapterstatus');
        }

        if ($userType == 'board') {
            // Send to President or Member Profile Screen
            $user_bdPositionId = $user['user_bdPositionId'];
            $user_chapterId = $user['user_chapterId'];

            return redirect()->to('board/profile/' . $user_chapterId);

            // if ($user_bdPositionId == '1') {
            //     return redirect()->to('board/president/' . $user_chapterId);
            // } else {
            //     return redirect()->to('board/member/' . $user_chapterId);
            // }
        }

        if ($userType == 'outgoing') {
            // Send Outgoing Board Members to Financial Report ONLY
            $userName = $user['user_name'];
            $userEmail = $user['user_email'];
            $loggedInName = $user['user_name'];
            $chId = $user['user_outChapterId'];

            $baseQuery = $this->baseBoardController->getChapterDetails($chId);
            $chDetails = $baseQuery['chDetails'];
            $chActiveId = $baseQuery['chActiveId'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            // $submitted = $baseQuery['submitted'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $awards = $baseQuery['awards'];
            $allAwards = $baseQuery['allAwards'];

            $resources = Resources::with('resourceCategory')->get();
            $resourceCategories = ResourceCategory::all();

            $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'chDetails' => $chDetails, 'userType' => $userType,
                'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
                'chActiveId' => $chActiveId, 'resourceCategories' => $resourceCategories, 'allAwards' => $allAwards
            ];

            return view('boards.financial')->with($data);
        }

        if ($userType == 'disbanded') {
            // Send Disbanded Chapter Board Members to Disbanded Checklist and Financial Report
            $user_disChapterId = $user['user_disChapterId'];

            return redirect()->to('board/disbandchecklist/' . $user_disChapterId);

        } else {
            Auth::logout(); // logout non-user
            $request->session()->flush();

            return redirect()->to('/login');
        }
    }
}
