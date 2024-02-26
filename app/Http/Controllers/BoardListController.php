<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class BoardListController extends Controller
{
    public function index(): View
    {
        // Fetch all posts from the database and pass them to the view
        $posts = \App\Models\BoardList::all();

        return view('boardlist.index', ['posts' => $posts]);    }

    public function show($id): View
    {
        // Fetch a specific post by ID from the database and pass it to the view
        $post = \App\Models\BoardList::findOrFail($id);

        return view('boardlist.show', ['post' => $post]);    }
}
