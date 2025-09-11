<?php

namespace App\Http\Controllers;

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

        // Reflash session data before redirecting again
        session()->reflash();

        if ($userStatus != 1) {
            Auth::logout();  // logout inactive user
            $request->session()->flush();
            $request->session()->flash('error', 'User does not have an active profile');

            return redirect()->to('/login');
        }

        if ($userType == 'coordinator') {
            // Send to Coordinator Dashboard
            $user_coorId = $user['user_coorId'];

            return redirect()->to('coordviewprofile');
        }

        if ($userType == 'pending') {
            // Send Pending Founders to Status Inquiry Screen
            $user_pendChapterId = $user['user_pendChapterId'];

            return redirect()->to('board/newchapterstatus');
        }

        if ($userType == 'board') {
            // Send Active Board Members to Board Profile Screen
            $user_chapterId = $user['user_chapterId'];

            return redirect()->to('board/profile/'.$user_chapterId);
        }

        if ($userType == 'outgoing') {
            // Send Outgoing Board Members to Financial Report ONLY
            $user_outChapterId = $user['user_outChapterId'];

            return redirect()->to('board/financialreport/'.$user_outChapterId);
        }

        if ($userType == 'disbanded') {
            // Send Disbanded Chapter Board Members to Disbanded Checklist and Financial Report
            $user_disChapterId = $user['user_disChapterId'];

            return redirect()->to('board/disbandchecklist/'.$user_disChapterId);

        } else {
            Auth::logout(); // logout non-user
            $request->session()->flush();
            $request->session()->flash('error', 'User does not have an active profile');

            return redirect()->to('/login');
        }
    }
}
