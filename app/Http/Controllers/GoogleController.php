<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store990NGoogleRequest;
use App\Http\Requests\StoreAwardGoogleRequest;
use App\Http\Requests\StoreEINGoogleRequest;
use App\Http\Requests\StoreResourcesGoogleRequest;
use App\Http\Requests\StoreRosterGoogleRequest;
use App\Http\Requests\StoreStatement1GoogleRequest;
use App\Http\Requests\StoreStatement2GoogleRequest;
use App\Http\Requests\StoreToolkitGoogleRequest;
use App\Models\Chapters;
use App\Models\GoogleDrive;
use App\Models\Documents;
use App\Models\FolderRecord;
use App\Models\Resources;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

const client_id = 'YOUR_CLIENT_ID';
const client_secret = 'YOUR_CLIENT_SECRET';

class GoogleController extends Controller
{
    public function __construct()
    {
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

    /**
     * Upload PDF to Google Drive
     */
    public function uploadToGoogleDrive($file, $name, $sharedDriveId)
    {
        $client = new Client;
        $accessToken = $this->token();

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getPathname());

        $fileContentBase64 = base64_encode($fileContent);

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
            return $jsonResponse['id'];  // Return just the ID string instead of an array
        }

        return null; // Return null if upload fails
    }

    /**
     * Upload to EOY Google Drive
     */
    // public function uploadToEOYGoogleDrivePDF($pdfPath, &$pdfFileId, $sharedDriveId, $year, $conf, $state, $chapterName)
    // {
    //     $googleClient = new Client;
    //     $accessToken = $this->token();

    //     $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

    //     $filename = basename($pdfPath);
    //     $fileMetadata = [
    //         'name' => $filename,
    //         'mimeType' => 'application/pdf',
    //         'parents' => [$chapterFolderId],
    //         'parents' => [$sharedDriveId],
    //     ];

    //     $fileContent = file_get_contents($pdfPath);
    //     $fileContentBase64 = base64_encode($fileContent);
    //     $metadataJson = json_encode($fileMetadata);

    //     $response = $googleClient->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
    //         'headers' => [
    //             'Authorization' => 'Bearer '.$accessToken,
    //             'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
    //         ],
    //         'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
    //     ]);

    //     if ($response->getStatusCode() === 200) {
    //         $responseData = json_decode($response->getBody()->getContents(), true);
    //         $pdfFileId = $responseData['id'] ?? null; // Extract file ID

    //         return true;
    //     }

    //     return false;
    // }

    /**
     * Upload to EOY Google Drive -- To create folder/sub folder system.
     */
    public function uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)
    {
        $client = new Client;
        $accessToken = $this->token();

        $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'mimeType' => $file->getMimeType(),
            'parents' => [$chapterFolderId],
            'driveId' => $sharedDriveId,
        ];

        $fileContent = file_get_contents($file->getPathname());
        $fileContentBase64 = base64_encode($fileContent);
        $metadataJson = json_encode($fileMetadata);

        $response = $client->request('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true', [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'multipart/related; boundary=foo_bar_baz',
            ],
            'body' => "--foo_bar_baz\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n{$metadataJson}\r\n--foo_bar_baz\r\nContent-Type: {$fileMetadata['mimeType']}\r\nContent-Transfer-Encoding: base64\r\n\r\n{$fileContentBase64}\r\n--foo_bar_baz--",
        ]);

        $bodyContents = $response->getBody()->getContents();
        $jsonResponse = json_decode($bodyContents, true);

        if ($response->getStatusCode() === 200) {
            return $jsonResponse['id'];  // Return just the ID string instead of an array
        }

        return null; // Return null if upload fails
    }

    /**
     *  Save Chapter EIN Letter
     */
    public function storeEIN(StoreEINGoogleRequest $request, $id): JsonResponse
    {
        $chapter = Chapters::with('documents', 'state')->find($id);
        $ein = $chapter->ein;
        $chapterName = $chapter->name;
        $state = $chapter->state->state_short_name;
        $name = $ein.'_'.$chapterName.'_'.$state;

        $googleDrive = GoogleDrive::first();
        $einDrive = $googleDrive->ein_letter_uploads;
        $sharedDriveId = $einDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');

        if ($file_id = $this->uploadToGoogleDrive($file, $name, $sharedDriveId)) {
            $existingDocRecord = Documents::where('chapter_id', $id)->first();
            $existingDocRecord->update([
                'ein_letter_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'EIN Letter uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    /**
     *  Save Chapter Resource Items
     */
    public function storeResources(StoreResourcesGoogleRequest $request, $id): JsonResponse
    {
        $googleDrive = GoogleDrive::first();
        $resourcesDrive = $googleDrive->resources_uploads;
        $sharedDriveId = $resourcesDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');
        $name = Str::ascii($file->getClientOriginalName());

        if ($file_id = $this->uploadToGoogleDrive($file, $name, $sharedDriveId)) {
            $existingDocRecord = Resources::find($id);
            $existingDocRecord->update([
                'file_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    /**
     *  Save Coordinator Toolkit Itesm
     */
    public function storeToolkit(StoreToolkitGoogleRequest $request, $id): JsonResponse
    {
        $googleDrive = GoogleDrive::first();
        $resourcesDrive = $googleDrive->resources_uploads;
        $sharedDriveId = $resourcesDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');
        $name = Str::ascii($file->getClientOriginalName());

        if ($file_id = $this->uploadToGoogleDrive($file, $name, $sharedDriveId)) {
            $existingDocRecord = Resources::find($id);
            $existingDocRecord->update([
                'file_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    /**
     *  Save Roster for EOY Report Attachments
     */
    public function storeRoster(StoreRosterGoogleRequest $request, $id): JsonResponse
    {
        $chapter = Chapters::with('documents', 'state')->find($id);
        $conf = $chapter->conference_id;
        $state = $chapter->state->state_short_name;
        $chapterName = $chapter->name;
        $name = $state.'_'.$chapterName.'_Roster';

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');

        if ($file_id = $this->uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $id)->first();
            $existingDocRecord->update([
                'roster_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Roster uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }


    /**
     *  Save 990N Confirmation for EOY Report Attachments
     */
    public function store990N(Store990NGoogleRequest $request, $id): JsonResponse
    {
        $chapter = Chapters::with('documents', 'state')->find($id);
        $conf = $chapter->conference_id;
        $state = $chapter->state->state_short_name;
        $chapterName = $chapter->name;
        $name = $state.'_'.$chapterName.'_990N';

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');

        if ($file_id = $this->uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $id)->first();
            $existingDocRecord->update([
                'irs_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => '990N Confirmation uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    /**
     *  Save BankStatement for EOY Report Attachments
     */
    public function storeStatement1(StoreStatement1GoogleRequest $request, $id): JsonResponse
    {
        $chapter = Chapters::with('documents', 'state')->find($id);
        $conf = $chapter->conference_id;
        $state = $chapter->state->state_short_name;
        $chapterName = $chapter->name;
        $name = $state.'_'.$chapterName.'_Statement';

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');

        if ($file_id = $this->uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $id)->first();
            $existingDocRecord->update([
                'statement_1_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Statement uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    /**
     *  Save Additional Bank Statement for EOY Report Attachments
     */
    public function storeStatement2(StoreStatement2GoogleRequest $request, $id): JsonResponse
    {
        $chapter = Chapters::with('documents', 'state')->find($id);
        $conf = $chapter->conference_id;
        $state = $chapter->state->state_short_name;
        $chapterName = $chapter->name;
        $name = $state.'_'.$chapterName.'_Statement_2';

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');

        if ($file_id = $this->uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $id)->first();
            $existingDocRecord->update([
                'statement_2_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Additional Statement uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    public function storeAward(StoreAwardGoogleRequest $request, $id): JsonResponse
    {
        $chapter = Chapters::with('documents', 'state')->find($id);
        $conf = $chapter->conference_id;
        $state = $chapter->state->state_short_name;
        $chapterName = $chapter->name;
        $name = $state.'_'.$chapterName.'_Award';

        $googleDrive = GoogleDrive::first();
        $eoyDrive = $googleDrive->eoy_uploads;
        $year = $googleDrive->eoy_uploads_year;
        $sharedDriveId = $eoyDrive;  //Shared Drive -> EOY Uploads

        $file = $request->file('file');

        if ($file_id = $this->uploadToEOYGoogleDrive($file, $name, $sharedDriveId, $year, $conf, $state, $chapterName)) {
            $existingDocRecord = Documents::where('chapter_id', $id)->first();
            $existingDocRecord->update([
                'award_path' => $file_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Award File uploaded successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to upload file.',
        ], 500);
    }

    /**
     *  Create Folder Structure for EOY Report Attachments
     */
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
                'state' => $state,
                'chapter_name' => $chapterName,
                'folder_id' => $folderId,
            ]);

            return $folderId;
        }
    }

}
