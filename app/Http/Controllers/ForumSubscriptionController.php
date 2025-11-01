<?php

namespace App\Http\Controllers;

use App\Enums\ChapterCheckbox;
use App\Enums\CoordinatorCheckbox;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\ForumCategorySubscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

class ForumSubscriptionController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $baseChapterController;

    protected $baseCoordinatorController;

    public function __construct(UserController $userController, BaseChapterController $baseChapterController, BaseCoordinatorController $baseCoordinatorController)
    {

        $this->userController = $userController;
        $this->baseChapterController = $baseChapterController;
        $this->baseCoordinatorController = $baseCoordinatorController;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
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
    public function subscribeCategory(Request $request): JsonResponse
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

    public function unsubscribeCategory(Request $request): JsonResponse
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
    public function showChapterListSubscriptions(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $chapterList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[ChapterCheckbox::CHECK_PRIMARY];
        $checkBox3Status = $baseQuery[ChapterCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[ChapterCheckbox::CHECK_INTERNATIONAL];

        $countList = $chapterList->count();
        $data = ['countList' => $countList, 'chapterList' => $chapterList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status,
        ];

        return view('forum.chaptersubscriptionlist')->with($data);
    }

    /**
     *  Show list of coordinators subscribitios by email
     */
    public function showCoordinatorListSubscriptions(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseCoordinatorController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $coordinatorList = $baseQuery['query']->get();
        $checkBoxStatus = $baseQuery[CoordinatorCheckbox::CHECK_DIRECT];
        $checkBox3Status = $baseQuery[CoordinatorCheckbox::CHECK_CONFERENCE_REGION];
        $checkBox5Status = $baseQuery[CoordinatorCheckbox::CHECK_INTERNATIONAL];

        $emailListCord = $coordinatorList->pluck('email')->filter()->implode(';');

        $countList = count($coordinatorList);
        $data = ['countList' => $countList, 'coordinatorList' => $coordinatorList, 'checkBoxStatus' => $checkBoxStatus,
            'checkBox3Status' => $checkBox3Status, 'checkBox5Status' => $checkBox5Status, 'emailListCord' => $emailListCord,
        ];

        return view('forum.coordinatorsubscriptionlist')->with($data);
    }

    /**
     * Add all active coordinators to CoordinatorList Subscribe by Email
     */
    public function bulkAddCoordinatorsList(): RedirectResponse
    {
        $category = ForumCategory::where('title', 'CoordinatorList')
            ->first();
        $categoryId = $category->id;

        // Get coordinators who are active and not on leave
        $coordinatorUserIds = Coordinators::where('active_status', '1')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} coordinators to CoorinatorList");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active coordinators to BoardList Subscribe by Email
     */
    public function bulkAddCoordinatorsBoardList(): RedirectResponse
    {
        $category = ForumCategory::where('title', 'BoardList')
            ->first();
        $categoryId = $category->id;

        // Get all active coordinators
        $coordinatorUserIds = Coordinators::where('active_status', '1')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} coordinators to BoardList");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active coordinators to Public Announcements Subscribe by Email
     */
    public function bulkAddCoordinatorsPublicAnnounceements(): RedirectResponse
    {
        $category = ForumCategory::where('title', 'Public Announcements')
            ->first();
        $categoryId = $category->id;

        // Get all active coordinators
        $coordinatorUserIds = Coordinators::where('active_status', '1')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} coordinators to Public Announcements");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} coordinators, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active board members & coordinators to BoardList Subscribe by Email
     */
    public function bulkAddBoardList(): RedirectResponse
    {
        // Get category
        $category = ForumCategory::where('title', 'BoardList')
            ->first();
        $categoryId = $category->id;

        // Get active coordinators
        $coordinatorUserIds = Coordinators::where('active_status', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} users to BoardList");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} users, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active users to Public Annoncements Subscribe by Email
     */
    public function bulkAddPublicAnnouncements(): RedirectResponse
    {
        // Get category
        $category = ForumCategory::where('title', 'Public Announcements')
            ->first();
        $categoryId = $category->id;

        // Get active coordinators
        $coordinatorUserIds = Coordinators::where('active_status', '1')
            ->where('on_leave', '0')
            ->get()
            ->pluck('user_id')
            ->unique();

        $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} users to Public Announcements");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} users, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active board members to BoardList Subscribe by Email
     */
    public function bulkAddBoardBoardList(): RedirectResponse
    {
        // Get category
        $category = ForumCategory::where('title', 'BoardList')
            ->first();
        $categoryId = $category->id;

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} board members to BoardList");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} board members, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Add all active board members to Public Annoncements Subscribe by Email
     */
    public function bulkAddBoardPublicAnnouncements(): RedirectResponse
    {
        // Get category
        $category = ForumCategory::where('title', 'Public Announcements')
            ->first();
        $categoryId = $category->id;

        // Get board members from active chapters using with()
        $boardUserIds = Chapters::with('boards')
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
            return redirect()->back()->with('success', "Successfully subscribed {$subscriptionCount} board members to Public Announcements");
        } else {
            return redirect()->back()->with('warning', "Subscribed {$subscriptionCount} board members, but encountered errors: ".implode(', ', $errors));
        }
    }

    /**
     * Remove all coordinaors and board members from BoardList Subscription
     */
    public function bulkRemoveBoardList(): RedirectResponse
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'BoardList')
                ->first();

            // Delete all subscriptions for this category
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->delete();

            return redirect()->back()->with('success', "Successfully unsubscribed {$deletedCount} members from BoardList");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * Remove all coordinators from BoardList Subscription
     */
    public function bulkRemoveCoordinatorsBoardList(): RedirectResponse
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'BoardList')
                ->first();

            if (! $category) {
                return redirect()->back()->with('error', 'BoardList category not found');
            }

            // Delete subscriptions only for users who are coordinators
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'coordinator');
                })
                ->delete();

            return redirect()->back()->with('success', "Successfully unsubscribed {$deletedCount} coordinators from BoardList");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * Remove all board members from BoardList Subscription
     */
    public function bulkRemoveBoardBoardList(): RedirectResponse
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'BoardList')
                ->first();

            if (! $category) {
                return redirect()->back()->with('error', 'BoardList category not found');
            }

            // Delete subscriptions only for users who are board members
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'board');
                })
                ->delete();

            return redirect()->back()->with('success', "Successfully unsubscribed {$deletedCount} board members from BoardList");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * Remove all board members from BoardList Subscription
     */
    public function bulkRemoveBoardPublicAnnouncements(): RedirectResponse
    {
        try {
            // Get category
            $category = ForumCategory::where('title', 'Public Announcements')
                ->first();

            if (! $category) {
                return redirect()->back()->with('error', 'Public Announcements category not found');
            }

            // Delete subscriptions only for users who are board members
            $deletedCount = ForumCategorySubscription::where('category_id', $category->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'board');
                })
                ->delete();

            return redirect()->back()->with('success', "Successfully unsubscribed {$deletedCount} board members from Public Announcements");

        } catch (\Exception $e) {
            Log::error('Bulk unsubscribe error:', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', "Error during bulk unsubscribe: {$e->getMessage()}");
        }
    }

    /**
     * BoardList -- OLD LISTING VIEW
     */
    public function showChapterBoardlist(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $confId = $user['user_confId'];
        $regId = $user['user_regId'];
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];

        $baseQuery = $this->baseChapterController->getBaseQuery(1, $coorId, $confId, $regId, $positionId, $secPositionId);
        $activeChapterList = $baseQuery['query']->get();

        $countList = count($activeChapterList);
        $data = ['countList' => $countList, 'activeChapterList' => $activeChapterList];

        return view('chapters.chapboardlist')->with($data);
    }
}
