<?php

namespace App\Http\Controllers;

use App\Models\Resources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    protected $userController;

    protected $baseBoardController;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController)
    {
        $this->middleware('auth')->except('logout');
        $this->userController = $userController;
        $this->baseBoardController = $baseBoardController;
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

            return redirect()->to('/login');
        }

        if ($userType == 'coordinator') {
            // Send to Coordinator Dashboard
            return redirect()->to('coordviewprofile');
        }

        if ($userType == 'board') {
            // Send to President or Member Profile Screen
            $user_bdPositionId = $user['user_bdPositionId'];

            if ($user_bdPositionId == '1') {
                return redirect()->to('board/president');
            } else {
                return redirect()->to('board/member');
            }
        }

        if ($userType == 'outgoing') {
            // Send Outgoing Board Members to Financial Report ONLY
            $userName = $user['user_name'];
            $userEmail = $user['user_email'];
            $loggedInName = $user['user_name'];
            $chId = $user['user_OutchapterId'];

            $baseQuery = $this->baseBoardController->getChapterDetails($chId);
            $chDetails = $baseQuery['chDetails'];
            $stateShortName = $baseQuery['stateShortName'];
            $chDocuments = $baseQuery['chDocuments'];
            $submitted = $baseQuery['submitted'];
            $chFinancialReport = $baseQuery['chFinancialReport'];
            $awards = $baseQuery['awards'];
            $allAwards = $baseQuery['allAwards'];

            $resources = Resources::with('categoryName')->get();

            $data = ['chFinancialReport' => $chFinancialReport, 'loggedInName' => $loggedInName, 'submitted' => $submitted, 'chDetails' => $chDetails, 'userType' => $userType,
                'userName' => $userName, 'userEmail' => $userEmail, 'resources' => $resources, 'chDocuments' => $chDocuments, 'stateShortName' => $stateShortName,
                'awards' => $awards, 'allAwards' => $allAwards,
            ];

            return view('boards.financial')->with($data);

        } else {
            Auth::logout(); // logout non-user
            $request->session()->flush();

            return redirect()->to('/login');
        }
    }
}
