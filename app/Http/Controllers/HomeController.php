<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

#[Middleware('auth', except: ['logout'])]
class HomeController extends Controller
{
    public function __construct(
        protected UserController $userController,
        protected BaseBoardController $baseBoardController,
    ) {}

    /**
     * Home page for Coordinators & Board Members - logic for login redirect
     */
    public function index(Request $request): RedirectResponse
    {
        // Carry all flash data through the redirect chain
        // $request->session()->reflash();
        $request->session()->reflash();

        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userStatus = $user['userStatus'];

        if ($userStatus != 1) {
            Auth::logout();
            $request->session()->flush();
            $request->session()->flash('error', 'User does not have an active profile');

            return redirect()->to('/login');
        }

        if ($userTypeId == UserTypeEnum::COORD) {
            return redirect()->to('viewprofile');
        }

        if ($userTypeId == UserTypeEnum::PENDING) {
            return redirect()->to('board/newchapterstatus/'.$user['chapterId']);
        }

        if ($userTypeId == UserTypeEnum::BOARD) {
            return redirect()->to('board/chapterprofile/'.$user['chapterId']);
        }

        if ($userTypeId == UserTypeEnum::OUTGOING) {
            return redirect()->to('board/financialreport/'.$user['chapterId']);
        }

        if ($userTypeId == UserTypeEnum::DISBANDED) {
            return redirect()->to('board/disbandchecklist/'.$user['chapterId']);
        }

        // Default case - logout invalid users
        Auth::logout();
        $request->session()->flush();
        $request->session()->flash('error', 'User does not have an active profile');

        return redirect()->to('/login');
    }
}
