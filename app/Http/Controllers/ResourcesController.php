<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBugsAdminRequest;
use App\Http\Requests\AddResourcesAdminRequest;
use App\Http\Requests\AddToolkitAdminRequest;
use App\Http\Requests\UpdateBugsAdminRequest;
use App\Http\Requests\UpdateResourcesAdminRequest;
use App\Http\Requests\UpdateToolkitAdminRequest;
use App\Mail\AdminNewMIMIBugWish;
use App\Models\Bugs;
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
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ResourcesController extends Controller implements HasMiddleware
{
    protected $userController;

    protected $positionConditionsService;

    protected $learndashService;

    public function __construct(UserController $userController, PositionConditionsService $positionConditionsService, LearnDashService $learndashService)
    {
        $this->userController = $userController;
        $this->positionConditionsService = $positionConditionsService;
        $this->learndashService = $learndashService;

    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
            \App\Http\Middleware\EnsureUserIsActiveAndCoordinator::class,
        ];
    }

    /**
     * View the EOY Report Title
     */
    public function getPageTitle(Request $request)
    {
        $titles = [
            'admin_reports' => 'Admin Tasks/Reports',
            'admin_details' => 'Chapter Details',
            'resource_reports' => 'Resources',
            'resource_details' => 'Resource Details',
        ];

        return $titles;
    }

    /**
     * View Tasks on Bugs & Enhancements List
     */
    public function showBugs(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['resource_reports'];
        $breadcrumb = 'MIMI Bugs & Wishes';

        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditDetails = ($positionId == 13 || in_array(13, $secPositionId));  // IT Coordinator

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

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb, 'admin' => $admin, 'canEditDetails' => $canEditDetails];

        return view('resources.bugs')->with($data);
    }

    /**
     * Add New Task to Bugs & Enhancements List
     */
    public function addBugs(AddBugsAdminRequest $request)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $validatedData = $request->validated();

        $task = new Bugs;
        $task->task = $validatedData['taskNameNew'];
        $task->details = $validatedData['taskDetailsNew'];
        $task->priority = $validatedData['taskPriorityNew'];
        $task->reported_id = $coorId;
        $task->reported_date = $lastupdatedDate;

        $mailData = [
            'taskNameNew' => $task->task,
            'taskDetailsNew' => $task->details,
            'ReportedId' => $coorId,
            'ReportedDate' => $task->reported_date,
        ];

        // $to_email = 'jackie.mchenry@momsclub.org';
        $adminEmail = $this->positionConditionsService->getAdminEmail();
        $mimiAdmin = $adminEmail['mimi_admin'];  // Gsuite Coor Email

        Mail::to($mimiAdmin)->queue(new AdminNewMIMIBugWish($mailData));

        $task->save();
    }

    /**
     * Update Task on Bugs & Enhancements List
     */
    public function updateBugs(UpdateBugsAdminRequest $request, $id)
    {
        $validatedData = $request->validated();

        $task = Bugs::findOrFail($id);
        $task->details = $validatedData['taskDetails'];
        $task->notes = $validatedData['taskNotes'];
        $task->status = $validatedData['taskStatus'];
        $task->priority = $validatedData['taskPriority'];

        if ($validatedData['taskStatus'] == 3) {
            $task->completed_date = Carbon::today();
        }

        $task->save();
    }

    /**
     * View the Downloads List
     */
    public function showDownloads(Request $request): View
    {
        $titles = $this->getPageTitle($request);
        $title = $titles['resource_reports'];
        $breadcrumb = 'Download Reports';

        $data = ['title' => $title, 'breadcrumb' => $breadcrumb];

        return view('resources.downloads')->with($data);
    }

    /**
     * View Resources List
     */
    public function showResources(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditFiles = ($positionId == 7 || in_array(7, $secPositionId));  // CC Coordinator

        $resources = Resources::with('resourceCategory')->get();
        $resourceCategories = ResourceCategory::all();

        foreach ($resources as $resource) {
            $id = $resource->id;
        }

        $data = ['resources' => $resources, 'resourceCategories' => $resourceCategories, 'canEditFiles' => $canEditFiles, 'id' => $id];

        return view('resources.resources')->with($data);
    }

    /**
     * Add New Files or Links to the Resources List
     */
    public function addResources(AddResourcesAdminRequest $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $validatedData = $request->validated();

        $file = new Resources;
        $file->category = $validatedData['fileCategoryNew'];
        $file->name = $validatedData['fileNameNew'];
        $file->description = $validatedData['fileDescriptionNew'];
        $file->file_type = $validatedData['fileTypeNew'];
        $file->version = $validatedData['fileVersionNew'] ?? null;
        $file->link = $validatedData['LinkNew'] ?? null;
        $file->file_path = $validatedData['filePathNew'] ?? null;
        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();

        $id = $file->id;
        $fileType = $file->file_type;

        return response()->json(['id' => $id, 'file_type' => $fileType]);
    }

    /**
     * Update Files or Links on the Resources List
     */
    public function updateResources(UpdateResourcesAdminRequest $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        // Fetch admin details
        $file = DB::table('resources')
            ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
            ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
            ->first(); // Fetch only one record
        $validatedData = $request->validated();

        $file = Resources::findOrFail($id);
        $file->description = $validatedData['fileDescription'];
        $file->file_type = $validatedData['fileType'];

        // Check file_type value and set version and link accordingly
        if ($validatedData['fileType'] == 1) {
            $file->link = null;
            $file->version = $validatedData['fileVersion'] ?? null;
        } elseif ($validatedData['fileType'] == 2) {
            $file->version = null;
            $file->file_path = null;
            $file->link = $validatedData['link'] ?? null;
        }

        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();
    }

    /**
     * View Toolkit List
     */
    public function showToolkit(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditFiles = ($positionId == 13 || in_array(13, $secPositionId));  // IT Coordinator

        $resources = Resources::with('toolkitCategory')->get();
        $toolkitCategories = ToolkitCategory::all();

        $data = ['resources' => $resources, 'canEditFiles' => $canEditFiles, 'toolkitCategories' => $toolkitCategories];

        return view('resources.toolkit')->with($data);
    }

    /**
     * Add New Files or Links to the Toolkit List
     */
    public function addToolkit(AddToolkitAdminRequest $request): JsonResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $validatedData = $request->validated();

        $file = new Resources;
        $file->category = $request->fileCategoryNew;
        $file->name = $request->fileNameNew;
        $file->description = $request->fileDescriptionNew;
        $file->file_type = $request->fileTypeNew;

        if ($request->fileTypeNew == 1) {
            $file->link = null;
            $file->version = $request->fileVersionNew ?? null;
        } elseif ($request->fileTypeNew == 2) {
            $file->version = null;
            $file->file_path = null;
            $file->link = $request->linkNew ?? null;
        }

        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();

        return response()->json(['id' => $file->id, 'file_type' => $file->file_type]);
    }

    /**
     * Update Files or Links on the Toolkit List
     */
    public function updateToolkit(UpdateToolkitAdminRequest $request, $id)
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        // Fetch admin details
        $file = DB::table('resources')
            ->select('resources.*', DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'))
            ->leftJoin('coordinators as cd', 'resources.updated_id', '=', 'cd.id')
            ->first(); // Fetch only one record
        $validatedData = $request->validated();

        $file = Resources::findOrFail($id);
        $file->description = $validatedData['fileDescription'];
        $file->file_type = $validatedData['fileType'];

        // Check file_type value and set version and link accordingly
        if ($validatedData['fileType'] == 1) {
            $file->link = null;
            $file->version = $validatedData['fileVersion'] ?? null;
        } elseif ($validatedData['fileType'] == 2) {
            $file->version = null;
            $file->file_path = null;
            $file->link = $validatedData['link'] ?? null;
        }

        $file->updated_id = $coorId;
        $file->updated_date = $lastupdatedDate;

        $file->save();
    }

    /**
     * View eLearning Courses
     */
    public function showELearning(Request $request): View
    {
        $user = User::find($request->user()->id);

        $coordinatorCourses = $this->learndashService->getCoursesForUserType('coordinator');
        $boardCourses = $this->learndashService->getCoursesForUserType('board');

        // Add auto-login URLs to each course
        foreach ($coordinatorCourses as &$coordinatorCourse) {
            $coordinatorCourse['auto_login_url'] = $this->learndashService->getAutoLoginUrl($coordinatorCourse, $user);
        }

        foreach ($boardCourses as &$boardCourse) {
            $boardCourse['auto_login_url'] = $this->learndashService->getAutoLoginUrl($boardCourse, $user);
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

        return view('resources.elearning')->with($data);
    }

    public function redirectToCourse($courseId, Request $request): RedirectResponse
    {
        $token = $request->query('token');
        $courseUrl = urldecode($request->query('course_url'));

        $wpAutoLoginUrl = 'https://momsclub.org/elearning/wp-json/auth/v1/auto-login?'.http_build_query([
            'token' => $token,
            'course_url' => $courseUrl,
        ]);

        return redirect()->to($wpAutoLoginUrl);
    }
}
