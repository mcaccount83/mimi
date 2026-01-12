<?php

namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use App\Models\AdminRole;
use App\Models\User;
use App\Models\UserStatus;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BaseUserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
    }

    /**
     * Load Logged in User Information
     */
    public function loadUserInformation(Request $request)
    {
        return $this->getUserDetailsById($request->user()->id);
    }

    /**
     * Load ANY user by ID
     */
    public function getUserDetailsById($id)  // Fixed typo: Deteails -> Details
    {
        $userDetails = User::with(['board', 'boardPending', 'boardDisbanded', 'boardOutgoing',
            'board.position', 'boardPending.position', 'boardDisbanded.position', 'boardOutgoing.position'])->find($id);

        $userId = $userDetails->id;
        $userTypeId = $userDetails->type_id;
        $userName = $userDetails->first_name.' '.$userDetails->last_name;
        $updatedId = $userId;

        // Initialize variables
        $bdDetails = null;
        $bdChapterId = null;
        $bdPosition = null;

        if ($userTypeId == UserTypeEnum::BOARD) {
            $bdDetails = $userDetails->board;
            $bdChapterId = $userDetails->board->chapter_id;
            $bdPosition = $userDetails->board->position->position;
        }
        if ($userTypeId == UserTypeEnum::DISBANDED) {
            $bdDetails = $userDetails->boardDisbanded;
            $bdChapterId = $userDetails->boardDisbanded->chapter_id;
            $bdPosition = $userDetails->boardDisbanded->position->position;
        }
        if ($userTypeId == UserTypeEnum::PENDING) {
            $bdDetails = $userDetails->boardPending;
            $bdChapterId = $userDetails->boardPending->chapter_id;
            $bdPosition = $userDetails->boardPending->position->position;
        }
        if ($userTypeId == UserTypeEnum::OUTGOING) {
            $bdDetails = $userDetails->boardOutgoing;
            $bdChapterId = $userDetails->boardOutgoing->chapter_id;
            $bdPosition = $userDetails->boardOutgoing->position->position;
        }

        $AllUserStatus = UserStatus::all();
        $AllUserType = UserType::all();
        $AllAdminRole = AdminRole::all();

        return ['userDetails' => $userDetails, 'userId' => $userId, 'userTypeId' => $userTypeId, 'userName' => $userName,
            'bdDetails' => $bdDetails, 'bdChapterId' => $bdChapterId, 'AllUserStatus' => $AllUserStatus, 'AllAdminRole' => $AllAdminRole,
            'AllUserType' => $AllUserType, 'bdPosition' => $bdPosition, 'updatedId' => $updatedId];
    }
}
