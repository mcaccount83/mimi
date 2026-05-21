<?php

namespace App\Http\Controllers;

use App\Services\PositionConditionsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\View\View;

#[Middleware('auth', except: ['logout'])]
class BoardPendingController extends Controller
{
    public function __construct(
        protected UserController $userController,
        protected BaseBoardController $baseBoardController,
        protected PositionConditionsService $positionConditionsService,
    ) {}

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
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $PresDetails);
        $bdPositionId = $bdData['bdPositionId'];
        $borDetails = $bdData['bdDetails'];
        $bdTypeId = $bdData['bdTypeId'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin,
            'allCountries' => $allCountries,  'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'bdTypeId' => $bdTypeId, 'PresDetails' => $PresDetails,
        ];

        return view('boards.pending.newchapterstatus')->with($data);
    }
}
