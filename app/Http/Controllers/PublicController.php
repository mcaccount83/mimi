<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth')->except('logout');
    }

    public function chapterLinks()
    {
        $international = DB::table('chapters')
            ->select('chapters.*', 'state.state_short_name', 'state.state_long_name')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->where('state', '=', '52')
            ->where('is_active', '1')
            ->where('name', 'not like', '%test%')
            ->orderBy('name')
            ->get();

        $chapters = DB::table('chapters')
            ->select('chapters.*', 'state.state_short_name', 'state.state_long_name')
            ->join('state', 'chapters.state', '=', 'state.id')
            ->where('chapters.state', '<>', 52)
            ->where('is_active', '1')
            ->where('name', 'not like', '%test%')
            ->orderBy('state')
            ->orderBy('name')
            ->get();

        return view('public.chapterlinks', ['chapters' => $chapters, 'international' => $international]);
    }

    public function chapterResources()
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
}
