<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAward1GoogleRequest;
use App\Http\Requests\StoreAward2GoogleRequest;
use App\Http\Requests\StoreAward3GoogleRequest;
use App\Http\Requests\StoreAward4GoogleRequest;
use App\Http\Requests\StoreAward5GoogleRequest;
use App\Http\Requests\Store990NGoogleRequest;
use App\Http\Requests\StoreEINGoogleRequest;
use App\Http\Requests\StoreRosterGoogleRequest;
use App\Http\Requests\StoreStatement1GoogleRequest;
use App\Http\Requests\StoreStatement2GoogleRequest;
use App\Http\Requests\StoreResourcesGoogleRequest;
use App\Models\Chapter;
use App\Models\FinancialReport;
use App\Models\Resources;
use GuzzleHttp\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
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

        $name = $chapter->ein.'_'.$chapter->name.'_'.$chapter->state;
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1JAYKfJoo4USrEwkBkRKqIV-2PwouPv-m';   //Shared Drive -> CC Resources->IRS/EIN -> EIN Letters

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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

        $name = $chapter->state.'_'.$chapter->name.'_Roster';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'roster_path' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function store990N(Store990NGoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_990N';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'file_irs_path' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeStatement1(StoreStatement1GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Statement';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'bank_statement_included_path' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeStatement2(StoreStatement2GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Statement2';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'bank_statement_2_included_path' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeAward1(StoreAward1GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Award1';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'award_1_files' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeAward2(StoreAward2GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Award2';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'award_2_files' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }


    public function storeAward3(StoreAward3GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Award3';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'award_3_files' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }


    public function storeAward4(StoreAward4GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Award4';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'award_4_files' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }


    public function storeAward5(StoreAward5GoogleRequest $request, $id): RedirectResponse
    {
        $chapter = DB::table('chapters as ch')
            ->select('ch.*', 'ch.ein as ein', 'ch.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'ch.state', '=', 'st.id')
            ->where('ch.is_active', '=', '1')
            ->where('ch.id', '=', $id)
            ->first();

        $name = $chapter->state.'_'.$chapter->name.'_Award5';
        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '1Grx5na3UIpm0wq6AGBrK6tmNnqybLbvd';   //Shared Drive -> EOY Uploads -> 2024

        $fileMetadata = [
            'name' => Str::ascii($name.'.'.$file->getClientOriginalExtension()),
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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
            $existingRecord = FinancialReport::where('chapter_id', $id)->first();

            $existingRecord->update([
                'award_5_files' => $path,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }
    }

    public function storeResources(StoreResourcesGoogleRequest $request, $id): RedirectResponse
    {
        $resource = Resources::findOrFail($id);

        $accessToken = $this->token();

        $file = $request->file('file');
        $sharedDriveId = '17YQBX5T67g0azczV844XyUJH1TM5RAcA';   //Shared Drive -> CC Resources -> Resources - Uploaded Online

        $fileMetadata = [
            'name' => Str::ascii($file->getClientOriginalName()), // Use getClientOriginalName() to get the file name
            'parents' => [$sharedDriveId],
            'mimeType' => $file->getMimeType(),
        ];

        $metadataJson = json_encode($fileMetadata);
        $fileContent = file_get_contents($file->getRealPath());
        $fileContentBase64 = base64_encode($fileContent);

        $client = new Client();

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

            $resource->file_path = $path;
            $resource->save();

            return redirect()->back()->with('success', 'File uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File failed to upload');
        }

    }

}
