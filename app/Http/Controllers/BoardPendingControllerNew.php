<?php

namespace App\Http\Controllers;

use App\Services\PositionConditionsService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class BoardPendingControllerNew extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseBoardController;

    protected $positionConditionsService;

    public function __construct(UserController $userController, BaseBoardController $baseBoardController, PositionConditionsService $positionConditionsService)
    {
        $this->userController = $userController;
        $this->baseBoardController = $baseBoardController;
        $this->positionConditionsService = $positionConditionsService;
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
    public function showNewChapterStatus(Request $request, $chId): View
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

        return view('boards-new.pending.newchapterstatus')->with($data);
    }
}
