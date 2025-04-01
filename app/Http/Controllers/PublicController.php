<?php

namespace App\Http\Controllers;

use App\Models\Resources;
use App\Models\ResourceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Models\GoogleDrive;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;


class PublicController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth')->except('logout');
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

            $data = ['resources' => $resources, 'resourceCategories' => $resourceCategories,];

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
            $client = new Client();

            // Use the Google Drive API URL format for direct download
            $googleDriveUrl = "https://drive.google.com/uc?export=download&id={$fileId}";

            // Fetch the file from Google Drive
            $response = $client->get($googleDriveUrl, [
                'stream' => true,
                'timeout' => 30,
                'connect_timeout' => 30
            ]);

            // Get the content type from the response
            $contentType = $response->getHeaderLine('Content-Type');

            // Handle different content types appropriately
            if (strpos($contentType, 'text/html') !== false) {
                // Google sometimes returns HTML for large files with a download prompt
                return redirect("https://drive.google.com/file/d/{$fileId}/view");
            }

            // Stream the file back to the client
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
                    'Cache-Control' => 'public, max-age=3600'
                ]
            );

        } catch (GuzzleException $e) {
            return response()->json([
                'error' => 'Failed to fetch file from Google Drive',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
