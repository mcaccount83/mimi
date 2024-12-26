<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordUserRequest;
use App\Http\Requests\UpdatePasswordUserRequest;
use App\Models\User;
use App\Models\Chapters;
use App\Models\Chapter;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('logout');
    }

    /**
     * Check Email duplication for Board Member or Coordinator when Adding or Updating
     */
    public function checkEmail($email): JsonResponse
    {
        $isExists = User::where('email', $email)->first();

        return response()->json(['exists' => (bool) $isExists]);
    }

    /**
     * Reset Password Button for Board Member or Coordinator -- Triggered by Coordinator
     */
    public function updatePassword(UpdatePasswordUserRequest $request): JsonResponse
    {
        $user = User::find($request->user_id);
        if ($user) {
            $user->password = Hash::make($request->new_password);
            $user->remember_token = null;
            $user->save();

            return response()->json(['message' => 'Password updated successfully']);
        }

        Log::warning('User not found with ID: ', [$request->user_id]);

        return response()->json(['error' => 'User not found'], 404);
    }

    /**
     * Verify Current Password for Reset
     */
    public function checkCurrentPassword(CheckCurrentPasswordUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $isValid = Hash::check($request->current_password, $user->password);

        return response()->json(['isValid' => $isValid]);
    }

    /**
     * Get Reporting Tree -- for chapter display based on chapters reporting to logged in PC and coordinators under them
     */
    public function loadReportingTree($corId)
    {
        $cdDetails = Coordinators::find($corId);
        $cdLayerId = $cdDetails->layer_id;
        $layerColumn = 'layer' . $cdLayerId; // Dynamic layer column based on the layer ID

        $reportIds = CoordinatorTree::where($layerColumn, '=', $corId)
            ->pluck('id'); // Get only the IDs directly

        $inQryArr = $reportIds->toArray();  // Convert the collection of IDs to an array

        return ['inQryArr' => $inQryArr];
    }

    /**
     * load Email Details -- Mail for full Board and full Coordinator Downline
     */
    public function loadEmailDetails($chId)
    {
        $chDetails = Chapters::with(['state', 'documents', 'boards', 'coordinatorTree'])->find($chId);
        $chEmail = trim($chDetails->email); // Trim spaces from chapter email
        $stateShortName = $chDetails->state->state_short_name;
        $documents = $chDetails->documents()->first();
        $boards = $chDetails->boards()->get();
        $coordiantors = $chDetails->coordinatorTree()->get();

        $emailListBoard = [];
        foreach ($boards as $val) {
            $email = trim($val->email); // Trim spaces from each email
            if (! empty($email)) { // Check for non-empty email
                $escaped_email = str_replace("'", "\\'", $email);
                $emailListBoard[] = $escaped_email; // Add to the array
            }
        }

        $emailListChap = $emailListBoard; // Copy the board emails to the chapter email list
        if (! empty($chEmail)) {
            $emailListChap[] = $chEmail; // Add the chapter email if it's not empty
        }

        $coordinatorList = [];
        foreach ($coordiantors as $value) {
            $attributes = $value->getAttributes(); // Use Eloquent's method to get raw attributes
            // Log::info('Extracted Attributes:', $attributes);

            for ($i = 1; $i <= 8; $i++) {
                $layerKey = 'layer' . $i;

                if (!empty($attributes[$layerKey]) && is_numeric($attributes[$layerKey])) {
                    $coordinatorList[] = (int) $attributes[$layerKey];
                }
            }
        }

        $coordinatorList = array_unique($coordinatorList);

        if (!empty($coordinatorList)) {
            $emailListCoord = DB::table('coordinators as cd')
                ->whereIn('cd.id', $coordinatorList)
                ->where('cd.is_active', 1)
                ->pluck('cd.email')
                ->filter(function ($email) {
                    return !empty(trim($email)); // Exclude empty or null emails
                })
                ->toArray();
        }

        return ['chapEmail' => $chEmail, 'emailListChap' => $emailListChap, // Return as an array
            'emailListChapString' => implode(';', $emailListChap), // Return as a comma-separated string
            'emailListCoord' => $emailListCoord, // Return as an array
            'emailListCoordString' => implode(';', $emailListCoord), // Return as a comma-separated string
            'board_submitted' => $documents->new_board_submitted ?? null,
            'report_received' => $documents->financial_report_received ?? null,
            'ein_letter' => $documents->ein_letter ?? null,
            'name' => $chDetails->name, 'state' => $stateShortName,
        ];
    }

    /**
     * load Coordinator List
     */
    public function loadCoordinatorList($chPcId): JsonResponse
    {
        $coordiantors = CoordinatorTree::with('coordinator')->find($chPcId);
        $attributes = $coordiantors->getAttributes();

        $coordinatorList = [];
        for ($i = 1; $i <= 8; $i++) {   // Get the list of coordinator IDs from layers 1-8, ignoring layer0
            $layerKey = 'layer' . $i;
            if (!empty($attributes[$layerKey]) && is_numeric($attributes[$layerKey])) {
                $coordinatorList[] = (int) $attributes[$layerKey];
            }
        }
        $coordinatorList = array_reverse($coordinatorList);  // Reverse the list of coordinator IDs to process in reverse order

        $str = '';   // Prepare the empty string for coordinator details
        $i = 0;
        if (!empty($coordinatorList)) {
            $coordinators = Coordinators::with(['coorDispPosition', 'coorSecPosition'])->whereIn('id', $coordinatorList)
                ->where('is_active', 1)
                ->orderByRaw('FIELD(id, ' . implode(',', $coordinatorList) . ')') // Ensure order is based on reversed IDs
                ->get();

            // Iterate over coordinators in the reversed order
            foreach ($coordinators as $cor) {
                $name = $cor->first_name . ' ' . $cor->last_name;
                $email = $cor->email;
                $displayPosition = $cor->coorDispPosition ? $cor->coorDispPosition->short_title : '';
                $secondaryPosition = $cor->coorSecPosition ? $cor->coorSecPosition->short_title : '';
                $position = '';
                    if ($displayPosition || $secondaryPosition) {
                        $position = '(' . $displayPosition;
                        if ($secondaryPosition) {
                            $position .= '/' . $secondaryPosition;
                        }
                        $position .= ')';
                    }

                $title = match ($i) {
                    0 => 'Primary Coordinator:',
                    1 => 'Secondary Coordinator:',
                    2 => 'Additional Coordinator:',
                    default => ''
                };

                $str .= "<b>{$title}</b><span class='float-right'><a href='mailto:{$email}' target='_top'>{$name}</a> {$position}</span><br>";
                $i++;
            }
        }

        return response()->json($str);
    }

    /**
     * Load Conference Coordinators for each Conference
     */
    public function loadConferenceCoord($chPcid)
    {
        $layer1 = CoordinatorTree::where('coordinator_id', $chPcid)
            ->value('layer1'); // Fetch only the value of the 'layer1' column

        $ccDetails = Coordinators::with(['coorDispPosition', 'coorSecPosition', 'conference'])
            ->where('id', $layer1)
            ->where('is_active', 1)
            ->first(); // Fetch only the first record directly

        $cc_id = $ccDetails->id;
        $cc_fname = $ccDetails->first_name;
        $cc_lname = $ccDetails->last_name;
        $cc_email = $ccDetails->email;
        $cc_conf = $ccDetails->conference_id;
        $cc_conf_name = $ccDetails->conference->conference_name;
        $cc_conf_desc = $ccDetails->conference->conference_description;
        $cc_pos = $ccDetails->coorDispPosition->long_title ?? 'N/A';

        return ['cc_id' => $cc_id, 'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_email' => $cc_email, 'cc_conf' => $cc_conf,
            'cc_conf_name' =>$cc_conf_name, 'cc_conf_desc' => $cc_conf_desc,
        ];
    }

    /**
     * Load Primkary Dropdown List
     */
    public function loadPrimaryList($chRegId, $chConfId)
    {
        $chList = Chapters::with([
            'primaryCoordinator' => function ($query) use ($chRegId, $chConfId) {
                $query->with(['coorDispPosition', 'coorSecPosition'])
                    ->where(function ($q) use ($chRegId, $chConfId) {
                        $q->where('region_id', $chRegId)
                        ->orWhere(function ($subQuery) use ($chConfId) {
                            $subQuery->where('region_id', 0)
                                    ->where('conference_id', $chConfId);
                        });
                    })
                    ->whereBetween('position_id', [1, 7])
                    ->where('is_active', 1);
            }
        ])->get();

        $pcList = $chList->pluck('primaryCoordinator')->filter();

        $pcDetails = $pcList->map(function ($coordinator) {
            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'cpos' => $coordinator->coorDispPosition->short_title ?? 'No Position',
            ];
        });

        // Remove duplicates based on the 'cid' field
        $pcDetails = $pcDetails->unique('cid');

        return $pcDetails; // Return all coordinators as a collection
    }

   /**
     * Load Reviewer Dropdown List
     */
    public function loadReviewerList($chRegId, $chConfId)
    {
        $chList = Chapters::with([
            'financialReport' => function ($query) {
                $query->with('reviewingCoordinator'); // Eager load the reviewingCoordinator through financialReport
            },
            'financialReport.reviewingCoordinator' => function ($query) use ($chRegId, $chConfId) {
                $query->with(['coorDispPosition', 'coorSecPosition'])
                    ->where(function ($q) use ($chRegId, $chConfId) {
                        $q->where('region_id', $chRegId)
                            ->orWhere(function ($subQuery) use ($chConfId) {
                                $subQuery->where('region_id', 0)
                                         ->where('conference_id', $chConfId);
                            });
                    })
                    ->whereBetween('position_id', [1, 7])
                    ->where('is_active', 1);
            }
        ])->get();

        // Collect all reviewing coordinators from the chapters' financial reports
        $rcList = $chList->pluck('financialReport.reviewingCoordinator')->flatten()->filter();

        // Map to extract relevant details (avoiding duplicates)
        $rcDetails = $rcList->map(function ($coordinator) {
            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'cpos' => $coordinator->coorDispPosition->short_title ?? 'No Position',
            ];
        });

        // Remove duplicates based on the 'cid' field
        $rcDetails = $rcDetails->unique('cid');

        return $rcDetails; // Return all coordinators as a collection
    }


    public function loadReviewerList2($chRegId, $chConfId)
    {
        $chList = Chapters::with([
            'financialReport.reviewingCoordinator' => function ($query) use ($chRegId, $chConfId) {
                $query->with(['coorDispPosition', 'coorSecPosition'])
                    ->where(function ($q) use ($chRegId, $chConfId) {
                        $q->where('region_id', $chRegId)
                        ->orWhere(function ($subQuery) use ($chConfId) {
                            $subQuery->where('region_id', 0)
                                    ->where('conference_id', $chConfId);
                        });
                    })
                    ->whereBetween('position_id', [1, 7])
                    ->where('is_active', 1);
            }
        ])->get();

        // Extract reviewing coordinators
        $rcList = $chList->pluck('financialReport.reviewingCoordinator')->filter();

        // Transform the data
        $rcDetails = $rcList->map(function ($coordinator) {
            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'cpos' => $coordinator->coorDispPosition->short_title ?? 'No Position',
            ];
        });

        // Remove duplicates based on the 'cid' field
        $rcDetails = $rcDetails->unique('cid');

        return $rcDetails; // Return all unique reviewing coordinators
    }


    /**
     * Load Logged in Coordinator
     */
    // public function loadUser(Request $request)
    // {
    //     // Find the user by ID from the request
    //     $user = User::find($request->user_id);
    //     $userId = $user->id;

    //     // Get the coordinator details
    //     $corList = DB::table('coordinators as cd')
    //         ->select('cd.id as cid', 'cd.first_name as fname', 'cd.last_name as lname', 'cp.long_title as pos')
    //         ->join('coordinator_position as cp', 'cd.position_id', '=', 'cp.id')
    //         ->where('cd.user_id', '=', $userId)
    //         ->get();

    //     $cor_fname = $corList[0]->fname;
    //     $cor_lname = $corList[0]->lname;
    //     $cor_pos = $corList[0]->pos;

    //     return ['corList' => $corList, 'cor_fname' => $cor_fname, 'cor_lname' => $cor_lname, 'cor_pos' => $cor_pos];
    // }

    /**
     * load Coordinator Email -- Mail for full Coordinator Upline
     */
    // public function loadCoordEmail($corId)
    // {
    //     $coordinatorDetails = DB::table('coordinators as cd')
    //         ->select('cd.*')
    //         ->where('cd.id', $corId)
    //         ->get();

    //     $coordEmail = $coordinatorDetails[0]->email;
    //     $coordSecEmail = $coordinatorDetails[0]->sec_email;

    //     $coordinatorEmailList = DB::table('coordinator_reporting_tree')
    //         ->select('*')
    //         ->where('coordinator_id', '=', $corId)
    //         ->get();

    //     foreach ($coordinatorEmailList as $key => $value) {
    //         $coordinatorList[$key] = (array) $value;
    //     }
    //     $filterCoordinatorList = array_filter($coordinatorList[0]);
    //     unset($filterCoordinatorList['id']);
    //     unset($filterCoordinatorList['coordinator_id']);
    //     unset($filterCoordinatorList['layer0']);
    //     $filterCoordinatorList = array_reverse($filterCoordinatorList);
    //     $str = '';
    //     $array_rows = count($filterCoordinatorList);
    //     $i = 0;

    //     $emailListCoord = '';
    //     foreach ($filterCoordinatorList as $key => $val) {
    //         if ($val > 1) {
    //             $corList = DB::table('coordinators as cd')
    //                 ->select('cd.email as cord_email')
    //                 ->where('cd.id', '=', $val)
    //                 ->where('cd.is_active', '=', 1)
    //                 ->get();
    //             if (count($corList) > 0) {
    //                 $cordEmail = $corList[0]->cord_email;
    //                 // Check if the cord_email is different from coordEmail
    //                 if ($cordEmail !== $coordEmail) {
    //                     if ($emailListCoord == '') {
    //                         $emailListCoord = $cordEmail;
    //                     } else {
    //                         $emailListCoord .= ','.$cordEmail;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return ['coordEmail' => $coordEmail, 'coordSecEmail' => $coordSecEmail, 'emailListCoord' => $emailListCoord];
    // }


}
