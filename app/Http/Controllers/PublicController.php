<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\GoogleDrive;
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

    public function chapterResources(): View
    {
        $resources = DB::table('resources')
            ->select('resources.*',
                DB::raw('CASE
                    WHEN category = 1 THEN "BYLAWS"
                    WHEN category = 2 THEN "FACT SHEETS"
                    WHEN category = 3 THEN "COPY READY MATERIAL"
                    WHEN category = 4 THEN "IDEAS AND INSPIRATION"
                    WHEN category = 5 THEN "CHAPTER RESOURCES"
                    WHEN category = 6 THEN "SAMPLE CHPATER FILES"
                    WHEN category = 7 THEN "END OF YEAR"
                    ELSE "Unknown"
                END as priority_word'))
            ->orderBy('name')
            ->get();

        return view('public.resources', ['resources' => $resources]);
    }

    // public function showPdf(Request $request)
    // {
    //     $fileId = $request->query('id');

    //     if (empty($fileId)) {
    //         return abort(404, 'No file ID provided.');
    //     }

    //     // You can use the fileId to fetch the actual file path from the database or storage.
    //     $filePath = $this->getFilePathFromId($fileId);

    //     if (!$filePath) {
    //         return abort(404, 'File not found.');
    //     }

    //     return view('public.pdf-viewer', ['filePath' => $filePath]);
    // }

    // private function getFilePathFromId($fileId)
    // {
    //     // Retrieve the actual file path from your storage based on the file ID.
    //     // Example: Check in your database or cloud storage for the file path.
    //     // This can be the path in your cloud storage, or Google Drive, etc.

    //     return 'path/to/your/file.pdf';  // Replace with actual logic
    // }

    /**
     * Show the PDF viewer page
     */
    public function showPdfViewer(Request $request)
    {
        $pdfUrl = $request->input('url', '');
        $googleDriveId = $request->input('gdrive_id', '');

        // Check if pdfUrl looks like a Google Drive ID and no explicit gdrive_id was provided
        if (empty($googleDriveId) && !empty($pdfUrl) && preg_match('/^[a-zA-Z0-9_-]{25,35}$/', $pdfUrl)) {
            $googleDriveId = $pdfUrl;
            $pdfUrl = ''; // Clear pdfUrl since we're treating it as a Drive ID
        }

        // If Google Drive ID is provided, get a token
        $googleDriveToken = null;
        if ($googleDriveId) {
            $googleDriveToken = $this->token();
        }

        return view('public.pdf-viewer', [
            'pdfUrl' => $pdfUrl,
            'googleDriveId' => $googleDriveId,
            'googleDriveToken' => $googleDriveToken
        ]);
    }


      /**
 * Proxy PDF content from Google Drive to avoid CORS issues
 */
public function proxyGoogleDrivePdf(Request $request)
{
    $fileId = $request->input('file_id');
    $download = $request->input('download', 0);

    if (!$fileId) {
        return response()->json(['error' => 'File ID is required'], 400);
    }

    try {
        // Get access token
        $accessToken = $this->token();

        // Use Guzzle client as per your existing setup
        $client = new Client();
        $response = $client->request('GET', "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        // Get file metadata
        $metadataResponse = $client->request('GET', "https://www.googleapis.com/drive/v3/files/{$fileId}?fields=name,mimeType", [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ]);

        $metadata = json_decode($metadataResponse->getBody(), true);
        $filename = $metadata['name'] ?? 'document.pdf';
        $mimeType = $metadata['mimeType'] ?? 'application/pdf';

        // Set content disposition based on download parameter
        $contentDisposition = $download ? 'attachment' : 'inline';

        // Return PDF content with appropriate headers
        return response($response->getBody())
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $contentDisposition . '; filename="' . $filename . '"');
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Get access token for Google Drive API
     * Using the existing token method from your application
     */
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
     * Alternative method to use GoogleDrive model if it has methods to fetch files
     * Uncomment and adapt based on your GoogleDrive model capabilities
     */
    /*
    public function proxyGoogleDrivePdfUsingModel(Request $request)
    {
        $fileId = $request->input('file_id');

        if (!$fileId) {
            return response()->json(['error' => 'File ID is required'], 400);
        }

        try {
            // Assuming GoogleDrive model has methods to fetch file content and metadata
            $googleDrive = new GoogleDrive();
            $fileContent = $googleDrive->getFileContent($fileId);
            $fileMetadata = $googleDrive->getFileMetadata($fileId);

            return response($fileContent)
                ->header('Content-Type', $fileMetadata['mimeType'] ?? 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . ($fileMetadata['name'] ?? 'document.pdf') . '"');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    */

}
