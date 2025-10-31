<?php

namespace App\Http\Controllers;

use App\Models\Boards;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserReportController extends Controller implements HasMiddleware
{
    protected $userController;

    public function __construct(UserController $userController)
    {
        $this->userController = $userController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * User Admins
     */
    public function showUserAdmin(): View
    {
        $adminList = User::where('is_admin', '!=', '0')
            ->where('is_active', '1')
            ->get();

        $countList = count($adminList);
        $data = ['countList' => $countList, 'adminList' => $adminList];

        return view('userreports.useradmin')->with($data);
    }

    /**
     * List of Duplicate Users
     */
    public function showDuplicate(): View
    {
        $userData = User::where('is_active', '=', '1')
            ->groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = User::where('is_active', '=', '1')
            ->whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('userreports.duplicateuser')->with($data);
    }

    /**
     *List of duplicate Board IDs
     */
    public function showDuplicateId(): View
    {
        $userData = Boards::groupBy('email')
            ->having(DB::raw('count(email)'), '>', 1)
            ->pluck('email');

        $userList = Boards::whereIn('email', $userData)
            ->get();

        $data = ['userList' => $userList];

        return view('userreports.duplicateboardid')->with($data);
    }

    /**
     * boards with no president
     */
    public function showNoPresident(): View
    {
        $PresId = DB::table('boards')
            ->where('board_position_id', '=', '1')
            ->pluck('chapter_id');

        $ChapterPres = DB::table('chapters')
            ->where('active_status', '=', '1')
            ->whereNotIn('id', $PresId)
            ->get();

        $data = ['ChapterPres' => $ChapterPres];

        return view('userreports.nopresident')->with($data);
    }

    /**
     * board member with inactive user
     */
    public function showNoActiveBoard(): View
    {
        $noActiveList = User::with(['board'])
            ->whereHas('board') // This ensures only users WITH a board relationship are included
            ->where('user_type', 'board')
            ->where('is_active', '0')
            ->get();

        $countList = count($noActiveList);
        $data = ['countList' => $countList, 'noActiveList' => $noActiveList];

        return view('userreports.noactiveboard')->with($data);
    }

    /**
     * Outgoing Board Members
     */
    public function showOutgoingBoard(): View
    {
        $outgoingList = User::with(['boardOutgoing', 'boardOutgoing.chapters'])
            ->where('user_type', 'outgoing')
            ->where('is_active', '1')
            ->get();

        $countList = count($outgoingList);
        $data = ['countList' => $countList, 'outgoingList' => $outgoingList];

        return view('userreports.outgoingboard')->with($data);
    }

    /**
     * Disbanded Board Members
     */
    public function showDisbandedBoard(): View
    {
        $disbandedList = User::with(['boardDisbanded', 'boardDisbanded.chapters'])
            ->where('user_type', 'disbanded')
            ->where('is_active', '1')
            ->get();

        $countList = count($disbandedList);
        $data = ['countList' => $countList, 'disbandedList' => $disbandedList];

        return view('userreports.disbandedboard')->with($data);
    }
}
