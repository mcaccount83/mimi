<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordUserRequest;
use App\Http\Requests\UpdatePasswordUserRequest;
use App\Models\Chapter;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
        $layerColumn = 'layer'.$cdLayerId; // Dynamic layer column based on the layer ID

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

        $chEmail = trim($chDetails->email);
        $stateShortName = $chDetails->state->state_short_name ?? '';
        $documents = $chDetails->documents()->first();
        $boards = $chDetails->boards()->pluck('email')->filter()->toArray();
        $coordiantors = $chDetails->coordinatorTree()->get();

        $emailListChap = $boards;
        if (! empty($chEmail)) {
            $emailListChap[] = $chEmail;
        }

        $coordinatorList = collect($coordiantors)
            ->flatMap(function ($value) {
                $attributes = $value->getAttributes();

                return collect(range(1, 8))
                    ->map(fn ($i) => $attributes['layer'.$i] ?? null)
                    ->filter(fn ($id) => is_numeric($id));
            })
            ->unique();

        $emailListCoord = Coordinators::whereIn('id', $coordinatorList)->pluck('email')->filter()->toArray();

        return [
            'chapEmail' => $chEmail,
            'emailListChap' => $emailListChap,
            'emailListCoord' => $emailListCoord,
            'board_submitted' => $documents->new_board_submitted ?? null,
            'report_received' => $documents->financial_report_received ?? null,
            'ein_letter' => $documents->ein_letter ?? null,
            'name' => $chDetails->name,
            'state' => $stateShortName,
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
            $layerKey = 'layer'.$i;
            if (! empty($attributes[$layerKey]) && is_numeric($attributes[$layerKey])) {
                $coordinatorList[] = (int) $attributes[$layerKey];
            }
        }
        $coordinatorList = array_reverse($coordinatorList);  // Reverse the list of coordinator IDs to process in reverse order

        $str = '';   // Prepare the empty string for coordinator details
        $i = 0;
        if (! empty($coordinatorList)) {
            $coordinators = Coordinators::with(['displayPosition', 'secondaryPosition'])->whereIn('id', $coordinatorList)
                ->where('is_active', 1)
                ->orderByRaw('FIELD(id, '.implode(',', $coordinatorList).')') // Ensure order is based on reversed IDs
                ->get();

            // Iterate over coordinators in the reversed order
            foreach ($coordinators as $cor) {
                $name = $cor->first_name.' '.$cor->last_name;
                $email = $cor->email;
                $displayPosition = $cor->displayPosition ? $cor->displayPosition->short_title : '';
                $secondaryPosition = $cor->secondaryPosition ? $cor->secondaryPosition->short_title : '';
                $position = '';
                if ($displayPosition || $secondaryPosition) {
                    $position = '('.$displayPosition;
                    if ($secondaryPosition) {
                        $position .= '/'.$secondaryPosition;
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

        $ccDetails = Coordinators::with(['displayPosition', 'conference'])
            ->where('id', $layer1)
            ->where('is_active', 1)
            ->first(); // Fetch only the first record directly

        $cc_id = $ccDetails->id;
        $cc_fname = $ccDetails->first_name;
        $cc_lname = $ccDetails->last_name;
        $cc_email = $ccDetails->email;
        $cc_conf_name = $ccDetails->conference->conference_name;
        $cc_conf_desc = $ccDetails->conference->conference_description;
        $cc_pos = $ccDetails->displayPosition->long_title;

        return ['cc_id' => $cc_id, 'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_email' => $cc_email,
            'cc_conf_name' => $cc_conf_name, 'cc_conf_desc' => $cc_conf_desc, 'cc_id' => $cc_id,
        ];
    }

    /**
     * Load Primkary Dropdown List
     */
    public function loadPrimaryList($chRegId, $chConfId)
    {
        $chList = Chapters::with([
            'primaryCoordinator' => function ($query) use ($chRegId, $chConfId) {
                $query->with(['displayPosition', 'secondaryPosition'])
                    ->where(function ($q) use ($chRegId, $chConfId) {
                        $q->where('region_id', $chRegId)
                            ->orWhere(function ($subQuery) use ($chConfId) {
                                $subQuery->where('region_id', 0)
                                    ->where('conference_id', $chConfId);
                            });
                    })
                    ->whereBetween('position_id', [1, 7])
                    ->where('is_active', 1);
            },
        ])->get();

        $pcList = $chList->pluck('primaryCoordinator')->filter();

        $pcDetails = $pcList->map(function ($coordinator) {
            $cpos = $coordinator->displayPosition->short_title ?? '';
            if (isset($coordinator->secondaryPosition->short_title)) {
                $cpos = "({$cpos}/{$coordinator->secondaryPosition->short_title})";
            } elseif ($cpos) {
                $cpos = "({$cpos})";
            }

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $coordinator->displayPosition->short_title ?? '',
                'spos' => $coordinator->secondaryPosition->short_title ?? '',
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $pcDetails = $pcDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        return $pcDetails; // Return all coordinators as a collection
    }

    /**
     * Load Reviewer Dropdown List
     */
    public function loadReviewerList($chRegId, $chConfId)
    {
        $chList = Chapters::with([
            'reportReviewer' => function ($query) use ($chRegId, $chConfId) {
                $query->with(['displayPosition', 'secondaryPosition'])
                    ->where(function ($q) use ($chRegId, $chConfId) {
                        $q->where('region_id', $chRegId)
                            ->orWhere(function ($subQuery) use ($chConfId) {
                                $subQuery->where('region_id', 0)
                                    ->where('conference_id', $chConfId);
                            });
                    })
                    ->whereBetween('position_id', [1, 7])
                    ->where('is_active', 1);
            },
        ])->get();

        $rrList = $chList->pluck('reportReviewer')->filter();

        $rrDetails = $rrList->map(function ($coordinator) {
            $cpos = $coordinator->displayPosition->short_title ?? '';
            if (isset($coordinator->secondaryPosition->short_title)) {
                $cpos = "({$cpos}/{$coordinator->secondaryPosition->short_title})";
            } elseif ($cpos) {
                $cpos = "({$cpos})";
            }

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $coordinator->displayPosition->short_title ?? '',
                'spos' => $coordinator->secondaryPosition->short_title ?? '',
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $rrDetails = $rrDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        return $rrDetails; // Return all coordinators as a collection
    }

     /**
     * Load Reports To Dropdown List
     */
    public function loadReportsToList($cdId, $cdConfId, $cdPositionid)
    {
        if ($cdConfId == 0 || $cdPositionid == 7){
            $rcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
                ->where('position_id', '>', 7)
                ->where('position_id', '>=', $cdPositionid)
                ->where('id', '!=', $cdId)
                ->where('is_active', 1)
                ->where('on_leave', '!=', '1')
                ->get();
        }else{
            $rcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
                ->where('conference_id', $cdConfId)
                ->whereBetween('position_id', [3, 7])
                ->where('position_id', '>=', $cdPositionid)
                ->where('id', '!=', $cdId)
                ->where('is_active', 1)
                ->where('on_leave', '!=', '1')
                ->get();
        }

        $rcDetails = $rcList->map(function ($coordinator) {
            $cpos = $coordinator->displayPosition->short_title ?? '';
            if (isset($coordinator->secondaryPosition->short_title)) {
                $cpos = "({$cpos}/{$coordinator->secondaryPosition->short_title})";
            } elseif ($cpos) {
                $cpos = "({$cpos})";
            }

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $coordinator->displayPosition->short_title ?? '',
                'spos' => $coordinator->secondaryPosition->short_title ?? '',
                'posid' => $coordinator->position_id,
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $rcDetails = $rcDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        return $rcDetails; // Return all coordinators as a collection
    }

     /**
     * Load Direct Reports To Dropdown List
     */
    // public function loadDirectReportsList($cdId, $cdConfId, $cdPositionid)
    // {
    //     $drList = Coordinators::with(['displayPosition', 'secondaryPosition'])
    //         ->where('conference_id', $cdConfId)
    //         ->whereBetween('position_id', [1, 6])
    //         ->where('position_id', '<=', $cdPositionid)
    //         ->where('id', '!=', $cdId)
    //         ->where('report_id', $cdId)
    //         ->where('is_active', 1)
    //         ->where('on_leave', '!=', '1')
    //         ->get();

    //     $drDetails = $drList->map(function ($coordinator) {
    //         $cpos = $coordinator->displayPosition->short_title ?? '';
    //         if (isset($coordinator->secondaryPosition->short_title)) {
    //             $cpos = "({$cpos}/{$coordinator->secondaryPosition->short_title})";
    //         } elseif ($cpos) {
    //             $cpos = "({$cpos})";
    //         }

    //         return [
    //             'cid' => $coordinator->id,
    //             'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
    //             'dpos' => $coordinator->displayPosition->short_title ?? '',
    //             'spos' => $coordinator->secondaryPosition->short_title ?? '',
    //             'cpos' => $cpos,
    //             'regid' => $coordinator->region_id,
    //         ];
    //     });

    //     $drDetails = $drDetails->unique('cid');  // Remove duplicates based on the 'cid' field

    //     return $drDetails; // Return all coordinators as a collection
    // }



}
