<?php

namespace App\Http\Controllers;

use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\ForumCategorySubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

class ForumSubscriptionController extends Controller
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public function defaultCategories()
    {
        // Public Annnouncemenets = 1
        // CoordinatorList = 2
        // BoardList =3

        $coordinatorCategories = [1, 2, 3];
        $boardCategories = [1, 3];

        return [
            'coordinatorCategories' => $coordinatorCategories,
            'boardCategories' => $boardCategories,
        ];
    }

    /**
     *  Coordinator Subscribe FOR the Board Member or Coordinator on Details Page
     */
    public function subscribeCategory(Request $request)
    {
        ForumCategorySubscription::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
        ]);

        return response()->json([
            'message' => 'Successfully subscribed to category',
            'redirect' => back()->getTargetUrl(),
        ]);
    }

    public function unsubscribeCategory(Request $request)
    {
        ForumCategorySubscription::where([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
        ])
            ->delete();

        return response()->json([
            'message' => 'Successfully unsubscribed from category',
            'redirect' => back()->getTargetUrl(),
        ]);
    }

    /**
     *  Show list of chapters subscribitios by email
     */
    public function showChapterListSubscriptions(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus];

        return view('forum.chaptersubscriptionlist')->with($data);
    }

    /**
     *  Show list of coordinators subscribitios by email
     */
    public function showCoordinatorListSubscriptions(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseCoordinatorController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery['checkBoxStatus'];

        $emailListCord = $coordinatorList->pluck('email')->filter()->implode(';');

        $countList = count($coordinatorList);
        $data = ['countList' => $countList, 'coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus, 'emailListCord' => $emailListCord];

        return view('forum.coordinatorsubscriptionlist')->with($data);
    }

    /**
     *  Show list of intrnational chapters subscribitios by email
     */
    public function showInternationalChapterListSubscriptions(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $chapterList = $baseQuery['query']->get();

        $countList = count($chapterList);
        $data = ['countList' => $countList, 'chapterList' => $chapterList];

        return view('forum.internationalchaptersubscriptionlist')->with($data);
    }

    /**
     *  Show list of intrnational coordinators subscribitios by email
     */
    public function showInternationalCoordinatorListSubscriptions(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $cdDetails = $user->coordinator;
        $cdId = $cdDetails->id;
        $cdConfId = $cdDetails->conference_id;
        $cdRegId = $cdDetails->region_id;
        $cdPositionid = $cdDetails->position_id;
        $cdSecPositionid = $cdDetails->sec_position_id;

        $baseQuery = $this->baseCoordinatorController->getActiveInternationalBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
        $coordinatorList = $baseQuery['query']->get();

        $data = ['coordinatorList' => $coordinatorList];

        return view('forum.internationalcoordinatorsubscriptionlist')->with($data);
    }

    /**
     * Add all active coordinators to CoordinatorList Subscribe by Email
     */
    public function bulkAddCoordinatorsList()
    {
        $category = ForumCategory::where('title', 'CoordinatorList')
            ->first();
        $categoryId = $category->id;

        // Get coordinators who are active and not on leave
        $coordinatorUserIds = Coordinators::where('is_active', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        $subscriptionCount = 0;
        $errors = [];

        foreach ($activeCoordinators as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} coordinators to CoorinatorList");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active coordinators to BoardList Subscribe by Email
     */
    public function bulkAddCoordinatorsBoardList()
    {
        $category = ForumCategory::where('title', 'BoardList')
            ->first();
        $categoryId = $category->id;

        // Get all active coordinators
        $coordinatorUserIds = Coordinators::where('is_active', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        $subscriptionCount = 0;
        $errors = [];

        foreach ($activeCoordinators as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} coordinators to BoardList");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active coordinators to Public Announcements Subscribe by Email
     */
    public function bulkAddCoordinatorsPublicAnnounceements()
    {
        $category = ForumCategory::where('title', 'Public Announcements')
            ->first();
        $categoryId = $category->id;

        // Get all active coordinators
        $coordinatorUserIds = Coordinators::where('is_active', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        $subscriptionCount = 0;
        $errors = [];

        foreach ($activeCoordinators as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} coordinators to Public Announcements");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active board members & coordinators to BoardList Subscribe by Email
     */
    public function bulkAddBoardList()
    {
        // Get category
        $category = ForumCategory::where('title', 'BoardList')
            ->first();
        $categoryId = $category->id;

        // Get active coordinators
        $coordinatorUserIds = Coordinators::where('is_active', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
            ->where('is_active', true)
            ->get()
            ->pluck('boards')
            ->flatten()
            ->pluck('user_id')
            ->unique();

        $activeBoards = User::whereIn('id', $boardUserIds)->get();

        // Combine the collections
        $allUsers = $activeCoordinators->concat($activeBoards);

        // Remove duplicates if a user is both coordinator and board member
        $uniqueUsers = $allUsers->unique('id');

        $subscriptionCount = 0;
        $errors = [];

        foreach ($uniqueUsers as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} users to BoardList");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} users, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active users to Public Annoncements Subscribe by Email
     */
    public function bulkAddPublicAnnouncements()
    {
        // Get category
        $category = ForumCategory::where('title', 'Public Announcements')
            ->first();
        $categoryId = $category->id;

        // Get active coordinators
        $coordinatorUserIds = Coordinators::where('is_active', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
            ->where('is_active', true)
            ->get()
            ->pluck('boards')
            ->flatten()
            ->pluck('user_id')
            ->unique();

        $activeBoards = User::whereIn('id', $boardUserIds)->get();

        // Combine the collections
        $allUsers = $activeCoordinators->concat($activeBoards);

        // Remove duplicates if a user is both coordinator and board member
        $uniqueUsers = $allUsers->unique('id');

        $subscriptionCount = 0;
        $errors = [];

        foreach ($uniqueUsers as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} users to Public Announcements");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} users, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active board members to BoardList Subscribe by Email
     */
    public function bulkAddBoardBoardList()
    {
        // Get category
        $category = ForumCategory::where('title', 'BoardList')
            ->first();
        $categoryId = $category->id;

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
            ->where('is_active', true)
            ->get()
            ->pluck('boards')
            ->flatten()
            ->pluck('user_id')
            ->unique();

        $activeBoards = User::whereIn('id', $boardUserIds)->get();

        $subscriptionCount = 0;
        $errors = [];

        foreach ($activeBoards as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} board members to BoardList");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} board members, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active board members to Public Annoncements Subscribe by Email
     */
    public function bulkAddBoardPublicAnnouncements()
    {
        // Get category
        $category = ForumCategory::where('title', 'Public Announcements')
            ->first();
        $categoryId = $category->id;

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
            ->where('is_active', true)
            ->get()
            ->pluck('boards')
            ->flatten()
            ->pluck('user_id')
            ->unique();

        $activeBoards = User::whereIn('id', $boardUserIds)->get();

        $subscriptionCount = 0;
        $errors = [];

        foreach ($activeBoards as $user) {
            try {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryId)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                    ]);
                    $subscriptionCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to subscribe user {$user->id}: {$e->getMessage()}";
            }
        }

        if (empty($errors)) {
            return back()->with('success', "Successfully subscribed {$subscriptionCount} board members to Public Announcements");
        } else {
            return back()->with('warning', "Subscribed {$subscriptionCount} board members, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Remove all coordinaors and board members from BoardList Subscription
     */
    public function bulkRemoveBoardList()
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'BoardList')
                ->first();

            // Delete all subscriptions for this category
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->delete();

            return back()->with('success', "Successfully unsubscribed {$deletedCount} members from BoardList");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * Remove all coordinators from BoardList Subscription
     */
    public function bulkRemoveCoordinatorsBoardList()
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'BoardList')
                ->first();

            if (! $category) {
                return back()->with('error', 'BoardList category not found');
            }

            // Delete subscriptions only for users who are coordinators
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'coordinator');
                })
                ->delete();

            return back()->with('success', "Successfully unsubscribed {$deletedCount} coordinators from BoardList");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * Remove all board members from BoardList Subscription
     */
    public function bulkRemoveBoardBoardList()
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'BoardList')
                ->first();

            if (! $category) {
                return back()->with('error', 'BoardList category not found');
            }

            // Delete subscriptions only for users who are board members
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'board');
                })
                ->delete();

            return back()->with('success', "Successfully unsubscribed {$deletedCount} board members from BoardList");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * Remove all board members from BoardList Subscription
     */
    public function bulkRemoveBoardPublicAnnouncements()
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'Public Announcements')
                ->first();

            if (! $category) {
                return back()->with('error', 'Public Announcements category not found');
            }

            // Delete subscriptions only for users who are board members
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'board');
                })
                ->delete();

            return back()->with('success', "Successfully unsubscribed {$deletedCount} board members from Public Announcements");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }
}
