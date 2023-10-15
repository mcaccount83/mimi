<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class GoogleController extends Controller
{
    protected $drive;

    public function __construct(Google_Client $client)
    {
        $client->setClientId(config('app.google_drive_client_id'));
        $client->setClientSecret(config('app.google_drive_client_secret'));
        $client->setRedirectUri(config('app.google_drive_redirect_uri'));
        $client->setScopes([Google_Service_Drive::DRIVE_FILE]);

        $this->drive = new Google_Service_Drive($client);
    }

    public function redirectToGoogle()
    {
        return Redirect::to($this->drive->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $code = $request->get('code');

        if (!empty($code)) {
            $token = $this->drive->fetchAccessTokenWithAuthCode($code);
            $request->session()->put('google_token', $token);
            // Redirect to a success page or perform other actions
        }

        // Handle the case where authorization failed
        // Redirect to an error page or perform other actions
    }

    public function uploadToDrive(Request $request)
    {
        $token = $request->session()->get('
