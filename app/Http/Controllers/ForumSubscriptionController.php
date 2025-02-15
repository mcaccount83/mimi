<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ForumCategorySubscription;
use TeamTeaTime\Forum\Models\Category;
use Illuminate\Http\Request;

class ForumSubscriptionController extends Controller
{
    public function subscribe(Request $request, $categoryId)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        ForumCategorySubscription::create([
            'user_id' => $userId,
            'category_id' => $categoryId,
        ]);

        return back()->with('success', 'Successfully subscribed to category');
    }

    public function unsubscribe(Request $request, $categoryId)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        ForumCategorySubscription::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->delete();

        return back()->with('success', 'Successfully unsubscribed from category');
    }

}
