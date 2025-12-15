<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller implements HasMiddleware
{
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
    public function index(Request $request): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userTypeId'];
        $userStatus = $user['userStatus'];

        // Keep specific flash data for one more request (if needed)
        // $request->session()->keep(['key1', 'key2']);

        if ($userStatus != 1) {
            Auth::logout();
            $request->session()->flush();
            $request->session()->flash('error', 'User does not have an active profile');

            return redirect()->to('/login');
        }

        if ($userType == UserTypeEnum::COORD) {
            return redirect()->to('viewprofile');
        }

        if ($userType == UserTypeEnum::PENDING) {
            return redirect()->to('board/newchapterstatus/'.$user['user_pendChapterId']);
        }

        if ($userType == UserTypeEnum::BOARD) {
            return redirect()->to('board/profile/'.$user['user_chapterId']);
        }

        if ($userType == UserTypeEnum::OUTGOING) {
            return redirect()->to('board/financialreport/'.$user['user_outChapterId']);
        }

        if ($userType == UserTypeEnum::DISBANDED) {
            return redirect()->to('board/disbandchecklist/'.$user['user_disChapterId']);
        }

        // Default case - logout invalid users
        Auth::logout();
        $request->session()->flush();
        $request->session()->flash('error', 'User does not have an active profile');

        return redirect()->to('/login');
    }
}
