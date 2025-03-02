<?php

namespace App\Http\Controllers;

class BaseMailDataController extends Controller
{
    protected $userController;
    protected $baseChapterController;
    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    /**
     *  Get Basic Chapter Mail Data Information
     */
    public function getChapterBasicData($chDetails, $stateShortName)
    {
        return [
            'chapterName' => $chDetails->name,
            'chapterState' => $stateShortName,
            'chapterConf' => $chDetails->conference_id,
            'chapterEIN' => $chDetails->ein,
            'chapterEmail' => $chDetails->email,
        ];
    }

    public function getUserData($user)
    {
        return [
            'userName' => $user['user_name'],
            'userPosition' => $user['user_position'],
            'userConfName' => $user['user_conf_name'],
            'userConfDesc' => $user['user_conf_desc'],
            'userEmail' => $user['user_email'],
        ];
    }

    public function getPresData($PresDetails)
    {
        return [
            'presName' => $PresDetails->first_name.' '. $PresDetails->last_name,
            'presAddress' => $PresDetails->street_address,
            'presCity' => $PresDetails->city,
            'presState' => $PresDetails->state,
            'presZip' => $PresDetails->zip,
            'presEmail' => $PresDetails->email,
        ];
    }

    public function getPCData($pcDetails)
    {
        return [
            'pcEmail' => $pcDetails->email,
            'pcName' => $pcDetails->first_name.' '.$pcDetails->last_name,
        ];
    }



}
