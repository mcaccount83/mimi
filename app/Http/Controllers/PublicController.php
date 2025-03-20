<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


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

    public function showPdf(Request $request)
    {
        $fileId = $request->query('id');

        if (empty($fileId)) {
            return abort(404, 'No file ID provided.');
        }

        // You can use the fileId to fetch the actual file path from the database or storage.
        $filePath = $this->getFilePathFromId($fileId);

        if (!$filePath) {
            return abort(404, 'File not found.');
        }

        return view('public.pdf-viewer', ['filePath' => $filePath]);
    }

    private function getFilePathFromId($fileId)
    {
        // Retrieve the actual file path from your storage based on the file ID.
        // Example: Check in your database or cloud storage for the file path.
        // This can be the path in your cloud storage, or Google Drive, etc.

        return 'path/to/your/file.pdf';  // Replace with actual logic
    }
}
