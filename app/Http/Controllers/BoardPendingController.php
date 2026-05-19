<?php

namespace App\Http\Controllers;

use App\Services\PositionConditionsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class BoardPendingController extends Controller implements HasMiddleware
{
    public function __construct(
        protected UserController $userController,
        protected BaseBoardController $baseBoardController,
        protected PositionConditionsService $positionConditionsService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
    }

    /**
     * View New Application Status for Pending Board Members
     */
    public function showNewChapterStatus(Request $request, int $chId): View
    {
        $user = $this->userController->loadUserInformation($request);
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $PresDetails = $baseQuery['PresDetails'];
        $bdData = $this->positionConditionsService->getViewAs($userTypeId,  $PresDetails);
        $bdPositionId = $bdData['bdPositionId'];
        $borDetails = $bdData['bdDetails'];
        $bdTypeId = $bdData['bdTypeId'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin,
        'allCountries' => $allCountries,  'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'bdTypeId' => $bdTypeId, 'PresDetails' => $PresDetails
        ];

        return view('boards.pending.newchapterstatus')->with($data);
    }
}
