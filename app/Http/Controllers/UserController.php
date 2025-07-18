<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordUserRequest;
use App\Http\Requests\UpdatePasswordUserRequest;
use App\Models\Boards;
use App\Models\BoardsOutgoing;
use App\Models\Chapters;
use App\Models\Coordinators;
use App\Models\CoordinatorTree;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['logout']),
        ];
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
     * Load User Information
     */
    public function loadUserInformation(Request $request)
    {
        $user = User::find($request->user()->id);

        // Basic user info that's always needed
        $userInfo = [
            'userId' => $user->id,
            'userType' => $user->user_type,
            'userAdmin' => $user->is_admin,
            'userStatus' => $user->is_active,
            'user_name' => $user->first_name.' '.$user->last_name,
            'user_email' => $user->email,
        ];

        // Only load detailed information if the user is active
        if ($user->is_active == 1) {
            // Now load relations only if needed
            $user = User::with(['coordinator', 'coordinator.region', 'coordinator.conference',
                'coordinator.displayPosition', 'coordinator.secondaryPosition',
                'board', 'board.position', 'boardOutgoing', 'boardDisbanded', 'boardPending'])
                ->find($user->id);

            switch ($user->user_type) {
                case 'coordinator':
                    $secondaryPosition = [];
                    $secondaryPositionShort = [];
                    $secondaryPositionId = [];
                    if ($user->coordinator->secondaryPosition && $user->coordinator->secondaryPosition->count() > 0) {
                        $secondaryPosition = $user->coordinator->secondaryPosition->pluck('long_title')->toArray();
                        $secondaryPositionShort = $user->coordinator->secondaryPosition->pluck('short_title')->toArray();
                        $secondaryPositionId = $user->coordinator->secondaryPosition->pluck('id')->toArray();
                    }

                    $userInfo += [
                        'user_coorId' => $user->coordinator->id,
                        'user_confId' => $user->coordinator->conference_id,
                        'user_regId' => $user->coordinator->region_id,
                        'user_conference' => $user->coordinator->conference,
                        'user_conf_name' => $user->coordinator->conference?->conference_name,
                        'user_conf_desc' => $user->coordinator->conference?->conference_description,
                        'user_region' => $user->coordinator->region,
                        'user_positionId' => $user->coordinator->position_id, // Returns position_id
                        'user_position' => $user->coordinator->displayPosition->long_title,  // Returns display title
                        'user_secPositionId' => $secondaryPositionId, // Returns array of secondary ids
                        'user_secPosition' => $secondaryPosition, // Returns array of secondary titles
                        'user_layerId' => $user->coordinator->layer_id,
                    ];
                    break;

                case 'pending':
                    $userInfo += [
                        'user_bdPendId' => $user->boardPending->id,
                        'user_pendChapterId' => $user->boardPending->chapter_id,
                    ];
                    break;

                case 'board':
                    $userInfo += [
                        'user_bdDetails' => $user->board,
                        'user_bdId' => $user->board->id,
                        'user_bdPositionId' => $user->board->board_position_id,
                        'user_bdPosition' => $user->board->position?->postion,
                        'user_chapterId' => $user->board->chapter_id,
                    ];
                    break;

                case 'outgoing':
                    $userInfo += [
                        'user_bdOutId' => $user->boardOutgoing->id,
                        'user_outChapterId' => $user->boardOutgoing->chapter_id,
                    ];
                    break;

                case 'disbanded':
                    $userInfo += [
                        'user_bdDisId' => $user->boardDisbanded->id,
                        'user_disChapterId' => $user->boardDisbanded->chapter_id,
                    ];
                    break;
            }
        }

        return $userInfo;
    }

    /**
     * Get Reporting Tree -- for chapter display based on chapters reporting to logged in PC and coordinators under them
     */
    public function loadReportingTree($cdId)
    {
        $cdDetails = Coordinators::find($cdId);
        $cdLayerId = $cdDetails->layer_id;
        $layerColumn = 'layer'.$cdLayerId; // Dynamic layer column based on the layer ID

        $reportIds = CoordinatorTree::where($layerColumn, '=', $cdId)
            ->pluck('coordinator_id'); // Get only the IDs directly

        $inQryArr = $reportIds->toArray();  // Convert the collection of IDs to an array

        return ['inQryArr' => $inQryArr];
    }

    /**
     * load Email Details -- Mail for full Board AND full Coordinator Downline
     */
    public function loadEmailDetails($chId)
    {
        $chDetails = Chapters::with(['state', 'documents', 'boards', 'coordinatorTree', 'boardsDisbanded'])->find($chId);
        $chActiveId = $chDetails->active_status;
        $chEmail = trim($chDetails->email);
        $stateShortName = $chDetails->state->state_short_name ?? '';
        $documents = $chDetails->documents()->first();

        if ($chActiveId == '1') {
            $boards = $chDetails->boards()->pluck('email')->filter()->toArray();
        } else {
            $boards = $chDetails->boardsDisbanded()->pluck('email')->filter()->toArray();
        }

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
     * load Email Details -- Mail for Coordinator Downline bassed on CoordId
     */
    public function loadCoordEmailDetails($cdId)
{
    $cdDetails = Coordinators::with(['coordTree'])->find($cdId);
    $coordinators = $cdDetails->coordTree()->get();

    $coordinatorList = collect($coordinators)
            ->filter(function ($coordTree) use ($cdId) {
                // Only process rows where one of the layers contains our $cdId
                $attributes = $coordTree->getAttributes();
                for ($i = 1; $i <= 8; $i++) {
                    if (($attributes['layer'.$i] ?? null) == $cdId) {
                        return true;
                    }
                }
                return false;
            })
            ->flatMap(function ($value) use ($cdId) {
                $attributes = $value->getAttributes();
                $foundLayer = null;

                // Find which layer contains our $cdId
                for ($i = 1; $i <= 8; $i++) {
                    if (($attributes['layer'.$i] ?? null) == $cdId) {
                        $foundLayer = $i;
                        break;
                    }
                }

                if (!$foundLayer) return [];

                // Return $cdId and all layers BEFORE it (upline hierarchy)
                return collect(range(1, $foundLayer))
                    ->map(fn ($i) => $attributes['layer'.$i] ?? null)
                    ->filter(fn ($id) => is_numeric($id));
            })
            ->unique();

    // Get the main coordinator's email (the "to" email)
    $toEmail = Coordinators::where('id', $cdId)
        ->where('active_status', 1)
        ->where('on_leave', '!=', 1)
        ->pluck('email')
        ->filter()
        ->first();

    // Get upline emails (excluding the main coordinator for CC)
    $ccEmailList = Coordinators::whereIn('id', $coordinatorList->reject($cdId))
        ->where('active_status', 1)
        ->where('on_leave', '!=', 1)
        ->pluck('email')
        ->filter()
        ->toArray();

    return [
        'toCoordEmail' => $toEmail,
        'ccCoordEmailList' => $ccEmailList,
    ];
}


   public function loadCoordEmailDownlineDetails($cdId)
{
    // Find all coordinators whose coordTree contains $cdId in any layer
    $downlineCoordinators = Coordinators::with(['coordTree'])
        ->where('active_status', 1)
        ->where('on_leave', '!=', 1)
        ->whereHas('coordTree', function($query) use ($cdId) {
            for ($i = 1; $i <= 8; $i++) {
                $query->orWhere("layer{$i}", $cdId);
            }
        })
        ->get();

    // Extract only the coordinator IDs that are BELOW $cdId in the hierarchy
    $coordinatorList = collect($downlineCoordinators)
            ->flatMap(function ($coordinator) use ($cdId) {
                $coordTree = $coordinator->coordTree()->first();
                if (!$coordTree) return [];

                $attributes = $coordTree->getAttributes();
                $foundLayer = null;

                // Find which layer contains our $cdId
                for ($i = 1; $i <= 8; $i++) {
                    if (($attributes['layer'.$i] ?? null) == $cdId) {
                        $foundLayer = $i;
                        break;
                    }
                }

                if (!$foundLayer) return [];

                // Return only layers AFTER the found layer (lower in hierarchy)
                return collect(range($foundLayer + 1, 8))
                    ->map(fn ($i) => $attributes['layer'.$i] ?? null)
                    ->filter(fn ($id) => is_numeric($id));
            })
            ->unique();

    $emailListCoord = Coordinators::whereIn('id', $coordinatorList)
        ->where('active_status', 1)
        ->where('on_leave', '!=', 1)
        ->pluck('email')
        ->filter()
        ->toArray();

    return [
        'emailListCoord' => $emailListCoord,
    ];
}

    /**
     * load Coordinator List for a PC selected and their downline
     */
    public function loadCoordinatorList($chPcId): JsonResponse
    {
        $coordiantors = CoordinatorTree::with('coordinator')
            ->where('coordinator_id', $chPcId)
            ->first();
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
                ->where('active_status', 1)
                ->orderByRaw('FIELD(id, '.implode(',', $coordinatorList).')') // Ensure order is based on reversed IDs
                ->get();

            // Iterate over coordinators in the reversed order
            foreach ($coordinators as $cor) {
                $name = $cor->first_name.' '.$cor->last_name;
                $email = $cor->email;
                $displayPosition = $cor->displayPosition ? $cor->displayPosition->short_title : '';
                $secondaryTitles = '';

                // Handle secondary positions
                if (! empty($cor->secondaryPosition) && $cor->secondaryPosition->count() > 0) {
                    $secondaryTitles = $cor->secondaryPosition->pluck('short_title')->implode('/');
                }

                // Combine primary and secondary positions
                $position = '';
                if ($displayPosition) {
                    $position = "({$displayPosition}";

                    if (! empty($secondaryTitles)) {
                        $position .= '/'.$secondaryTitles;
                    }

                    $position .= ')';
                }

                // Set the title based on the iteration index
                $title = match ($i) {
                    0 => 'Primary Coordinator:',
                    1 => 'Secondary Coordinator:',
                    2 => 'Additional Coordinator:',
                    default => ''
                };

                // Build the final string
                $str .= "<b>{$title}</b><span class='float-right'><a href='mailto:{$email}' target='_top'>{$name}</a> {$position}</span><br>";
                $i++;
            }
        }

        return response()->json($str);
    }

    public function loadReportToCoord($cdId)
    {
        $rcDetails =  Coordinators::with(['displayPosition', 'secondaryPosition', 'reportsTo'])
                ->where('id', '=', $cdId)
            ->first(); // Returns a single model instance

        $rc_id = $rcDetails->reportsTo->id ?? null;
        $rc_fname = $rcDetails->reportsTo->first_name ?? null;
        $rc_lname = $rcDetails->reportsTo->last_name ?? null;
        $rc_email = $rcDetails->reportsTo->email ?? null;
        $rc_pos = $rcDetails->reportsTo->displayPosition->long_title ?? null;

        return ['rc_id' => $rc_id, 'rc_fname' => $rc_fname, 'rc_lname' => $rc_lname, 'rc_pos' => $rc_pos, 'rc_email' => $rc_email,
        ];
    }

    /**
     * Load Conference Coordinator Information for each Conference based on the PC selected - used for emails and pdfs
     */
    public function loadConferenceCoord($chPcid)
    {
        $layer1 = CoordinatorTree::where('coordinator_id', $chPcid)
            ->value('layer1'); // Fetch only the value of the 'layer1' column

        $ccDetails = Coordinators::with(['displayPosition', 'conference'])
            ->where('id', $layer1)
            ->where('active_status', 1)
            ->first(); // Fetch only the first record directly

        $cc_id = $ccDetails?->id;
        $cc_layer_id = $ccDetails?->layer_id;
        $cc_fname = $ccDetails?->first_name;
        $cc_lname = $ccDetails?->last_name;
        $cc_email = $ccDetails?->email;
        $cc_phone = $ccDetails?->phone;
        $cc_conf_name = $ccDetails?->conference->conference_name;
        $cc_conf_desc = $ccDetails?->conference->conference_description;
        $cc_pos = $ccDetails?->displayPosition->long_title;

        return ['cc_id' => $cc_id, 'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_email' => $cc_email,
            'cc_conf_name' => $cc_conf_name, 'cc_conf_desc' => $cc_conf_desc, 'cc_id' => $cc_id, 'cc_phone' => $cc_phone,
            'cc_layer_id' => $cc_layer_id,
        ];
    }

    /**
     * Load Conference Coordinator Information for each Conference based on the Conference selected - used for emails and pdfs
     */
    public function loadConferenceCoordConf($cdConfId)
    {
        $ccDetails = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('conference_id', $cdConfId)
            ->where('position_id', 7)
            ->where('active_status', 1)
            ->where('on_leave', '!=', '1')
            ->first();

        $cc_id = $ccDetails?->id;
        $cc_layer_id = $ccDetails?->layer_id;
        $cc_fname = $ccDetails?->first_name;
        $cc_lname = $ccDetails?->last_name;
        $cc_email = $ccDetails?->email;
        $cc_phone = $ccDetails?->phone;
        $cc_conf_name = $ccDetails?->conference->conference_name;
        $cc_conf_desc = $ccDetails?->conference->conference_description;
        $cc_pos = $ccDetails?->displayPosition->long_title;

        return ['cc_id' => $cc_id, 'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_email' => $cc_email,
            'cc_conf_name' => $cc_conf_name, 'cc_conf_desc' => $cc_conf_desc, 'cc_id' => $cc_id, 'cc_phone' => $cc_phone,
            'cc_layer_id' => $cc_layer_id,
        ];
    }

    /**
     * Load EIN Coordinator Information  - used for emails and pdfs
     */
    public function loadEINCoord()
    {
        $query = Coordinators::with(['displayPosition', 'secondaryPosition'])
            ->where('coordinators.active_status', 1)
            ->where(function ($q) {
                $q->where('coordinators.position_id', '12')
                    ->orWhereHas('secondaryPosition', function ($subQuery) {
                        $subQuery->where('coordinator_position.id', '12'); // Assuming positions is your positions table
                    });
            })
            ->first();

        $ccDetails = $query;

        $ein_id = $ccDetails?->id;
        $ein_fname = $ccDetails?->first_name;
        $ein_lname = $ccDetails?->last_name;
        $ein_email = $ccDetails?->email;
        $ein_phone = $ccDetails?->phone;

        return [
            'ein_id' => $ein_id,
            'ein_fname' => $ein_fname,
            'ein_lname' => $ein_lname,
            'ein_email' => $ein_email,
            'ein_phone' => $ein_phone,
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
                    ->where('active_status', 1);
            },
        ])->get();

        $pcList = $chList->pluck('primaryCoordinator')->filter();

        $pcDetails = $pcList->map(function ($coordinator) {
            $mainTitle = $coordinator->displayPosition->short_title ?? '';
            $secondaryTitles = '';

            if (! empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0) {
                $secondaryTitles = $coordinator->secondaryPosition->pluck('short_title')->implode('/');
            }

            $combinedTitle = $mainTitle;
            if (! empty($secondaryTitles)) {
                $combinedTitle .= '/'.$secondaryTitles;
            }

            $cpos = "({$combinedTitle})";

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $mainTitle,
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
     // Remove the reportReviewer relationship entirely and just query coordinators directly
public function loadReviewerList($chRegId, $chConfId)
{
    $rrList = Coordinators::with(['displayPosition', 'secondaryPosition'])
        ->where(function ($q) use ($chRegId, $chConfId) {
            $q->where('region_id', $chRegId)
                ->orWhere(function ($subQuery) use ($chConfId) {
                    $subQuery->where('region_id', 0)
                        ->where('conference_id', $chConfId);
                });
        })
        ->whereBetween('position_id', [1, 7])
        ->where('active_status', 1)
        ->get();

    $rrDetails = $rrList->map(function ($coordinator) {
        $mainTitle = $coordinator->displayPosition->short_title ?? '';
        $secondaryTitles = '';

        if (!empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0) {
            $secondaryTitles = $coordinator->secondaryPosition->pluck('short_title')->implode('/');
        }

        $combinedTitle = $mainTitle;
        if (!empty($secondaryTitles)) {
            $combinedTitle .= '/' . $secondaryTitles;
        }

        $cpos = "({$combinedTitle})";

        return [
            'cid' => $coordinator->id,
            'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
            'dpos' => $mainTitle,
            'cpos' => $cpos,
            'regid' => $coordinator->region_id,
        ];
    });

    return $rrDetails->unique('cid');
}

    /**
     * Load Reports To Dropdown List
     */
    public function loadReportsToList($cdId, $cdConfId, $cdPositionid)
    {
        if ($cdConfId == 0 || $cdPositionid == 7) {
            $rcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
                ->where('position_id', '>', 7)
                ->where('position_id', '>=', $cdPositionid)
                ->where('id', '!=', $cdId)
                ->where('active_status', 1)
                ->where('on_leave', '!=', '1')
                ->get();
        } else {
            $rcList = Coordinators::with(['displayPosition', 'secondaryPosition'])
                ->where('conference_id', $cdConfId)
                ->whereBetween('position_id', [3, 7])
                ->where('position_id', '>=', $cdPositionid)
                ->where('id', '!=', $cdId)
                ->where('active_status', 1)
                ->where('on_leave', '!=', '1')
                ->get();
        }

        $rcDetails = $rcList->map(function ($coordinator) {
            $mainTitle = $coordinator->displayPosition->short_title ?? '';
            $secondaryTitles = '';

            if (! empty($coordinator->secondaryPosition) && $coordinator->secondaryPosition->count() > 0) {
                $secondaryTitles = $coordinator->secondaryPosition->pluck('short_title')->implode('/');
            }

            $combinedTitle = $mainTitle;
            if (! empty($secondaryTitles)) {
                $combinedTitle .= '/'.$secondaryTitles;
            }

            $cpos = "({$combinedTitle})";

            return [
                'cid' => $coordinator->id,
                'cname' => "{$coordinator->first_name} {$coordinator->last_name}",
                'dpos' => $mainTitle,
                'cpos' => $cpos,
                'regid' => $coordinator->region_id,
            ];
        });

        $rcDetails = $rcDetails->unique('cid');  // Remove duplicates based on the 'cid' field

        return $rcDetails; // Return all coordinators as a collection
    }

    /**
     * Update User to Outgoing when replaced on board
     */
    public function updateUserToOutgoing($userId, $lastUpdatedBy, $lastupdatedDate)
    {
        $boardDetails = Boards::where('user_id', $userId)->get();

        User::where('id', $userId)->update([
            'user_type' => 'outgoing',
            'updated_at' => $lastupdatedDate,
        ]);

        BoardsOutgoing::updateOrCreate(
            [
                'user_id' => $boardDetails->user_id,
                'chapter_id' => $boardDetails->chapter_id,
                'board_position_id' => $boardDetails->board_position_id,
            ],
            [
                'first_name' => $boardDetails->first_name,
                'last_name' => $boardDetails->last_name,
                'email' => $boardDetails->email,
                'phone' => $boardDetails->phone,
                'street_address' => $boardDetails->street_address,
                'city' => $boardDetails->city,
                'state_id' => $boardDetails->state_id,
                'zip' => $boardDetails->zip,
                'country_id' => $boardDetails->country_id,
                'last_updated_by' => $lastUpdatedBy,
                'last_updated_date' => $lastupdatedDate,
            ]
        );
    }
}
