<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\Documents;
use App\Models\DocumentsEOY;
use App\Models\FolderRecord;
use App\Models\GoogleDrive;
use App\Models\Resources;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

const client_id = 'YOUR_CLIENT_ID';
const client_secret = 'YOUR_CLIENT_SECRET';

class GoogleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
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
    public function uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)
    {
        $client = new Client;
        $accessToken = $this->token();

        $fileMetadata = [
            // 'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'name' => $filename,
            'parents' => [$sharedDriveId],
            // 'mimeType' => $file->getMimeType(),
            'mimeType' => $mimetype,

        ];

        $metadataJson = json_encode($fileMetadata);
        // $fileContent = file_get_contents($file->getPathname());
        $fileContent = $filecontent;
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

        if ($response->getStatusCode() == 200) { // Check for a successful status code
            return $jsonResponse['id'];  // Return just the ID string instead of an array
        }

        return null; // Return null if upload fails
    }

    /**
     * Upload to EOY Google Drive -- To create folder/sub folder system.
     */
    public function uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)
    {
        $client = new Client;
        $accessToken = $this->token();

        $chapterFolderId = $this->createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId);

        $fileMetadata = [
            // 'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'name' => $filename,
            // 'mimeType' => $file->getMimeType(),
            'mimeType' => $mimetype,
            'parents' => [$chapterFolderId],
            'driveId' => $sharedDriveId,
        ];

        // $fileContent = file_get_contents($file->getPathname());
        $fileContent = $filecontent;
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

        if ($response->getStatusCode() == 200) {
            return $jsonResponse['id'];  // Return just the ID string instead of an array
        }

        return null; // Return null if upload fails
    }

    /**
     *  Save Chapter EIN Letter
     */
    public function storeEIN(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $chapter = Chapters::with('documentsEOY', 'state')->find($id);
            $ein = $chapter->ein;
            $chapterName = $chapter->name;
            $state = $chapter->state->state_short_name;
            $name = $ein.'_'.$chapterName.'_'.$state;

            $googleDrive = GoogleDrive::first();
            $einDrive = $googleDrive->ein_letter_uploads;
            $sharedDriveId = $einDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
                $existingDocRecord = Documents::where('chapter_id', $id)->first();
                if ($existingDocRecord) {
                    $existingDocRecord->ein_letter_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['ein_letter_path'] = $file_id;
                    $newDocData['ein_letter'] = '1';
                    Documents::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'EIN Letter uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // LOG THE ACTUAL ERROR
            Log::error('EIN upload error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Save Chapter Resource Items
     */
    public function storeResources(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $googleDrive = GoogleDrive::first();
            $resourcesDrive = $googleDrive->resources_uploads;
            $sharedDriveId = $resourcesDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $name = Str::ascii($file->getClientOriginalName());
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
                $existingDocRecord = Resources::find($id);
                if ($existingDocRecord) {
                    $existingDocRecord->file_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['file_path'] = $file_id;
                    Resources::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'File uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Save Coordinator Toolkit Items
     */
    public function storeToolkit(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $googleDrive = GoogleDrive::first();
            $resourcesDrive = $googleDrive->resources_uploads;
            $sharedDriveId = $resourcesDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $name = Str::ascii($file->getClientOriginalName());
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId)) {
                $existingDocRecord = Resources::find($id);
                if ($existingDocRecord) {
                    $existingDocRecord->file_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['file_path'] = $file_id;
                    Resources::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'File uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Save Roster for EOY Report Attachments
     */
    public function storeRoster(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $chapter = Chapters::with('documentsEOY', 'state')->find($id);
            $conf = $chapter->conference_id;
            $state = $chapter->state->state_short_name;
            $chapterName = $chapter->name;
            $name = $state.'_'.$chapterName.'_Roster';

            $googleDrive = GoogleDrive::first();
            $eoyDrive = $googleDrive->eoy_uploads;
            $year = $googleDrive->eoy_uploads_year;
            $sharedDriveId = $eoyDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)) {
                $existingDocRecord = DocumentsEOY::where('chapter_id', $id)->first();
                if ($existingDocRecord) {
                    $existingDocRecord->roster_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['roster_path'] = $file_id;
                    DocumentsEOY::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Roster uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Save 990N Confirmation for EOY Report Attachments
     */
    public function store990N(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $chapter = Chapters::with('documentsEOY', 'state')->find($id);
            $conf = $chapter->conference_id;
            $state = $chapter->state->state_short_name;
            $chapterName = $chapter->name;
            $name = $state.'_'.$chapterName.'_990N';

            $googleDrive = GoogleDrive::first();
            $eoyDrive = $googleDrive->eoy_uploads;
            $year = $googleDrive->eoy_uploads_year;
            $sharedDriveId = $eoyDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)) {
                $existingDocRecord = DocumentsEOY::where('chapter_id', $id)->first();
                if ($existingDocRecord) {
                    $existingDocRecord->irs_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['irs_path'] = $file_id;
                    DocumentsEOY::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => '990N Confirmation uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Save BankStatement for EOY Report Attachments
     */
    public function storeStatement1(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $chapter = Chapters::with('documentsEOY', 'state')->find($id);
            $conf = $chapter->conference_id;
            $state = $chapter->state->state_short_name;
            $chapterName = $chapter->name;
            $name = $state.'_'.$chapterName.'_Statement';

            $googleDrive = GoogleDrive::first();
            $eoyDrive = $googleDrive->eoy_uploads;
            $year = $googleDrive->eoy_uploads_year;
            $sharedDriveId = $eoyDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)) {
                $existingDocRecord = DocumentsEOY::where('chapter_id', $id)->first();
                if ($existingDocRecord) {
                    $existingDocRecord->statement_1_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['statement_1_path'] = $file_id;
                    DocumentsEOY::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Statement uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Save Additional Bank Statement for EOY Report Attachments
     */
    public function storeStatement2(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $chapter = Chapters::with('documentsEOY', 'state')->find($id);
            $conf = $chapter->conference_id;
            $state = $chapter->state->state_short_name;
            $chapterName = $chapter->name;
            $name = $state.'_'.$chapterName.'_Statement_2';

            $googleDrive = GoogleDrive::first();
            $eoyDrive = $googleDrive->eoy_uploads;
            $year = $googleDrive->eoy_uploads_year;
            $sharedDriveId = $eoyDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)) {
                $existingDocRecord = DocumentsEOY::where('chapter_id', $id)->first();
                if ($existingDocRecord) {
                    $existingDocRecord->statement_2_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['statement_2_path'] = $file_id;
                    DocumentsEOY::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Additional Statement uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    public function storeAward(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'file' => 'required|file',
            ]);

            $chapter = Chapters::with('documentsEOY', 'state')->find($id);
            $conf = $chapter->conference_id;
            $state = $chapter->state->state_short_name;
            $chapterName = $chapter->name;
            $name = $state.'_'.$chapterName.'_Award';

            $googleDrive = GoogleDrive::first();
            $eoyDrive = $googleDrive->eoy_uploads;
            $year = $googleDrive->eoy_uploads_year;
            $sharedDriveId = $eoyDrive;  // Shared Drive -> EOY Uploads

            $file = $request->file('file');
            $filename = Str::ascii($name.'.'.$file->getClientOriginalExtension());
            $mimetype = $file->getMimeType();
            $filecontent = file_get_contents($file->getPathname());

            if ($file_id = $this->uploadToEOYGoogleDrive($filename, $mimetype, $filecontent, $sharedDriveId, $year, $conf, $state, $chapterName)) {
                $existingDocRecord = DocumentsEOY::where('chapter_id', $id)->first();
                if ($existingDocRecord) {
                    $existingDocRecord->award_path = $file_id;
                    $existingDocRecord->save();
                } else {
                    Log::error("Expected document record for chapter_id {$id} not found");
                    $newDocData = ['chapter_id' => $id];
                    $newDocData['award_path'] = $file_id;
                    DocumentsEOY::create($newDocData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Award File uploaded successfully.',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload file.',
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     *  Create Folder Structure for EOY Report Attachments
     */
    public function createFolderIfNotExists($year, $conf, $state, $chapterName, $accessToken, $sharedDriveId)
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

            // Log::info('Creating year folder with data: '.json_encode($folderMetadata));

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
                'driveId' => $sharedDriveId,
                'mimeType' => 'application/vnd.google-apps.folder',
            ];

            // Log::info('Creating conference folder with data: '.json_encode($folderMetadata));

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

            // Log::info('Creating state folder with data: '.json_encode($folderMetadata));

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

            // Log::info('Creating chapter folder with data: '.json_encode($folderMetadata));

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
