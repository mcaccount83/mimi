<?php

namespace App\Http\Controllers;

use App\Http\Requests\Store990NGoogleRequest;
use App\Http\Requests\StoreEIN3GoogleRequest;
use App\Http\Requests\StoreEINGoogleRequest;
use App\Http\Requests\StoreGoogleRequest;
use App\Http\Requests\StoreRosterGoogleRequest;
use App\Http\Requests\StoreStatement1GoogleRequest;
use App\Http\Requests\StoreStatement2GoogleRequest;
use App\Models\EinUploads;
use App\Models\EoyUploads;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

const client_id = 'YOUR_CLIENT_ID';
const client_secret = 'YOUR_CLIENT_SECRET';

class GoogleController extends Controller
{
    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth')->except('logout');
    }

    private function token()
    {
        $client_id = \config('services.google.client_id');
        $client_secret = \config('services.google.client_secret');
        $refresh_token = \config('services.google.refresh_token');
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        $accessToken = json_decode((string) $response->getBody(), true)['access_token'];

        return $accessToken;
    }

    public function storeEIN(StoreEINGoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();
        $validation = $request->validated();
        $name = $chapter->ein.'_'.$chapter->name.'_'.$chapter->state;
        $accessToken = $this->token();

        $fileMetadata = [
            'name' => $name.'.'.$request->file->getClientOriginalExtension(),
            'parents' => ['1iwap3d3feX2cYaODJrANEMnT1fjIDHD2'],
            'mimeType' => $request->file->getMimeType(),
        ];

        Log::info('File Metadata: ', ['file_metadata' => $fileMetadata]);

        $fileContent = file_get_contents($request->file->getRealPath());

        Log::info('File Content: ', ['file_content' => $fileContent]);

        $headers = [
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)
            ->attach(
                'file',
                $fileContent,
                $fileMetadata['name'],
                [
                    'Content-Type' => $fileMetadata['mimeType'],
                ]
            )
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart', [
                'metadata' => json_encode($fileMetadata),
            ]);

        Log::info('Google Drive API Response: ', $response->json());

        if ($response->successful()) {
            $file_id = $response->json()['id'];
            $existingRecord = EinUploads::where('chapter_id', $id)->first();
            if ($existingRecord) {
                $existingRecord->update([
                    'file_name' => $name,
                    'file_id' => $file_id,
                ]);
            } else {
                $uploadedfile = new EinUploads;
                $uploadedfile->chapter_id = $id;
                $uploadedfile->file_name = $name;
                $uploadedfile->file_id = $file_id;
                $uploadedfile->save();
            }

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeEIN3(StoreEIN3GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $validation = $request->validated();

        $name = $chapter->ein.'_'.$chapter->name.'_'.$chapter->state;

        $client = new Client();
        $client->setAccessToken($this->token());
        $driveService = new Drive($client);

        $fileMetadata = new DriveFile([
            'name' => $name,
            'parents' => ['1iwap3d3feX2cYaODJrANEMnT1fjIDHD2'],
        ]);

        $fileContent = file_get_contents($request->file->getRealPath());
        $createdFile = $driveService->files->create($fileMetadata, [
            'data' => $fileContent,
            'mimeType' => $request->file->getClientMimeType(),
            'uploadType' => 'multipart',
        ]);

        // If the file was created successfully, update database
        if (! empty($createdFile->id)) {
            $file_id = $createdFile->id;

            $existingRecord = EinUploads::where('chapter_id', $id)->first();

            if ($existingRecord) {
                $existingRecord->update([
                    'file_name' => $name,
                    'file_id' => $file_id,
                ]);
            } else {
                $uploadedfile = new EinUploads;
                $uploadedfile->chapter_id = $id;
                $uploadedfile->file_name = $name;
                $uploadedfile->file_id = $file_id;
                $uploadedfile->save();
            }

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeRoster(StoreRosterGoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $validation = $request->validated();

        $accessToken = $this->token();
        $parentFolderId = '1YYW1ct6oO1h9v5E7KdaeM-y8NVnTYeT7';  //this needs to change based on the YEAR of the EOY Reports
        $stateFolderName = $chapter->state;
        $chapterFolderName = $chapter->name;
        $name = $chapter->name.'_'.$chapter->state.'_Roster';
        $mime = $request->file->getClientMimeType();

        $stateFolderResponse = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $stateFolderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'uploadType' => 'resumable',
            'parents' => [$parentFolderId],
        ]);

        $stateFolderId = $stateFolderResponse->json()['id'];

        $chapterFolderResponse = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $chapterFolderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'uploadType' => 'resumable',
            'parents' => [$stateFolderId],
        ]);

        $chapterFolderId = $chapterFolderResponse->json()['id'];

        $fileResponse = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $name,
            'mimeType' => $mime,
            'uploadType' => 'resumable',
            'parents' => [$chapterFolderId],
        ]);

        if ($fileResponse->successful()) {

            $file_id = json_decode($fileResponse->body())->id;

            $uploadedfile = new EoyUploads;
            $uploadedfile->chapter_id = $id;
            $uploadedfile->file_name = $name;
            $uploadedfile->file_id = $file_id;
            $uploadedfile->save();
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    public function store990N(Store990NGoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $validation = $request->validated();

        $accessToken = $this->token();
        $name = $chapter->name.'_'.$chapter->state.'_990n';
        $mime = $request->file->getClientMimeType();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $name,
            'mimeType' => $mime,
            'uploadType' => 'resumable',
            'parents' => ['1czyKRfuAzWGOcc_wqROeQSefI5S1ssCX'],
        ]);

        if ($response->successful()) {

            $file_id = json_decode($response->body())->id;

            $uploadedfile = new EoyUploads;
            $uploadedfile->chapter_id = $id;
            $uploadedfile->file_name = $name;
            $uploadedfile->file_id = $file_id;
            $uploadedfile->save();
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    public function storeStatement1(StoreStatement1GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $validation = $request->validated();
        $accessToken = $this->token();

        $name = $chapter->name.', '.$chapter->state.'_Statement1';
        $mime = $request->file->getClientMimeType();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $name,
            'mimeType' => $mime,
            'uploadType' => 'resumable',
            'parents' => ['1czyKRfuAzWGOcc_wqROeQSefI5S1ssCX'],
        ]);

        if ($response->successful()) {

            $file_id = json_decode($response->body())->id;

            $uploadedfile = new EoyUploads;
            $uploadedfile->chapter_id = $id;
            $uploadedfile->file_name = $name;
            $uploadedfile->file_id = $file_id;
            $uploadedfile->save();
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    public function storeStatement2(StoreStatement2GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $validation = $request->validated();
        $accessToken = $this->token();

        $name = $chapter->name.', '.$chapter->state.'_Statement2';
        $mime = $request->file->getClientMimeType();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $name,
            'mimeType' => $mime,
            'uploadType' => 'resumable',
            'parents' => ['1czyKRfuAzWGOcc_wqROeQSefI5S1ssCX'],
        ]);

        if ($response->successful()) {

            $file_id = json_decode($response->body())->id;

            $uploadedfile = new EoyUploads;
            $uploadedfile->chapter_id = $id;
            $uploadedfile->file_name = $name;
            $uploadedfile->file_id = $file_id;
            $uploadedfile->save();
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    public function store(StoreGoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $validation = $request->validated();
        $accessToken = $this->token();
        $name = $chapter->ein.' | '.$chapter->name.', '.$chapter->state;
        $mime = $request->file->getClientMimeType();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
            'Content-Type' => 'Application/json',
        ])->post('https://www.googleapis.com/drive/v3/files', [
            'name' => $name,
            'mimeType' => $mime,
            'uploadType' => 'resumable',
        ])->throw();

        if ($response->successful()) {

            $file_id = json_decode($response->body())->id;

            $uploadedfile = new EoyUploads;
            $uploadedfile->chapter_id = $id;
            $uploadedfile->file_name = $name;
            $uploadedfile->file_id = $file_id;
            $uploadedfile->save();
        }

        return redirect()->back()->with('success', 'File uploaded successfully!');
    }

    public function show($id): View
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->get();

        $data = ['chapter' => $chapter, 'id' => $id];

        return view('files.googletest')->with($data);
    }
}
