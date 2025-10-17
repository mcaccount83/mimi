<?php

namespace App\Http\Controllers;

use App\Enums\CoordinatorPosition;
use App\Models\Admin;
use App\Models\AdminEmail;
use App\Models\Boards;
use App\Models\BoardsDisbanded;
use App\Models\BoardsIncoming;
use App\Models\BoardsOutgoing;
use App\Models\BoardsPending;
use App\Models\Chapters;
use App\Models\Conference;
use App\Models\CoordinatorApplication;
use App\Models\CoordinatorRecognition;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\Documents;
use App\Models\FinancialReport;
use App\Models\ForumCategorySubscription;
use App\Models\GoogleDrive;
use App\Models\Payments;
use App\Models\ProbationSubmission;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use TeamTeaTime\Forum\Models\Category as ForumCategory;

class AdminController extends Controller implements HasMiddleware
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
     * Admin Choose Active Chapter for Viewing
     */
    public function listActiveChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapters = $baseQuery['query']->get();

        $countList = count($chapters);
        $data = ['countList' => $countList, 'chapters' => $chapters];

        return view('admin.chapterlist')->with($data);

    }

    /**
     * Admin Choose Zapped Chapter for Viewing
     */
    public function listZappedChapters(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getZappedInternationalBaseQuery($coorId);
        $chapters = $baseQuery['query']->orderByDesc('chapters.zap_date')->get();

        $countList = count($chapters);
        $data = ['countList' => $countList, 'chapters' => $chapters];

        return view('admin.chapterlistzapped')->with($data);

    }

    /**
     * View List of ReReg Payments if Dates Need to be Udpated
     */
    public function showReRegDate(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $coorId = $user['user_coorId'];

        $baseQuery = $this->baseChapterController->getActiveInternationalBaseQuery($coorId);
        $chapterList = $baseQuery['query']->get();

        $data = ['chapterList' => $chapterList];

        return view('admin.reregdate')->with($data);
    }

    public function editReRegDate(Request $request, $id): View
    {
        $baseQuery = $this->baseChapterController->getChapterDetails($id);
        $chDetails = $baseQuery['chDetails'];
        $stateShortName = $baseQuery['stateShortName'];
        $chPayments = $baseQuery['chPayments'];
        $allMonths = $baseQuery['allMonths'];

        $data = ['id' => $id, 'chDetails' => $chDetails, 'stateShortName' => $stateShortName, 'chPayments' => $chPayments, 'allMonths' => $allMonths];

        return view('admin.editreregdate')->with($data);
    }

    public function updateReRegDate(Request $request, $id): RedirectResponse
    {
        $user = $this->userController->loadUserInformation($request);
        $lastUpdatedBy = $user['user_name'];
        $lastupdatedDate = date('Y-m-d H:i:s');

        $chapter = Chapters::find($id);
        $payments = Payments::find($id);

        DB::beginTransaction();
        try {
            $chapter->start_month_id = $request->input('ch_founddate');
            $chapter->next_renewal_year = $request->input('ch_renewyear');
            $chapter->last_updated_by = $lastUpdatedBy;
            $chapter->last_updated_date = $lastupdatedDate;

            $chapter->save();

            $payments->rereg_date = $request->input('ch_duespaid');
            $payments->rereg_members = $request->input('ch_members');

            $payments->save();

            DB::commit();

            return redirect()->to('/admin/reregdate')->with('error', 'Failed to update Re-Reg Date.');
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit();
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return redirect()->to('/admin/reregdate')->with('success', 'Re-Reg Date updated successfully.');
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Reset Quarterly Report Data
     */
    public function resetProbationSubmission(): JsonResponse
    {
        try {
            ProbationSubmission::query()->delete();

            $probationPartyChapters = Chapters::with('probation')
                ->where('active_status', 1)
                ->where('probation_id', 3)
                ->get();
            foreach ($probationPartyChapters as $chapter) {
                ProbationSubmission::create([
                    'chapter_id' => $chapter->id,
                ]);
            }

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Quarterly Report Data reset successfully.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Show EOY Procedures
     */
    public function showEOY(Request $request): View
    {
        $user = $this->userController->loadUserInformation($request);
        $positionId = $user['user_positionId'];
        $secPositionId = $user['user_secPositionId'];
        $canEditFiles = ($positionId == CoordinatorPosition::IT || in_array(CoordinatorPosition::IT, $secPositionId));
        // $canEditFiles = ($positionId == 13 || in_array(13, $secPositionId));  // IT Coordinator

        $admin = DB::table('admin')
            ->select('admin.*',
                DB::raw('CONCAT(cd.first_name, " ", cd.last_name) AS updated_by'), )
            ->leftJoin('coordinators as cd', 'admin.updated_id', '=', 'cd.id')
            ->orderByDesc('admin.id') // Assuming 'id' represents the order of insertion
            ->first();

        // Fetch distinct fiscal years
        $fiscalYears = DB::table('admin')->distinct()->pluck('fiscal_year');

        $resetEOYTableItems = [
            'Set all Outgoing Users to Inactive',
            'Clear Outgoing Board Member Table',
            'Clear Incoming Board Member Table',
            'Ending Balance Added to Documents from Financial Reports',
            'Copy/Rename Financial Reports Table',
            'Clear Financial Reports Table',
            'Pre-Balance Added to Financial Report from Documents',
            'EOY Fields Reset in Chapters Table',
            'EOY Fields Reset in Documents Table',
            'Add Outgoing Board Members from Board Details',
            'Update Google Shared Drive to new year for Attachmnet Uploads',
        ];

        $displayTestingItemsItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display Board Election Report Button  for testers',
            'Display Financal Report Button for testers',
        ];

        $displayLiveItemsItems = [
            'Display EOY Dashboard Menu Items for all Coordinators',
            'Display Board Election Report Button for Board Members after May 1st',
            'Display Financal Report Button for Board Members after June 1st',
        ];

        $displayCoorindatorMenuItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display EOY Chapter Profile Buttons for testers',
            'Financial Report for Chapters - button will be available after June 1st.',
        ];

        $displayCoorindatorMenuItems = [
            'Display EOY Dashboard Menu Items for testers',
            'Display EOY Chapter Profile Buttons for testers',
            'Financial Report for Chapters - button will be available after June 1st.',
        ];

        $displayChapterButtonItems = [
            'Board Election Report for Chapters - button will be available after May 1st.',
            'Report Menu for Coordinators - menu will be available after May 1st.',
        ];

        $resetAFTERtestingItems = [
            'Ending Balance Added to Documents from Last Year Financial Reports',
            'Clear Outgoing Board Member Table',
            'Clear Incoming Board Member Table',
            'Clear Financial Reports Table',
            'Pre-Balance Added to Financial Report from Documents',
            'EOY Fields Reset in Chapters Table',
            'EOY Fields Reset in Documents Table',
            'Update Outgoing Board Members from Board Details',
        ];

        $updateUserTablesItems = [
            'Copy/Rename Chapters Table',
            'Copy/Rename Boards Table',
            'Copy/Rename Coordinators Table',
            'Copy/Rename Users Table',
        ];

        $unSubscribeListItems = [
            'Remove Board Members from BoardList',
            'Remove Board Members from Publc Announcements',
            'Remove Coordinators from BoardList',
        ];

        $subscribeListItems = [
            'Subscribe Coordinators to BoardList',
            'Subscribe Board Members to BoardList',
            'Subscribe Board Members to Public Announcements',
        ];

        $data = ['admin' => $admin, 'canEditFiles' => $canEditFiles, 'fiscalYears' => $fiscalYears,
            'resetEOYTableItems' => $resetEOYTableItems, 'displayCoorindatorMenuItems' => $displayCoorindatorMenuItems, 'displayChapterButtonItems' => $displayChapterButtonItems,
            'displayTestingItemsItems' => $displayTestingItemsItems, 'displayLiveItemsItems' => $displayLiveItemsItems, 'unSubscribeListItems' => $unSubscribeListItems,
            'resetAFTERtestingItems' => $resetAFTERtestingItems, 'updateUserTablesItems' => $updateUserTablesItems, 'subscribeListItems' => $subscribeListItems,
        ];

        return view('admin.eoy')->with($data);
    }

    /**
     * Reset Disbanded Users to NOT active
     */
    public function resetDisbandedUsers(): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Make disbanded users inactive
            DB::table('users')
                ->where('user_type', 'disbanded')
                ->where('active_status', '1')
                ->update([
                    'active_status' => '0',
                ]);

            DB::commit();

            $message = 'Disbanded users successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('admin.disbandedboard'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('admin.disbandedboard'),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Reset Outgoing Users to NOT active
     */
    public function resetOutgoingUsers(): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Make outgoing users inactive
            DB::table('users')
                ->where('user_type', 'outgoing')
                ->where('active_status', '1')
                ->update([
                    'active_status' => '0',
                ]);

            DB::commit();

            $message = 'Outgoing users successfully updated';

            // Return JSON response
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'redirect' => route('admin.outgoingboard'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            // Return JSON error response
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'redirect' => route('admin.outgoingboard'),
            ]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Reset EOY Procedurles for New year
     */
    public function resetYear(): JsonResponse
    {
        try {
            // Create a new Admin instance
            $admin = new Admin;

            // Calculate the fiscal year (current year - next year)
            $currentYear = Carbon::now()->year;
            $nextYear = $currentYear + 1;
            $fiscalYear = $currentYear.'-'.$nextYear;

            // Set the fiscal year field
            $admin->fiscal_year = $fiscalYear;

            // Save the new entry
            $admin->save();

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Fiscal year reset successfully.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Udate EOY Database Tables  // Step
     */
    public function updateEOYDatabase(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Get the current year +/- 1 for table renaming
            $currentYear = Carbon::now()->year;
            $nextYear = $currentYear + 1;
            $lastyear = $currentYear - 1;

            // Make outgoing users inactive
            DB::table('users')
                ->where('user_type', 'outgoing')
                ->where('active_status', '1')
                ->update([
                    'active_status' => '0',
                ]);

            // Remove Data from the `outgoing_board_member` and `incoming_board_member` tables
            BoardsOutgoing::query()->delete();
            BoardsIncoming::query()->delete();

            // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
            $chapters = Chapters::with('financialReport', 'documents')->get();
            foreach ($chapters as $chapter) {
                if ($chapter->financialReport && $chapter->documents) {
                    $document = $chapter->documents;
                    $document->balance = $chapter->financialReport->post_balance;
                    $document->save();
                }
            }

            // Copy and rename the `financial_report` table
            DB::statement("CREATE TABLE financial_report_12_$lastyear LIKE financial_report");
            DB::statement("INSERT INTO financial_report_12_$lastyear SELECT * FROM financial_report");

            // Remove Data from the `financial_report` table
            FinancialReport::query()->delete();

            // Fetch all active chapters and insert each chapter's balance into financial_report
            $activeChapters = Chapters::with('documents')->where('active_status', 1)->get();
            foreach ($activeChapters as $chapter) {
                FinancialReport::create([
                    'chapter_id' => $chapter->id,  // Ensure chapter_id is provided
                    'pre_balance' => $chapter->documents->balance,
                    'amount_reserved_from_previous_year' => $chapter->documents->balance,
                ]);
            }

            // Update chapters table: Set specified columns to NULL
            DB::table('chapters')->update([
                'boundary_issues' => null,
                'boundary_issue_notes' => null,
                'boundary_issue_resolved' => null,
            ]);

            // Update documents table: Set specified columns to NULL
            DB::table('documents')->update([
                'new_board_submitted' => null,
                'new_board_active' => null,
                'financial_report_received' => null,
                'report_received' => null,
                'financial_review_complete' => null,
                'review_complete' => null,
                'report_notes' => null,
                'report_extension' => null,
                'extension_notes' => null,
                'financial_pdf_path' => null,
                'roster_path' => null,
                'irs_path' => null,
                'statement_1_path' => null,
                'statement_2_path' => null,
                'award_path' => null,
            ]);

            // Change Year for Google Drive Financial Report Attachmnets
            DB::table('google_drive')->update([
                'eoy_uploads_year' => $nextYear,
            ]);

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'reset_eoy_tables' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Udate EOY Database Tables AFTER Testing
     */
    public function updateEOYDatabaseAFTERTesting(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Fetch all chapters with their financial reports and update the balance BEFORE removing data from table
            $chapters = Chapters::with('financialReportLastYear', 'documents')->get();
            foreach ($chapters as $chapter) {
                if ($chapter->financialReportLastYear && $chapter->documents) {
                    $document = $chapter->documents;
                    $document->balance = $chapter->financialReportLastYear->post_balance;
                    $document->save();
                }
            }

            BoardsOutgoing::query()->delete();
            BoardsIncoming::query()->delete();
            FinancialReport::query()->delete();

            // Fetch all active chapters and add balance into financial_report
            $activeChapters = Chapters::with('documents')->where('active_status', 1)->get();
            foreach ($activeChapters as $chapter) {
                FinancialReport::create([
                    'chapter_id' => $chapter->id,  // Ensure chapter_id is provided
                    'pre_balance' => $chapter->documents->balance,
                    'amount_reserved_from_previous_year' => $chapter->documents->balance,
                ]);
            }

            // Update chapters table: Set specified columns to NULL
            DB::table('chapters')->update([
                'boundary_issues' => null,
                'boundary_issue_notes' => null,
                'boundary_issue_resolved' => null,
            ]);

            // Update documents table: Set specified columns to NULL
            DB::table('documents')->update([
                'new_board_submitted' => null,
                'new_board_active' => null,
                'financial_report_received' => null,
                'report_received' => null,
                'financial_review_complete' => null,
                'review_complete' => null,
                'report_notes' => null,
                'report_extension' => null,
                'extension_notes' => null,
                'financial_pdf_path' => null,
                'roster_path' => null,
                'irs_path' => null,
                'statement_1_path' => null,
                'statement_2_path' => null,
                'award_path' => null,
            ]);

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'reset_AFTER_testing' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Data sucessfully reset.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Udate User Database Tables
     */
    public function updateDataDatabase(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Get the current month and year for table renaming
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            // Copy and rename the `chapters` table
            DB::statement("CREATE TABLE chapters_{$currentMonth}_{$currentYear} LIKE chapters");
            DB::statement("INSERT INTO chapters_{$currentMonth}_{$currentYear} SELECT * FROM chapters");

            // Copy and rename the `boards` table
            DB::statement("CREATE TABLE boards_{$currentMonth}_{$currentYear} LIKE boards");
            DB::statement("INSERT INTO boards_{$currentMonth}_{$currentYear} SELECT * FROM boards");

            // Copy and rename the `coordinators` table
            DB::statement("CREATE TABLE coordinators_{$currentMonth}_{$currentYear} LIKE coordinators");
            DB::statement("INSERT INTO coordinators_{$currentMonth}_{$currentYear} SELECT * FROM coordinators");

            // Copy and rename the `users` table
            DB::statement("CREATE TABLE users_{$currentMonth}_{$currentYear} LIKE users");
            DB::statement("INSERT INTO users_{$currentMonth}_{$currentYear} SELECT * FROM users");

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'update_user_tables' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'User data tables successfully updated, copied, and renamed.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * View EOY Teating Items
     */
    public function updateEOYTesting(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'display_testing' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Testing items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * View EOY Live Items
     */
    public function updateEOYLive(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'display_live' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Live items successfully set to view.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function updateSubscribeLists(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // Get BoardList category
            // $categoryBoardList = ForumCategory::where('title', 'BoardList')
            //     ->first();
            // $categoryIdBoardList = $categoryBoardList->id;

            // Get Public Announcement category
            $categoryPublic = ForumCategory::where('title', 'Public Announcements')
                ->first();
            $categoryIdPublic = $categoryPublic->id;

            // Get active coordinators
            $coordinatorUserIds = Coordinators::where('active_status', '1')
                ->where('on_leave', '0')
                ->get()
                ->pluck('user_id')
                ->unique();
            $activeCoordinators = User::whereIn('id', $coordinatorUserIds)->get();

            // Get board members from active chapters using with()
            $boardUserIds = Chapters::with('boards')
                ->where('active_status', 1)
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

            // foreach ($uniqueUsers as $user) {
            //     // Check if subscription already exists
            //     $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
            //         ->where('category_id', $categoryIdBoardList)
            //         ->first();

            //     if (! $existingSubscription) {
            //         ForumCategorySubscription::create([
            //             'user_id' => $user->id,
            //             'category_id' => $categoryIdBoardList,
            //         ]);
            //     }
            // }

            foreach ($activeBoards as $user) {
                // Check if subscription already exists
                $existingSubscription = ForumCategorySubscription::where('user_id', $user->id)
                    ->where('category_id', $categoryIdPublic)
                    ->first();

                if (! $existingSubscription) {
                    ForumCategorySubscription::create([
                        'user_id' => $user->id,
                        'category_id' => $categoryIdPublic,
                    ]);
                }
            }

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'subscribe_list' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully subscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    public function updateUnsubscribeLists(Request $request): JsonResponse
    {
        try {
            $user = $this->userController->loadUserInformation($request);
            $coorId = $user['user_coorId'];
            $lastUpdatedBy = $user['user_name'];
            $lastupdatedDate = date('Y-m-d H:i:s');

            // $categoryBoardList = ForumCategory::where('title', 'BoardList')
            //     ->first();

            $categoryPublic = ForumCategory::where('title', 'Public Announcements')
                ->first();

            // Delete all subscriptions for this category
            // $unsubscribeBoardList = ForumCategorySubscription::where('category_id', $categoryBoardList->id)
            //     ->delete();

            // Delete board members for this category
            $unsubscribePublic = ForumCategorySubscription::where('category_id', $categoryPublic->id)
                ->whereHas('user', function ($query) {
                    $query->where('user_type', 'board');
                })
                ->delete();

            // Update admin table: Set specified columns to 1
            DB::table('admin')
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'unsubscribe_list' => '1',
                    'updated_id' => $coorId,
                    'updated_at' => $lastupdatedDate,
                ]);

            DB::commit(); // Commit transaction

            return response()->json(['success' => 'Successfully unsubscribed to lists.']);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback Transaction
            Log::error($e); // Log the error

            return response()->json(['fail' => 'An error occurred while updating the data.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * view Google Drive Shared Folder Ids
     */
    public function showGoogleDrive(): View
    {
        $googleDrive = GoogleDrive::get();

        $data = ['googleDrive' => $googleDrive];

        return view('admin.googledrive')->with($data);
    }

    /**
     * Update Google Drive Shared Folder Ids
     */
    public function updateGoogleDrive(Request $request): JsonResponse
    {
        try {
            $drive = GoogleDrive::firstOrFail();
            $drive->ein_letter_uploads = $request->input('einLetterDrive');
            $drive->eoy_uploads = $request->input('eoyDrive');
            $drive->eoy_uploads_year = $request->input('eoyDriveYear');
            $drive->resources_uploads = $request->input('resourcesDrive');
            $drive->disband_letter = $request->input('disbandDrive');
            $drive->final_financial_report = $request->input('finalReportDrive');
            $drive->good_standing_letter = $request->input('goodStandingDrive');
            $drive->probation_letter = $request->input('probationDrive');
            $drive->irs_letter = $request->input('irsDrive');

            $drive->save();

            DB::commit();

            $message = 'Google Drive ID updated successfully';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('admin.googledrive')]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('admin.googledrive')]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * view Email Addresses not assigned by positionId
     */
    public function showAdminEmail(): View
    {
        $adminEmail = AdminEmail::get();

        $data = ['adminEmail' => $adminEmail];

        return view('admin.adminemail')->with($data);
    }

    /**
     * Update Email Addresses not assigned by positionId
     */
    public function updateAdminEmail(Request $request): JsonResponse
    {
        try {
            $email = AdminEmail::firstOrFail();
            $email->list_admin = $request->input('listAdminEmail');
            $email->payments_admin = $request->input('paymentAdminEmail');
            $email->ein_admin = $request->input('einAdminEmail');
            $email->gsuite_admin = $request->input('gsuiteAdminEmail');
            $email->mimi_admin = $request->input('mimiAdminEmail');

            $email->save();

            DB::commit();

            $message = 'Admin Emails updated successfully';

            return response()->json(['status' => 'success', 'message' => $message, 'redirect' => route('admin.adminemail')]);

        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            $message = 'Something went wrong, Please try again.';

            return response()->json(['status' => 'error', 'message' => $message, 'redirect' => route('admin.adminemail')]);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Delete Chapter/Board from Database -- cannot be undone!
     */
    public function updateChapterDelete(Request $request): JsonResponse
    {
        $input = $request->all();
        $chapterid = $input['chapterid'];
        $activeStatus = $input['activeStatus'];

        try {
            DB::beginTransaction();

            // Delete chapter and related data
            Chapters::where('id', $chapterid)->delete();
            Documents::where('chapter_id', $chapterid)->delete();
            FinancialReport::where('chapter_id', $chapterid)->delete();

            // Get board details based on status
            $boardDetails = collect(); // Initialize empty collection

            if ($activeStatus == 'Active') {
                $boardDetails = Boards::where('chapter_id', $chapterid)->get();
                Boards::where('chapter_id', $chapterid)->delete(); // Delete from Boards table
            } elseif ($activeStatus == 'Zapped') {
                $boardDetails = BoardsDisbanded::where('chapter_id', $chapterid)->get();
                BoardsDisbanded::where('chapter_id', $chapterid)->delete(); // Delete from BoardsDisbanding table
            } elseif ($activeStatus == 'Pending' || $activeStatus == 'Not Approved') {
                $boardDetails = BoardsPending::where('chapter_id', $chapterid)->get();
                BoardsPending::where('chapter_id', $chapterid)->delete(); // Delete from BoardsPending table
            }

            // Delete users and subscriptions if board members exist
            if ($boardDetails->isNotEmpty()) {
                $userIds = $boardDetails->pluck('user_id')->toArray();
                User::whereIn('id', $userIds)->delete();
                ForumCategorySubscription::whereIn('user_id', $userIds)->delete();
            }

            DB::commit();

            return response()->json(['success' => 'Chapter successfully deleted.']);
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * Delete Coordinator from Database -- cannot be undone!
     */
    public function updateCoordinatorDelete(Request $request): JsonResponse
    {
        $input = $request->all();
        $coordid = $input['coordid'];
        // Note: You're not using activeStatus in this function, so you might not need it

        try {
            DB::beginTransaction();

            // Delete coordinator and related data
            CoordinatorApplication::where('coordinator_id', $coordid)->delete();
            CoordinatorRecognition::where('coordinator_id', $coordid)->delete();
            CoordinatorTree::where('coordinator_id', $coordid)->delete();

            // Get coordinator details BEFORE deleting the coordinator record
            $coordDetails = Coordinators::where('id', $coordid)->first(); // Use first() instead of get()

            // Get the user_id before deleting the coordinator
            $userId = $coordDetails->user_id;

            // Delete the coordinator record
            Coordinators::where('id', $coordid)->delete();

            // Delete user and related subscriptions
            if ($userId) {
                User::where('id', $userId)->delete(); // Use where() instead of whereIn() for single ID
                ForumCategorySubscription::where('user_id', $userId)->delete(); // Use where() instead of whereIn()
            }

            DB::commit();

            return response()->json(['success' => 'Coordinator successfully deleted.']); // Fixed message
        } catch (\Exception $e) {
            DB::rollback();  // Rollback Transaction
            Log::error($e);  // Log the error

            return response()->json(['fail' => 'Something went wrong, Please try again.'], 500);
        } finally {
            // This ensures DB connections are released even if exceptions occur
            DB::disconnect();
        }
    }

    /**
     * view Conference & Region Lists
     */
    public function showConfRegList(): View
    {
        $confList = Conference::orderBy('id')
            ->with(['regions' => function ($query) {
                $query->orderBy('short_name');
            }])
            ->get();

        $data = ['confList' => $confList];

        return view('admin.confreglist')->with($data);
    }

    public function editConfList(): View
    {
        $confList = Conference::orderBy('id')
            ->get();

        $data = ['confList' => $confList];

        return view('admin.editconflist')->with($data);
    }

    public function updateConfList(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:conference,id',
                'conference_name' => 'required|string|max:255',
                'short_name' => 'required|string|max:50',
                'conference_description' => 'required|string|max:500',
                'short_description' => 'required|string|max:10',
            ]);

            $conference = Conference::findOrFail($validated['id']);
            $conference->update([
                'conference_name' => $validated['conference_name'],
                'short_name' => $validated['short_name'],
                'conference_description' => $validated['conference_description'],
                'short_description' => $validated['short_description'],
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function storeConf(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate([
                'conference_name' => 'required|string|max:255',
                'short_name' => 'required|string|max:50',
                'conference_description' => 'required|string|max:500',
                'short_description' => 'required|string|max:10',
            ]);

            $conference = Conference::create($validated);

            return response()->json(['success' => true, 'id' => $conference->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function deleteConf($id): JsonResponse
    {
        try {
            $conference = Conference::findOrFail($id);
            $conference->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
