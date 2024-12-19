<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckCurrentPasswordUserRequest;
use App\Http\Requests\UpdatePasswordUserRequest;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Coordinators;
use App\Models\ReportingTree;
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
            // Update the user's password
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
     * Get Reporting Tree -- for coordinator utilization report
     */
    // public function loadReportingTree($corId)
    // {
    //       $corDetails = DB::table('coordinators as cd')
    //         ->select('cd.*')
    //         ->where('cd.id', $corId)
    //         ->get();
    //     $corlayerId = $corDetails[0]->layer_id;
    //     $sqlLayerId = 'crt.layer'.$corlayerId;

    //     $reportIdList = DB::table('coordinator_reporting_tree as crt')
    //         ->select('crt.coordinator_id as id')
    //         ->where($sqlLayerId, '=', $corId)
    //         ->get();
    //     $inQryStr = '';
    //     foreach ($reportIdList as $key => $val) {
    //         $inQryStr .= $val->id.',';
    //     }
    //     $inQryStr = rtrim($inQryStr, ',');
    //     $inQryArr = explode(',', $inQryStr);

    //     return ['inQryArr' => $inQryArr];
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

    /**
     * load Email Details -- Mail for full Board and full Coordinator Downline
     */
    public function loadEmailDetails($chId)
    {
        $chapterList = DB::table('chapters')
            ->select('chapters.id as id', 'chapters.email as chap_email', 'chapters.primary_coordinator_id as primary_coordinator_id',
                'chapters.name as name', 'st.state_short_name as state')
            ->leftJoin('state as st', 'chapters.state_id', '=', 'st.id')
            ->where('chapters.id', '=', $chId)
            ->first();

        $documentList = DB::table('documents')
            ->select('documents.financial_report_received as report_received', 'documents.new_board_submitted as board_submitted',
                'documents.ein_letter as ein_letter')
            ->where('documents.chapter_id', '=', $chId)
            ->first();

        $chapEmail = trim($chapterList->chap_email); // Trim spaces from chapter email

        $boardEmailList = DB::table('boards as bd')
            ->select('bd.email as bor_email')
            ->where('bd.chapter_id', '=', $chId)
            ->get();

        $emailListBoard = [];
        foreach ($boardEmailList as $val) {
            $email = trim($val->bor_email); // Trim spaces from each email
            if (! empty($email)) { // Check for non-empty email
                $escaped_email = str_replace("'", "\\'", $email);
                $emailListBoard[] = $escaped_email; // Add to the array
            }
        }

        $emailListChap = $emailListBoard; // Copy the board emails to the chapter email list
        if (! empty($chapEmail)) {
            $emailListChap[] = $chapEmail; // Add the chapter email if it's not empty
        }

        $coordinatorEmailList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('coordinator_id', '=', $chapterList->primary_coordinator_id)
            ->get();

        $coordinatorList = [];
        foreach ($coordinatorEmailList as $key => $value) {
            $coordinatorList[$key] = (array) $value;
        }
        $filterCoordinatorList = array_filter($coordinatorList[0]);
        unset($filterCoordinatorList['id'], $filterCoordinatorList['coordinator_id'], $filterCoordinatorList['layer0']);
        $filterCoordinatorList = array_reverse($filterCoordinatorList);

        $emailListCoord = [];
        foreach ($filterCoordinatorList as $key => $val) {
            if ($val > 1) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.email as cord_email')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();
                if (count($corList) > 0) {
                    $emailCoord = trim($corList[0]->cord_email); // Trim spaces
                    if (! empty($emailCoord)) {
                        $emailListCoord[] = $emailCoord; // Add to the array
                    }
                }
            }
        }

        return [
            'chapEmail' => $chapEmail,
            'emailListChap' => $emailListChap, // Return as an array
            'emailListChapString' => implode(';', $emailListChap), // Return as a comma-separated string
            'emailListCoord' => $emailListCoord, // Return as an array
            'emailListCoordString' => implode(';', $emailListCoord), // Return as a comma-separated string
            'board_submitted' => $documentList->board_submitted,
            'report_received' => $documentList->report_received,
            'ein_letter' => $documentList->ein_letter,
            'name' => $chapterList->name,
            'state' => $chapterList->state,
        ];
    }

    /**
     * Get Full Coordinator List Tree -- for displaying ALL coordinatrs on Chapter pages
     */
    public function loadCoordinatorList($id): JsonResponse
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('*')
            ->where('coordinator_id', '=', $id)
            ->get();

        if ($reportingList->isNotEmpty()) {
            $reportingList = (array) $reportingList[0];
            $filterReportingList = array_filter($reportingList);
            unset($filterReportingList['id'], $filterReportingList['coordinator_id'], $filterReportingList['layer0']);
            $filterReportingList = array_reverse($filterReportingList);

            // Inside your loadCoordinatorList method
            $str = ''; // Initialize with an empty string

            $i = 0;
            foreach ($filterReportingList as $key => $val) {
                $corList = DB::table('coordinators as cd')
                    ->select('cd.first_name as fname', 'cd.last_name as lname', 'cd.email as email', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
                    ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
                    ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
                    ->where('cd.id', '=', $val)
                    ->where('cd.is_active', '=', 1)
                    ->get();

                if ($corList->isNotEmpty()) {
                    $name = $corList[0]->fname.' '.$corList[0]->lname;
                    $email = $corList[0]->email;
                    $position = ! empty($corList[0]->sec_pos) ? "({$corList[0]->pos}/{$corList[0]->sec_pos})" : "({$corList[0]->pos})";

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

            if ($str == '') {
                $str = 'No coordinators found for the given ID.';
            }

            return response()->json($str);
        } else {
            return response()->json("<li class='list-group-item'>No reporting data found for the given ID.</li>");
        }
    }

    /**
     * Load Conference Coordinators for each Conference BASED on the Primary Coordinator of the Chapter
     */
    public function loadConferenceCoord($chPcid)
    {
        $reportingList = DB::table('coordinator_reporting_tree')
            ->select('layer1')
            ->where('coordinator_id', '=', $chPcid)
            ->first();  // Use first() to get a single result

        $layer1 = $reportingList->layer1;  // Get the value of layer1 directly

        $ccDetails = DB::table('coordinators as cd')
            ->select('cd.id as cc_id', 'cd.conference_id as cc_conf', 'cd.first_name as cc_fname', 'cd.last_name as cc_lname', 'cd.email as cc_email', 'cp.long_title as cc_pos',
                'cf.conference_description as cc_conf_desc', 'cf.conference_name as cc_conf_name')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->join('conference as cf', 'cd.conference_id', '=', 'cf.id')
            ->where('cd.id', '=', $layer1)  // Use the layer1 value directly here
            ->where('cd.is_active', '=', 1)
            ->get();

        $cor = $ccDetails->first();  // Get the first item from the collection

        $cc_id = $cor->cc_id;
        $cc_fname = $cor->cc_fname;
        $cc_lname = $cor->cc_lname;
        $cc_pos = $cor->cc_pos;
        $cc_email = $cor->cc_email;
        $cc_conf = $cor->cc_conf;
        $cc_conf_name = $cor->cc_conf_name;
        $cc_conf_desc = $cor->cc_conf_desc;

        return ['cc_id' => $cc_id, 'cc_fname' => $cc_fname, 'cc_lname' => $cc_lname, 'cc_pos' => $cc_pos, 'cc_email' => $cc_email, 'cc_conf' => $cc_conf,
            'cc_conf_desc' => $cc_conf_desc, 'cc_conf_name' =>$cc_conf_name
        ];

    }

    /**
     * Load Primkary Dropdown List
     */
    public function loadPrimaryList($chRegId, $chConfId)
    {
        $pcList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as cor_f_name', 'cd.last_name as cor_l_name', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->join('coordinators as cd', 'cd.id', '=', 'ch.primary_coordinator_id')
            ->join('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function ($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function ($subQuery) use ($chConfId) {
                        $subQuery->where('cd.region_id', '=', 0)
                            ->where('cd.conference_id', $chConfId);
                    });
            })
            ->whereBetween('cd.position_id', [1, 7])
            ->where('cd.is_active', '=', '1')
            ->groupBy('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title', 'pos2.short_title')
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->get();

        $cor_f_name = $pcList[0]->cor_f_name;
        $cor_l_name = $pcList[0]->cor_l_name;
        $pos = $pcList[0]->pos;
        $secpos = $pcList[0]->sec_pos;
        $cid = $pcList[0]->cid;

        return ['pcList' => $pcList, 'cor_f_name' => $cor_f_name, 'cor_l_name' => $cor_l_name, 'pos' => $pos, 'secpos' => $secpos,
            'cid' => $cid,];
    }

    /**
     * Load Reviewer Dropdown List
     */
    public function loadReviewerList($chRegId, $chConfId)
    {
        $revList = DB::table('chapters as ch')
            ->select('cd.id as cid', 'cd.first_name as rfname', 'cd.last_name as rlname', 'cp.short_title as pos', 'pos2.short_title as sec_pos')
            ->leftJoin('financial_report as fr', 'fr.chapter_id', '=', 'ch.id')
            ->leftJoin('coordinators as cd', 'fr.reviewer_id', '=', 'cd.id')
            ->leftJoin('coordinator_position as cp', 'cd.display_position_id', '=', 'cp.id')
            ->leftJoin('coordinator_position as pos2', 'pos2.id', '=', 'cd.sec_position_id')
            ->where(function ($query) use ($chRegId, $chConfId) {
                $query->where('cd.region_id', '=', $chRegId)
                    ->orWhere(function ($subQuery) use ($chConfId) {
                        $subQuery->where('cd.region_id', '=', 0)
                            ->where('cd.conference_id', $chConfId);
                    });
            })
            ->whereBetween('cd.position_id', [1, 7])
            ->where('cd.is_active', '=', '1')
            ->groupBy('cd.id', 'cd.first_name', 'cd.last_name', 'cp.short_title', 'pos2.short_title')
            ->orderBy('cd.position_id')
            ->orderBy('cd.first_name')
            ->get();

        $rfname = $revList[0]->rfname;
        $rlname = $revList[0]->rlname;
        $pos = $revList[0]->pos;
        $secpos = $revList[0]->sec_pos;
        $cid = $revList[0]->cid;

        return ['revList' => $revList, 'rfname' => $rfname, 'rlname' => $rlname, 'pos' => $pos, 'secpos' => $secpos,
            'cid' => $cid,];
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
}
