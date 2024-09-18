<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store990NGoogleRequest;
use App\Http\Requests\StoreAward1GoogleRequest;
use App\Http\Requests\StoreAward2GoogleRequest;
use App\Http\Requests\StoreAward3GoogleRequest;
use App\Http\Requests\StoreAward4GoogleRequest;
use App\Http\Requests\StoreAward5GoogleRequest;
use App\Http\Requests\StoreEINGoogleRequest;
use App\Http\Requests\StoreResourcesGoogleRequest;
use App\Http\Requests\StoreRosterGoogleRequest;
use App\Http\Requests\StoreStatement1GoogleRequest;
use App\Http\Requests\StoreStatement2GoogleRequest;
use App\Http\Requests\StoreToolkitGoogleRequest;
use App\Models\Chapter;
use App\Models\FinancialReport;
use App\Models\FolderRecord;
use App\Models\Resources;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

const client_id = 'YOUR_CLIENT_ID';
const client_secret = 'YOUR_CLIENT_SECRET';

class GoogleController extends Controller
{
    public function __construct()
    {
        //$this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    private function token()
    {
        $client_id = config('services.google.client_id');
        $client_secret = config('services.google.client_secret');
        $refresh_token = config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
            'scope' => 'https://www.googleapis.com/auth/drive', // Add the necessary scope for Shared Drive access
        ]);

        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        return $accessToken;
    }

    public function storeEIN(StoreEINGoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.ein', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $name = $chapter->ein.'_'.$chapter->name.'_'.$chapter->state;
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
                ->select('google_drive.ein_letter_uploads as ein_letter_uploads')
                ->get();
            $einDrive = $googleDrive[0]->ein_letter_uploads;

            $file = $request->file('file');
            // $sharedDriveId = '1JAYKfJoo4USrEwkBkRKqIV-2PwouPv-m';
            $sharedDriveId = $einDrive;   //Shared Drive -> CC Resources->IRS/EIN -> EIN Letters

            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
                'parents' => [$sharedDriveId],
                'mimeType' => $file->getMimeType(),
            ];

            $metadataJson = json_encode($fileMetadata);
            // $fileContent = file_get_contents($file->getRealPath());
            $fileContent = file_get_contents($file->getPathname());

            $fileContentBase64 = base64_encode($fileContent);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $path = 'https://drive.google.com/file/d/'.$file_id.'/view?usp=drive_link';
                $existingRecord = Chapter::where('id', $id)->first();

                $existingRecord->update([
                    'ein_letter_path' => $path,
                    'ein_letter' => '1',
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    private function createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId)
    {
         // Check if the year folder exists, create it if not
         $yearFolderId = $this->getOrCreateYearFolder($year, $accessToken, $sharedDriveId);

        // Check if the conference folder exists, create it if not
        $confFolderId = $this->getOrCreateConfFolder($year, $conf, $yearFolderId, $accessToken, $sharedDriveId);

        // Check if the state folder exists, create it if not
        $stateFolderId = $this->getOrCreateStateFolder($year, $conf, $state, $confFolderId, $accessToken, $sharedDriveId);

        // Check if the chapter folder exists, create it if not
        $chapterFolderId = $this->getOrCreateChapterFolder($year, $conf, $state, $chapterName, $stateFolderId, $accessToken, $sharedDriveId);

        return $chapterFolderId;
    }

    private function getOrCreateYearFolder($year, $accessToken, $sharedDriveId)
    {
        // Check if the year folder exists in the records
        $yearRecord = FolderRecord::where('year', $year)->first();

        if ($yearRecord) {
            // Year folder exists, return its ID
            return $yearRecord->folder_id;
        } else {
            // Year folder doesn't exist, create it
            $client = new Client;
            $folderMetadata = [
                'name' => "EOY $year",
                'parents' => [$sharedDriveId],
                'driveId' => $sharedDriveId,
                'mimeType' => 'application/vnd.google-apps.folder',
            ];
            $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $folderMetadata,
            ]);
            $folderId = json_decode($response->getBody()->getContents(), true)['id'];

            // Record the created folder ID for future reference
            FolderRecord::create([
                'year' => $year,
                'folder_id' => $folderId,
            ]);

            return $folderId;
        }
    }


    private function getOrCreateConfFolder($year, $conf, $yearFolderId, $accessToken, $sharedDriveId)
    {
        // Check if the conference folder exists in the records
        $confRecord = FolderRecord::where('conf', $conf)
            ->where('year', $year)
            ->first();

        if ($confRecord) {
            // Conference folder exists, return its ID
            return $confRecord->folder_id;
        } else {
            // Conference folder doesn't exist, create it
            $client = new Client;
            $folderMetadata = [
                'name' => "Conference $conf",
                'parents' => [$yearFolderId],
                // 'parents' => [$sharedDriveId],
                'driveId' => $sharedDriveId,
                'mimeType' => 'application/vnd.google-apps.folder',
            ];
            $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $folderMetadata,
            ]);
            $folderId = json_decode($response->getBody()->getContents(), true)['id'];

            // Record the created folder ID for future reference
            FolderRecord::create([
                'year' => $year,
                'conf' => $conf,
                'folder_id' => $folderId,
            ]);

            return $folderId;
        }
    }

    private function getOrCreateStateFolder($year, $conf, $state, $confFolderId, $accessToken, $sharedDriveId)
    {
        // Check if the state folder exists for the given year and conference
        $stateRecord = FolderRecord::where('state', $state)
                        ->where('year', $year)
                        ->where('conf', $conf)
                        ->first();

        if ($stateRecord) {
            // State folder exists, return its ID
            return $stateRecord->folder_id;
        } else {
            // State folder doesn't exist, create it
            $client = new Client;
            $folderMetadata = [
                'name' => $state,
                'parents' => [$confFolderId],
                'driveId' => $sharedDriveId,
                'mimeType' => 'application/vnd.google-apps.folder',
            ];
            $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $folderMetadata,
            ]);
            $folderId = json_decode($response->getBody()->getContents(), true)['id'];

            // Record the created folder ID for future reference
            FolderRecord::create([
                'year' => $year,
                'conf' => $conf,
                'state' => $state,
                'folder_id' => $folderId,
            ]);

            return $folderId;
        }
    }


    private function getOrCreateChapterFolder($year, $conf, $state, $chapterName, $stateFolderId, $accessToken, $sharedDriveId)
    {
        // Check if the chapter folder exists for the given year, conference, and state
        $chapterRecord = FolderRecord::where('chapter_name', $chapterName)
                        ->where('year', $year)
                        ->where('conf', $conf)
                        ->where('state', $state)
                        ->first();

        if ($chapterRecord) {
            // Chapter folder exists, return its ID
            return $chapterRecord->folder_id;
        } else {
            // Chapter folder doesn't exist, create it
            $client = new Client;
            $folderMetadata = [
                'name' => $chapterName,
                'parents' => [$stateFolderId],
                'driveId' => $sharedDriveId,
                'mimeType' => 'application/vnd.google-apps.folder',
            ];
            $response = $client->request('POST', 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $folderMetadata,
            ]);
            $folderId = json_decode($response->getBody()->getContents(), true)['id'];

            // Record the created folder ID for future reference
            FolderRecord::create([
                'year' => $year,
                'conf' => $conf,
                'state' => $state,
                'chapter_name' => $chapterName,
                'folder_id' => $folderId,
            ]);

            return $folderId;
        }
    }


    public function storeRoster(StoreRosterGoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $state.'_'.$chapterName.'_Roster';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
                ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
                ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'roster_path' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function store990N(Store990NGoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_990N';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'file_irs_path' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeStatement1(StoreStatement1GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Statement';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'bank_statement_included_path' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeStatement2(StoreStatement2GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Statement2';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'bank_statement_2_included_path' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeAward1(StoreAward1GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Award1';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'award_1_files' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeAward2(StoreAward2GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Award2';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'award_2_files' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeAward3(StoreAward3GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Award3';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'award_3_files' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeAward4(StoreAward4GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Award4';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'award_4_files' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeAward5(StoreAward5GoogleRequest $request, $id)
    {
        try{
            $chapter = DB::table('chapters as ch')
                ->select('ch.conference', 'ch.state', 'ch.name', 'st.state_short_name as state')
                ->leftJoin('state as st', 'ch.state', '=', 'st.id')
                ->where('ch.is_active', '=', '1')
                ->where('ch.id', '=', $id)
                ->first();

            $conf = $chapter->conference;
            $state = $chapter->state;
            $chapterName = $chapter->name;
            $name = $chapter->state.'_'.$chapter->name.'_Award5';
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
            ->select('google_drive.eoy_uploads as eoy_uploads', 'google_drive.eoy_uploads_year as eoy_uploads_year')
            ->get();

            $eoyDrive = $googleDrive[0]->eoy_uploads;
            $year = $googleDrive[0]->eoy_uploads_year;

            // $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';
            $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

            // Create conference folder if it doesn't exist in the shared drive
            $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

            // Set parent IDs for the file
            $fileMetadata = [
                'name' => Str::ascii($name.'.'.$request->file('file')->getClientOriginalExtension()),
                'mimeType' => $request->file('file')->getMimeType(),
                'parents' => [$chapterFolderId],
                'driveId' => $sharedDriveId, // Specify the Shared Drive ID
            ];

            // Upload the file
            $fileContent = file_get_contents($request->file('file')->getPathname());
            $fileContentBase64 = base64_encode($fileContent);
            $metadataJson = json_encode($fileMetadata);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];
                $existingRecord = FinancialReport::where('chapter_id', $id)->first();

                $existingRecord->update([
                    'award_5_files' => $file_id,
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeResources(StoreResourcesGoogleRequest $request, $id)
    {
        try{
            $resource = Resources::findOrFail($id);

            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
                ->select('google_drive.resources_uploads as resources_uploads')
                ->get();
            $resourcesDrive = $googleDrive[0]->resources_uploads;

            $file = $request->file('file');
            // $sharedDriveId = '17YQBX5T67g0azczV844XyUJH1TM5RAcA';
            $sharedDriveId = $resourcesDrive;   //Shared Drive -> CC Resources -> Resources - Uploaded Online

            $fileMetadata = [
                'name' => Str::ascii($file->getClientOriginalName()), // Use getClientOriginalName() to get the file name
                'parents' => [$sharedDriveId],
                'mimeType' => $file->getMimeType(),
            ];

            $metadataJson = json_encode($fileMetadata);
            $fileContent = file_get_contents($file->getPathname());
            $fileContentBase64 = base64_encode($fileContent);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];

                $resource->file_path = "https://drive.google.com/uc?export=download&id=$file_id";
                $resource->save();

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }

    public function storeToolkit(StoreToolkitGoogleRequest $request, $id)
    {
        try{
            $resource = Resources::findOrFail($id);
            $accessToken = $this->token();

            $googleDrive = DB::table('google_drive')
                ->select('google_drive.resources_uploads as resources_uploads')
                ->get();
            $resourcesDrive = $googleDrive[0]->resources_uploads;

            $file = $request->file('file');
            // $sharedDriveId = '17YQBX5T67g0azczV844XyUJH1TM5RAcA';
            $sharedDriveId = $resourcesDrive;   //Shared Drive -> CC Resources -> Resources - Uploaded Online

            $fileMetadata = [
                'name' => Str::ascii($file->getClientOriginalName()), // Use getClientOriginalName() to get the file name
                'parents' => [$sharedDriveId],
                'mimeType' => $file->getMimeType(),
            ];

            $metadataJson = json_encode($fileMetadata);
            $fileContent = file_get_contents($file->getPathname());
            $fileContentBase64 = base64_encode($fileContent);

            $client = new Client;

            $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
                ],
                'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
            ]);

            $bodyContents = $response->getBody()->getContents();
            $jsonResponse = json_decode($bodyContents, true);

            if ($response->getStatusCode() === 200) { // Check for a successful status code
                $file_id = $jsonResponse['id'];

                $resource->file_path = "https://drive.google.com/uc?export=download&id=$file_id";
                $resource->save();

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            } else {
                return response()->json(['message' => 'File failed to upload'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('File upload error: '.$e->getMessage());
            return response()->json(['message' => 'An error occurred during the upload'], 500);
        }
    }
}
