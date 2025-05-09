<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class BoardPendingController extends Controller implements HasMiddleware
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
     * View New Application Status for Pending Board Members
     */
    public function showNewChapterStatus(Request $request, $chapter_id = null): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userType = $user['userType'];
        $userAdmin = $user['userAdmin'];

        if ($userAdmin == 1 && isset($chapter_id)) {
            $chId = $chapter_id;
        } else {
            $chId = $user['user_pendChapterId'];
        }

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $allStates = $baseQuery['allStates'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'userType' => $userType, 'userAdmin' => $userAdmin,
        ];

        return view('boards.newchapterstatus')->with($data);
    }
}
