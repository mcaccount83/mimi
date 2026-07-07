<?php

namespace App\Http\Controllers;

use App\Services\PositionConditionsService;
use App\Models\Boards;
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
        $userId = $user['userId'];
        $userTypeId = $user['userTypeId'];
        $userAdmin = $user['userAdmin'];

        $baseQuery = $this->baseBoardController->getChapterDetails($chId);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];

        $allStates = $baseQuery['allStates'];
        $allCountries = $baseQuery['allCountries'];

        $ownBoardDetails = Boards::with(['state', 'country', 'user'])
            ->where('chapter_id', $chId)
            ->where('user_id', $userId)
            ->first();

        $PresDetails = $baseQuery['PresDetails'];
        $bdData = $this->positionConditionsService->getViewAs($userTypeId, $userId, $PresDetails, $ownBoardDetails);
        $bdPositionId = $bdData['bdPositionId'];
        $borDetails = $bdData['bdDetails'];
        $bdTypeId = $bdData['bdTypeId'];

        $data = ['chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'allStates' => $allStates, 'userTypeId' => $userTypeId, 'userAdmin' => $userAdmin,
            'allCountries' => $allCountries,  'bdPositionId' => $bdPositionId, 'borDetails' => $borDetails, 'bdTypeId' => $bdTypeId, 'PresDetails' => $PresDetails,
        ];

        return view('boards.pending.newchapterstatus')->with($data);
    }
}
