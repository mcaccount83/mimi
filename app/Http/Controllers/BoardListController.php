<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BoardListController extends Controller
{
    public function index()
    {
        // Fetch all posts from the database and pass them to the view
        $posts = \App\Models\BoardList::all();
        return view('boardlist.index', compact('posts'));
    }

    public function show($id)
    {
        // Fetch a specific post by ID from the database and pass it to the view
        $post = \App\Models\BoardList::findOrFail($id);
        return view('boardlist.show', compact('post'));
    }

}
