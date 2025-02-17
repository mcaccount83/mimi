<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\State;
use App\Models\Region;
use App\Models\Conference;
use Illuminate\Support\Facades\DB;
use App\Models\ForumCategorySubscription;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

use Illuminate\Http\Request;

class ForumSubscriptionController extends Controller
{
    protected $userController;
    protected $chapterController;
    protected $coordinatorController;

    public function __construct(UserController $userController, ChapterController $chapterController, CoordinatorController $coordinatorController)
    {
        $this->middleware('auth')->except('logout');
        $this->middleware(\App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class);
        $this->userController = $userController;
        $this->chapterController = $chapterController;
        $this->coordinatorController = $coordinatorController;
    }

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

        $baseQuery = $this->chapterController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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

        $baseQuery = $this->coordinatorController->getActiveBaseQuery($cdConfId, $cdRegId, $cdId, $cdPositionid, $cdSecPositionid);
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

        $intChapterList = Chapters::with(['state', 'conference', 'region', 'president', 'primaryCoordinator'])
            ->where('is_active', 1)
            ->orderBy(Conference::select('short_name')
            ->whereColumn('conference.id', 'chapters.conference_id')
            )
            ->orderBy(
                Region::select('short_name')
                        ->whereColumn('region.id', 'chapters.region_id')
            )
            ->orderBy(State::select('state_short_name')
                    ->whereColumn('state.id', 'chapters.state_id'), 'asc')

            ->orderBy('chapters.name')
            ->get();

        $countList = count($intChapterList);
        $data = ['countList' => $countList, 'intChapterList' => $intChapterList];

        return view('forum.internationalchaptersubscriptionlist', compact('intChapterList'));
    }

    /**
     *  Show list of intrnational coordinators subscribitios by email
     */
    public function showInternationalCoordinatorListSubscriptions(Request $request)
    {
        $user = User::find($request->user()->id);
        $userId = $user->id;

        $intCoordinatorList = Coordinators::with(['state', 'conference', 'region', 'displayPosition', 'mimiPosition', 'secondaryPosition', 'reportsTo'])
            ->where('is_active', 1)
            ->orderBy(Conference::select(DB::raw("CASE WHEN short_name = 'Intl' THEN '' ELSE short_name END"))
                    ->whereColumn('conference.id', 'coordinators.conference_id')
                    ->limit(1)
            )
            ->orderBy(
                Region::select(DB::raw("CASE WHEN short_name = 'None' THEN '' ELSE short_name END"))
                        ->whereColumn('region.id', 'coordinators.region_id')
                        ->limit(1)
            )
            ->orderBy('coordinator_start_date')

            ->get();

        $data = ['intCoordinatorList' => $intCoordinatorList];

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

            if (!$existingSubscription) {
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
        return back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: " . implode(', ', $errors));
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

            if (!$existingSubscription) {
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
        return back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: " . implode(', ', $errors));
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

            if (!$existingSubscription) {
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
        return back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: " . implode(', ', $errors));
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

                if (!$existingSubscription) {
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
            return back()->with('warning', "Subscribed {$subscriptionCount} users, but encountered errors: " . implode(', ', $errors));
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

                if (!$existingSubscription) {
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
            return back()->with('warning', "Subscribed {$subscriptionCount} users, but encountered errors: " . implode(', ', $errors));
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

                if (!$existingSubscription) {
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
            return back()->with('warning', "Subscribed {$subscriptionCount} board members, but encountered errors: " . implode(', ', $errors));
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

                if (!$existingSubscription) {
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
            return back()->with('warning', "Subscribed {$subscriptionCount} board members, but encountered errors: " . implode(', ', $errors));
        }
    }


}
