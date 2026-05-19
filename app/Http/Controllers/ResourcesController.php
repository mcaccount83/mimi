<?php

namespace App\Http\Controllers;

use App\Enums\CoordinatorPosition;
use App\Mail\AdminNewMIMIBugWish;
use App\Models\Bugs;
use App\Models\FinancialReportAwards;
use App\Models\FinancialReportAwardsBadges;
use App\Models\FiscalYear;
use App\Models\ResourceCategory;
use App\Models\Resources;
use App\Models\ToolkitCategory;
use App\Models\User;
use App\Services\LearnDashService;
use App\Services\PositionConditionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ResourcesController extends Controller implements HasMiddleware
{
    public function __construct(
        protected UserController $userController,
        protected PositionConditionsService $positionConditionsService,
        protected LearnDashService $learndashService,
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View Tasks on Bugs & Enhancements List
     */
    public function showBugs(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $canEditDetails = ($positionId == CoordinatorPosition::IT || in_array(CoordinatorPosition::IT, $secPositionId));
        // $canEditDetails = ($positionId == 13 || in_array(13, $secPositionId));  // IT Coordinator

        $admin = DB::table('bugs')
            ->select('bugs.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS reported_by'),
                DB::raw('CASE
                    WHEN priority = 1 THEN "LOW"
                    WHEN priority = 2 THEN "NORMAL"
                    WHEN priority = 3 THEN "HIGH"
                    ELSE "Unknown"
                END as priority_word'))
            ->leftJoin('coordinators as cd', 'bugs.reported_id', '=', 'cd.id')
            ->orderByDesc('priority')
            ->get();

        $data = ['admin' => $admin, 'canEditDetails' => $canEditDetails];

        return view('coordinators.resources.bugs')->with($data);
    }

    /**
     * Add New Task to Bugs & Enhancements List
     */
    public function addBugs(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['cdId'];
            $updatedBy = $user['userName'];

            $validatedData = $request->validate([
                'taskNameNew' => 'required|string|max:255',
                'taskDetailsNew' => 'required|string',
                'taskPriorityNew' => 'required',
            ]);

            $task = new Bugs;
            $task->task = $validatedData['taskNameNew'];
            $task->details = $validatedData['taskDetailsNew'];
            $task->priority = $validatedData['taskPriorityNew'];
            $task->reported_id = $coorId;
            $task->reported_date = Carbon::now();

            $mailData = [
                'taskNameNew' => $task->task,
                'taskDetailsNew' => $task->details,
                'ReportedId' => $coorId,
                'ReportedDate' => $task->reported_date,
            ];

            $adminEmail = $this->positionConditionsService->getAdminEmail();
            $mimiAdmin = $adminEmail['mimi_admin'];

            Mail::to($mimiAdmin)->queue(new AdminNewMIMIBugWish($mailData));

            $task->save();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while submitting the bug report.'], 500);
        }
    }

    /**
     * Update Task on Bugs & Enhancements List
     */
    public function updateBugs(Request $request, int $id): JsonResponse
    {
        $dateOptions = $this->positionConditionsService->getDateOptions();
        $currentDate = $dateOptions['currentDate'];

        try {
            $validatedData = $request->validate([
                'taskDetails' => 'required|string',
                'taskNotes' => 'nullable|string',
                'taskStatus' => 'required',
                'taskPriority' => 'required',
            ]);

            $task = Bugs::findOrFail($id);
            $task->details = $validatedData['taskDetails'];
            $task->notes = $validatedData['taskNotes'];
            $task->status = $validatedData['taskStatus'];
            $task->priority = $validatedData['taskPriority'];

            if ($validatedData['taskStatus'] == 3) {
                $task->completed_date = $currentDate;
            }

            $task->save();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while updating the bug report.'], 500);
        }
    }

    /**
     * View Resources List
     */
    public function showResources(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $canEditFiles = ($positionId == CoordinatorPosition::IT || in_array(CoordinatorPosition::IT, $secPositionId));
        // $canEditFiles = ($positionId == CoordinatorPosition::CC || in_array(CoordinatorPosition::CC, $secPositionId));

        $resources = Resources::with('resourceCategory', 'updatedBy')->get();
        $resourceCategories = ResourceCategory::all();

        foreach ($resources as $resource) {
            $id = $resource->id;
        }

        $data = ['resources' => $resources, 'resourceCategories' => $resourceCategories, 'canEditFiles' => $canEditFiles, 'id' => $id];

        return view('coordinators.resources.resources')->with($data);
    }

    public function addResources(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $updatedId = $user['userId'];

            $validatedData = $request->validate([
                'fileCategoryNew' => 'required',
                'fileNameNew' => 'required|string|max:50',
                'fileDescriptionNew' => 'required|string|max:500',
                'fileTypeNew' => 'required|in:1,2,3',
                'fileVersionNew' => 'nullable|string|max:25',
                'LinkNew' => 'nullable|string|max:255',
                'routeNew' => 'nullable|string|max:255',
                'filePathNew' => 'nullable|string|max:255',
            ]);

            $file = new Resources;
            $file->resource_category = $validatedData['fileCategoryNew'];
            $file->name = $validatedData['fileNameNew'];
            $file->description = $validatedData['fileDescriptionNew'];
            $file->file_type = $validatedData['fileTypeNew'];

            // Handle based on file type
            if ($validatedData['fileTypeNew'] == 1) {
                // File - uses version and file_path
                $file->version = $validatedData['fileVersionNew'] ?? null;
                $file->file_path = $validatedData['filePathNew'] ?? null;
                $file->link = null;
            } elseif ($validatedData['fileTypeNew'] == 2) {
                // External Link - uses link field
                $file->link = $validatedData['LinkNew'] ?? null;
                $file->version = null;
                $file->file_path = null;
            } elseif ($validatedData['fileTypeNew'] == 3) {
                // Route - uses link field for route name
                $file->link = $validatedData['routeNew'] ?? null;
                $file->version = null;
                $file->file_path = null;
            }

            $file->updated_id = $updatedId;
            $file->save();

            return response()->json(['success' => true, 'id' => $file->id, 'file_type' => $file->file_type]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while adding the resource.'], 500);
        }
    }

    public function updateResources(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $updatedId = $user['userId'];

            $validatedData = $request->validate([
                'fileDescription' => 'required|string|max:500',
                'fileType' => 'required|in:1,2,3',
                'fileVersion' => 'nullable|string|max:25',
                'link' => 'nullable|string|max:255',
                'route' => 'nullable|string|max:255',
            ]);

            $file = Resources::findOrFail($id);
            $file->description = $validatedData['fileDescription'];
            $file->file_type = $validatedData['fileType'];

            // Handle based on file type
            if ($validatedData['fileType'] == 1) {
                // File - uses version and file_path
                $file->version = $validatedData['fileVersion'] ?? null;
                // Note: file_path stays the same unless new file uploaded
                $file->link = null;
            } elseif ($validatedData['fileType'] == 2) {
                // External Link - uses link field
                $file->link = $validatedData['link'] ?? null;
                $file->version = null;
                $file->file_path = null;
            } elseif ($validatedData['fileType'] == 3) {
                // Route - uses link field for route name
                $file->link = $validatedData['route'] ?? null;
                $file->version = null;
                $file->file_path = null;
            }

            $file->updated_id = $updatedId;
            $file->save();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while updating the resource.'], 500);
        }
    }

    /**
     * View Toolkit List
     */
    public function showToolkit(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $canEditFiles = ($positionId == CoordinatorPosition::IT || in_array(CoordinatorPosition::IT, $secPositionId));

        $resources = Resources::with('toolkitCategory', 'updatedBy')->get();
        $toolkitCategories = ToolkitCategory::all();

        foreach ($resources as $resource) {
            $id = $resource->id;
        }

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'toolkitCategories' => $toolkitCategories, 'id' => $id];

        return view('coordinators.resources.toolkit')->with($data);
    }

    /**
     * Add New Files or Links to the Toolkit List
     */
    public function addToolkit(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $updatedId = $user['userId'];

            $validatedData = $request->validate([
                'fileCategoryNew' => 'required',
                'fileNameNew' => 'required|string|max:50',
                'fileDescriptionNew' => 'required|string|max:500',
                'fileTypeNew' => 'required|in:1,2,3',
                'fileVersionNew' => 'nullable|string|max:25',
                'LinkNew' => 'nullable|string|max:255',
                'routeNew' => 'nullable|string|max:255',
                'filePathNew' => 'nullable|string|max:255',
            ]);

            $file = new Resources;
            $file->toolkit_category = $validatedData['fileCategoryNew'];
            $file->name = $validatedData['fileNameNew'];
            $file->description = $validatedData['fileDescriptionNew'];
            $file->file_type = $validatedData['fileTypeNew'];

            // Handle based on file type
            if ($validatedData['fileTypeNew'] == 1) {
                // File - uses version and file_path
                $file->version = $validatedData['fileVersionNew'] ?? null;
                $file->file_path = $validatedData['filePathNew'] ?? null;
                $file->link = null;
            } elseif ($validatedData['fileTypeNew'] == 2) {
                // External Link - uses link field
                $file->link = $validatedData['LinkNew'] ?? null;
                $file->version = null;
                $file->file_path = null;
            } elseif ($validatedData['fileTypeNew'] == 3) {
                // Route - uses link field for route name
                $file->link = $validatedData['routeNew'] ?? null;
                $file->version = null;
                $file->file_path = null;
            }

            $file->updated_id = $updatedId;
            $file->save();

            return response()->json(['success' => true, 'id' => $file->id, 'file_type' => $file->file_type]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while adding the toolkit item.'], 500);
        }
    }

    /**
     * Update Files or Links on the Toolkit List
     */
    public function updateToolkit(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $updatedId = $user['userId'];

            $validatedData = $request->validate([
                'fileDescription' => 'required|string|max:500',
                'fileType' => 'required|in:1,2,3',
                'fileVersion' => 'nullable|string|max:25',
                'link' => 'nullable|string|max:255',
                'route' => 'nullable|string|max:255',
            ]);

            $file = Resources::findOrFail($id);
            $file->description = $validatedData['fileDescription'];
            $file->file_type = $validatedData['fileType'];

            // Handle based on file type
            if ($validatedData['fileType'] == 1) {
                // File - uses version and file_path
                $file->version = $validatedData['fileVersion'] ?? null;
                // Note: file_path stays the same unless new file uploaded
                $file->link = null;
            } elseif ($validatedData['fileType'] == 2) {
                // External Link - uses link field
                $file->link = $validatedData['link'] ?? null;
                $file->version = null;
                $file->file_path = null;
            } elseif ($validatedData['fileType'] == 3) {
                // Route - uses link field for route name
                $file->link = $validatedData['route'] ?? null;
                $file->version = null;
                $file->file_path = null;
            }

            $file->updated_id = $updatedId;
            $file->save();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while updating the toolkit item.'], 500);
        }
    }

    /**
     * View Award Badges List
     */
    public function showAwards(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['cdPositionId'];
        $secPositionId = $user['cdSecPositionId'];
        $canEditFiles = ($positionId == CoordinatorPosition::IT || in_array(CoordinatorPosition::IT, $secPositionId));

        $awards = FinancialReportAwardsBadges::with('fiscalYear', 'eoyAward')->get();

        $reportYears = FiscalYear::with(['awardBadges.eoyAward'])->orderByDesc('id')->get();

        $eoyAwards = FinancialReportAwards::orderBy('award_type')->get();

        $data = ['reportYears' => $reportYears, 'eoyAwards' => $eoyAwards, 'canEditFiles' => $canEditFiles];

        return view('coordinators.resources.awardbadges')->with($data);
    }

    /**
     * Add New Files or Links to the Awards List
     */
    public function addAwardBadge(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'reportYearNew' => 'required|exists:fiscal_year,id',
                'eoyAwardNew' => 'required|exists:financial_report_awards,id',
                'fileNameNew' => 'required|file|mimes:png|max:2048',
            ]);

            // Check for existing badge for this year/award combination
            $exists = FinancialReportAwardsBadges::where('report_year_id', $validatedData['reportYearNew'])
                ->where('eoy_award_id', $validatedData['eoyAwardNew'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'error' => 'A badge already exists for this award type and fiscal year. Use UPDATE instead.',
                ], 422);
            }

            $file = FinancialReportAwardsBadges::create([
                'report_year_id' => $validatedData['reportYearNew'],
                'eoy_award_id' => $validatedData['eoyAwardNew'],
                'file_path' => null, // storeAwardBadges will set this
            ]);

            return response()->json(['success' => true, 'id' => $file->id]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'An error occurred while adding the badge.'], 500);
        }
    }

    public function updateAwardBadge(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'fileName' => 'required|file|mimes:png|max:2048',
            ]);

            // Just confirm the record exists — file_path gets updated by storeAwardBadges
            FinancialReportAwardsBadges::findOrFail($id);

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('updateAwardBadge error', ['message' => $e->getMessage()]);

            return response()->json(['success' => false, 'error' => 'An error occurred while updating the badge.'], 500);
        }
    }

    /**
     * View eLearning Courses
     */
    public function showELearning(Request $request): View
    {
        $user = User::find($request->user()->id);

        $coordinatorCourses = $this->learndashService->getCoursesForUserType('coordinator');
        $boardCourses = $this->learndashService->getCoursesForUserType('board');

        // Fetch progress keyed by course ID
        $userProgress = $this->learndashService->getUserProgress($user->email);

        // Merge progress + auto-login URLs
        foreach ($coordinatorCourses as &$course) {
            $course['auto_login_url'] = $this->learndashService->getAutoLoginUrl($course, $user);
            $course['progress'] = $userProgress[$course['id']] ?? null;
        }

        foreach ($boardCourses as &$course) {
            $course['auto_login_url'] = $this->learndashService->getAutoLoginUrl($course, $user);
            $course['progress'] = $userProgress[$course['id']] ?? null;
        }

        // Group by category - store both name and slug
        $coordinatorCoursesByCategory = collect($coordinatorCourses)->groupBy(function ($coordinatorCourse) {
            return $coordinatorCourse['categories'][0]['slug'] ?? 'uncategorized';
        })->map(function ($courses, $slug) {
            return [
                'name' => $courses->first()['categories'][0]['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'courses' => $courses,
            ];
        });

        $boardCoursesByCategory = collect($boardCourses)->groupBy(function ($course) {
            return $course['categories'][0]['slug'] ?? 'uncategorized';
        })->map(function ($courses, $slug) {
            return [
                'name' => $courses->first()['categories'][0]['name'] ?? ucfirst(str_replace('-', ' ', $slug)),
                'courses' => $courses,
            ];
        });

        $data = [
            'coordinatorCourses' => $coordinatorCourses,
            'boardCourses' => $boardCourses,
            'coordinatorCoursesByCategory' => $coordinatorCoursesByCategory,
            'boardCoursesByCategory' => $boardCoursesByCategory,
        ];

        return view('coordinators.resources.elearning')->with($data);
    }

    public function redirectToCourse($courseId, Request $request): RedirectResponse
    {
        $token = $request->query('token');
        $courseUrl = urldecode($request->query('course_url'));

        // $wpAutoLoginUrl = "https://momsclub.org/elearning/wp-json/auth/v1/auto-login?" . http_build_query([
        $wpAutoLoginUrl = 'https://momsclub.org/elearning/wp-json/auth/v1/auto-login?'.http_build_query([
            'token' => $token,
            'course_url' => $courseUrl,
        ]);

        // return redirect($wpAutoLoginUrl);
        return redirect()->to($wpAutoLoginUrl);
    }
}
