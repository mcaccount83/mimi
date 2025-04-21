<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use App\Models\Resources;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

const client_id = 'YOUR_CLIENT_ID';
const client_secret = 'YOUR_CLIENT_SECRET';

class PublicController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth')->except('logout');
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

    public function chapterLinks(): View
    {
        $international = DB::table('chapters')
            ->select('chapters.*', 'state.state_short_name', 'state.state_long_name')
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->where('state_id', '=', '52')
            ->where('is_active', '1')
            ->where('name', 'not like', '%test%')
            ->orderBy('name')
            ->get();

        $chapters = DB::table('chapters')
            ->select('chapters.*', 'state.state_short_name', 'state.state_long_name')
            ->join('state', 'chapters.state_id', '=', 'state.id')
            ->where('chapters.state_id', '<>', 52)
            ->where('is_active', '1')
            ->where('name', 'not like', '%test%')
            ->orderBy('state_id')
            ->orderBy('name')
            ->get();

        // Preprocess website URLs
        foreach ($chapters as $chapter) {
            if (! Str::startsWith($chapter->website_url, ['http://', 'https://'])) {
                $chapter->website_url = 'https://'.$chapter->website_url;
            }
        }

        return view('public.chapterlinks', ['chapters' => $chapters, 'international' => $international]);
    }

    /**
     * Show the Chapter Resources Page
     */
    public function chapterResources(): View
    {

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        $data = ['resources' => $resources, 'resourceCategories' => $resourceCategories];

        return view('public.resources')->with($data);

    }

    /**
     * Show the PDF viewer page
     */
    public function showPdf(Request $request)
    {
        $fileId = $request->query('id');

        if (empty($fileId)) {
            return abort(404, 'No file ID provided.');
        }

        return view('public.pdf-viewer', ['fileId' => $fileId]);
    }

    /**
     * Proxy for Google Drive files to avoid CORS issues
     */
    public function proxyGoogleDriveFile(Request $request)
{
    $fileId = $request->query('id');

    if (empty($fileId)) {
        return abort(404, 'File ID is required');
    }

    try {
        // Use your existing token method that's already working for uploads
        $accessToken = $this->token();

        $client = new Client();

        // Use the Google Drive API directly with your auth token
        $response = $client->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&supportsAllDrives=true", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'stream' => true,
            'timeout' => 30,
        ]);

        // Get content type from response headers
        $contentType = $response->getHeaderLine('Content-Type');

        // Stream the response back to the client
        return response()->stream(
            function () use ($response) {
                $body = $response->getBody();
                while (!$body->eof()) {
                    echo $body->read(1024);
                }
            },
            200,
            [
                'Content-Type' => $contentType ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="document.pdf"',
                'Cache-Control' => 'no-cache',
            ]
        );

    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Google Drive API error', [
            'message' => $e->getMessage(),
            'file_id' => $fileId,
        ]);

        return response()->json([
            'error' => 'Failed to fetch file from Google Drive',
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
